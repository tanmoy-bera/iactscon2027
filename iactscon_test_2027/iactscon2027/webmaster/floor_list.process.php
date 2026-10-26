<?php

include_once('./includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];

switch ($act) {
    case 'add_floor':
        try {
            $name       = trim($_POST['floor_name'] ?? '');
            $status     = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $created_by = $loggedUserID;

            if ($name === '') {
                throw new Exception("Floor name is required.");
            }
            $name = function_exists('mb_substr') ? mb_substr($name, 0, 255) : substr($name, 0, 255);

            if (floorNameExists($mycms, $name, 0)) {
                throw new Exception("This floor already exists.");
            }

            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_floor_master`
                             SET floor_name=?, status=?, created_by=?";
            $sql['PARAM'] = [
                ['FILD' => 'floor_name', 'DATA' => $name,       'TYP' => 's'],
                ['FILD' => 'status',     'DATA' => $status,     'TYP' => 's'],
                ['FILD' => 'created_by', 'DATA' => $created_by, 'TYP' => 'i']
            ];
            $mycms->sql_insert($sql);

            setAlert('success', 'Floor added successfully.');
            pageRedirection("floor_list.php", 1);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("floor_list.php", 0, "&show=add");
        }
        exit();

    case 'edit_floor':
        $floor_id = (int)($_POST['floor_id'] ?? 0);
        try {
            $name       = trim($_POST['floor_name'] ?? '');
            $status     = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $updated_by = $loggedUserID;

            if ($floor_id <= 0) {
                throw new Exception("Floor ID is required.");
            }
            if ($name === '') {
                throw new Exception("Floor name is required.");
            }
            $name = function_exists('mb_substr') ? mb_substr($name, 0, 255) : substr($name, 0, 255);

            if (floorNameExists($mycms, $name, $floor_id)) {
                throw new Exception("This floor already exists.");
            }

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_floor_master`
                             SET floor_name=?, status=?, updated_by=?, updated_at=NOW()
                             WHERE floor_id=? AND status != 'D'";
            $sql['PARAM'] = [
                ['FILD' => 'floor_name', 'DATA' => $name,       'TYP' => 's'],
                ['FILD' => 'status',     'DATA' => $status,     'TYP' => 's'],
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'floor_id',   'DATA' => $floor_id,   'TYP' => 'i']
            ];
            $mycms->sql_update($sql);

            setAlert('success', 'Floor updated successfully.');
            pageRedirection("floor_list.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            $back = ($floor_id > 0) ? "&show=edit&floor_id=" . $floor_id : "";
            pageRedirection("floor_list.php", 0, $back);
        }
        exit();

    case 'Delete':
        try {
            if (empty($_GET['id'])) {
                throw new Exception("Floor ID is required.");
            }

            $id         = (int)$_GET['id'];
            $updated_by = $loggedUserID;

            // Soft delete
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_floor_master`
                             SET status='D', updated_by=?, updated_at=NOW()
                             WHERE floor_id=? AND status != 'D'";
            $sql['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'floor_id',   'DATA' => $id,         'TYP' => 'i']
            ];
            $mycms->sql_update($sql);

            setAlert('success', 'Floor deleted successfully.');
            pageRedirection("floor_list.php", 3);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("floor_list.php", 0);
        }
        exit();

    case 'Active':
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing Floor ID.");
            }

            $id         = (int)$_REQUEST['id'];
            $newStatus  = ($act === 'Active') ? 'A' : 'I';
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_floor_master`
                             SET status=?, updated_by=?, updated_at=NOW()
                             WHERE floor_id=? AND status != 'D'";
            $sql['PARAM'] = [
                ['FILD' => 'status',     'DATA' => $newStatus,  'TYP' => 's'],
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'floor_id',   'DATA' => $id,         'TYP' => 'i']
            ];
            $mycms->sql_update($sql);

            setAlert('success', 'Floor marked as ' . (($act === 'Active') ? 'active' : 'inactive') . ' successfully.');
            pageRedirection("floor_list.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("floor_list.php", 0);
        }
        exit();

    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No floors selected.");
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
                $sql['QUERY'] = "UPDATE `rcg_floor_master`
                                 SET status=?, updated_by=?, updated_at=NOW()
                                 WHERE floor_id=? AND status != 'D'";
                $sql['PARAM'] = [
                    ['FILD' => 'status',     'DATA' => $statusMap[$action], 'TYP' => 's'],
                    ['FILD' => 'updated_by', 'DATA' => $loggedUserID,       'TYP' => 'i'],
                    ['FILD' => 'floor_id',   'DATA' => $id,                 'TYP' => 'i']
                ];
                $mycms->sql_update($sql);
                $count++;
            }

            $verb = ['Active' => 'marked as active', 'Inactive' => 'marked as inactive', 'Remove' => 'removed'];
            setAlert('success', $count . ' floor(s) ' . $verb[$action] . ' successfully.');
            pageRedirection("floor_list.php", ($action === 'Remove') ? 3 : 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("floor_list.php", 0);
        }
        exit();
}

/*============== HELPER: FLASH ALERT (shown once on the list page) ================*/
function setAlert($type, $message)
{
    $_SESSION['floor_alert'] = [
        'type'    => ($type === 'error') ? 'error' : 'success',
        'message' => $message
    ];
}

/*============== HELPER: DUPLICATE NAME CHECK ================*/
function floorNameExists($mycms, $name, $excludeId = 0)
{
    $sql = [];
    $sql['QUERY'] = "SELECT `floor_id` FROM `rcg_floor_master`
                     WHERE LOWER(`floor_name`) = LOWER(?) AND `status` != 'D' AND `floor_id` != ?
                     LIMIT 1";
    $sql['PARAM'] = [
        ['FILD' => 'floor_name', 'DATA' => $name,           'TYP' => 's'],
        ['FILD' => 'floor_id',   'DATA' => (int)$excludeId, 'TYP' => 'i']
    ];
    $res = $mycms->sql_select($sql);
    return !empty($res);
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