<?php

require_once __DIR__ . '/AdminNotificationService.php';

/**
 * AdminNotificationPreferenceService
 * ---------------------------------------------------------------------------
 * Check the user's notification preference first. Only if the notification is
 * allowed: (1) save it in rcg_notifications, then (2) push it with Firebase (FCM).
 * The DB save always happens before the push; a failed push never undoes the save.
 *
 * Uses the project's DB wrapper ($mycms) and the project's FCM helper
 * (includes/fcm-helper.php -> sendFcmToUser), so it works from any page in
 * webmaster/ or any webmaster/section_* folder.
 *
 * Easiest way to use it is through includes/notification-helper.php:
 *   adminNotify(101, 'quiz_reminder', 'Quiz starts soon', 'Starts in 10 minutes', ['reference_id' => $quizId]);
 *   adminNotifyActionable(101, 'connection_request', 'New request', 'John sent a request', ['accept', 'reject']);
 *   adminNotifyMany([101, 102, 103], 'announcement', 'Notice', 'Venue changed');
 *
 * The notification $type is matched with rcg_notification_settings.setting_key.
 * Pass $options['setting_key'] when a type should use a different setting.
 *
 * Preference rules (per user):
 *   saved row (row_status A)  -> its is_enabled
 *   no saved row              -> setting.default_value
 *   no active setting at all  -> ALLOW_UNKNOWN (true: the type is not gated, it is sent)
 *   any error while checking  -> not sent (logged)
 *
 * $pusher (2nd constructor argument):
 *   null  -> default: push with sendFcmToUser() from fcm-helper.php
 *   false -> never push (save only)
 *   callable function ($userId, array $payload) {...; return true;}  or an object with
 *   pushNotification($userId, array $payload) -> custom pusher
 *
 * Every method is safe to call from anywhere: nothing throws, failures are logged.
 *
 * PHP 7.3 compatible.
 */
class AdminNotificationPreferenceService
{
    const SETTINGS_TABLE = 'rcg_notification_settings';
    const SAVED_TABLE    = 'rcg_user_saved_notification_settings';
    const ALLOW_UNKNOWN  = true;
    const IN_CHUNK       = 500;

    /** @var object project DB wrapper ($mycms) */
    protected $db;

    /** @var AdminNotificationService */
    protected $notifications;

    /** @var callable|object|false|null */
    protected $pusher;

    /**
     * @param object|null                   $db            the $mycms object; defaults to the global $mycms
     * @param callable|object|false|null    $pusher        see class comment
     * @param AdminNotificationService|null $notifications
     */
    public function __construct($db = null, $pusher = null, AdminNotificationService $notifications = null)
    {
        $this->db            = $db ? $db : (isset($GLOBALS['mycms']) ? $GLOBALS['mycms'] : null);
        $this->pusher        = $pusher;
        $this->notifications = $notifications ? $notifications : new AdminNotificationService($this->db);
    }

