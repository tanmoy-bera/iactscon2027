<?php

include_once('./includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = $_REQUEST['act'] ?? '';

$allowedDigitalIdCardExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

// Images are physically saved in:  <this folder>/upload/digital_id_cards/
// DB stores only the relative part "upload/digital_id_cards/<file>"
// API / listing prepend server_base_url('webmaster/section_quiz/') to it.

/**
 * Handles upload for a single logo/image field.
 * Returns the relative path on success, or null if NO file was chosen.
 * Throws Exception on any real upload problem (size limit, bad format, move failure).
 */
function handleDigitalIdCardUpload($fieldName)
{
    global $allowedDigitalIdCardExt;

    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // nothing chosen for this field
    }

    $err = $_FILES[$fieldName]['error'];
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        throw new Exception("File for " . $fieldName . " is too large (server limit: " . ini_get('upload_max_filesize') . ").");
    }
    if ($err !== UPLOAD_ERR_OK) {
        throw new Exception("Upload failed for " . $fieldName . " (error code " . $err . ").");
    }

    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedDigitalIdCardExt, true)) {
        throw new Exception("Invalid image format for " . $fieldName . ". Allowed: jpg, jpeg, png, webp, svg");
    }

    // Real content check for raster images (SVG is text, skipped)
    if ($ext !== 'svg' && @getimagesize($_FILES[$fieldName]['tmp_name']) === false) {
        throw new Exception("File for " . $fieldName . " is not a valid image.");
    }

    $uploadDir = __DIR__ . "/upload/digital_id_cards/";
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
        throw new Exception("Upload folder could not be created.");
    }

    $newFilename = uniqid('id_card_' . $fieldName . '_') . "." . $ext;
    $uploadPath  = $uploadDir . $newFilename;

    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadPath)) {
        throw new Exception("Failed to upload " . $fieldName . " (check folder write permission).");
    }

    return "upload/digital_id_cards/" . $newFilename;
}

/**
 * Validates the event fields (start date, end date, location).
 */
function validateDigitalIdCardEvent($start, $end, $location)
{
    $startObj = DateTime::createFromFormat('Y-m-d', $start);
    if ($start === '' || !$startObj || $startObj->format('Y-m-d') !== $start) {
        throw new Exception("Event start date is required (valid date).");
    }

    if ($end !== '') {
        $endObj = DateTime::createFromFormat('Y-m-d', $end);
        if (!$endObj || $endObj->format('Y-m-d') !== $end) {
            throw new Exception("Event end date is not a valid date.");
        }
        if ($end < $start) {
            throw new Exception("Event end date cannot be before event start date.");
        }
    }

    if ($location === '') {
        throw new Exception("Event location is required.");
    }
}

