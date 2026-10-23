<?php
include_once('./includes/init.php');
include_once(__DIR__ . '/includes/notification-helper.php'); // preference check + save + FCM push (loads fcm-helper itself)
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
                throw new Exception("Group not found or not available for invitations.");
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

            // Notification: preference check -> save in rcg_notifications -> FCM push.
            // The invitation above is always created; only the notification follows the user's saved preference.
            $title  = "Group Invitation";
            $body   = "You have been invited to join \"" . $group['group_name'] . "\"";
            // Actionable notification: the app shows Accept / Decline buttons on it.
            // Saved in additional_data as {"group_id":..,"invitation_id":..,"is_actionable":true,"action_status":"pending","actions":["accept","decline"]}
            $notify = adminNotifyActionable($userId, 'group_invite', $title, $body, ['accept', 'decline'], [
                'reference_id'    => $invitationId,
                'created_by'      => $adminId,
                'additional_data' => [
                    'group_id'      => $groupId,
                    'invitation_id' => $invitationId,
                    'is_actionable' => true,
                    'action_status' => 'pending'
                ],
                // 'setting_key'  => 'group_invitations', // only if the setting_key differs from the type 'group_invite'
            ]);

            // 1 = invite sent, 3 = invite created but the user has turned this notification off (no alert sent)
            if (!empty($notify['allowed'])) {
                setAlert('success', 'Invitation sent successfully.');
            } else {
                setAlert('success', 'Invitation created. This user has turned off this notification, so no alert was sent.');
            }
            pageRedirection("group_invite.php", !empty($notify['allowed']) ? 1 : 3);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_invite.php", 0);
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

            // The invitation is cancelled, so remove its notification (soft delete: row_status = 'D').
            // A failure here is logged and must not break the cancel itself.
            try {
                $nsql = [];
                $nsql['QUERY'] = "UPDATE `rcg_notifications`
                              SET `row_status`='D', `updated_at`=NOW(), `updated_by`=?
                              WHERE `type`='group_invite' AND `reference_id`=? AND `row_status`='A'";
                $nsql['PARAM'] = [
                    ['FILD' => 'updated_by',   'DATA' => $adminId,             'TYP' => 'i'],
                    ['FILD' => 'reference_id', 'DATA' => (string)$invitationId, 'TYP' => 's'],
                ];
                $mycms->sql_update($nsql);
            } catch (Throwable $e) {
                error_log('cancel_invite: notification row_status update failed - ' . $e->getMessage());
            }

            setAlert('success', 'Invitation cancelled.');
            pageRedirection("group_invite.php", 2); // 2 = cancelled
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("group_invite.php", 0);
        }
        exit();

    default:
        die('Invalid action.');
}

/*============== HELPER: FLASH ALERT (shown once on group_invite.php) ================*/
function setAlert($type, $message)
{
    $_SESSION['invite_alert'] = [
        'type'    => ($type === 'error') ? 'error' : 'success',
        'message' => $message
    ];
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