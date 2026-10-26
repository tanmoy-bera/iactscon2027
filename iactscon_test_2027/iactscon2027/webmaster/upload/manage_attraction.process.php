<?php

include_once('./includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];

/**
 * "Highlights" textarea (one highlight per line) -> JSON array string for DB.
 * Returns null when there is nothing to store.
 * Example: "Street murals\nGreat photo spots" -> ["Street murals","Great photo spots"]
 */
function normalizeAttractionHighlights($raw)
{
    $clean = [];
    foreach (preg_split('/\R/', (string)$raw) as $line) {
        $line = trim($line);
        if ($line !== '') {
            $clean[] = $line;
        }
    }
    return !empty($clean) ? json_encode($clean, JSON_UNESCAPED_UNICODE) : null;
}

/**
 * Required-field + URL validation shared by store and edit.
 * Throws Exception on invalid data.
 */
function validateAttractionFields($name, $category, $description, $location_url, $distance_from_venue, $time_to_reach)
{
    if ($name === '')                { throw new Exception("Name is required."); }
    if ($category === '')            { throw new Exception("Category is required."); }
    if ($description === '')         { throw new Exception("Description is required."); }
    if ($location_url === '')        { throw new Exception("Location url is required."); }
    if (!filter_var($location_url, FILTER_VALIDATE_URL)) {
        throw new Exception("Invalid location url");
    }
    if ($distance_from_venue === '') { throw new Exception("Distance from venue is required."); }
    if ($time_to_reach === '')       { throw new Exception("Time to reach is required."); }
}

switch ($act) {
    case 'store':
        try {
            date_default_timezone_set('Asia/Kolkata');
            $current_time           = time();

            // Sanitize & Assign
            $name                   = addslashes(trim($_REQUEST['name'] ?? ''));
            $local_name             = trim($_REQUEST['local_name'] ?? '');
            $category               = trim($_REQUEST['category'] ?? '');
            $description            = trim($_REQUEST['description'] ?? '');
            $highlights             = normalizeAttractionHighlights($_REQUEST['highlights'] ?? '');
            $tip                    = trim($_REQUEST['tip'] ?? '');
            $location_url           = isset($_REQUEST['location_url']) ? trim($_REQUEST['location_url']) : '';
            $distance_from_venue    = isset($_REQUEST['distance_from_venue']) ? addslashes(trim($_REQUEST['distance_from_venue'])) : '';
            $time_to_reach          = isset($_REQUEST['time_to_reach']) ? addslashes(trim($_REQUEST['time_to_reach'])) : '';
            $created_by             = $loggedUserID;
            $final_media_url        = '';

            // Validate everything before any file is uploaded
            validateAttractionFields($name, $category, $description, $location_url, $distance_from_venue, $time_to_reach);

            // Optional fields -> NULL when empty
            $local_name = ($local_name !== '') ? $local_name : null;
            $tip        = ($tip !== '') ? $tip : null;
            
            if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . "/upload/attractions/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array(strtolower($ext), $allowedExt)) {
                    throw new Exception("Invalid image format. Allowed: jpg, jpeg, png, webp");
                }

                $newFilename = uniqid('attraction_') . "." . $ext;
                $uploadPath  = $uploadDir . $newFilename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    throw new Exception("Failed to upload image.");
                }

                // Save relative URL for DB
                $final_media_url = "upload/attractions/" . $newFilename;
            } else {
                throw new Exception("Image file not uploaded properly.");
            }   
            
            // Build query
            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_nearby_attractions`
                        SET name=?, local_name=?, category=?, description=?, highlights=?, tip=?,
                            location_url=?, distance_from_venue=?, 
                            time_to_reach=?, image=?, created_by=?";

            $sql['PARAM'] = [
                ['FILD' => 'name',                  'DATA' => $name,                'TYP' => 's'],
                ['FILD' => 'local_name',            'DATA' => $local_name,          'TYP' => 's'],
                ['FILD' => 'category',              'DATA' => $category,            'TYP' => 's'],
                ['FILD' => 'description',           'DATA' => $description,         'TYP' => 's'],
                ['FILD' => 'highlights',            'DATA' => $highlights,          'TYP' => 's'],
                ['FILD' => 'tip',                   'DATA' => $tip,                 'TYP' => 's'],
                ['FILD' => 'location_url',          'DATA' => $location_url,        'TYP' => 's'],
                ['FILD' => 'distance_from_venue',   'DATA' => $distance_from_venue, 'TYP' => 's'],
                ['FILD' => 'time_to_reach',         'DATA' => $time_to_reach,       'TYP' => 's'],
                ['FILD' => 'image',                 'DATA' => $final_media_url,     'TYP' => 's'],
                ['FILD' => 'created_by',            'DATA' => $created_by,          'TYP' => 'i']
            ];

            $poll_id = $mycms->sql_insert($sql);

            pageRedirection("manage_attraction.php", 1);
        } catch (Exception $e) {
            pageRedirection("manage_attraction.php", "Error: " . $e->getMessage(), "&show=add");
        }
        exit();
    case 'edit':
        $editId = (int)($_POST['id'] ?? 0);
        try {
            date_default_timezone_set('Asia/Kolkata');
            $current_time = time();
            
            // Sanitize inputs
            $id                     = $editId;
            $name                   = addslashes(trim($_POST['name'] ?? ''));
            $local_name             = trim($_POST['local_name'] ?? '');
            $category               = trim($_POST['category'] ?? '');
            $description            = trim($_POST['description'] ?? '');
            $highlights             = normalizeAttractionHighlights($_POST['highlights'] ?? '');
            $tip                    = trim($_POST['tip'] ?? '');
            $location_url           = isset($_REQUEST['location_url']) ? trim($_REQUEST['location_url']) : '';
            $distance_from_venue    = isset($_POST['distance_from_venue']) ? addslashes(trim($_POST['distance_from_venue'])) : '';
            $time_to_reach          = isset($_POST['time_to_reach']) ? addslashes(trim($_POST['time_to_reach'])) : '';
            $updated_by             = $loggedUserID; //(int)$_SESSION['user_id'];

            $final_media_url        = '';

            if ($id <= 0) {
                throw new Exception("Missing ID.");
            }

            // Validate everything before any file is uploaded
            validateAttractionFields($name, $category, $description, $location_url, $distance_from_venue, $time_to_reach);

            // Optional fields -> NULL when empty
            $local_name = ($local_name !== '') ? $local_name : null;
            $tip        = ($tip !== '') ? $tip : null;
            
            if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . "/upload/attractions/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array(strtolower($ext), $allowedExt)) {
                    throw new Exception("Invalid image format. Allowed: jpg, jpeg, png, webp");
                }

                $newFilename = uniqid('attraction_') . "." . $ext;
                $uploadPath  = $uploadDir . $newFilename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    throw new Exception("Failed to upload image.");
                }

                // Save relative URL for DB
                $final_media_url = "upload/attractions/" . $newFilename;
            } else {
                // Keep previous image if no new upload
                $final_media_url = $_POST['prev_image'] ?? null;
            }  

            if (empty($final_media_url)) {
                throw new Exception("Image is required.");
            }

            // Update details
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_nearby_attractions` 
                        SET name=?, local_name=?, category=?, description=?, highlights=?, tip=?,
                            location_url=?, distance_from_venue=?, time_to_reach=?, 
                            image=?, updated_by=? 
                        WHERE id=? AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'name',                  'DATA' => $name,                'TYP' => 's'],
                ['FILD' => 'local_name',            'DATA' => $local_name,          'TYP' => 's'],
                ['FILD' => 'category',              'DATA' => $category,            'TYP' => 's'],
                ['FILD' => 'description',           'DATA' => $description,         'TYP' => 's'],
                ['FILD' => 'highlights',            'DATA' => $highlights,          'TYP' => 's'],
                ['FILD' => 'tip',                   'DATA' => $tip,                 'TYP' => 's'],
                ['FILD' => 'location_url',          'DATA' => $location_url,        'TYP' => 's'],
                ['FILD' => 'distance_from_venue',   'DATA' => $distance_from_venue, 'TYP' => 's'],
                ['FILD' => 'time_to_reach',         'DATA' => $time_to_reach,       'TYP' => 's'],
                ['FILD' => 'image',                 'DATA' => $final_media_url,     'TYP' => 's'],
                ['FILD' => 'updated_by',            'DATA' => $updated_by,          'TYP' => 'i'],
                ['FILD' => 'id',                    'DATA' => $id,                  'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("manage_attraction.php", 2); // 2 = updated successfully
            
        } catch (Exception $e) {
            // Send the user back to the edit form so the error is visible next to the data
            $back = ($editId > 0) ? "&show=edit&id=" . $editId : "";
            pageRedirection("manage_attraction.php", "Error: " . $e->getMessage(), $back);
        }
        exit();
    case 'Remove':
        try {
            // Validation
            if (empty($_GET['id'])) {
                throw new Exception("ID is required.");
            }

            // Sanitize
            $id         = (int)$_GET['id'];
            $updated_by = $loggedUserID; //(int)$_SESSION['user_id']; // assuming session
            $updated_at = date('Y-m-d H:i:s');

            // Build query
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_nearby_attractions`
                         SET status=?,  updated_by=?, updated_at=?
                         WHERE id=? AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'status',        'DATA' => 'D',          'TYP' => 's'],
                ['FILD' => 'updated_by',    'DATA' => $updated_by,  'TYP' => 'i'],
                ['FILD' => 'updated_at',    'DATA' => $updated_at,  'TYP' => 's'],
                ['FILD' => 'id',            'DATA' => $id,          'TYP' => 'i']
            ];

            // Execute update
            $mycms->sql_update($sql);

            // Redirect with message code 3 (deleted)
            pageRedirection("manage_attraction.php", 3);
        } catch (Exception $e) {
            pageRedirection("manage_attraction.php", "Error: " . $e->getMessage());
            // echo "Error: " . $e->getMessage();
        }
        exit();
    case 'Active':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing ID to activate.");
            }

            $id    = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID; //(int)$_SESSION['user_id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_nearby_attractions` 
                         SET status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ?";

            $sql['PARAM'][] = ['FILD' => 'status',      'DATA' => 'A',          'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by',  'DATA' => $updated_by,  'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',          'DATA' => $id,          'TYP' => 'i'];

            $mycms->sql_update($sql);

            pageRedirection("manage_attraction.php", 2); // message code 2 = success
        } catch (Exception $e) {
            pageRedirection("manage_attraction.php", "Error: " . $e->getMessage()); // message code 2 = success
            // echo "Error: " . $e->getMessage();
        }
        exit();
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing ID to inactivate.");

            $poll_id    = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID; //(int)$_SESSION['user_id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_nearby_attractions` 
                         SET status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ?";

            $sql['PARAM'][] = ['FILD' => 'status',      'DATA' => 'I',           'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by',  'DATA' => $updated_by,   'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',          'DATA' => $poll_id,      'TYP' => 'i'];

            $mycms->sql_update($sql);
            pageRedirection("manage_attraction.php", 2);  // 2 = success
        } catch (Exception $e) {
            pageRedirection("manage_attraction.php", "Error: " . $e->getMessage());  // 2 = success
            // echo "Error: " . $e->getMessage();
        }
        exit();
    case 'MultiAction':
        try {
            // Validation
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No nearby attraction selected.");
            }
            if (empty($_POST['action'])) {
                throw new Exception("Please choose an action.");
            }

            // Sanitize
            $ids         = array_map('intval', $_POST['ids']);
            $action      = $_POST['action'];
            $updated_by  = $loggedUserID; //(int)$_SESSION['user_id'];
            $updated_at  = date('Y-m-d H:i:s');

            foreach ($ids as $id) {
                $sql = [];

                switch ($action) {
                    case 'Active':
                        $sql['QUERY'] = "UPDATE `rcg_nearby_attractions` 
                                        SET status=?, updated_at = NOW(), updated_by = ?  
                                     WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'status',        'DATA' => 'A',          'TYP' => 's'],
                            ['FILD' => 'updated_by',    'DATA' => $updated_by,  'TYP' => 'i'],
                            ['FILD' => 'id',            'DATA' => $id,          'TYP' => 'i']
                        ];
                        break;

                    case 'Inactive':
                        $sql['QUERY'] = "UPDATE `rcg_nearby_attractions` 
                                        SET status=?, updated_at = NOW(), updated_by = ?  
                                     WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'status',        'DATA' => 'I',          'TYP' => 's'],
                            ['FILD' => 'updated_by',    'DATA' => $updated_by,  'TYP' => 'i'],
                            ['FILD' => 'id',            'DATA' => $id,          'TYP' => 'i']
                        ];
                        break;

                    case 'Remove':
                        $sql['QUERY'] = "UPDATE `rcg_nearby_attractions` 
                                        SET status=?, updated_at = NOW(), updated_by = ?
                                     WHERE id=? ";
                        $sql['PARAM'] = [
                            ['FILD' => 'status',        'DATA' => 'D',         'TYP' => 's'],
                            ['FILD' => 'updated_by',    'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',            'DATA' => $id,         'TYP' => 'i']
                        ];
                        break;

                    default:
                        throw new Exception("Invalid action selected.");
                }

                $mycms->sql_update($sql);
            }

            // Redirect with message
            switch ($action) {
                case 'Active':
                    pageRedirection("manage_attraction.php", 2); // msg code 1 = activated
                    break;
                case 'Inactive':
                    pageRedirection("manage_attraction.php", 2); // msg code 2 = deactivated
                    break;
                case 'Remove':
                    pageRedirection("manage_attraction.php", 3); // msg code 3 = deleted
                    break;
            }
        } catch (Exception $e) {
            pageRedirection("manage_attraction.php", "Error: " . $e->getMessage());
            // echo "Error: " . $e->getMessage();
        }
        exit();
    
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey = "_pgn_";
    $pageKeyVal = ($_REQUEST[$pageKey]=="") ? 0 : $_REQUEST[$pageKey];

    @$searchString = "";
    $searchArray  = array();

    $searchArray[$pageKey]                 = $pageKeyVal;

    // $searchArray = [
    //     'name'          => isset($_REQUEST['name']) ? urlencode(trim($_REQUEST['name'])) : '',
    // ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . urlencode($messageCode) . $additionalString . $searchString);
}