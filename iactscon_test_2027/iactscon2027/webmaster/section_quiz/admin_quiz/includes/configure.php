<?php
//CODE configuration parameters - USE ONLY TO OVERRIDE THE PARAMETER VALUES.

$__WEBMASTERURL	 			= $cfg['DOMAIN_URL'].'section_configuration/';
$cfg['SECTION_BASE_URL']    = $cfg['DOMAIN_URL'].'section_quiz/';

$cfg['DIR_LC_IMAGES']		= $__WEBMASTERURL.'images/';
$cfg['DIR_LC_JSCRIPT']		= $__WEBMASTERURL.'js/';
$cfg['DIR_LC_CSS']			= $__WEBMASTERURL.'css/';

$cfg['SECTION']             = "#SECTION_QUIZ";
$cfg['SECTION_PATH']        = "../section_quiz/";

$cfg['APP_NAME_DISP']       = $cfg['APP_NAME']." - ".$cfg['SECTION'];
// $cfg['FIREBASE_URL']        = 'https://console.firebase.google.com/u/1/project/concloudquiz/database/concloudquiz-default-rtdb/data/~2F';
// $cfg['FIREBASE_URL']        = 'https://concloudquiz-default-rtdb.asia-southeast1.firebasedatabase.app/';
$cfg['FIREBASE_URL']        = 'https://concloud-708c9-default-rtdb.asia-southeast1.firebasedatabase.app/';
$cfg['FIREBASE_SECRET']       = 'RgsdRqXupNUX1Gn794PLK0rHssfkgSmpDmB6BLnL';
?>