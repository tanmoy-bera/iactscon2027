<?php
include_once('./includes/init.php');
include_once('./includes/role_helper.php');

$loggedUserId = $mycms->getLoggedUserId();

if (!$loggedUserId) {
    die('Access denied. Please login as admin.');
}

$act    = $_REQUEST['act'] ?? '';
$userId = (int)($_REQUEST['user_id'] ?? 0);

if ($userId <= 0) {
    die('Invalid user_id');
}

try {
    $chk = [];
    $chk['QUERY'] = "SELECT `id`, `status`
                     FROM `rcg_user_registration`
                     WHERE `id`=? AND `status`='A' LIMIT 1";
    $chk['PARAM'] = [['FILD' => 'id', 'DATA' => $userId, 'TYP' => 'i']];
    $user = $mycms->sql_select($chk);

    if (empty($user)) {
        throw new Exception("User not found or inactive.");
    }

    switch ($act) {
        case 'make':
            makeModerator($mycms, $userId, $loggedUserId);
            pageRedirection("user_list.php", 1);
            break;

        case 'remove':
            removeModerator($mycms, $userId, $loggedUserId);
            pageRedirection("user_list.php", 2);
            break;

        default:
            throw new Exception("Invalid action.");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms;

    $pageKey    = "_pgn_";
    $pageKeyVal = empty($_REQUEST[$pageKey]) ? 0 : (int)$_REQUEST[$pageKey];

    $searchString = "";

    $searchArray = [
        $pageKey      => $pageKeyVal,
        'search_name' => isset($_REQUEST['search_name']) ? urlencode(trim($_REQUEST['search_name'])) : '',
    ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}