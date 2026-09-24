<?php
//CODE configuration parameters - USE ONLY TO OVERRIDE THE PARAMETER VALUES.

$__WEBMASTERURL	 			= $cfg['DOMAIN_URL'].'section_configuration/';
$cfg['SECTION_BASE_URL']    = $cfg['DOMAIN_URL'].'section_master_system/';

$cfg['DIR_LC_IMAGES']		= $__WEBMASTERURL.'images/';
$cfg['DIR_LC_JSCRIPT']		= $__WEBMASTERURL.'js/';
$cfg['DIR_LC_CSS']			= $__WEBMASTERURL.'css/';

$cfg['SECTION_FULL_URL']    = $cfg['SECTION_BASE_URL']."scripts/";

$cfg['SECTION']             = "#SYSTEM_MASTER";
$cfg['SECTION_PATH']        = "../section_master_system/";

$cfg['APP_NAME_DISP']       = $cfg['APP_NAME']." - ".$cfg['SECTION'];
?>

