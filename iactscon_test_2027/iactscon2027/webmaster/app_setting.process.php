<?php
include_once('includes/init.php');

$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];
date_default_timezone_set('Asia/Kolkata');

/* ==================================================================
   Helpers
   ================================================================== */

/* Read + validate the add/edit form. Throws Exception on bad input. */
function readSettingPost()
{
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        throw new Exception("Title is required.");
    }

    $type        = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon        = trim($_POST['icon'] ?? '');

    $additional = trim($_POST['additional_data'] ?? '');
    if ($additional !== '') {
        json_decode($additional);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Additional Data must be valid JSON.");
        }
    }

    $rowStatus = (($_POST['row_status'] ?? 'A') === 'I') ? 'I' : 'A';

    return [
        'type'            => ($type !== '') ? $type : null,
        'title'           => $title,
        'description'     => ($description !== '') ? $description : null,
        'icon'            => ($icon !== '') ? $icon : null,
        'default_value'   => (isset($_POST['default_value']) && (int)$_POST['default_value'] === 1) ? 1 : 0,
        'sort_order'      => isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0,
        'additional_data' => ($additional !== '') ? $additional : null,
        'row_status'      => $rowStatus,
    ];
}

/* "Session Start Reminders" -> "session_start_reminders" */
function makeSettingKeySlug($title)
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);   // anything else -> underscore
    $slug = trim($slug, '_');
    if ($slug === '') {
        $slug = 'setting';
    }
    return substr($slug, 0, 90);                          // leave room for _2, _3 ... suffix
}

/*
 * Unique slug from title. setting_key is UNIQUE across ALL rows (even removed ones),
 * so if the slug is taken we append _2, _3 ...
 */
function generateUniqueSettingKey($title)
{
    global $mycms;

    $base = makeSettingKeySlug($title);
    $key  = $base;
    $n    = 1;

    while (true) {
        $q = [];
        $q['QUERY'] = "SELECT setting_id FROM `rcg_notification_settings` WHERE setting_key = ? LIMIT 1";
        $q['PARAM'][] = ['FILD' => 'setting_key', 'DATA' => $key, 'TYP' => 's'];
        $res = $mycms->sql_select($q);

        if (empty($res)) {
            return $key;
        }
        $n++;
        $key = $base . '_' . $n;
    }
}

