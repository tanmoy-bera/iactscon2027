<?php
error_reporting(1);
@ini_set('display_errors', '1');
@ini_set('session.name', 'sid');

$cfg=array();

define("_HTTP_SERVER_", 		'http://'.$_SERVER['HTTP_HOST'].'/');

// eg, https://localhost - should not be empty for productive servers
define("_HTTPS_SERVER_", 		"https://".$_SERVER['HTTP_HOST']."/");

//define("_WEBSITE_BASE_URL_", 	"http://www.aiccrcog2019.com/");
//define("_EVENT_BASE_URL_", 		"https://www.ruedakolkata.com/20-aic_crc-og19/");
//define("_BASE_URL_", 			"https://www.ruedakolkata.com/20-aic_crc-og19/");
define("_WEBSITE_BASE_URL_", 	"https://ruedakolkata.com/iactscon2027/");
define("_EVENT_BASE_URL_", 		"https://ruedakolkata.com/iactscon2027/");
define("_BASE_URL_", 			"https://ruedakolkata.com/iactscon2027/");

// Front End Path
define("_DIR_CM_IMAGES_",		_BASE_URL_.'images/');
define("_DIR_CM_JSCRIPT_", 		_BASE_URL_.'js/');
define("_DIR_CM_CSS_", 			_BASE_URL_.'css/');
define("_DIR_CM_UTIL_", 		_BASE_URL_.'util/');
define("_DIR_CM_FONTS_", 		_BASE_URL_.'fonts/');

define("_ADMIN_NAME_", 			'ADMIN');
?>

