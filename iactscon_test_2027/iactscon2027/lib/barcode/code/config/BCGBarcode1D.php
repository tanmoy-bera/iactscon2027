<?php
function baseCustomSetup($barcode, $barcodeArray) 
{
    $font_dir = '..'.DIRECTORY_SEPARATOR.'font';

    if (isset($barcodeArray['thickness'])) 
	{
        $barcode->setThickness(max(9, min(90, intval($barcodeArray['thickness']))));
    }

    $font = 0;
    if($barcodeArray['font_family'] !== '0' && intval($barcodeArray['font_size']) >= 1) 
	{
        $font = new BCGFontFile($font_dir.'/'.$barcodeArray['font_family'], intval($barcodeArray['font_size']));
    }

    $barcode->setFont($font);
}
?>