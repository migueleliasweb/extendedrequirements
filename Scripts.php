<?php

use Composer\Script\Event;

namespace migueleliasweb\ExtendedRequirements;

class Scripts
{
    
    const CUSTOM_EXTRA_KEY = 'extended-script-requirements';

    public static function postCmd (Event $Event) {
        $ExtraConfigs = $Event->getComposer()->getConfig()->get('extra');
        
        foreach ($ExtraConfigs[CUSTOM_EXTRA_KEY] as $ExtraRequirement) {
            
            $ExtraRequirementPath = realpath(current($ExtraRequirement));
            $ExtraRequirementLabel = ucfirst(key($ExtraRequirement));
            
            if (`$ExtraRequirementPath` != '1') {
                $Event->getIo()->write('Requirement verification failed for: '.$ExtraRequirementLabel);
            }
        }
    }
}