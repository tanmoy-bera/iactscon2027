<?php
define('IN_CB', true);
include_once('include/function.php');

// CONFIGURATION SECTION
$barcodeArray = array();
$barcodeArray['code']          = "BCGcode128";
$barcodeArray['dpi']           = "72";
$barcodeArray['scale']         = "1";
$barcodeArray['rotation']      = "0";
$barcodeArray['filetype']      = "PNG";
$barcodeArray['thickness']     = "50";
$barcodeArray['font_family']   = "Arial.ttf";
$barcodeArray['font_size']     = "0";

function showError() 
{
    header('Content-Type: image/png');
    readfile('error.png');
    exit;
}

$requiredKeys = array('text');

// Check if everything is present in the request
foreach($requiredKeys as $key) 
{
    if(!isset($_GET[$key])) 
	{
        showError();
    }
}

if(!preg_match('/^[A-Za-z0-9]+$/', $barcodeArray['code'])) 
{
    showError();
}

// Check if the code is valid
include_once('config/BCGcode128.php');
require_once('../class/BCGColor.php');
require_once('../class/BCGBarcode.php');
require_once('../class/BCGDrawing.php');
require_once('../class/BCGFontFile.php');
include_once('../class/BCGcode128.barcode.php');
include_once('config/BCGBarcode1D.php');

$filetypes     = array('PNG' => BCGDrawing::IMG_FORMAT_PNG, 'JPEG' => BCGDrawing::IMG_FORMAT_JPEG, 'GIF' => BCGDrawing::IMG_FORMAT_GIF);

$drawException = null;

try 
{
    $color_black    = new BCGColor(0, 0, 0);
    $color_white    = new BCGColor(255, 255, 255);
    $code_generated = new $className();

    if(function_exists('baseCustomSetup')) 
	{
        baseCustomSetup($code_generated, $barcodeArray);
    }

    if(function_exists('customSetup')) 
	{
        customSetup($code_generated, $barcodeArray);
    }

    $code_generated->setScale(max(1, min(4, $barcodeArray['scale'])));
    $code_generated->setBackgroundColor($color_white);
    $code_generated->setForegroundColor($color_black);

    if ($_GET['text'] !== '') 
	{
        $text = convertText($_GET['text']);
        $code_generated->parse($text);
    }
} 
catch(Exception $exception) 
{
    $drawException = $exception;
}

$drawing = new BCGDrawing('', $color_white);
if($drawException) 
{
    $drawing->drawException($drawException);
} 
else 
{
    $drawing->setBarcode($code_generated);
    $drawing->setRotationAngle($barcodeArray['rotation']);
    $drawing->setDPI($barcodeArray['dpi']);
    $drawing->draw();
}

switch($barcodeArray['filetype']) 
{
    case'PNG':
        header('Content-Type: image/png');
        break;
    case'JPEG':
        header('Content-Type: image/jpeg');
        break;
    case'GIF':
        header('Content-Type: image/gif');
        break;
}

$drawing->finish($filetypes[$barcodeArray['filetype']]);
?>