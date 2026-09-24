<?php
require_once __DIR__ . '/vendor/autoload.php'; // path to mPDF autoload
// include(__DIR__."/lib/vendor/autoload.php");
$autoloadPath = __DIR__ . '/lib/vendor/autoload.php';


require_once $autoloadPath;

ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
try {

    if (!isset($_GET['file']) || empty($_GET['file'])) {
        throw new Exception("Invalid file request.");
    }

    // Prevent directory traversal attack
    $fileName = basename($_GET['file']);

    $htmlFile = __DIR__ . "/uploads/registration_confirmation/" . $fileName;

    if (!file_exists($htmlFile)) {
        throw new Exception("HTML file not found.");
    }

    $html = file_get_contents($htmlFile);

    $mpdf = new \Mpdf\Mpdf([
        'format' => 'A4-L',
        'margin_left' => 0,
        'margin_right' => 0,
        'margin_top' => 0,
        'margin_bottom' => 0,
        'margin_header' => 0,
        'margin_footer' => 0,
        'default_font' => 'dejavusans'
    ]);

    $mpdf->WriteHTML($html);

    $pdfFileName = str_replace('.html', '.pdf', $fileName);

    $mpdf->Output($pdfFileName, 'D');

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}