<?php

include_once('./includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];


define('GROUP_COVER_URL', '/webmaster/uploads/groups/');
define('GROUP_COVER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/webmaster/uploads/groups/');

/* This page manages PRIVATE groups only (group_type is fixed in the backend, never shown or chosen in the UI).
   Public groups are managed from cardiac_talks_topics.php. */
switch ($act) {
    case 'add_group':
        try {
            $group_name        = trim($_POST['group_name'] ?? '');
            $group_description = isset($_POST['group_description']) ? trim($_POST['group_description']) : null;
            $status            = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $created_by        = $loggedUserID;

            if ($group_name === '') {
                throw new Exception("Group name is required.");
            }
            // Name must be unique across ALL groups (private groups + public topics)
            if (groupNameExists($mycms, $group_name)) {
                throw new Exception("A group or topic named \"" . $group_name . "\" already exists. Please use a different name.");
            }

            // Cover image (optional)
            $cover_image = uploadGroupCover('cover_image');

            // private groups have no preference, so it is always stored as 'all'
            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_groups`
                        SET group_name=?, group_description=?, group_type='private', preference_type='all', cover_image=?,
                            created_by=?, total_members=?, status=?";

            $sql['PARAM'] = [
                ['FILD' => 'group_name',        'DATA' => $group_name,        'TYP' => 's'],
                ['FILD' => 'group_description', 'DATA' => $group_description, 'TYP' => 's'],
                ['FILD' => 'cover_image',       'DATA' => $cover_image,       'TYP' => 's'],
                ['FILD' => 'created_by',        'DATA' => $created_by,        'TYP' => 'i'],
                ['FILD' => 'total_members',     'DATA' => 0,                  'TYP' => 'i'],
                ['FILD' => 'status',            'DATA' => $status,            'TYP' => 's']
            ];

            $mycms->sql_insert($sql);

            setAlert('success', 'Group created successfully.');
            pageRedirection("group_list.php", 1);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_list.php", 0, "&show=add");
        }
        exit();

    case 'edit_group':
        $group_id = (int)($_POST['group_id'] ?? 0);
        try {
            $group_name        = trim($_POST['group_name'] ?? '');
            $group_description = isset($_POST['group_description']) ? trim($_POST['group_description']) : null;
            $status            = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $old_cover_image   = trim($_POST['old_cover_image'] ?? '');
            $updated_by        = $loggedUserID;

            if ($group_id <= 0) {
                throw new Exception("Group ID is required.");
            }
            if ($group_name === '') {
                throw new Exception("Group name is required.");
            }
            // Name must be unique across ALL groups (ignore this group itself)
            if (groupNameExists($mycms, $group_name, $group_id)) {
                throw new Exception("A group or topic named \"" . $group_name . "\" already exists. Please use a different name.");
            }

            // New cover image uploaded? otherwise keep the old one
            $cover_image = uploadGroupCover('cover_image');
            if ($cover_image === null) {
                $cover_image = ($old_cover_image !== '') ? $old_cover_image : null;
            } elseif ($old_cover_image !== '' && is_file(GROUP_COVER_PATH . basename($old_cover_image))) {
                @unlink(GROUP_COVER_PATH . basename($old_cover_image));
            }

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_groups`
                        SET group_name=?, group_description=?, cover_image=?,
                            status=?, updated_by=?, updated_at=NOW()
                        WHERE id=? AND group_type='private' AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'group_name',        'DATA' => $group_name,        'TYP' => 's'],
                ['FILD' => 'group_description', 'DATA' => $group_description, 'TYP' => 's'],
                ['FILD' => 'cover_image',       'DATA' => $cover_image,       'TYP' => 's'],
                ['FILD' => 'status',            'DATA' => $status,            'TYP' => 's'],
                ['FILD' => 'updated_by',        'DATA' => $updated_by,        'TYP' => 'i'],
                ['FILD' => 'id',                'DATA' => $group_id,          'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            setAlert('success', 'Group updated successfully.');
            pageRedirection("group_list.php", 2); // 2 = updated successfully
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            $back = ($group_id > 0) ? "&show=edit&group_id=" . $group_id : "";
            pageRedirection("group_list.php", 0, $back);
        }
        exit();

    case 'Delete':
        try {
            if (empty($_GET['id'])) {
                throw new Exception("Group ID is required.");
            }

            $id         = (int)$_GET['id'];
            $updated_by = $loggedUserID;

            // Soft delete
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_groups`
                         SET status=?, updated_by=?, updated_at=NOW()
                         WHERE id=? AND group_type='private' AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'status',     'DATA' => 'D',         'TYP' => 's'],
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            setAlert('success', 'Group deleted successfully.');
            pageRedirection("group_list.php", 3); // 3 = deleted
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_list.php", 0);
        }
        exit();

    case 'Active':
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing Group ID.");
            }

            $group_id   = (int)$_REQUEST['id'];
            $newStatus  = ($act === 'Active') ? 'A' : 'I';
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_groups`
                         SET status = ?, updated_at = NOW(), updated_by = ?
                         WHERE id = ? AND group_type='private' AND status != 'D'";

            $sql['PARAM'][] = ['FILD' => 'status',     'DATA' => $newStatus,  'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $group_id,   'TYP' => 'i'];

            $mycms->sql_update($sql);

            setAlert('success', 'Group marked as ' . (($act === 'Active') ? 'active' : 'inactive') . ' successfully.');
            pageRedirection("group_list.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_list.php", 0);
        }
        exit();

    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No groups selected.");
            }
            if (empty($_POST['action'])) {
                throw new Exception("Please choose an action.");
            }

            $ids       = array_map('intval', $_POST['ids']);
            $action    = $_POST['action'];
            $statusMap = ['Active' => 'A', 'Inactive' => 'I', 'Remove' => 'D'];

            if (!isset($statusMap[$action])) {
                throw new Exception("Invalid action selected.");
            }

            $count = 0;
            foreach ($ids as $id) {
                if ($id <= 0) {
                    continue;
                }
                $sql = [];
                $sql['QUERY'] = "UPDATE `rcg_groups`
                                 SET status=?, updated_by=?, updated_at=NOW()
                                 WHERE id=? AND group_type='private' AND status != 'D'";
                $sql['PARAM'] = [
                    ['FILD' => 'status',     'DATA' => $statusMap[$action], 'TYP' => 's'],
                    ['FILD' => 'updated_by', 'DATA' => $loggedUserID,       'TYP' => 'i'],
                    ['FILD' => 'id',         'DATA' => $id,                 'TYP' => 'i']
                ];
                $mycms->sql_update($sql);
                $count++;
            }

            $verb = ['Active' => 'marked as active', 'Inactive' => 'marked as inactive', 'Remove' => 'removed'];
            setAlert('success', $count . ' group(s) ' . $verb[$action] . ' successfully.');
            pageRedirection("group_list.php", ($action === 'Remove') ? 3 : 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_list.php", 0);
        }
        exit();
}

/*============== HELPER: FLASH ALERT (shown once on the list page) ================*/
function setAlert($type, $message)
{
    $_SESSION['group_alert'] = [
        'type'    => ($type === 'error') ? 'error' : 'success',
        'message' => $message
    ];
}

/*============== HELPER: DUPLICATE NAME CHECK ================*/
/* A group name must be unique across private groups AND public topics (deleted ones are ignored).
   Comparison ignores case and leading/trailing spaces. $excludeId = the record being edited. */
function groupNameExists($mycms, $name, $excludeId = 0)
{
    $q = [];
    $q['QUERY'] = "SELECT `id` FROM `rcg_groups`
                   WHERE `status` != 'D' AND LOWER(TRIM(`group_name`)) = LOWER(?) AND `id` != ?
                   LIMIT 1";
    $q['PARAM'] = [
        ['FILD' => 'group_name', 'DATA' => trim($name),      'TYP' => 's'],
        ['FILD' => 'id',         'DATA' => (int)$excludeId,  'TYP' => 'i']
    ];
    $r = $mycms->sql_select($q);
    return !empty($r);
}

/*============== HELPER: COVER IMAGE UPLOAD ================*/
/* Returns new file name, or null if no file was uploaded */
function uploadGroupCover($fieldName)
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Cover image upload failed.");
    }

    $maxSize = 5 * 1024 * 1024; // 5 MB
    if ($_FILES[$fieldName]['size'] > $maxSize) {
        throw new Exception("Cover image must be less than 5 MB.");
    }

    $ext     = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        throw new Exception("Only JPG, PNG, GIF or WEBP images are allowed.");
    }

    // Confirm it is really an image
    if (@getimagesize($_FILES[$fieldName]['tmp_name']) === false) {
        throw new Exception("Uploaded file is not a valid image.");
    }

    if (!is_dir(GROUP_COVER_PATH)) {
        mkdir(GROUP_COVER_PATH, 0755, true);
    }

    $newName = 'group_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], GROUP_COVER_PATH . $newName)) {
        throw new Exception("Could not save cover image.");
    }

    return $newName;
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey    = "_pgn_";
    $pageKeyVal = empty($_REQUEST[$pageKey]) ? 0 : (int)$_REQUEST[$pageKey];

    @$searchString = "";

    $searchArray = [
        $pageKey        => $pageKeyVal,
        'search_name'   => isset($_REQUEST['search_name']) ? urlencode(trim($_REQUEST['search_name'])) : '',
        'search_status' => isset($_REQUEST['search_status']) ? urlencode(trim($_REQUEST['search_status'])) : '',
    ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}