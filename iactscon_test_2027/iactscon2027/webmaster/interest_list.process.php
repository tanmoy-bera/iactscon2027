<?php

include_once('./includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];

switch ($act) {
    case 'add_interest':
        try {
            $name       = trim($_POST['name'] ?? '');
            $status     = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $created_by = $loggedUserID;

            if ($name === '') {
                throw new Exception("Interest name is required.");
            }
            $name = function_exists('mb_substr') ? mb_substr($name, 0, 255) : substr($name, 0, 255);

            if (interestNameExists($mycms, $name, 0)) {
                throw new Exception("This interest already exists.");
            }

            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_interests`
                             SET name=?, status=?, created_by=?";
            $sql['PARAM'] = [
                ['FILD' => 'name',       'DATA' => $name,       'TYP' => 's'],
                ['FILD' => 'status',     'DATA' => $status,     'TYP' => 's'],
                ['FILD' => 'created_by', 'DATA' => $created_by, 'TYP' => 'i']
            ];
            $mycms->sql_insert($sql);

            setAlert('success', 'Interest added successfully.');
            pageRedirection("interest_list.php", 1);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("interest_list.php", 0, "&show=add");
        }
        exit();

    case 'edit_interest':
        $interest_id = (int)($_POST['interest_id'] ?? 0);
        try {
            $name        = trim($_POST['name'] ?? '');
            $status      = (($_POST['status'] ?? 'A') === 'I') ? 'I' : 'A';
            $updated_by  = $loggedUserID;

            if ($interest_id <= 0) {
                throw new Exception("Interest ID is required.");
            }
            if ($name === '') {
                throw new Exception("Interest name is required.");
            }
            $name = function_exists('mb_substr') ? mb_substr($name, 0, 255) : substr($name, 0, 255);

            if (interestNameExists($mycms, $name, $interest_id)) {
                throw new Exception("This interest already exists.");
            }

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_interests`
                             SET name=?, status=?, updated_by=?, updated_at=NOW()
                             WHERE interest_id=? AND status != 'D'";
            $sql['PARAM'] = [
                ['FILD' => 'name',        'DATA' => $name,        'TYP' => 's'],
                ['FILD' => 'status',      'DATA' => $status,      'TYP' => 's'],
                ['FILD' => 'updated_by',  'DATA' => $updated_by,  'TYP' => 'i'],
                ['FILD' => 'interest_id', 'DATA' => $interest_id, 'TYP' => 'i']
            ];
            $mycms->sql_update($sql);

            setAlert('success', 'Interest updated successfully.');
            pageRedirection("interest_list.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            $back = ($interest_id > 0) ? "&show=edit&interest_id=" . $interest_id : "";
            pageRedirection("interest_list.php", 0, $back);
        }
        exit();

    case 'Delete':
        try {
            if (empty($_GET['id'])) {
                throw new Exception("Interest ID is required.");
            }

            $id         = (int)$_GET['id'];
            $updated_by = $loggedUserID;

            // Soft delete
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_interests`
                             SET status='D', updated_by=?, updated_at=NOW()
                             WHERE interest_id=? AND status != 'D'";
            $sql['PARAM'] = [
                ['FILD' => 'updated_by',  'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'interest_id', 'DATA' => $id,         'TYP' => 'i']
            ];
            $mycms->sql_update($sql);

            setAlert('success', 'Interest deleted successfully.');
            pageRedirection("interest_list.php", 3);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("interest_list.php", 0);
        }
        exit();

    case 'Active':
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing Interest ID.");
            }

            $id         = (int)$_REQUEST['id'];
            $newStatus  = ($act === 'Active') ? 'A' : 'I';
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_interests`
                             SET status=?, updated_by=?, updated_at=NOW()
                             WHERE interest_id=? AND status != 'D'";
            $sql['PARAM'] = [
                ['FILD' => 'status',      'DATA' => $newStatus,  'TYP' => 's'],
                ['FILD' => 'updated_by',  'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'interest_id', 'DATA' => $id,         'TYP' => 'i']
            ];
            $mycms->sql_update($sql);

            setAlert('success', 'Interest marked as ' . (($act === 'Active') ? 'active' : 'inactive') . ' successfully.');
            pageRedirection("interest_list.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("interest_list.php", 0);
        }
        exit();

    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No interests selected.");
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
                $sql['QUERY'] = "UPDATE `rcg_interests`
                                 SET status=?, updated_by=?, updated_at=NOW()
                                 WHERE interest_id=? AND status != 'D'";
                $sql['PARAM'] = [
                    ['FILD' => 'status',      'DATA' => $statusMap[$action], 'TYP' => 's'],
                    ['FILD' => 'updated_by',  'DATA' => $loggedUserID,       'TYP' => 'i'],
                    ['FILD' => 'interest_id', 'DATA' => $id,                 'TYP' => 'i']
                ];
                $mycms->sql_update($sql);
                $count++;
            }

            $verb = ['Active' => 'marked as active', 'Inactive' => 'marked as inactive', 'Remove' => 'removed'];
            setAlert('success', $count . ' interest(s) ' . $verb[$action] . ' successfully.');
            pageRedirection("interest_list.php", ($action === 'Remove') ? 3 : 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("interest_list.php", 0);
        }
        exit();
}

/*============== HELPER: FLASH ALERT (shown once on the list page) ================*/
function setAlert($type, $message)
{
    $_SESSION['interest_alert'] = [
        'type'    => ($type === 'error') ? 'error' : 'success',
        'message' => $message
    ];
}

/*============== HELPER: DUPLICATE NAME CHECK ================*/
function interestNameExists($mycms, $name, $excludeId = 0)
{
    $sql = [];
    $sql['QUERY'] = "SELECT `interest_id` FROM `rcg_interests`
                     WHERE LOWER(`name`) = LOWER(?) AND `status` != 'D' AND `interest_id` != ?
                     LIMIT 1";
    $sql['PARAM'] = [
        ['FILD' => 'name',        'DATA' => $name,             'TYP' => 's'],
        ['FILD' => 'interest_id', 'DATA' => (int)$excludeId,   'TYP' => 'i']
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