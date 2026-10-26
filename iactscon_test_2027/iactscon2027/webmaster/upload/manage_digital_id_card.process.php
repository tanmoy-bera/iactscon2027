<?php

include_once('./includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];

$allowedDigitalIdCardExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

// Images are physically saved in:  webmaster/upload/digital_id_cards/   (this file lives in webmaster/)
// DB stores only the relative part "upload/digital_id_cards/<file>";
// the API prepends  server_base_url('webmaster/')  to it.

/**
 * Handles upload for a single logo/image field.
 * Returns the relative path on success, or null if no file was uploaded.
 * Throws Exception on invalid format or failed move.
 */
function handleDigitalIdCardUpload($fieldName)
{
    global $allowedDigitalIdCardExt;

    if (!isset($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null; // No file uploaded for this field
    }

    $uploadDir = __DIR__ . "/upload/digital_id_cards/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedDigitalIdCardExt)) {
        throw new Exception("Invalid image format for " . $fieldName . ". Allowed: jpg, jpeg, png, webp, svg");
    }

    $newFilename = uniqid('id_card_' . $fieldName . '_') . "." . $ext;
    $uploadPath = $uploadDir . $newFilename;

    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadPath)) {
        throw new Exception("Failed to upload " . $fieldName . ".");
    }

    return "upload/digital_id_cards/" . $newFilename;
}

