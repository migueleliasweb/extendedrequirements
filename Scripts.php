<?php

use Composer\Script\Event;

namespace migueleliasweb\extendedrequirements;

class Scripts
{

    const CUSTOM_EXTRA_KEY = 'extended-script-requirements';
    
    private $AllowedConstraints = array ('<', 'lt', '<=', 'le', '>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=', '<>', 'ne');

    public function getAllowedConstraints () {
        return $this->AllowedConstraints;
    }
    
    public static function postCmd (Event $Event) {
        $ExtraConfigs = $Event->getComposer()->getConfig()->get('extra');

        foreach ($ExtraConfigs[CUSTOM_EXTRA_KEY] as $ExtraRequirement) {
            $Requirement = ucfirst(key($ExtraRequirement));
            $RequiredVersion = ucfirst(key($ExtraRequirement));
            $MethodName = 'get'.$Requirement.'Version';
            
            if (method_exists($this, $MethodName)) {
                $InstalledVersion = Script::$MethodName();
            } elseif (class_exists($Requirement)) {
                $InstalledVersion = new $Requirement();
            } elseif (file_exists($Requirement) AND is_executable($Requirement)) {
                $InstalledVersion = shell_exec($Requirement);
            } else {
                $InstalledVersion = false;
            }
            
            try {
                if ($this->compareVersionRequirements($RequiredVersion, $InstalledVersion)) {
                    $Event->getIo()->write('Requirement verification succeeded for: ' . $Requirement);
                } else {
                    $Event->getIo()->write('Requirement verification failed for: ' . $Requirement);

                    if ($InstalledVersion !== false) {
                        $Event->getIo()->write('--- Required version: '.$RequiredVersion.', installed version: '.$InstalledVersion);
                    } else {
                        $Event->getIo()->write('--- File not found or not enough permission to execute it.');
                    }
                }    
            } catch (Exception $E) {
                $Event->getIo()->write('Requirement verification failed for: ' . $Requirement);
                $Event->getIo()->write('--- Could not compare versions.');
            }
        }
    }
    
    /**
     * @param string $InstalledVersion (Cannot contain operators) Ex: 5.9.3 or 7.0.1 or 2.5.1
     * @param string $RequiredVersion (Can contain operators) Ex: >=1.2.3 or <12.44 or 5.7.1
     * @throws Exception Throws exception if version could not be compared correctly 
     * @return boolean Returns wether the required version was fullfilled
     */
    private static function compareVersionRequirements ($InstalledVersion, $RequiredVersion) {
        if (in_array($RequiredVersion{0}, $this->getAllowedConstraints())) {
            $Operator = $RequiredVersion{0};
            $RequiredVersion = substr($RequiredVersion, 1);
        } elseif (in_array(substr($RequiredVersion, 0, 2), $this->getAllowedConstraints())) {
            $Operator = substr($RequiredVersion, 0, 2);
            $RequiredVersion = substr($RequiredVersion, 2);
        } elseif ($InstalledVersion === $RequiredVersion) {
            return true;//Could't say I didn't try
        } else {
            throw new Exception('Could not compare versions.');
        }
        
        return version_compare($InstalledVersion, $RequiredVersion, $Operator);
    }

    private static function getPythonVersion () {
        
    }

    private static function getRubyVersion () {
        
    }

    private static function getJavaVersion () {
        
    }

    private static function getMongoDbVersion () {
        
    }

    private static function getPostgreSqlVersion () {
        
    }

    private static function getMySqlVersion () {
        
    }

    private static function getNodeJsVersion () {
        
    }

}
