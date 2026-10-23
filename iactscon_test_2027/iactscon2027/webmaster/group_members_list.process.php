<?php
include_once('./includes/init.php');
include_once('./includes/fcm-helper.php');
$adminId = $mycms->getLoggedUserId();
if (!$adminId) {
    die('Access denied. Please login as admin.');
}

$act = $_REQUEST['act'] ?? '';

switch ($act) {

    /*============== REMOVE FROM GROUP ================*/
    case 'remove_member':
        try {
            $groupId = (int)($_REQUEST['group_id'] ?? 0);
            $userId  = (int)($_REQUEST['user_id'] ?? 0);
            $group   = getGroupOrFail($mycms, $groupId, $userId);
            assertActiveMember($mycms, $groupId, $userId);

            if ((int)$userId === (int)$group['created_by']) {
                throw new Exception("The group owner cannot be removed.");
            }

            // soft remove: keep the row, reset role so a later re-join does not keep old moderator rights
            $updMember = [];
            $updMember['QUERY'] = "UPDATE `rcg_group_members`
                                   SET status='R', left_at=NOW(), role='member'
                                   WHERE group_id=? AND user_id=? AND status='A'";
            $updMember['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            $mycms->sql_update($updMember);

            // accepted invitation -> cancelled (so the user can be invited again)
            $updInv = [];
            $updInv['QUERY'] = "UPDATE `rcg_group_invitations`
                                SET status='cancelled', responded_at=NOW()
                                WHERE group_id=? AND invited_user_id=? AND status='accepted'";
            $updInv['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'invited_user_id', 'DATA' => $userId, 'TYP' => 'i']
            ];
            $mycms->sql_update($updInv);

            notifyUser($mycms, $userId, 'group_removed', "Removed from Group",
                "You have been removed from \"" . $group['group_name'] . "\"",
                ['type' => 'group_removed', 'group_id' => $groupId]);

            setAlert('success', 'Member removed from group.');
            pageRedirection("group_members_list.php", 1);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_members_list.php", 0);
        }
        exit();

    /*============== BLOCK (admin chat block, this group only) ================*/
    case 'block_member':
        try {
            $groupId     = (int)($_POST['group_id'] ?? 0);
            $userId      = (int)($_POST['user_id'] ?? 0);
            $restriction = $_POST['restriction'] ?? '';
            $reason      = trim((string)($_POST['reason'] ?? ''));
            $reason      = $reason !== '' ? mb_substr($reason, 0, 255) : null;

            $group = getGroupOrFail($mycms, $groupId, $userId, true);
            assertActiveMember($mycms, $groupId, $userId);

            if (!in_array($restriction, ['no_chat', 'read_only'], true)) {
                throw new Exception("Choose a restriction.");
            }
            if ((int)$userId === (int)$group['created_by']) {
                throw new Exception("The group owner cannot be blocked.");
            }

            // existing admin block for this user + group (active or not)?
            $find = [];
            $find['QUERY'] = "SELECT `id` FROM `rcg_chat_blocks`
                              WHERE block_type='admin' AND blocked_user_id=? AND group_id=? LIMIT 1";
            $find['PARAM'] = [
                ['FILD' => 'blocked_user_id', 'DATA' => $userId,  'TYP' => 'i'],
                ['FILD' => 'group_id',        'DATA' => $groupId, 'TYP' => 'i']
            ];
            $existing = $mycms->sql_select($find);

            if (!empty($existing)) {
                $upd = [];
                $upd['QUERY'] = "UPDATE `rcg_chat_blocks`
                                 SET restriction=?, reason=?, status='A', updated_by=?, updated_at=NOW()
                                 WHERE id=?";
                $upd['PARAM'] = [
                    ['FILD' => 'restriction', 'DATA' => $restriction, 'TYP' => 's'],
                    ['FILD' => 'reason',      'DATA' => $reason,      'TYP' => 's'],
                    ['FILD' => 'updated_by',  'DATA' => $adminId,     'TYP' => 'i'],
                    ['FILD' => 'id',          'DATA' => (int)$existing[0]['id'], 'TYP' => 'i']
                ];
                $mycms->sql_update($upd);
            } else {
                $ins = [];
                $ins['QUERY'] = "INSERT INTO `rcg_chat_blocks`
                                 SET block_type='admin', blocked_user_id=?, blocker_id=0, group_id=?,
                                     restriction=?, reason=?, status='A',
                                     created_by=?, created_at=NOW(), updated_by=?, updated_at=NOW()";
                $ins['PARAM'] = [
                    ['FILD' => 'blocked_user_id', 'DATA' => $userId,      'TYP' => 'i'],
                    ['FILD' => 'group_id',        'DATA' => $groupId,     'TYP' => 'i'],
                    ['FILD' => 'restriction',     'DATA' => $restriction, 'TYP' => 's'],
                    ['FILD' => 'reason',          'DATA' => $reason,      'TYP' => 's'],
                    ['FILD' => 'created_by',      'DATA' => $adminId,     'TYP' => 'i'],
                    ['FILD' => 'updated_by',      'DATA' => $adminId,     'TYP' => 'i']
                ];
                $mycms->sql_insert($ins);
            }

            setAlert('success', 'User blocked in this group.');
            pageRedirection("group_members_list.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_members_list.php", 0);
        }
        exit();

    /*============== UNBLOCK (this group only) ================*/
    case 'unblock_member':
        try {
            $groupId = (int)($_REQUEST['group_id'] ?? 0);
            $userId  = (int)($_REQUEST['user_id'] ?? 0);
            getGroupOrFail($mycms, $groupId, $userId, true);

            $upd = [];
            $upd['QUERY'] = "UPDATE `rcg_chat_blocks`
                             SET status='I', updated_by=?, updated_at=NOW()
                             WHERE block_type='admin' AND status='A' AND blocked_user_id=? AND group_id=?";
            $upd['PARAM'] = [
                ['FILD' => 'updated_by',      'DATA' => $adminId, 'TYP' => 'i'],
                ['FILD' => 'blocked_user_id', 'DATA' => $userId,  'TYP' => 'i'],
                ['FILD' => 'group_id',        'DATA' => $groupId, 'TYP' => 'i']
            ];
            $mycms->sql_update($upd);

            setAlert('success', 'User unblocked.');
            pageRedirection("group_members_list.php", 3);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_members_list.php", 0);
        }
        exit();

    /*============== PROMOTE TO MODERATOR ================*/
    case 'promote_member':
        try {
            $groupId = (int)($_REQUEST['group_id'] ?? 0);
            $userId  = (int)($_REQUEST['user_id'] ?? 0);
            $group   = getGroupOrFail($mycms, $groupId, $userId, true);
            $member  = assertActiveMember($mycms, $groupId, $userId);

            if ($member['role'] === 'moderator') {
                throw new Exception("User is already a moderator.");
            }

            $upd = [];
            $upd['QUERY'] = "UPDATE `rcg_group_members` SET `role`='moderator'
                             WHERE group_id=? AND user_id=? AND status='A'";
            $upd['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            $mycms->sql_update($upd);

            $payload = ['type' => 'group_promoted', 'group_id' => $groupId];
            $title = "Promoted to Moderator";
            $body  = "You have been made a moderator of \"" . $group['group_name'] . "\"";
            notifyUser($mycms, $userId, 'group_promoted', $title, $body, $payload);
            sendFcmToUser($mycms, $userId, $title, $body, $payload); // best-effort

            setAlert('success', 'Member promoted to moderator.');
            pageRedirection("group_members_list.php", 4);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_members_list.php", 0);
        }
        exit();

    /*============== DEMOTE TO MEMBER ================*/
    case 'demote_member':
        try {
            $groupId = (int)($_REQUEST['group_id'] ?? 0);
            $userId  = (int)($_REQUEST['user_id'] ?? 0);
            $group   = getGroupOrFail($mycms, $groupId, $userId, true);

            if ((int)$userId === (int)$group['created_by']) {
                throw new Exception("The group owner's role cannot be changed.");
            }

            $upd = [];
            $upd['QUERY'] = "UPDATE `rcg_group_members` SET `role`='member'
                             WHERE group_id=? AND user_id=? AND status='A' AND role='moderator'";
            $upd['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            $affected = $mycms->sql_update($upd);
            if (!$affected) {
                throw new Exception("User is not currently a moderator of this group.");
            }

            notifyUser($mycms, $userId, 'group_demoted', "Moderator Role Removed",
                "You are no longer a moderator of \"" . $group['group_name'] . "\"",
                ['type' => 'group_demoted', 'group_id' => $groupId]);

            setAlert('success', 'Moderator demoted to member.');
            pageRedirection("group_members_list.php", 5);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_members_list.php", 0);
        }
        exit();

    default:
        die('Invalid action.');
}

/*============== HELPERS ================*/
/* One-time flash alert, shown on group_members_list.php */
function setAlert($type, $message)
{
    $_SESSION['members_alert'] = [
        'type'    => ($type === 'error') ? 'error' : 'success',
        'message' => $message
    ];
}

function getGroupOrFail($mycms, $groupId, $userId, $privateOnly = false)
{
    if ($groupId <= 0 || $userId <= 0) {
        throw new Exception("group_id and user_id are required.");
    }
    $q = [];
    $q['QUERY'] = "SELECT `id`, `group_name`, `group_type`, `created_by` FROM `rcg_groups` WHERE `id`=? AND `status` != 'D'";
    $q['PARAM'] = [['FILD' => 'id', 'DATA' => $groupId, 'TYP' => 'i']];
    $g = $mycms->sql_select($q);
    if (empty($g)) {
        throw new Exception("Group not found.");
    }
    if ($privateOnly && $g[0]['group_type'] !== 'private') {
        throw new Exception("This action is not available for this group.");
    }
    return $g[0];
}

function assertActiveMember($mycms, $groupId, $userId)
{
    $q = [];
    $q['QUERY'] = "SELECT `id`, `role` FROM `rcg_group_members` WHERE `group_id`=? AND `user_id`=? AND `status`='A' LIMIT 1";
    $q['PARAM'] = [
        ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
        ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
    ];
    $r = $mycms->sql_select($q);
    if (empty($r)) {
        throw new Exception("User is not an active member of this group.");
    }
    return $r[0];
}

function notifyUser($mycms, $userId, $type, $title, $body, $dataPayload)
{
    $n = [];
    $n['QUERY'] = "INSERT INTO `rcg_notifications` SET user_id=?, type=?, title=?, body=?, data=?";
    $n['PARAM'] = [
        ['FILD' => 'user_id', 'DATA' => $userId, 'TYP' => 'i'],
        ['FILD' => 'type',    'DATA' => $type,   'TYP' => 's'],
        ['FILD' => 'title',   'DATA' => $title,  'TYP' => 's'],
        ['FILD' => 'body',    'DATA' => $body,   'TYP' => 's'],
        ['FILD' => 'data',    'DATA' => json_encode($dataPayload), 'TYP' => 's']
    ];
    $mycms->sql_insert($n);
}

function pageRedirection($fileName, $messageCode)
{
    global $mycms;

    $pageKey    = "_pgn_";
    $pageKeyVal = empty($_REQUEST[$pageKey]) ? 0 : (int)$_REQUEST[$pageKey];

    $searchString = "";
    $searchArray = [
        $pageKey      => $pageKeyVal,
        'group_id'    => isset($_REQUEST['group_id']) ? (int)$_REQUEST['group_id'] : '',
        'search_name' => isset($_REQUEST['search_name']) ? urlencode(trim($_REQUEST['search_name'])) : '',
    ];
    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $searchString);
}