/**
 * Validates the event fields (start date, end date, location).
 * Throws Exception on invalid data.
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

            // Check if any digital ID card already exists
            $sql = [];
            $sql['QUERY'] = "SELECT COUNT(*) as total FROM `rcg_digital_id_card` WHERE status != 'D'";
            $result = $mycms->sql_select($sql);
            
            if ($result && $result[0]['total'] > 0) {
                throw new Exception("Only one digital ID card background can exist. Please edit or delete the existing one.");
            }

            $created_by = $loggedUserID;
            $final_media_url = '';

            // Event details (date / location) — validated before any file is uploaded
            $event_start_date = trim($_POST['event_start_date'] ?? '');
            $event_end_date   = trim($_POST['event_end_date'] ?? '');
            $event_location   = trim($_POST['event_location'] ?? '');
            validateDigitalIdCardEvent($event_start_date, $event_end_date, $event_location);
            $event_end_date = ($event_end_date !== '') ? $event_end_date : null;
            
            // Validate and upload background image (required)
            if (isset($_FILES['background_image']['name']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . "/upload/digital_id_cards/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = strtolower(pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedDigitalIdCardExt)) {
                    throw new Exception("Invalid image format for background_image. Allowed: jpg, jpeg, png, webp, svg");
                }

                $newFilename = uniqid('id_card_bg_') . "." . $ext;
                $uploadPath = $uploadDir . $newFilename;

                if (!move_uploaded_file($_FILES['background_image']['tmp_name'], $uploadPath)) {
                    throw new Exception("Failed to upload image.");
                }

                $final_media_url = "upload/digital_id_cards/" . $newFilename;
            } else {
                throw new Exception("Background image is required.");
            }   

            // Header logo uploads — now mandatory
            $header_logo_1 = handleDigitalIdCardUpload('header_logo_1');
            if (empty($header_logo_1)) {
                throw new Exception("Header Logo 1 is required.");
            }

            $header_logo_2 = handleDigitalIdCardUpload('header_logo_2');
            if (empty($header_logo_2)) {
                throw new Exception("Header Logo 2 is required.");
            }

            $header_logo_3 = handleDigitalIdCardUpload('header_logo_3');
            if (empty($header_logo_3)) {
                throw new Exception("Header Logo 3 is required.");
            }
            
            // Build query - new record will be Active by default (only one can exist)
            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_digital_id_card`
                        SET background_image=?, header_logo_1=?, header_logo_2=?, header_logo_3=?,
                            event_start_date=?, event_end_date=?, event_location=?,
                            created_by=?, status='A'";

            $sql['PARAM'] = [
                ['FILD' => 'background_image', 'DATA' => $final_media_url,   'TYP' => 's'],
                ['FILD' => 'header_logo_1',    'DATA' => $header_logo_1,     'TYP' => 's'],
                ['FILD' => 'header_logo_2',    'DATA' => $header_logo_2,     'TYP' => 's'],
                ['FILD' => 'header_logo_3',    'DATA' => $header_logo_3,     'TYP' => 's'],
                ['FILD' => 'event_start_date', 'DATA' => $event_start_date,  'TYP' => 's'],
                ['FILD' => 'event_end_date',   'DATA' => $event_end_date,    'TYP' => 's'],
                ['FILD' => 'event_location',   'DATA' => $event_location,    'TYP' => 's'],
                ['FILD' => 'created_by',       'DATA' => $created_by,        'TYP' => 'i']
            ];

            $id = $mycms->sql_insert($sql);

            pageRedirection("manage_digital_id_card.php", 1);
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage(), "&show=add");
        }
        exit();

    case 'edit':
        try {
            date_default_timezone_set('Asia/Kolkata');
            
            $id = (int)$_POST['id'];
            $updated_by = $loggedUserID;
            $final_media_url = '';

            // Event details (date / location) — validated before any file is uploaded
            $event_start_date = trim($_POST['event_start_date'] ?? '');
            $event_end_date   = trim($_POST['event_end_date'] ?? '');
            $event_location   = trim($_POST['event_location'] ?? '');
            validateDigitalIdCardEvent($event_start_date, $event_end_date, $event_location);
            $event_end_date = ($event_end_date !== '') ? $event_end_date : null;
            
            // Check if new background image uploaded
            if (isset($_FILES['background_image']['name']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . "/upload/digital_id_cards/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = strtolower(pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedDigitalIdCardExt)) {
                    throw new Exception("Invalid image format for background_image. Allowed: jpg, jpeg, png, webp, svg");
                }

                $newFilename = uniqid('id_card_bg_') . "." . $ext;
                $uploadPath = $uploadDir . $newFilename;

                if (!move_uploaded_file($_FILES['background_image']['tmp_name'], $uploadPath)) {
                    throw new Exception("Failed to upload image.");
                }

                $final_media_url = "upload/digital_id_cards/" . $newFilename;
            } else {
                // Keep previous image if no new upload
                $final_media_url = $_POST['prev_background_image'] ?? null;
            }  

            if (empty($final_media_url)) {
                throw new Exception("Background image is required.");
            }

            // Header logos — mandatory; keep previous if no new upload
            $header_logo_1 = handleDigitalIdCardUpload('header_logo_1');
            if ($header_logo_1 === null) {
                $header_logo_1 = $_POST['prev_header_logo_1'] ?? null;
            }
            if (empty($header_logo_1)) {
                throw new Exception("Header Logo 1 is required.");
            }

            $header_logo_2 = handleDigitalIdCardUpload('header_logo_2');
            if ($header_logo_2 === null) {
                $header_logo_2 = $_POST['prev_header_logo_2'] ?? null;
            }
            if (empty($header_logo_2)) {
                throw new Exception("Header Logo 2 is required.");
            }

            $header_logo_3 = handleDigitalIdCardUpload('header_logo_3');
            if ($header_logo_3 === null) {
                $header_logo_3 = $_POST['prev_header_logo_3'] ?? null;
            }
            if (empty($header_logo_3)) {
                throw new Exception("Header Logo 3 is required.");
            }

            // Update query
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_digital_id_card` 
                        SET background_image=?, header_logo_1=?, header_logo_2=?, header_logo_3=?,
                            event_start_date=?, event_end_date=?, event_location=?,
                            updated_by=? 
                        WHERE id=? AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'background_image', 'DATA' => $final_media_url,   'TYP' => 's'],
                ['FILD' => 'header_logo_1',    'DATA' => $header_logo_1,     'TYP' => 's'],
                ['FILD' => 'header_logo_2',    'DATA' => $header_logo_2,     'TYP' => 's'],
                ['FILD' => 'header_logo_3',    'DATA' => $header_logo_3,     'TYP' => 's'],
                ['FILD' => 'event_start_date', 'DATA' => $event_start_date,  'TYP' => 's'],
                ['FILD' => 'event_end_date',   'DATA' => $event_end_date,    'TYP' => 's'],
                ['FILD' => 'event_location',   'DATA' => $event_location,    'TYP' => 's'],
                ['FILD' => 'updated_by',       'DATA' => $updated_by,        'TYP' => 'i'],
                ['FILD' => 'id',               'DATA' => $id,                'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("manage_digital_id_card.php", 2);
            
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage());
        }
        exit();

    case 'Remove':
        try {
            if (empty($_GET['id'])) {
                throw new Exception("ID is required.");
            }

            $id = (int)$_GET['id'];
            $updated_by = $loggedUserID;
            $updated_at = date('Y-m-d H:i:s');

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_digital_id_card`
                         SET status=?, updated_by=?, updated_at=?
                         WHERE id=? AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'status',     'DATA' => 'D',          'TYP' => 's'],
                ['FILD' => 'updated_by', 'DATA' => $updated_by,  'TYP' => 'i'],
                ['FILD' => 'updated_at', 'DATA' => $updated_at,  'TYP' => 's'],
                ['FILD' => 'id',         'DATA' => $id,          'TYP' => 'i']
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
                         WHERE id = ?";

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
                         WHERE id = ?";

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
                             WHERE id=?";
                $sql['PARAM'] = [
                    ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                    ['FILD' => 'id',         'DATA' => $ids[0],     'TYP' => 'i']
                ];
                $mycms->sql_update($sql);

                pageRedirection("manage_digital_id_card.php", 2);
                exit();
            }

            foreach ($ids as $id) {
                $sql = [];

                switch ($action) {
                    case 'Inactive':
                        $sql['QUERY'] = "UPDATE `rcg_digital_id_card` 
                                        SET status=?, updated_at = NOW(), updated_by = ?  
                                     WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'status',     'DATA' => 'I',         'TYP' => 's'],
                            ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
                        ];
                        break;

                    case 'Remove':
                        $sql['QUERY'] = "UPDATE `rcg_digital_id_card` 
                                        SET status=?, updated_at = NOW(), updated_by = ?
                                     WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'status',     'DATA' => 'D',         'TYP' => 's'],
                            ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
                        ];
                        break;

                    default:
                        throw new Exception("Invalid action selected.");
                }

                $mycms->sql_update($sql);
            }

            switch ($action) {
                case 'Inactive':
                    pageRedirection("manage_digital_id_card.php", 2);
                    break;
                case 'Remove':
                    pageRedirection("manage_digital_id_card.php", 3);
                    break;
            }
        } catch (Exception $e) {
            pageRedirection("manage_digital_id_card.php", "Error: " . $e->getMessage());
        }
        exit();
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey = "_pgn_";
    $pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

    @$searchString = "";
    $searchArray = array();
    $searchArray[$pageKey] = $pageKeyVal;

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . urlencode($messageCode) . $additionalString . $searchString);
}