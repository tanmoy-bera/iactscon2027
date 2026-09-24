<?php
//Configuration
include_once("configure.php"); 
include_once("configure.DB.php");
include_once("configure.DB.Tables.php");
include_once("configure.FILES.php");
include_once("configure.ESCAPE.php");

include_once("configure.license.php");
include_once("configure.mail.php");

//Class Library
include_once("engine/class.common.php"); 
include_once('lib/mailer/PHPMailerAutoload.php');	
include_once("configure.CONSTANTS.php");
//Functions
include_once("frontend.template.php");
include_once("function.architecture.php");
include_once("function.queryset.php");
include_once("function.messaging.php");
include_once("function.php");
include_once("function.edit.php");

?>