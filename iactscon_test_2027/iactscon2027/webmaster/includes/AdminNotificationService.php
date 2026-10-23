<?php

/**
 * AdminNotificationService
 * ---------------------------------------------------------------------------
 * Saves a notification row in `rcg_notifications` (no preference check, no push).
 * Uses the project's own DB wrapper ($mycms), so it needs no separate DB connection
 * and works from any page in webmaster/ or any webmaster/section_* folder.
 *
 * Normally you do not use this class directly - use the helper functions in
 * includes/notification-helper.php (adminNotify / adminNotifyMany / ...), which also
 * check the user's notification preference and send the Firebase push.
 *
 * Direct use (save only):
 *   $svc = new AdminNotificationService();          // picks up the global $mycms
 *   $id  = $svc->send(101, 'quiz_reminder', 'Quiz starts soon', 'Starts in 10 minutes', ['reference_id' => $quizId]);
 *
 * $options keys: sender_id, reference_id, body, additional_data (array), created_by
 *   (created_by defaults to sender_id; null for system generated notifications)
 *
 * Every method is safe to call from anywhere: nothing throws, failures are logged
 * with error_log() and send() returns null.
 *
 * PHP 7.3 compatible.
 */
class AdminNotificationService
{
    const TABLE = 'rcg_notifications';

    /** @var object project DB wrapper ($mycms) */
    protected $db;

    /**
     * @param object|null $db the $mycms object; defaults to the global $mycms
     */
    public function __construct($db = null)
    {
        $this->db = $db ? $db : (isset($GLOBALS['mycms']) ? $GLOBALS['mycms'] : null);
    }

    /**
     * Save one notification.
     *
     * @return int|null notification id, or null on failure
     */
    public function send($userId, $type, $subject, $message, array $options = [])
    {
        try {
            if (empty($userId) || $this->db === null) {
                return null;
            }

            return $this->insertRow($this->buildRow($userId, $type, $subject, $message, $options));
        } catch (Throwable $e) {
            error_log('AdminNotificationService::send failed - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Save a notification the user can act on (is_actionable = true).
     * $actions example: ['accept', 'reject']
     *
     * @return int|null
     */
    public function sendActionable($userId, $type, $subject, $message, array $actions, array $options = [])
    {
        return $this->send($userId, $type, $subject, $message, self::actionableOptions($actions, $options));
    }

    /**
     * Save the same notification for several users.
     *
     * @return int number of rows saved
     */
    public function sendToMany(array $userIds, $type, $subject, $message, array $options = [])
    {
        return count($this->sendToManyIds($userIds, $type, $subject, $message, $options));
    }

    /**
     * Same as sendToMany() but returns [user_id => notification_id] for the rows saved.
     * (Rows are saved one by one; a failed row is logged and skipped.)
     *
     * @return array
     */
    public function sendToManyIds(array $userIds, $type, $subject, $message, array $options = [])
    {
        $saved = [];

        try {
            $userIds = array_values(array_unique(array_filter($userIds)));

            foreach ($userIds as $userId) {
                $id = $this->send($userId, $type, $subject, $message, $options);

                if ($id) {
                    $saved[(int) $userId] = $id;
                }
            }
        } catch (Throwable $e) {
            error_log('AdminNotificationService::sendToManyIds failed - ' . $e->getMessage());
        }

        return $saved;
    }

    /**
     * Adds the actionable flags to $options['additional_data'].
     * Shared with AdminNotificationPreferenceService.
     *
     * @return array the new $options
     */
    public static function actionableOptions(array $actions, array $options = [])
    {
        $additional = isset($options['additional_data']) && is_array($options['additional_data'])
            ? $options['additional_data']
            : [];

        $additional['is_actionable'] = true;
        $additional['action_status'] = isset($additional['action_status']) ? $additional['action_status'] : 'pending';
        $additional['actions']       = $actions;

        $options['additional_data'] = $additional;

        return $options;
    }

    // ------------------------------------------------------------------
    // Internals
    // ------------------------------------------------------------------

    /**
     * @return int|null
     */
    protected function insertRow(array $row)
    {
        $set = [];
        foreach (array_keys($row) as $field) {
            $set[] = '`' . $field . '`=?';
        }

        $sql          = [];
        $sql['QUERY'] = 'INSERT INTO `' . self::TABLE . '` SET ' . implode(', ', $set);
        $sql['PARAM'] = [];

        foreach ($row as $field => $value) {
            $sql['PARAM'][] = [
                'FILD' => $field,
                'DATA' => $value,
                'TYP'  => is_int($value) ? 'i' : 's', // null goes as 's' -> stored as NULL
            ];
        }

        $id = (int) $this->db->sql_insert($sql);

        return $id > 0 ? $id : null;
    }

    protected function buildRow($userId, $type, $subject, $message, array $options)
    {
        $now        = date('Y-m-d H:i:s');
        $senderId   = $this->intOrNull(isset($options['sender_id']) ? $options['sender_id'] : null);
        $createdBy  = array_key_exists('created_by', $options) ? $this->intOrNull($options['created_by']) : $senderId;
        $additional = isset($options['additional_data']) ? $options['additional_data'] : null;

        if (is_array($additional)) {
            $additional = json_encode($additional, JSON_UNESCAPED_UNICODE);
        }

        return [
            'user_id'         => (int) $userId,
            'sender_id'       => $senderId,
            'type'            => (string) $type,
            'reference_id'    => isset($options['reference_id']) ? (string) $options['reference_id'] : null,
            'subject'         => (string) $subject,
            'message'         => (string) $message,
            'body'            => isset($options['body']) ? (string) $options['body'] : null,
            'additional_data' => $additional,
            'is_read'         => 0,
            'row_status'      => 'A',
            'created_at'      => $now,
            'created_by'      => $createdBy,
            'updated_at'      => $now,
            'updated_by'      => $createdBy,
        ];
    }

    protected function intOrNull($value)
    {
        return ($value === null || $value === '' || !is_numeric($value)) ? null : (int) $value;
    }
}