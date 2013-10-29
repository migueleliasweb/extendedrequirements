<?php

namespace migueleliasweb\extendedrequirements;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Requirements implements PluginInterface
{

    public function activate (Composer $Composer, IOInterface $IO) {
        $IO->write('Installing handlers for ExtendedRequirements');
        
//        $Composer->getConfig()->merge(array(
//            'scripts' => array(
//                "post-install-cmd" => "migueleliasweb\\extended-requirements\\Scripts::postCmd",
//                "post-update-cmd" => "migueleliasweb\\extended-requirements\\Scripts::postCmd"
//            )
//        ));
    }

}
