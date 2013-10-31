<?php

use Composer\Script\Event;

namespace migueleliasweb\extendedrequirements;

class Scripts
{

    const CUSTOM_EXTRA_KEY = 'extended-script-requirements';
    
    public static $AllowedConstraints = array ('<', 'lt', '<=', 'le', '>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=', '<>', 'ne');
    
    public function getAllowedConstraints () {
        return $this->AllowedConstraints;
    }

        
    public static function postCmd (Event $Event) {
        $ExtraConfigs = $Event->getComposer()->getConfig()->get('extra');//PHP 5.3 ='(
        
        foreach ($ExtraConfigs[CUSTOM_EXTRA_KEY] as $Requirement => $Info) {
            //Ex: getPythonVersion(), getRubyVersion(), getJavaVersion
            $MethodName = 'get'.$Requirement.'Version';
            
            if (method_exists(Scripts, $MethodName)) {
                $RequiredVersion = $Info;
                $InstalledVersion = Script::$MethodName();
            } elseif (is_array($RequiredVersion)) {//For custom comparisons
                $RequiredVersion = $Info['RequiredVersion'];
                        
                if (file_exists($Info['InstalledVersion']) AND is_executable($Info['InstalledVersion'])) {
                    $InstalledVersion = shell_exec($Info['InstalledVersion']);
                } else {
                    $Event->getIo()->write('Requirement verification failed for: ' . $Requirement);
                    $Event->getIo()->write('--- File not found or not enough permission to execute it.');
                    continue;
                }
            }
            
            if (Scripts::compareVersionRequirements($InstalledVersion, $RequiredVersion)) {
                $Event->getIo()->write('Requirement verification succeeded for: ' . $Requirement);
            } else {
                $Event->getIo()->write('Requirement verification failed for: ' . $Requirement);
                $Event->getIo()->write('--- Required version: '.$RequiredVersion.', installed version: '.$InstalledVersion);
            }
        }
    }
    
    /**
     * Defaults to ">="
     * @param string $RequiredVersion Ex: >=5.3.1, 0.0.1, =8.1
     * @return string The operator string to be use on version_compare().
     */
    public static function stripVersionOperator ($RequiredVersion) {
        if (in_array($RequiredVersion{0}, $this->getAllowedConstraints())) {
            return $RequiredVersion{0};
        } elseif (in_array(substr($RequiredVersion, 0, 2), $this->getAllowedConstraints())) {
            return substr($RequiredVersion, 0, 2);
        } else {
            return ">=";
        }
    }
    
    /**
     * 
     * @throws Exception Throws excaption if version is not correctly formed
     * @param type $RequiredVersion
     * @return string The standardized "version" of $RequiredVersion
     */
    public static function stripRequiredVersion ($RequiredVersion) {
        if (in_array($RequiredVersion{0}, $this->getAllowedConstraints())) {
            return substr($RequiredVersion, 1);
        } elseif (in_array(substr($RequiredVersion, 0, 2), $this->getAllowedConstraints())) {
            return substr($RequiredVersion, 2);
        } else {
            throw new Exception('The version '.$RequiredVersion.' is off standards.');
        }
    }


    /**
     * @param string $InstalledVersion (Cannot contain operators) Ex: 5.9.3 or 7.0.1 or 2.5.1
     * @param string $RequiredVersion Ex: >=1.2.3 or <12.44 or =5.7.1 or >1.1 or =0.9.1
     * @throws Exception Throws exception if version could not be compared correctly 
     * @return boolean True whether the requirements were fulfilled
     */
    public static function compareVersionRequirements ($InstalledVersion, $RequiredVersion) {
        if (in_array($RequiredVersion{0}, $this->getAllowedConstraints())) {
            $Operator = $RequiredVersion{0};
            $RequiredVersion = substr($RequiredVersion, 1);
        } elseif (in_array(substr($RequiredVersion, 0, 2), $this->getAllowedConstraints())) {
            $Operator = substr($RequiredVersion, 0, 2);
            $RequiredVersion = substr($RequiredVersion, 2);
        } elseif ($InstalledVersion === $RequiredVersion) {
            return true;//Could't say I didn't try
        } else {
            $Operator = '>=';//Ex: >=5.3.5, >=7, >=0.0.1
        }
        
        return version_compare($InstalledVersion, $RequiredVersion, $Operator);
    }

    public static function getPythonVersion () {
        
    }

    public static function getRubyVersion () {
        
    }

    public static function getJavaVersion () {
        
    }

    public static function getMongoDbVersion () {
        
    }

    public static function getPostgreSqlVersion () {
        
    }

    public static function getMySqlVersion () {
        
    }

    public static function getNodeJsVersion () {
        
    }

}
