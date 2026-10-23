<?php

include_once('./includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];


define('GROUP_COVER_URL', '/webmaster/uploads/groups/');
define('GROUP_COVER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/webmaster/uploads/groups/');

/* Cardiac Talks Topics = PUBLIC groups. group_type is fixed in the backend, never shown or chosen in the UI.
   Private groups are managed from group_list.php. */
switch ($act) {
    case 'add_topic':
        try {
            $group_name        = trim($_POST['group_name'] ?? '');
            $group_description = isset($_POST['group_description']) ? trim($_POST['group_description']) : null;
            $preference_type   = (($_POST['preference_type'] ?? 'all') === 'interest') ? 'interest' : 'all';
            $interest_ids      = $_POST['interest_ids'] ?? [];
            $status            = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $created_by        = $loggedUserID;

            if ($group_name === '') {
                throw new Exception("Topic name is required.");
            }
            if ($preference_type === 'interest' && (!is_array($interest_ids) || count($interest_ids) === 0)) {
                throw new Exception("Please select at least one interest.");
            }

            // Cover image (optional)
            $cover_image = uploadGroupCover('cover_image');

            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_groups`
                        SET group_name=?, group_description=?, group_type='public', preference_type=?, cover_image=?,
                            created_by=?, total_members=?, status=?";

            $sql['PARAM'] = [
                ['FILD' => 'group_name',        'DATA' => $group_name,        'TYP' => 's'],
                ['FILD' => 'group_description', 'DATA' => $group_description, 'TYP' => 's'],
                ['FILD' => 'preference_type',   'DATA' => $preference_type,   'TYP' => 's'],
                ['FILD' => 'cover_image',       'DATA' => $cover_image,       'TYP' => 's'],
                ['FILD' => 'created_by',        'DATA' => $created_by,        'TYP' => 'i'],
                ['FILD' => 'total_members',     'DATA' => 0,                  'TYP' => 'i'],
                ['FILD' => 'status',            'DATA' => $status,            'TYP' => 's']
            ];

            $group_id = $mycms->sql_insert($sql);

            // Save interest mapping (only when "interest wise")
            if ($preference_type === 'interest') {
                saveGroupInterests($mycms, $group_id, $interest_ids, $created_by);
            }

            setAlert('success', 'Topic created successfully.');
            pageRedirection("cardiac_talks_topics.php", 1);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_topics.php", 0, "&show=add");
        }
        exit();

    case 'edit_topic':
        $group_id = (int)($_POST['group_id'] ?? 0);
        try {
            $group_name        = trim($_POST['group_name'] ?? '');
            $group_description = isset($_POST['group_description']) ? trim($_POST['group_description']) : null;
            $preference_type   = (($_POST['preference_type'] ?? 'all') === 'interest') ? 'interest' : 'all';
            $interest_ids      = $_POST['interest_ids'] ?? [];
            $status            = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $old_cover_image   = trim($_POST['old_cover_image'] ?? '');
            $updated_by        = $loggedUserID;

            if ($group_id <= 0) {
                throw new Exception("Topic ID is required.");
            }
            if ($group_name === '') {
                throw new Exception("Topic name is required.");
            }
            if ($preference_type === 'interest' && (!is_array($interest_ids) || count($interest_ids) === 0)) {
                throw new Exception("Please select at least one interest.");
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
                        SET group_name=?, group_description=?, preference_type=?, cover_image=?,
                            status=?, updated_by=?, updated_at=NOW()
                        WHERE id=? AND group_type='public' AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'group_name',        'DATA' => $group_name,        'TYP' => 's'],
                ['FILD' => 'group_description', 'DATA' => $group_description, 'TYP' => 's'],
                ['FILD' => 'preference_type',   'DATA' => $preference_type,   'TYP' => 's'],
                ['FILD' => 'cover_image',       'DATA' => $cover_image,       'TYP' => 's'],
                ['FILD' => 'status',            'DATA' => $status,            'TYP' => 's'],
                ['FILD' => 'updated_by',        'DATA' => $updated_by,        'TYP' => 'i'],
                ['FILD' => 'id',                'DATA' => $group_id,          'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            // Re-sync interest mapping: soft-delete all current rows,
            // then re-activate / insert the selected ones (none when switched to "all")
            $del = [];
            $del['QUERY'] = "UPDATE `rcg_group_interest_map`
                             SET status='D', updated_by=?, updated_at=NOW()
                             WHERE group_id=? AND status != 'D'";
            $del['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'group_id',   'DATA' => $group_id,   'TYP' => 'i']
            ];
            $mycms->sql_update($del);

            if ($preference_type === 'interest') {
                saveGroupInterests($mycms, $group_id, $interest_ids, $updated_by);
            }

            setAlert('success', 'Topic updated successfully.');
            pageRedirection("cardiac_talks_topics.php", 2); // 2 = updated successfully
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            $back = ($group_id > 0) ? "&show=edit&group_id=" . $group_id : "";
            pageRedirection("cardiac_talks_topics.php", 0, $back);
        }
        exit();

    case 'Delete':
        try {
            if (empty($_GET['id'])) {
                throw new Exception("Topic ID is required.");
            }

            $id         = (int)$_GET['id'];
            $updated_by = $loggedUserID;

            // Soft delete
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_groups`
                         SET status=?, updated_by=?, updated_at=NOW()
                         WHERE id=? AND group_type='public' AND status != 'D'";

            $sql['PARAM'] = [
                ['FILD' => 'status',     'DATA' => 'D',         'TYP' => 's'],
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            setAlert('success', 'Topic deleted successfully.');
            pageRedirection("cardiac_talks_topics.php", 3); // 3 = deleted
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_topics.php", 0);
        }
        exit();

    case 'Active':
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing Topic ID.");
            }

            $group_id   = (int)$_REQUEST['id'];
            $newStatus  = ($act === 'Active') ? 'A' : 'I';
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_groups`
                         SET status = ?, updated_at = NOW(), updated_by = ?
                         WHERE id = ? AND group_type='public' AND status != 'D'";

            $sql['PARAM'][] = ['FILD' => 'status',     'DATA' => $newStatus,  'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $group_id,   'TYP' => 'i'];

            $mycms->sql_update($sql);

            setAlert('success', 'Topic marked as ' . (($act === 'Active') ? 'active' : 'inactive') . ' successfully.');
            pageRedirection("cardiac_talks_topics.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_topics.php", 0);
        }
        exit();

    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No topics selected.");
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
                                 WHERE id=? AND group_type='public' AND status != 'D'";
                $sql['PARAM'] = [
                    ['FILD' => 'status',     'DATA' => $statusMap[$action], 'TYP' => 's'],
                    ['FILD' => 'updated_by', 'DATA' => $loggedUserID,       'TYP' => 'i'],
                    ['FILD' => 'id',         'DATA' => $id,                 'TYP' => 'i']
                ];
                $mycms->sql_update($sql);
                $count++;
            }

            $verb = ['Active' => 'marked as active', 'Inactive' => 'marked as inactive', 'Remove' => 'removed'];
            setAlert('success', $count . ' topic(s) ' . $verb[$action] . ' successfully.');
            pageRedirection("cardiac_talks_topics.php", ($action === 'Remove') ? 3 : 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_topics.php", 0);
        }
        exit();
}

/*============== HELPER: FLASH ALERT (shown once on the list page) ================*/
function setAlert($type, $message)
{
    $_SESSION['topic_alert'] = [
        'type'    => ($type === 'error') ? 'error' : 'success',
        'message' => $message
    ];
}

/*============== HELPER: SAVE INTEREST MAPPING ================*/
function saveGroupInterests($mycms, $group_id, $interest_ids, $user_id)
{
    if (!is_array($interest_ids)) {
        return;
    }

    $done = [];
    foreach ($interest_ids as $iid) {
        $iid = (int)$iid;
        if ($iid <= 0 || isset($done[$iid])) {
            continue;
        }
        $done[$iid] = true;

        // Only active interests are allowed
        $find = [];
        $find['QUERY'] = "SELECT `interest_id` FROM `rcg_interests`
                          WHERE `interest_id`=? AND `status`='A' LIMIT 1";
        $find['PARAM'][] = ['FILD' => 'interest_id', 'DATA' => $iid, 'TYP' => 'i'];
        $found = $mycms->sql_select($find);

        if (empty($found)) {
            throw new Exception("Invalid or inactive interest selected.");
        }

        // Row already exists (e.g. soft-deleted earlier)? re-activate it, else insert
        $chk = [];
        $chk['QUERY'] = "SELECT `id` FROM `rcg_group_interest_map`
                         WHERE `group_id`=? AND `interest_id`=? LIMIT 1";
        $chk['PARAM'] = [
            ['FILD' => 'group_id',    'DATA' => $group_id, 'TYP' => 'i'],
            ['FILD' => 'interest_id', 'DATA' => $iid,      'TYP' => 'i']
        ];
        $existing = $mycms->sql_select($chk);

        if (!empty($existing)) {
            $upd = [];
            $upd['QUERY'] = "UPDATE `rcg_group_interest_map`
                             SET status='A', updated_by=?, updated_at=NOW()
                             WHERE id=?";
            $upd['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $user_id,                'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => (int)$existing[0]['id'], 'TYP' => 'i']
            ];
            $mycms->sql_update($upd);
        } else {
            $map_sql = [];
            $map_sql['QUERY'] = "INSERT INTO `rcg_group_interest_map`
                                 SET group_id=?, interest_id=?, status=?, created_by=?";
            $map_sql['PARAM'] = [
                ['FILD' => 'group_id',    'DATA' => $group_id, 'TYP' => 'i'],
                ['FILD' => 'interest_id', 'DATA' => $iid,      'TYP' => 'i'],
                ['FILD' => 'status',      'DATA' => 'A',       'TYP' => 's'],
                ['FILD' => 'created_by',  'DATA' => $user_id,  'TYP' => 'i']
            ];
            $mycms->sql_insert($map_sql);
        }
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
        $pageKey           => $pageKeyVal,
        'search_name'      => isset($_REQUEST['search_name']) ? urlencode(trim($_REQUEST['search_name'])) : '',
        'search_pref_type' => isset($_REQUEST['search_pref_type']) ? urlencode(trim($_REQUEST['search_pref_type'])) : '',
        'search_status'    => isset($_REQUEST['search_status']) ? urlencode(trim($_REQUEST['search_status'])) : '',
    ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}