    /**
     * Has this user allowed this notification type?
     *
     * @return bool
     */
    public function isAllowed($userId, $settingKey)
    {
        try {
            $setting = $this->findActiveSetting($settingKey);

            if (empty($setting)) {
                return self::ALLOW_UNKNOWN;
            }

            $q          = [];
            $q['QUERY'] = 'SELECT `is_enabled` FROM `' . self::SAVED_TABLE . "`
                           WHERE `user_id` = ? AND `setting_id` = ? AND `row_status` = 'A'
                           LIMIT 1";
            $q['PARAM'] = [
                ['FILD' => 'user_id',    'DATA' => (int) $userId,                'TYP' => 'i'],
                ['FILD' => 'setting_id', 'DATA' => (int) $setting['setting_id'], 'TYP' => 'i'],
            ];
            $saved = $this->db->sql_select($q);

            if (!empty($saved)) {
                return (int) $saved[0]['is_enabled'] === 1;
            }

            return (int) $setting['default_value'] === 1;
        } catch (Throwable $e) {
            error_log('AdminNotificationPreferenceService::isAllowed failed - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Same check for many users.
     *
     * @return int[] the user ids that allow this notification
     */
    public function filterAllowedUsers(array $userIds, $settingKey)
    {
        try {
            $userIds = array_values(array_unique(array_map('intval', array_filter($userIds))));

            if (empty($userIds)) {
                return [];
            }

            $setting = $this->findActiveSetting($settingKey);

            if (empty($setting)) {
                return self::ALLOW_UNKNOWN ? $userIds : [];
            }

            $saved = [];

            foreach (array_chunk($userIds, self::IN_CHUNK) as $chunk) {
                $marks = implode(',', array_fill(0, count($chunk), '?'));

                $q          = [];
                $q['QUERY'] = 'SELECT `user_id`, `is_enabled` FROM `' . self::SAVED_TABLE . "`
                               WHERE `setting_id` = ? AND `row_status` = 'A' AND `user_id` IN ({$marks})";
                $q['PARAM'] = [['FILD' => 'setting_id', 'DATA' => (int) $setting['setting_id'], 'TYP' => 'i']];

                foreach ($chunk as $i => $id) {
                    $q['PARAM'][] = ['FILD' => 'u' . $i, 'DATA' => (int) $id, 'TYP' => 'i'];
                }

                $rows = $this->db->sql_select($q);

                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $saved[(int) $row['user_id']] = (int) $row['is_enabled'];
                    }
                }
            }

            $default = (int) $setting['default_value'] === 1;
            $allowed = [];

            foreach ($userIds as $userId) {
                $enabled = array_key_exists($userId, $saved) ? $saved[$userId] === 1 : $default;

                if ($enabled) {
                    $allowed[] = $userId;
                }
            }

            return $allowed;
        } catch (Throwable $e) {
            error_log('AdminNotificationPreferenceService::filterAllowedUsers failed - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check preference, save, push.
     *
     * @return array ['allowed' => bool, 'notification_id' => int|null, 'pushed' => bool]
     */
    public function send($userId, $type, $subject, $message, array $options = [])
    {
        $result = ['allowed' => false, 'notification_id' => null, 'pushed' => false];

        try {
            if (empty($userId)) {
                return $result;
            }

            $settingKey = !empty($options['setting_key']) ? $options['setting_key'] : $type;

            if (!$this->isAllowed($userId, $settingKey)) {
                return $result;
            }

            $result['allowed'] = true;

            $notificationId = $this->notifications->send($userId, $type, $subject, $message, $options);

            if (empty($notificationId)) {
                return $result;
            }

            $result['notification_id'] = $notificationId;
            $result['pushed']          = $this->pushToFirebase(
                $userId,
                $this->buildPayload($notificationId, $userId, $type, $subject, $message, $options)
            );

            return $result;
        } catch (Throwable $e) {
            error_log('AdminNotificationPreferenceService::send failed - ' . $e->getMessage());
            return $result;
        }
    }

    /**
     * Actionable notification (is_actionable = true), e.g. ['accept', 'reject'].
     *
     * @return array same shape as send()
     */
    public function sendActionable($userId, $type, $subject, $message, array $actions, array $options = [])
    {
        return $this->send($userId, $type, $subject, $message, AdminNotificationService::actionableOptions($actions, $options));
    }

    /**
     * Same notification for several users; users who turned it off are skipped.
     *
     * @return array ['requested' => int, 'allowed' => int, 'saved' => int, 'pushed' => int,
     *                'notification_ids' => [user_id => notification_id]]
     */
    public function sendToMany(array $userIds, $type, $subject, $message, array $options = [])
    {
        $summary = ['requested' => 0, 'allowed' => 0, 'saved' => 0, 'pushed' => 0, 'notification_ids' => []];

        try {
            $userIds = array_values(array_unique(array_filter($userIds)));

            $summary['requested'] = count($userIds);

            if (empty($userIds)) {
                return $summary;
            }

            $settingKey = !empty($options['setting_key']) ? $options['setting_key'] : $type;
            $allowed    = $this->filterAllowedUsers($userIds, $settingKey);

            $summary['allowed'] = count($allowed);

            foreach ($allowed as $userId) {
                $notificationId = $this->notifications->send($userId, $type, $subject, $message, $options);

                if (empty($notificationId)) {
                    continue;
                }

                $summary['saved']++;
                $summary['notification_ids'][$userId] = $notificationId;

                if ($this->pushToFirebase($userId, $this->buildPayload($notificationId, $userId, $type, $subject, $message, $options))) {
                    $summary['pushed']++;
                }
            }

            return $summary;
        } catch (Throwable $e) {
            error_log('AdminNotificationPreferenceService::sendToMany failed - ' . $e->getMessage());
            return $summary;
        }
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    protected function findActiveSetting($settingKey)
    {
        $q          = [];
        $q['QUERY'] = 'SELECT `setting_id`, `default_value` FROM `' . self::SETTINGS_TABLE . "`
                       WHERE `setting_key` = ? AND `row_status` = 'A'
                       LIMIT 1";
        $q['PARAM'] = [['FILD' => 'setting_key', 'DATA' => (string) $settingKey, 'TYP' => 's']];

        $rows = $this->db->sql_select($q);

        return !empty($rows) ? $rows[0] : null;
    }

    protected function buildPayload($notificationId, $userId, $type, $subject, $message, array $options)
    {
        $additional = isset($options['additional_data']) && is_array($options['additional_data'])
            ? $options['additional_data']
            : [];

        return [
            'notification_id' => (int) $notificationId,
            'user_id'         => (int) $userId,
            'sender_id'       => isset($options['sender_id']) ? $options['sender_id'] : null,
            'type'            => $type,
            'reference_id'    => isset($options['reference_id']) ? $options['reference_id'] : null,
            'subject'         => $subject,
            'message'         => $message,
            'body'            => isset($options['body']) ? $options['body'] : null,
            'additional_data' => $additional,
            'is_actionable'   => isset($additional['is_actionable']) ? $additional['is_actionable'] : false,
            'action_status'   => isset($additional['action_status']) ? $additional['action_status'] : null,
            'actions'         => isset($additional['actions']) ? $additional['actions'] : [],
            'is_read'         => false,
            'created_at'      => date('d-m-Y H:i:s'),
        ];
    }

    /**
     * @return bool true when the push went through
     */
    protected function pushToFirebase($userId, array $payload)
    {
        try {
            if ($this->pusher === false) {
                return false;
            }

            if ($this->pusher === null) {
                return $this->pushWithFcmHelper($userId, $payload);
            }

            if (is_object($this->pusher) && method_exists($this->pusher, 'pushNotification')) {
                return (bool) $this->pusher->pushNotification((int) $userId, $payload);
            }

            if (is_callable($this->pusher)) {
                return (bool) call_user_func($this->pusher, (int) $userId, $payload);
            }

            return false;
        } catch (Throwable $e) {
            error_log('AdminNotificationPreferenceService::pushToFirebase failed - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Default pusher: the project's sendFcmToUser() (includes/fcm-helper.php).
     * FCM data values are sent as flat strings.
     */
    protected function pushWithFcmHelper($userId, array $payload)
    {
        if (!function_exists('sendFcmToUser')) {
            error_log('AdminNotificationPreferenceService: sendFcmToUser() not loaded - include includes/notification-helper.php or includes/fcm-helper.php');
            return false;
        }

        $data = [
            'type'            => (string) $payload['type'],
            'notification_id' => (string) $payload['notification_id'],
            'is_actionable'   => !empty($payload['is_actionable']) ? '1' : '0',
        ];

        foreach (['reference_id', 'sender_id', 'action_status'] as $key) {
            if (isset($payload[$key]) && $payload[$key] !== '') {
                $data[$key] = (string) $payload[$key];
            }
        }

        if (!empty($payload['actions'])) {
            $data['actions'] = json_encode($payload['actions']);
        }

        // sendFcmToUser's return value is not relied on: only an explicit false counts as a failed push
        $sent = sendFcmToUser($this->db, (int) $userId, (string) $payload['subject'], (string) $payload['message'], $data);

        return $sent !== false;
    }
}