switch ($act) {
    case 'store':
        try {
            date_default_timezone_set('Asia/Kolkata');

            // If the whole POST is empty, post_max_size was exceeded
            if (empty($_POST)) {
                throw new Exception("Upload too large (post_max_size = " . ini_get('post_max_size') . "). Use smaller images.");
            }

            // Multiple records allowed; the newest one becomes the single Active record
            $created_by = $loggedUserID;

            // Event details - validated before any file is uploaded
            $event_start_date = trim($_POST['event_start_date'] ?? '');
            $event_end_date   = trim($_POST['event_end_date'] ?? '');
            $event_location   = trim($_POST['event_location'] ?? '');
            validateDigitalIdCardEvent($event_start_date, $event_end_date, $event_location);

            // Check all required files are present BEFORE moving any of them
            foreach (['background_image' => 'Background image', 'header_logo_1' => 'CCICON logo', 'header_logo_2' => 'CONCLOUD logo', 'header_logo_3' => 'Venue logo'] as $f => $label) {
                if (!isset($_FILES[$f]) || $_FILES[$f]['error'] === UPLOAD_ERR_NO_FILE) {
                    throw new Exception($label . " is required.");
                }
            }

            $final_media_url = handleDigitalIdCardUpload('background_image');
            $header_logo_1   = handleDigitalIdCardUpload('header_logo_1');
            $header_logo_2   = handleDigitalIdCardUpload('header_logo_2');
            $header_logo_3   = handleDigitalIdCardUpload('header_logo_3');

            // End date: send '' and let NULLIF turn it into SQL NULL (avoids invalid DATE '' errors)
            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_digital_id_card`
                        SET background_image=?, header_logo_1=?, header_logo_2=?, header_logo_3=?,
                            event_start_date=?, event_end_date=NULLIF(?, ''), event_location=?,
                            created_by=?, status='A'";

            $sql['PARAM'] = [
                ['FILD' => 'background_image', 'DATA' => $final_media_url,  'TYP' => 's'],
                ['FILD' => 'header_logo_1',    'DATA' => $header_logo_1,    'TYP' => 's'],
                ['FILD' => 'header_logo_2',    'DATA' => $header_logo_2,    'TYP' => 's'],
                ['FILD' => 'header_logo_3',    'DATA' => $header_logo_3,    'TYP' => 's'],
                ['FILD' => 'event_start_date', 'DATA' => $event_start_date, 'TYP' => 's'],
                ['FILD' => 'event_end_date',   'DATA' => $event_end_date,   'TYP' => 's'],
                ['FILD' => 'event_location',   'DATA' => $event_location,   'TYP' => 's'],
                ['FILD' => 'created_by',       'DATA' => $created_by,       'TYP' => 'i']
            ];

            $newId = (int)$mycms->sql_insert($sql);

            // Only one Active at a time: deactivate every other record
            if ($newId > 0) {
                $sql = [];
                $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                                 SET status='I', updated_at=NOW(), updated_by=?
                                 WHERE status='A' AND id != ?";
                $sql['PARAM'] = [
                    ['FILD' => 'updated_by', 'DATA' => $created_by, 'TYP' => 'i'],
                    ['FILD' => 'id',         'DATA' => $newId,      'TYP' => 'i']
                ];
                $mycms->sql_update($sql);
            }

            pageRedirection("manage_digital_id_card.php", 1);
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage(), "&show=add");
        }
        exit();

    case 'edit':
        $id = (int)($_POST['id'] ?? 0);
        try {
            date_default_timezone_set('Asia/Kolkata');

            if (empty($_POST)) {
                throw new Exception("Upload too large (post_max_size = " . ini_get('post_max_size') . "). Use smaller images.");
            }
            if ($id <= 0) {
                throw new Exception("Invalid record ID.");
            }

            $updated_by = $loggedUserID;

            // Event details - validated before any file is uploaded
            $event_start_date = trim($_POST['event_start_date'] ?? '');
            $event_end_date   = trim($_POST['event_end_date'] ?? '');
            $event_location   = trim($_POST['event_location'] ?? '');
            validateDigitalIdCardEvent($event_start_date, $event_end_date, $event_location);

            // New upload replaces the old image, otherwise keep the previous one
            $final_media_url = handleDigitalIdCardUpload('background_image');
            if ($final_media_url === null) {
                $final_media_url = $_POST['prev_background_image'] ?? '';
            }
            if (empty($final_media_url)) {
                throw new Exception("Background image is required.");
            }

            $header_logo_1 = handleDigitalIdCardUpload('header_logo_1');
            if ($header_logo_1 === null) {
                $header_logo_1 = $_POST['prev_header_logo_1'] ?? '';
            }
            if (empty($header_logo_1)) {
                throw new Exception("Header Logo 1 is required.");
            }

            $header_logo_2 = handleDigitalIdCardUpload('header_logo_2');
            if ($header_logo_2 === null) {
                $header_logo_2 = $_POST['prev_header_logo_2'] ?? '';
            }
            if (empty($header_logo_2)) {
                throw new Exception("Header Logo 2 is required.");
            }

            $header_logo_3 = handleDigitalIdCardUpload('header_logo_3');
            if ($header_logo_3 === null) {
                $header_logo_3 = $_POST['prev_header_logo_3'] ?? '';
            }
            if (empty($header_logo_3)) {
                throw new Exception("Header Logo 3 is required.");
            }

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                        SET background_image=?, header_logo_1=?, header_logo_2=?, header_logo_3=?,
                            event_start_date=?, event_end_date=NULLIF(?, ''), event_location=?,
                            updated_by=?, updated_at=NOW()
                        WHERE id=? AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'background_image', 'DATA' => $final_media_url,  'TYP' => 's'],
                ['FILD' => 'header_logo_1',    'DATA' => $header_logo_1,    'TYP' => 's'],
                ['FILD' => 'header_logo_2',    'DATA' => $header_logo_2,    'TYP' => 's'],
                ['FILD' => 'header_logo_3',    'DATA' => $header_logo_3,    'TYP' => 's'],
                ['FILD' => 'event_start_date', 'DATA' => $event_start_date, 'TYP' => 's'],
                ['FILD' => 'event_end_date',   'DATA' => $event_end_date,   'TYP' => 's'],
                ['FILD' => 'event_location',   'DATA' => $event_location,   'TYP' => 's'],
                ['FILD' => 'updated_by',       'DATA' => $updated_by,       'TYP' => 'i'],
                ['FILD' => 'id',               'DATA' => $id,               'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("manage_digital_id_card.php", 2);
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage(), "&show=edit&id=" . $id);
        }
        exit();

    case 'Remove':
        try {
            if (empty($_GET['id'])) {
                throw new Exception("ID is required.");
            }

            $id = (int)$_GET['id'];
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                         SET status='D', updated_by=?, updated_at=NOW()
                         WHERE id=? AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("manage_digital_id_card.php", 3);
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage());
        }
        exit();

    case 'Active':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing ID to activate.");
            }

            $id = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID;

            // Only one active record at a time: deactivate all others first
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                         SET status = 'I', updated_at = NOW(), updated_by = ?
                         WHERE status = 'A' AND id != ?";
            $sql['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];
            $mycms->sql_update($sql);

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                         SET status = 'A', updated_at = NOW(), updated_by = ?
                         WHERE id = ? AND status != 'D'";
            $sql['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];
            $mycms->sql_update($sql);

            pageRedirection("manage_digital_id_card.php", 2);
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage());
        }
        exit();

    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing ID to inactivate.");
            }

            $id = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                         SET status = 'I', updated_at = NOW(), updated_by = ?
                         WHERE id = ? AND status != 'D'";
            $sql['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("manage_digital_id_card.php", 2);
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage());
        }
        exit();

    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No digital ID card selected.");
            }
            if (empty($_POST['action'])) {
                throw new Exception("Please choose an action.");
            }

            $ids = array_map('intval', $_POST['ids']);
            $action = $_POST['action'];
            $updated_by = $loggedUserID;

            if ($action === 'Active') {
                if (count($ids) > 1) {
                    throw new Exception("Only one digital ID card can be active at a time. Please select only one record to activate.");
                }

                $sql = [];
                $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                                SET status='I', updated_at = NOW(), updated_by = ?
                             WHERE status = 'A' AND id != ?";
                $sql['PARAM'] = [
                    ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                    ['FILD' => 'id',         'DATA' => $ids[0],     'TYP' => 'i']
                ];
                $mycms->sql_update($sql);

                $sql = [];
                $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                                SET status='A', updated_at = NOW(), updated_by = ?
                             WHERE id=? AND status != 'D'";
                $sql['PARAM'] = [
                    ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                    ['FILD' => 'id',         'DATA' => $ids[0],     'TYP' => 'i']
                ];
                $mycms->sql_update($sql);

                pageRedirection("manage_digital_id_card.php", 2);
                exit();
            }

            if ($action !== 'Inactive' && $action !== 'Remove') {
                throw new Exception("Invalid action selected.");
            }

            $newStatus = ($action === 'Inactive') ? 'I' : 'D';

            foreach ($ids as $id) {
                $sql = [];
                $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                                SET status=?, updated_at = NOW(), updated_by = ?
                             WHERE id=? AND status != 'D'";
                $sql['PARAM'] = [
                    ['FILD' => 'status',     'DATA' => $newStatus,  'TYP' => 's'],
                    ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                    ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
                ];
                $mycms->sql_update($sql);
            }

            pageRedirection("manage_digital_id_card.php", ($action === 'Remove') ? 3 : 2);
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage());
        }
        exit();
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey    = "_pgn_";
    // null-safe: a missing _pgn_ (e.g. from the ajax multi-action) must not raise a PHP warning,
    // because any output before the redirect header breaks the redirect.
    $pageKeyVal = (isset($_REQUEST[$pageKey]) && $_REQUEST[$pageKey] !== '') ? (int)$_REQUEST[$pageKey] : 0;

    $searchString = "";
    $searchArray  = array();
    $searchArray[$pageKey] = $pageKeyVal;

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . urlencode($messageCode) . $additionalString . $searchString);
}
?>