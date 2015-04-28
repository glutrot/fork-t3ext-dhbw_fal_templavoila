<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['tx_templavoila_module1'] = array('className' => 'DHBW\\DhbwFalTemplavoila\\TemplavoilaMod1IndexXClass');

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila']['cm1']['eTypesConfGen']['fal_page'] = 'EXT:dhbw_fal_templavoila/Classes/User/ETypesConfGenFal.php:&ETypesConfGenFal->handleFal';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila']['cm1']['eTypesConfGen']['fal_fce'] = 'EXT:dhbw_fal_templavoila/Classes/User/ETypesConfGenFal.php:&ETypesConfGenFal->handleFal';
?>