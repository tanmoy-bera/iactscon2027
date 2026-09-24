<?php
//Configuration
include_once("../../includes/configure.php");
include_once("configure.override.php");

include_once("../../includes/configure.FILES.php");
include_once("configure.FILES.override.php");

include_once("../../includes/configure.ESCAPE.php");
include_once("configure.ESCAPE.override.php");

include_once("../../includes/configure.DB.php");
include_once("configure.DB.override.php");

include_once("../../includes/configure.DB.Tables.php");
include_once("configure.DB.Tables.override.php");

include_once("../../includes/configure.license.php");
include_once("configure.license.override.php");

//Class Library
include_once("../../engine/class.common.php"); 
include_once("../engine/class.common.extended.php"); 

//Functions
include_once("../../includes/function.architecture.php");
include_once("function.architecture.override.php");
include_once("../../includes/function.php");
include_once("function.override.php");
include_once("../../includes/function.queryset.php");
include_once("function.queryset.override.php");
//include_once("backend.template.php");
//include_once("backend.template.override.php");
include_once("../../includes/webmaster.template.php");
include_once("webmaster.template.override.php");


?>