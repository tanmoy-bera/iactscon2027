<?php
include_once('./includes/init.php');
include_once('./includes/fcm-helper.php'); 
$adminId = $mycms->getLoggedUserId();
if (!$adminId) {
    die('Access denied. Please login as admin.');
}

$act = $_REQUEST['act'] ?? '';

switch ($act) {

    case 'send_invite':
        try {
            $groupId = (int)($_REQUEST['group_id'] ?? 0);
            $userId  = (int)($_REQUEST['user_id'] ?? 0);

            if ($groupId <= 0 || $userId <= 0) {
                throw new Exception("group_id and user_id are required.");
            }

            // group active + private কিনা
            $gsql = [];
            $gsql['QUERY'] = "SELECT `id`, `group_name`, `created_by` FROM `rcg_groups`
                              WHERE `id`=? AND `status`='A' AND `group_type`='private'";
            $gsql['PARAM'] = [['FILD' => 'id', 'DATA' => $groupId, 'TYP' => 'i']];
            $group = $mycms->sql_select($gsql);
            if (empty($group)) {
                throw new Exception("Group not found or not a private group.");
            }
            $group = $group[0];

            // permission check — শুধু group admin (creator) invite পাঠাতে পারবে
            if ((int)$group['created_by'] !== (int)$adminId) {
                throw new Exception("Only the group admin can send invitations.");
            }

            // user active কিনা
            $usql = [];
            $usql['QUERY'] = "SELECT `id` FROM `rcg_user_registration` WHERE `id`=? AND `status`='A'";
            $usql['PARAM'] = [['FILD' => 'id', 'DATA' => $userId, 'TYP' => 'i']];
            if (empty($mycms->sql_select($usql))) {
                throw new Exception("User not found or inactive.");
            }

            // আগে থেকেই active member কিনা
            $msql = [];
            $msql['QUERY'] = "SELECT `id` FROM `rcg_group_members` WHERE `group_id`=? AND `user_id`=? AND `status`='A' LIMIT 1";
            $msql['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            if (!empty($mycms->sql_select($msql))) {
                throw new Exception("User is already a member of this group.");
            }

            // আগে থেকেই pending invite কিনা
            $isql = [];
            $isql['QUERY'] = "SELECT `id` FROM `rcg_group_invitations`
                              WHERE `group_id`=? AND `invited_user_id`=? AND `status`='pending' LIMIT 1";
            $isql['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'invited_user_id', 'DATA' => $userId, 'TYP' => 'i']
            ];
            if (!empty($mycms->sql_select($isql))) {
                throw new Exception("An invitation is already pending for this user.");
            }

            // invitation insert
            $insSql = [];
            $insSql['QUERY'] = "INSERT INTO `rcg_group_invitations`
                                SET group_id=?, invited_user_id=?, invited_by=?, status='pending'";
            $insSql['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'invited_user_id', 'DATA' => $userId, 'TYP' => 'i'],
                ['FILD' => 'invited_by', 'DATA' => $adminId, 'TYP' => 'i']
            ];
            $invitationId = $mycms->sql_insert($insSql);

            // in-app notification log
            $title = "Group Invitation";
            $body  = "You have been invited to join \"" . $group['group_name'] . "\"";
            $dataPayload = [
                'type'          => 'group_invite',
                'group_id'      => $groupId,
                'invitation_id' => $invitationId
            ];

            $notifSql = [];
            $notifSql['QUERY'] = "INSERT INTO `rcg_notifications`
                                  SET user_id=?, type='group_invite', title=?, body=?, data=?";
            $notifSql['PARAM'] = [
                ['FILD' => 'user_id', 'DATA' => $userId, 'TYP' => 'i'],
                ['FILD' => 'title',   'DATA' => $title,   'TYP' => 's'],
                ['FILD' => 'body',    'DATA' => $body,    'TYP' => 's'],
                ['FILD' => 'data',    'DATA' => json_encode($dataPayload), 'TYP' => 's']
            ];
            $mycms->sql_insert($notifSql);

            // FCM push (silently fail করলেও invitation তৈরি থাকবে, push শুধু best-effort)
            sendFcmToUser($mycms, $userId, $title, $body, $dataPayload);

            pageRedirection("group_invite.php", 1); // 1 = invite sent
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    case 'cancel_invite':
        try {
            $invitationId = (int)($_REQUEST['invitation_id'] ?? 0);
            if ($invitationId <= 0) {
                throw new Exception("invitation_id is required.");
            }

            $upd = [];
            $upd['QUERY'] = "UPDATE `rcg_group_invitations`
                             SET status='cancelled', responded_at=NOW()
                             WHERE id=? AND status='pending'";
            $upd['PARAM'] = [['FILD' => 'id', 'DATA' => $invitationId, 'TYP' => 'i']];
            $mycms->sql_update($upd);

            pageRedirection("group_invite.php", 2); // 2 = cancelled
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    case 'kick_member':
        try {
            $groupId = (int)($_REQUEST['group_id'] ?? 0);
            $userId  = (int)($_REQUEST['user_id'] ?? 0);

            if ($groupId <= 0 || $userId <= 0) {
                throw new Exception("group_id and user_id are required.");
            }

            // group active + private কিনা, ar admin ei group-er creator কিনা
            $gsql = [];
            $gsql['QUERY'] = "SELECT `id`, `group_name`, `created_by` FROM `rcg_groups`
                              WHERE `id`=? AND `status`='A' AND `group_type`='private'";
            $gsql['PARAM'] = [['FILD' => 'id', 'DATA' => $groupId, 'TYP' => 'i']];
            $group = $mycms->sql_select($gsql);
            if (empty($group)) {
                throw new Exception("Group not found or not a private group.");
            }
            $group = $group[0];

            if ((int)$group['created_by'] !== (int)$adminId) {
                throw new Exception("Only the group admin can remove members.");
            }

            // user ta actually active member কিনা check koro
            $msql = [];
            $msql['QUERY'] = "SELECT `id` FROM `rcg_group_members` WHERE `group_id`=? AND `user_id`=? AND `status`='A' LIMIT 1";
            $msql['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            $memberRow = $mycms->sql_select($msql);
            if (empty($memberRow)) {
                throw new Exception("User is not an active member of this group.");
            }

            // group_members-er status 'R' (removed) e update koro, left_at set koro, role o reset kore
            // dio 'member' e — jate pore abar add hole purono moderator role niye na dhoke — DELETE na kore
            $updMember = [];
            $updMember['QUERY'] = "UPDATE `rcg_group_members`
                                   SET status='R', left_at=NOW(), role='member'
                                   WHERE group_id=? AND user_id=? AND status='A'";
            $updMember['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            $mycms->sql_update($updMember);

            // matching invitation-ke o track er jonno 'cancelled' e mark koro (jodi accepted hisheve ache)
            $updInv = [];
            $updInv['QUERY'] = "UPDATE `rcg_group_invitations`
                                SET status='cancelled', responded_at=NOW()
                                WHERE group_id=? AND invited_user_id=? AND status='accepted'";
            $updInv['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'invited_user_id', 'DATA' => $userId, 'TYP' => 'i']
            ];
            $mycms->sql_update($updInv);

            // user-ke in-app notification pathao je take remove kora hoyeche
            $title = "Removed from Group";
            $body  = "You have been removed from \"" . $group['group_name'] . "\"";
            $notifSql = [];
            $notifSql['QUERY'] = "INSERT INTO `rcg_notifications`
                                  SET user_id=?, type='group_removed', title=?, body=?, data=?";
            $notifSql['PARAM'] = [
                ['FILD' => 'user_id', 'DATA' => $userId, 'TYP' => 'i'],
                ['FILD' => 'title',   'DATA' => $title,   'TYP' => 's'],
                ['FILD' => 'body',    'DATA' => $body,    'TYP' => 's'],
                ['FILD' => 'data',    'DATA' => json_encode(['type' => 'group_removed', 'group_id' => $groupId]), 'TYP' => 's']
            ];
            $mycms->sql_insert($notifSql);

            pageRedirection("group_invite.php", 3); // 3 = member removed
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    case 'promote_member':
        try {
            $groupId = (int)($_REQUEST['group_id'] ?? 0);
            $userId  = (int)($_REQUEST['user_id'] ?? 0);

            if ($groupId <= 0 || $userId <= 0) {
                throw new Exception("group_id and user_id are required.");
            }

            // group active + private কিনা, ar admin ei group-er creator কিনা
            $gsql = [];
            $gsql['QUERY'] = "SELECT `id`, `group_name`, `created_by` FROM `rcg_groups`
                              WHERE `id`=? AND `status`='A' AND `group_type`='private'";
            $gsql['PARAM'] = [['FILD' => 'id', 'DATA' => $groupId, 'TYP' => 'i']];
            $group = $mycms->sql_select($gsql);
            if (empty($group)) {
                throw new Exception("Group not found or not a private group.");
            }
            $group = $group[0];

            // শুধু group creator (admin) e promote korte parbe
            if ((int)$group['created_by'] !== (int)$adminId) {
                throw new Exception("Only the group admin can promote members.");
            }

            // active member কিনা check koro
            $msql = [];
            $msql['QUERY'] = "SELECT `id`, `role` FROM `rcg_group_members`
                              WHERE `group_id`=? AND `user_id`=? AND `status`='A' LIMIT 1";
            $msql['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            $memberRow = $mycms->sql_select($msql);
            if (empty($memberRow)) {
                throw new Exception("User is not an active member of this group.");
            }
            if ($memberRow[0]['role'] === 'moderator') {
                throw new Exception("User is already a moderator.");
            }

            $updSql = [];
            $updSql['QUERY'] = "UPDATE `rcg_group_members` SET `role`='moderator'
                                WHERE group_id=? AND user_id=? AND status='A'";
            $updSql['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            $mycms->sql_update($updSql);

            // user-ke in-app notification pathao je take moderator kora hoyeche
            $title = "Promoted to Moderator";
            $body  = "You have been made a moderator of \"" . $group['group_name'] . "\"";
            $dataPayload = ['type' => 'group_promoted', 'group_id' => $groupId];

            $notifSql = [];
            $notifSql['QUERY'] = "INSERT INTO `rcg_notifications`
                                  SET user_id=?, type='group_promoted', title=?, body=?, data=?";
            $notifSql['PARAM'] = [
                ['FILD' => 'user_id', 'DATA' => $userId, 'TYP' => 'i'],
                ['FILD' => 'title',   'DATA' => $title,   'TYP' => 's'],
                ['FILD' => 'body',    'DATA' => $body,    'TYP' => 's'],
                ['FILD' => 'data',    'DATA' => json_encode($dataPayload), 'TYP' => 's']
            ];
            $mycms->sql_insert($notifSql);

            // FCM push (best-effort)
            sendFcmToUser($mycms, $userId, $title, $body, $dataPayload);

            pageRedirection("group_invite.php", 4); // 4 = promoted
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    case 'demote_member':
        try {
            $groupId = (int)($_REQUEST['group_id'] ?? 0);
            $userId  = (int)($_REQUEST['user_id'] ?? 0);

            if ($groupId <= 0 || $userId <= 0) {
                throw new Exception("group_id and user_id are required.");
            }

            // group active + private কিনা, ar admin ei group-er creator কিনা
            $gsql = [];
            $gsql['QUERY'] = "SELECT `id`, `group_name`, `created_by` FROM `rcg_groups`
                              WHERE `id`=? AND `status`='A' AND `group_type`='private'";
            $gsql['PARAM'] = [['FILD' => 'id', 'DATA' => $groupId, 'TYP' => 'i']];
            $group = $mycms->sql_select($gsql);
            if (empty($group)) {
                throw new Exception("Group not found or not a private group.");
            }
            $group = $group[0];

            // শুধু group creator (admin) e demote korte parbe
            if ((int)$group['created_by'] !== (int)$adminId) {
                throw new Exception("Only the group admin can demote moderators.");
            }

            // group owner nijeke demote korte parbe na
            if ((int)$userId === (int)$group['created_by']) {
                throw new Exception("The group owner's role cannot be changed.");
            }

            $updSql = [];
            $updSql['QUERY'] = "UPDATE `rcg_group_members` SET `role`='member'
                                WHERE group_id=? AND user_id=? AND status='A' AND role='moderator'";
            $updSql['PARAM'] = [
                ['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i'],
                ['FILD' => 'user_id',  'DATA' => $userId,  'TYP' => 'i']
            ];
            $affected = $mycms->sql_update($updSql);
            if (!$affected) {
                throw new Exception("User is not currently a moderator of this group.");
            }

            // user-ke in-app notification pathao je take demote kora hoyeche
            $title = "Moderator Role Removed";
            $body  = "You are no longer a moderator of \"" . $group['group_name'] . "\"";
            $dataPayload = ['type' => 'group_demoted', 'group_id' => $groupId];

            $notifSql = [];
            $notifSql['QUERY'] = "INSERT INTO `rcg_notifications`
                                  SET user_id=?, type='group_demoted', title=?, body=?, data=?";
            $notifSql['PARAM'] = [
                ['FILD' => 'user_id', 'DATA' => $userId, 'TYP' => 'i'],
                ['FILD' => 'title',   'DATA' => $title,   'TYP' => 's'],
                ['FILD' => 'body',    'DATA' => $body,    'TYP' => 's'],
                ['FILD' => 'data',    'DATA' => json_encode($dataPayload), 'TYP' => 's']
            ];
            $mycms->sql_insert($notifSql);

            pageRedirection("group_invite.php", 5); // 5 = demoted
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    default:
        die('Invalid action.');
}

/*============== HELPER: PAGE REDIRECTION ================*/
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