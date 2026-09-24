<?php
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.abstract.php");
include_once('includes/function.accommodation.php');

$sqlMSG   =  array();
$sqlMSG['QUERY']    = "SELECT `color`,`light_color`,`dark_color` FROM " . _DB_COMPANY_INFORMATION_ . " 
            WHERE `id` = 1";
       
$result       = $mycms->sql_select($sqlMSG);

$row = $result[0];

?>
<style>
:root {
  --sky: #<?= $row['light_color'] ?>;
  --blue: #<?= $row['color'] ?>;
  --dark-blue: #<?= $row['dark_color'] ?>;
}
</style>
<!-- <link rel="stylesheet" href="css/style.css"> -->

</head>