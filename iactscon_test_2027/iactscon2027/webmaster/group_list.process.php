<?php

include_once('./includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];


define('GROUP_COVER_URL', '/webmaster/uploads/groups/');
define('GROUP_COVER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/webmaster/uploads/groups/');
switch ($act) {
    case 'add_group':
        try {
            $group_name        = trim($_POST['group_name'] ?? '');
            $group_description = isset($_POST['group_description']) ? trim($_POST['group_description']) : null;
            $group_type        = (($_POST['group_type'] ?? 'public') === 'private') ? 'private' : 'public';
            $status            = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $created_by        = $loggedUserID;

            if ($group_name === '') {
                throw new Exception("Group name is required.");
            }

            // Cover image (optional)
            $cover_image = uploadGroupCover('cover_image');

            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_groups`
                        SET group_name=?, group_description=?, group_type=?, cover_image=?,
                            created_by=?, total_members=?, status=?";

            $sql['PARAM'] = [
                ['FILD' => 'group_name',        'DATA' => $group_name,        'TYP' => 's'],
                ['FILD' => 'group_description', 'DATA' => $group_description, 'TYP' => 's'],
                ['FILD' => 'group_type',        'DATA' => $group_type,        'TYP' => 's'],
                ['FILD' => 'cover_image',       'DATA' => $cover_image,       'TYP' => 's'],
                ['FILD' => 'created_by',        'DATA' => $created_by,        'TYP' => 'i'],
                ['FILD' => 'total_members',     'DATA' => 0,                  'TYP' => 'i'],
                ['FILD' => 'status',            'DATA' => $status,            'TYP' => 's']
            ];

            $group_id = $mycms->sql_insert($sql);

            // Save preference mapping
         saveGroupPreferences($mycms, $group_id, $_POST['pref_type'] ?? [], $_POST['pref_name'] ?? [], $loggedUserID);

            pageRedirection("group_list.php", 1);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    case 'edit_group':
        try {
            $group_id          = (int)($_POST['group_id'] ?? 0);
            $group_name        = trim($_POST['group_name'] ?? '');
            $group_description = isset($_POST['group_description']) ? trim($_POST['group_description']) : null;
            $group_type        = (($_POST['group_type'] ?? 'public') === 'private') ? 'private' : 'public';
            $status            = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $old_cover_image   = trim($_POST['old_cover_image'] ?? '');
            $updated_by        = $loggedUserID;

            if ($group_id <= 0) {
                throw new Exception("Group ID is required.");
            }
            if ($group_name === '') {
                throw new Exception("Group name is required.");
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
                        SET group_name=?, group_description=?, group_type=?, cover_image=?,
                            status=?, updated_by=?, updated_at=NOW()
                        WHERE id=? AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'group_name',        'DATA' => $group_name,        'TYP' => 's'],
                ['FILD' => 'group_description', 'DATA' => $group_description, 'TYP' => 's'],
                ['FILD' => 'group_type',        'DATA' => $group_type,        'TYP' => 's'],
                ['FILD' => 'cover_image',       'DATA' => $cover_image,       'TYP' => 's'],
                ['FILD' => 'status',            'DATA' => $status,            'TYP' => 's'],
                ['FILD' => 'updated_by',        'DATA' => $updated_by,        'TYP' => 'i'],
                ['FILD' => 'id',                'DATA' => $group_id,          'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            // Re-sync preference mapping
            $del = [];
            $del['QUERY'] = "DELETE FROM `rcg_group_preference_map` WHERE group_id = ?";
            $del['PARAM'][] = ['FILD' => 'group_id', 'DATA' => $group_id, 'TYP' => 'i'];
            $mycms->sql_delete($del);

          saveGroupPreferences($mycms, $group_id, $_POST['pref_type'] ?? [], $_POST['pref_name'] ?? [], $loggedUserID);

            pageRedirection("group_list.php", 2); // 2 = updated successfully
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
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
                         WHERE id=? AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'status',     'DATA' => 'D',         'TYP' => 's'],
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("group_list.php", 3); // 3 = deleted
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    case 'Active':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing Group ID to activate.");
            }

            $group_id   = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_groups`
                         SET status = ?, updated_at = NOW(), updated_by = ?
                         WHERE id = ? AND status != 'D'";

            $sql['PARAM'][] = ['FILD' => 'status',     'DATA' => 'A',         'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $group_id,   'TYP' => 'i'];

            $mycms->sql_update($sql);

            pageRedirection("group_list.php", 2);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing Group ID to inactivate.");
            }

            $group_id   = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_groups`
                         SET status = ?, updated_at = NOW(), updated_by = ?
                         WHERE id = ? AND status != 'D'";

            $sql['PARAM'][] = ['FILD' => 'status',     'DATA' => 'I',         'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $group_id,   'TYP' => 'i'];

            $mycms->sql_update($sql);

            pageRedirection("group_list.php", 2);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
}

/*============== HELPER: SAVE PREFERENCE MAPPING ================*/
function saveGroupPreferences($mycms, $group_id, $types, $names, $created_by)
{
    if (!is_array($types) || !is_array($names)) {
        return;
    }

    $allowedTypes = ['university', 'institute', 'specialty'];
    $done         = [];

    foreach ($names as $i => $name) {
        $name = trim($name);
        $name = function_exists('mb_substr') ? mb_substr($name, 0, 255) : substr($name, 0, 255);
        $type = $types[$i] ?? '';

        if ($name === '' || !in_array($type, $allowedTypes, true)) {
            continue;
        }

        $key = $type . '|' . (function_exists('mb_strtolower') ? mb_strtolower($name) : strtolower($name));
        if (isset($done[$key])) {
            continue;
        }
        $done[$key] = true;

        // 1) already exists?
        $find = [];
        $find['QUERY'] = "SELECT `id` FROM `rcg_group_preferences`
                          WHERE `preference_type`=? AND `preference_name`=? AND `status` != 'D'
                          LIMIT 1";
        $find['PARAM'] = [
            ['FILD' => 'preference_type', 'DATA' => $type, 'TYP' => 's'],
            ['FILD' => 'preference_name', 'DATA' => $name, 'TYP' => 's']
        ];
        $found = $mycms->sql_select($find);

        if (!empty($found)) {
            $pref_id = (int)$found[0]['id'];
        } else {
            // 2) create it in rcg_group_preferences
            $ins = [];
            $ins['QUERY'] = "INSERT INTO `rcg_group_preferences`
                             SET preference_type=?, preference_name=?, status=?, created_by=?";
            $ins['PARAM'] = [
                ['FILD' => 'preference_type', 'DATA' => $type,       'TYP' => 's'],
                ['FILD' => 'preference_name', 'DATA' => $name,       'TYP' => 's'],
                ['FILD' => 'status',          'DATA' => 'A',         'TYP' => 's'],
                ['FILD' => 'created_by',      'DATA' => $created_by, 'TYP' => 'i']
            ];
            $pref_id = (int)$mycms->sql_insert($ins);
        }

        if ($pref_id <= 0) {
            throw new Exception("Could not save preference: " . $type . " - " . $name);
        }

        // 3) link group with preference in rcg_group_preference_map
        $map_sql = [];
        $map_sql['QUERY'] = "INSERT INTO `rcg_group_preference_map`
                             SET group_id=?, preference_id=?";
        $map_sql['PARAM'] = [
            ['FILD' => 'group_id',      'DATA' => $group_id, 'TYP' => 'i'],
            ['FILD' => 'preference_id', 'DATA' => $pref_id,  'TYP' => 'i']
        ];
        $mycms->sql_insert($map_sql);
    }
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
        $pageKey      => $pageKeyVal,
        'search_name' => isset($_REQUEST['search_name']) ? urlencode(trim($_REQUEST['search_name'])) : '',
        'search_type' => isset($_REQUEST['search_type']) ? urlencode(trim($_REQUEST['search_type'])) : '',
    ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}