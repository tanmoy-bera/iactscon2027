<?php
if (!defined('IN_CB')) { die('You are not allowed to access to this page.'); }

$imageKeys = array();
function registerImageKey($key, $value) {
    global $imageKeys;
    $imageKeys[$key] = $value;
}

function getImageKeys() {
    global $imageKeys;
    return $imageKeys;
}

/**
 * Returns the barcodes present for drawing.
 *
 * @return string[]
 */
function listbarcodes() {
    
	$supportedBarcodes = array(
		// 1D
		'BCGcodabar.php' => 'Codabar',
		'BCGcode11.php' => 'Code 11',
		'BCGcode39.php' => 'Code 39',
		'BCGcode39extended.php' => 'Code 39 Extended',
		'BCGcode93.php' => 'Code 93',
		'BCGcode128.php' => 'Code 128',
		'BCGean8.php' => 'EAN-8',
		'BCGean13.php' => 'EAN-13',
		'BCGgs1128.php' => 'GS1-128 (EAN-128)',
		'BCGisbn.php' => 'ISBN',
		'BCGi25.php' => 'Interleaved 2 of 5',
		'BCGs25.php' => 'Standard 2 of 5',
		'BCGmsi.php' => 'MSI Plessey',
		'BCGupca.php' => 'UPC-A',
		'BCGupce.php' => 'UPC-E',
		'BCGupcext2.php' => 'UPC Extenstion 2 Digits',
		'BCGupcext5.php' => 'UPC Extenstion 5 Digits',
		'BCGpostnet.php' => 'Postnet',
		'BCGintelligentmail.php' => 'Intelligent Mail',
		'BCGothercode.php' => 'Other Barcode',
	
		// Databar
		'BCGdatabarexpanded.php' => 'Databar Expanded',
		'BCGdatabarlimited.php' => 'Databar Limited',
		'BCGdatabaromni.php' => 'Databar Omni',
	
		// 2D
		'BCGaztec.php' => 'Aztec',
		'BCGdatamatrix.php' => 'DataMatrix',
		'BCGmaxicode.php' => 'MaxiCode',
		'BCGpdf417.php' => 'PDF417',
		'BCGqrcode.php' => 'QRCode'
	);

    $availableBarcodes = array();
    foreach ($supportedBarcodes as $file => $title) {
        if (file_exists($file)) {
            $availableBarcodes[$file] = $title;
        }
    }

    return $availableBarcodes;
}

function findValueFromKey($haystack, $needle) {
    foreach ($haystack as $key => $value) {
        if (strcasecmp($key, $needle) === 0) {
            return $value;
        }
    }

    return null;
}

function convertText($text) {
    $text = stripslashes($text);
    if (function_exists('mb_convert_encoding')) {
        $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }

    return $text;
}
?>