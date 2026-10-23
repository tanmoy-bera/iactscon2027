<?php

/**
 * notification-helper.php
 * ---------------------------------------------------------------------------
 * ONE include that gives any page in webmaster/ (or webmaster/section_* folders)
 * notification sending with: preference check -> save in rcg_notifications -> FCM push.
 *
 * Put these 3 files in  webmaster/includes/  (next to init.php and fcm-helper.php):
 *   notification-helper.php
 *   AdminNotificationService.php
 *   AdminNotificationPreferenceService.php
 *
 * Include it with ONE line (everything inside the helper uses __DIR__, so the path to
 * this file is the only thing that changes):
 *   page in webmaster/              include_once(__DIR__ . '/includes/notification-helper.php');
 *   page in webmaster/section_*     include_once(dirname(__DIR__) . '/includes/notification-helper.php');
 *
 * Then:
 *   adminNotify($userId, $type, $subject, $message, $options = []);
 *   adminNotifyActionable($userId, $type, $subject, $message, ['accept', 'reject'], $options = []);
 *   adminNotifyMany([$id1, $id2], $type, $subject, $message, $options = []);
 *   adminNotifier();   // the AdminNotificationPreferenceService object (isAllowed, filterAllowedUsers ...)
 *
 * $options: sender_id, reference_id, body, additional_data (array), created_by, setting_key
 *
 * Example:
 *   $r = adminNotify($userId, 'group_invite', 'Group Invitation', 'You are invited to "Cardio"', [
 *       'reference_id'    => $invitationId,
 *       'additional_data' => ['group_id' => $groupId],
 *   ]);
 *   // $r = ['allowed' => bool, 'notification_id' => int|null, 'pushed' => bool]
 *
 * The $type is matched with rcg_notification_settings.setting_key (use 'setting_key' in
 * $options to match a different one). A type with no active setting row is always sent.
 */

// $mycms (DB wrapper) - normally already created by the page's own init.php include
if (!isset($GLOBALS['mycms'])) {
    include_once __DIR__ . '/init.php';
}

// sendFcmToUser() - included here at file level, same as the pages do
if (!function_exists('sendFcmToUser') && is_file(__DIR__ . '/fcm-helper.php')) {
    include_once __DIR__ . '/fcm-helper.php';
}

require_once __DIR__ . '/AdminNotificationService.php';
require_once __DIR__ . '/AdminNotificationPreferenceService.php';

if (!function_exists('adminNotifier')) {
    /**
     * @return AdminNotificationPreferenceService shared instance (uses the global $mycms + FCM)
     */
    function adminNotifier()
    {
        static $svc = null;

        if ($svc === null) {
            $svc = new AdminNotificationPreferenceService(isset($GLOBALS['mycms']) ? $GLOBALS['mycms'] : null);
        }

        return $svc;
    }
}

if (!function_exists('adminNotify')) {
    /**
     * @return array ['allowed' => bool, 'notification_id' => int|null, 'pushed' => bool]
     */
    function adminNotify($userId, $type, $subject, $message, array $options = [])
    {
        return adminNotifier()->send($userId, $type, $subject, $message, $options);
    }
}

if (!function_exists('adminNotifyActionable')) {
    /**
     * @return array same shape as adminNotify()
     */
    function adminNotifyActionable($userId, $type, $subject, $message, array $actions, array $options = [])
    {
        return adminNotifier()->sendActionable($userId, $type, $subject, $message, $actions, $options);
    }
}

if (!function_exists('adminNotifyMany')) {
    /**
     * @return array ['requested', 'allowed', 'saved', 'pushed', 'notification_ids']
     */
    function adminNotifyMany(array $userIds, $type, $subject, $message, array $options = [])
    {
        return adminNotifier()->sendToMany($userIds, $type, $subject, $message, $options);
    }
}