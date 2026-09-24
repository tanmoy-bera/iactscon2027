<?php
include_once('includes/init.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");

$workshopId = isset($_POST['workshop_id']) ? (int)$_POST['workshop_id'] : 0;
$classificationId = isset($_POST['registration_classi_id']) ? (int)$_POST['registration_classi_id'] : 0;
$workshopCutoffId = isset($_POST['workshop_cutoff_id']) ? (int)$_POST['workshop_cutoff_id'] : 0;

if (!$workshopId || !$classificationId || !$workshopCutoffId) {
    echo json_encode(['success' => false, 'amount' => 0, 'display' => '--']);
    exit;
}

$sqlTarrifAmount = array();
$sqlTarrifAmount['QUERY'] = "SELECT * FROM " . _DB_TARIFF_WORKSHOP_ . " 
                              WHERE workshop_id = ?
                              AND tariff_cutoff_id = ?
                              AND registration_classification_id = '0'
                              AND OnlyWorkshop_clssId = ?
                              AND status = 'A'";
$sqlTarrifAmount['PARAM'][] = array('FILD' => 'workshop_id', 'DATA' => $workshopId, 'TYP' => 'i');
$sqlTarrifAmount['PARAM'][] = array('FILD' => 'tariff_cutoff_id', 'DATA' => $workshopCutoffId, 'TYP' => 'i');
$sqlTarrifAmount['PARAM'][] = array('FILD' => 'OnlyWorkshop_clssId', 'DATA' => $classificationId, 'TYP' => 'i');

$resTarrifAmount = $mycms->sql_select($sqlTarrifAmount);

if ($resTarrifAmount && $resTarrifAmount[0]['inr_amount'] > 0) {
    $amount = (float)$resTarrifAmount[0]['inr_amount'];
    echo json_encode([
        'success' => true,
        'amount' => $amount,
        'display' => number_format($amount, 2)
    ]);
} else {
    echo json_encode(['success' => true, 'amount' => 0, 'display' => 'Included in Registration']);
}
exit;