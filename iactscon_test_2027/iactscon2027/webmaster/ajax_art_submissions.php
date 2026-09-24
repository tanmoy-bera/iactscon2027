<?php
/**
 * ajax_art_submissions.php
 *
 * Flat AJAX endpoint — intentionally NOT included through the site's
 * normal page router (which prints header.php/left-menu.php before a
 * page's own PHP runs). Calling this file directly keeps the response
 * pure JSON with nothing prepended to it.
 *
 * Only action: soft-delete a submission. Search/filter/sort/pagination
 * now happen via normal full-page GET navigation on the listing page
 * itself, so they no longer need an AJAX endpoint.
 */

include_once("includes/source.php");
include_once('includes/init.php');
include_once(__DIR__ . "/includes/function.registration.php");
include_once(__DIR__ . "/includes/function.delegate.php");

// Make sure nothing any include may have echoed leaks into our JSON.
while (ob_get_level() > 0) {
    ob_end_clean();
}
header('Content-Type: application/json');

$act = isset($_POST['act']) ? $_POST['act'] : '';

if ($act === 'deleteSubmission') {
    $deleteId = trim($_POST['id'] ?? '');

    if ($deleteId === '' || !ctype_digit($deleteId)) {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid submission id.'));
        exit;
    }

    $sqlUpdate = array();
    $sqlUpdate['QUERY']   = "UPDATE `art_exhibition_submissions` SET `status` = ? WHERE `id` = ?";
    $sqlUpdate['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D',       'TYP' => 's');
    $sqlUpdate['PARAM'][] = array('FILD' => 'id',     'DATA' => $deleteId, 'TYP' => 'i');

    try {
        $mycms->sql_update($sqlUpdate, false);
    } catch (Throwable $e) {
        echo json_encode(array('status' => 'error', 'message' => 'Could not delete this submission. Please try again.'));
        exit;
    }

    echo json_encode(array('status' => 'success', 'message' => 'Submission deleted.'));
    exit;
}

echo json_encode(array('status' => 'error', 'message' => 'Invalid action.'));
exit;