switch ($act) {

    /* ------------------------------------------------------------
       CREATE
       ------------------------------------------------------------ */
    case 'add_setting':
        try {
            $d = readSettingPost();
            $d['setting_key'] = generateUniqueSettingKey($d['title']);

            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_notification_settings`
                            SET setting_key=?, type=?, title=?, description=?, icon=?,
                                default_value=?, sort_order=?, additional_data=?, row_status=?,
                                created_by=?, updated_by=?";

            $sql['PARAM'] = [
                ['FILD' => 'setting_key',     'DATA' => $d['setting_key'],     'TYP' => 's'],
                ['FILD' => 'type',            'DATA' => $d['type'],            'TYP' => 's'],
                ['FILD' => 'title',           'DATA' => $d['title'],           'TYP' => 's'],
                ['FILD' => 'description',     'DATA' => $d['description'],     'TYP' => 's'],
                ['FILD' => 'icon',            'DATA' => $d['icon'],            'TYP' => 's'],
                ['FILD' => 'default_value',   'DATA' => $d['default_value'],   'TYP' => 'i'],
                ['FILD' => 'sort_order',      'DATA' => $d['sort_order'],      'TYP' => 'i'],
                ['FILD' => 'additional_data', 'DATA' => $d['additional_data'], 'TYP' => 's'],
                ['FILD' => 'row_status',      'DATA' => $d['row_status'],      'TYP' => 's'],
                ['FILD' => 'created_by',      'DATA' => $loggedUserID,         'TYP' => 'i'],
                ['FILD' => 'updated_by',      'DATA' => $loggedUserID,         'TYP' => 'i'],
            ];

            $mycms->sql_insert($sql);
            pageRedirection("app_setting.php", 1);  // message code 1 = added
        } catch (Exception $e) {
            pageRedirection("app_setting.php", "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------
       UPDATE
       ------------------------------------------------------------ */
    case 'edit_setting':
        try {
            if (empty($_POST['setting_id'])) {
                throw new Exception("Setting ID is required.");
            }
            $id = (int)$_POST['setting_id'];

            $d = readSettingPost();

            // setting_key is NOT updated on edit: the app reads settings by key,
            // so it must stay stable even if the title is changed.
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_notification_settings`
                            SET type=?, title=?, description=?, icon=?,
                                default_value=?, sort_order=?, additional_data=?, row_status=?,
                                updated_at = NOW(), updated_by=?
                         WHERE setting_id=? AND row_status!='D'";

            $sql['PARAM'] = [
                ['FILD' => 'type',            'DATA' => $d['type'],            'TYP' => 's'],
                ['FILD' => 'title',           'DATA' => $d['title'],           'TYP' => 's'],
                ['FILD' => 'description',     'DATA' => $d['description'],     'TYP' => 's'],
                ['FILD' => 'icon',            'DATA' => $d['icon'],            'TYP' => 's'],
                ['FILD' => 'default_value',   'DATA' => $d['default_value'],   'TYP' => 'i'],
                ['FILD' => 'sort_order',      'DATA' => $d['sort_order'],      'TYP' => 'i'],
                ['FILD' => 'additional_data', 'DATA' => $d['additional_data'], 'TYP' => 's'],
                ['FILD' => 'row_status',      'DATA' => $d['row_status'],      'TYP' => 's'],
                ['FILD' => 'updated_by',      'DATA' => $loggedUserID,         'TYP' => 'i'],
                ['FILD' => 'setting_id',      'DATA' => $id,                   'TYP' => 'i'],
            ];

            $mycms->sql_update($sql);
            pageRedirection("app_setting.php", 2);  // message code 2 = updated
        } catch (Exception $e) {
            pageRedirection("app_setting.php", "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------
       DELETE (soft delete -> row_status = 'D')
       ------------------------------------------------------------ */
    case 'Remove':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Setting ID is required.");
            }
            $id = (int)$_REQUEST['id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_notification_settings`
                            SET row_status=?, updated_at = NOW(), updated_by=?
                         WHERE setting_id=? AND row_status!='D'";

            $sql['PARAM'] = [
                ['FILD' => 'row_status', 'DATA' => 'D',           'TYP' => 's'],
                ['FILD' => 'updated_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                ['FILD' => 'setting_id', 'DATA' => $id,           'TYP' => 'i'],
            ];

            $mycms->sql_update($sql);
            pageRedirection("app_setting.php", 3);  // message code 3 = deleted
        } catch (Exception $e) {
            pageRedirection("app_setting.php", "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------
       ACTIVE / INACTIVE toggle
       ------------------------------------------------------------ */
    case 'Active':
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing Setting ID.");
            }
            $id = (int)$_REQUEST['id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_notification_settings`
                            SET row_status = ?, updated_at = NOW(), updated_by = ?
                         WHERE setting_id = ? AND row_status != 'D'";

            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => ($act === 'Active') ? 'A' : 'I', 'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $loggedUserID,                   'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'setting_id', 'DATA' => $id,                             'TYP' => 'i'];

            $mycms->sql_update($sql);
            pageRedirection("app_setting.php", 2);
        } catch (Exception $e) {
            pageRedirection("app_setting.php", "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------
       MULTI ACTION (ajax POST from the listing)
       ------------------------------------------------------------ */
    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No settings selected.");
            }
            if (empty($_POST['action'])) {
                throw new Exception("Please choose an action.");
            }

            $ids    = array_map('intval', $_POST['ids']);
            $action = $_POST['action'];

            $statusMap = ['Active' => 'A', 'Inactive' => 'I', 'Remove' => 'D'];
            if (!isset($statusMap[$action])) {
                throw new Exception("Invalid action selected.");
            }

            foreach ($ids as $id) {
                $sql = [];
                $sql['QUERY'] = "UPDATE `rcg_notification_settings`
                                SET row_status=?, updated_at = NOW(), updated_by=?
                             WHERE setting_id=? AND row_status!='D'";
                $sql['PARAM'] = [
                    ['FILD' => 'row_status', 'DATA' => $statusMap[$action], 'TYP' => 's'],
                    ['FILD' => 'updated_by', 'DATA' => $loggedUserID,       'TYP' => 'i'],
                    ['FILD' => 'setting_id', 'DATA' => $id,                 'TYP' => 'i'],
                ];
                $mycms->sql_update($sql);
            }

            pageRedirection("app_setting.php", ($action === 'Remove') ? 3 : 2);
        } catch (Exception $e) {
            pageRedirection("app_setting.php", "Error: " . $e->getMessage());
        }
        exit();
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey    = "_pgn_";
    $pageKeyVal = (($_REQUEST[$pageKey] ?? '') == "") ? 0 : (int)$_REQUEST[$pageKey];

    $searchString = "";
    $searchArray  = [
        $pageKey     => $pageKeyVal,
        'src_title'  => isset($_REQUEST['src_title']) ? urlencode(trim($_REQUEST['src_title'])) : '',
        'src_type'   => isset($_REQUEST['src_type'])  ? urlencode(trim($_REQUEST['src_type']))  : '',
    ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}