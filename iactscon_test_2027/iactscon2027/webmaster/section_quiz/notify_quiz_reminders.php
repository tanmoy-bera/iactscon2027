<?php

/**
 * notify_quiz_reminders.php
 *
 * Cron entry point — cPanel hits this file directly on a schedule
 * (recommended: every 5 minutes) to send Firebase push notifications
 * for quizzes/sessions that are about to start.
 *
 * No auth check, no key — the cron just calls the file and the
 * function inside runs automatically, same pattern as
 * auto_reset_rescheduled_quiz.php.
 *
 * Reminders sent:
 *   - "3 hours before" reminder — now for PROGRAM SCHEDULE SESSIONS
 *     (rcg_program_schedule_session.session_start_time combined with
 *     the date-only part of rcg_program_schedule_date.conf_date via
 *     the session's session_date_id), NOT quizzes anymore.
 *   - "10 minutes before" reminder — unchanged, still quizzes
 *     (rcg_quizzes.start_time), exactly as before.
 *
 * Does NOT write to any table except deleting invalid/expired FCM
 * tokens (same cleanup behaviour as the existing "notify_all" action
 * in manage_quiz.process.php). It never marks a quiz/session as
 * "already notified" — instead $windowMinutes lines up with the cron
 * frequency so an item only ever falls inside a given window once.
 *
 * $windowMinutes below must match how often cPanel's cron actually
 * calls this script — change one, change the other.
 *
 * NOTE on session_start_time: rcg_program_schedule_session.session_start_time
 * is stored as varchar, not a TIME/DATETIME column. The 3h window below
 * assumes it is stored as "HH:MM:SS" (24hr, zero-padded, with seconds) so
 * that CONCAT(DATE(conf_date), ' ', session_start_time) produces a valid
 * "Y-m-d H:i:s" string. If any rows store the time as "H:MM", "HH:MM"
 * (no seconds), or with AM/PM, those rows will silently fail to match
 * the window and their reminder will never fire — worth confirming the
 * actual stored format if 3h reminders seem to be missing for some
 * sessions.
 *
 * Suggested cPanel cron line (every 5 minutes):
 *   [asterisk]/5 * * * * curl -s "https://ruedakolkata.com/ccicon_2025/webmaster/section_quiz/notify_quiz_reminders.php" >/dev/null 2>&1
 */


include_once('includes/init.php');

/**
 * Local JSON responder — this script only includes includes/init.php,
 * not the full admin request chain, so the app-wide sendResponse()
 * helper (defined elsewhere, loaded only on normal admin pages) is
 * not available here. Kept minimal since cron output is discarded.
 */
function sendJson($status, $message, $data = [])
{
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode([
        'status'  => $status,
        'message' => $message,
        'data'    => $data,
    ]);
}

function sendQuizReminders()
{
    global $mycms;

    // $logLine = date('Y-m-d H:i:s') . " | Triggered | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'cli') . "\n";
    // file_put_contents(__DIR__ . '/cron_hits.log', $logLine, FILE_APPEND);

    $windowMinutes = 1; // must match cron interval, see note above

    try {
        include_once('includes/fcm-helper.php');
        date_default_timezone_set('Asia/Kolkata');

        $results = [
            '3h_notified'  => [],
            '10m_notified' => [],
        ];

        // --- 3-hour reminder window (program schedule sessions) ---
        $sessions3h = getSessionsStartingIn($mycms, 180, $windowMinutes);
        foreach ($sessions3h as $session) {
            $sentCount = sendToAllTokens(
                $mycms,
                "Session Starting Soon",
                "\"" . $session['title'] . "\" starts in about 3 hours.",
                [
                    "type"       => "session_reminder_3h",
                    "session_id" => (string)$session['id']
                ]
            );
            $results['3h_notified'][] = [
                'session_id'      => $session['id'],
                'title'           => $session['title'],
                'session_datetime' => $session['session_datetime'],
                'sent_to'         => $sentCount,
            ];
        }

        // --- 10-minute reminder window (quizzes, unchanged) ---
        $quizzes10m = getQuizzesStartingIn($mycms, 10, $windowMinutes);
        foreach ($quizzes10m as $quiz) {
            $sentCount = sendToAllTokens(
                $mycms,
                "Quiz Reminder",
                "\"" . $quiz['title'] . "\" starts in 10 minutes. Get ready!",
                [
                    "type"    => "quiz_reminder_10m",
                    "quiz_id" => (string)$quiz['id']
                ]
            );
            $results['10m_notified'][] = [
                'quiz_id' => $quiz['id'],
                'title'   => $quiz['title'],
                'sent_to' => $sentCount,
            ];
        }

        sendJson(200, 'Quiz reminders processed', $results);
    } catch (Exception $e) {
        sendJson(422, 'Failed to send quiz reminders: ' . $e->getMessage());
    }
}

// ---------------------------------------------------------------
// Helper: send one notification to every registered FCM token,
// cleaning up dead tokens exactly like the existing notify_all case.
// ---------------------------------------------------------------
function sendToAllTokens($mycms, $title, $body, $data = [])
{
    $tokensSql = [];
    $tokensSql['QUERY'] = "SELECT token FROM rcg_user_fcm_tokens";
    $tokensData = $mycms->sql_select($tokensSql);

    if (empty($tokensData)) {
        return 0;
    }

    $sent = 0;

    foreach ($tokensData as $row) {
        $token = $row['token'];

        $message = [
            "token" => $token,
            "notification" => [
                "title" => $title,
                "body"  => $body
            ],
            "data" => $data
        ];

        $response = sendFCM($message);
        $sent++;

        // Remove invalid/unregistered tokens, same as notify_all
        if (
            strpos($response, 'UNREGISTERED') !== false ||
            strpos($response, 'INVALID_ARGUMENT') !== false
        ) {
            $del = [];
            $del['QUERY'] = "DELETE FROM rcg_user_fcm_tokens WHERE token = ?";
            $del['PARAM'][] = ['FILD' => 'token', 'DATA' => $token, 'TYP' => 's'];
            $mycms->sql_delete($del);
        }
    }

    return $sent;
}


// ---------------------------------------------------------------
// Helper: fetch quizzes whose start_time falls inside a window
// [targetOffsetMinutes, targetOffsetMinutes + windowMinutes) from now.
// e.g. targetOffsetMinutes = 180 -> quizzes starting in "about 3 hours"
// Unchanged — still used for the 10-minute quiz reminder.
// ---------------------------------------------------------------
function getQuizzesStartingIn($mycms, $targetOffsetMinutes, $windowMinutes)
{
    // Round "now" down to the start of the current minute first. Cron
    // rarely fires at exactly :00 seconds, so NOW()+N minutes usually
    // lands a few seconds past a clean minute boundary — while
    // start_time is always stored as HH:MM:00. Without rounding, that
    // few-second drift pushes start_time just outside the window on
    // every run, so the reminder never fires. Rounding first keeps the
    // window boundaries aligned to whole minutes, same as start_time.
    $nowMinute   = strtotime(date('Y-m-d H:i:00'));
    $windowStart = date('Y-m-d H:i:s', $nowMinute + $targetOffsetMinutes * 60);
    $windowEnd   = date('Y-m-d H:i:s', $nowMinute + ($targetOffsetMinutes + $windowMinutes) * 60);

    $sql = [];
    $sql['QUERY'] = "SELECT id, title, start_time
        FROM rcg_quizzes
        WHERE row_status = 'A'
          AND status IN ('active', 'draft')
          AND start_time >= ?
          AND start_time <  ?
        ORDER BY start_time ASC";

    $sql['PARAM'][] = ['FILD' => 'from_offset', 'DATA' => $windowStart, 'TYP' => 's'];
    $sql['PARAM'][] = ['FILD' => 'to_offset',   'DATA' => $windowEnd,   'TYP' => 's'];

    return $mycms->sql_select($sql);
}

// ---------------------------------------------------------------
// Helper: fetch program schedule sessions whose start moment falls
// inside a window [targetOffsetMinutes, targetOffsetMinutes + windowMinutes)
// from now. The session's start moment is built from two tables:
//   - rcg_program_schedule_session.session_start_time -> the TIME part
//   - rcg_program_schedule_date.conf_date (joined via session_date_id)
//     -> only the DATE part is used, via MySQL DATE()
// e.g. targetOffsetMinutes = 180 -> sessions starting in "about 3 hours"
// ---------------------------------------------------------------
function getSessionsStartingIn($mycms, $targetOffsetMinutes, $windowMinutes)
{
    // Same minute-rounding rationale as getQuizzesStartingIn() above,
    // so the window boundaries line up cleanly with a HH:MM:00
    // session_start_time.
    $nowMinute   = strtotime(date('Y-m-d H:i:00'));
    $windowStart = date('Y-m-d H:i:s', $nowMinute + $targetOffsetMinutes * 60);
    $windowEnd   = date('Y-m-d H:i:s', $nowMinute + ($targetOffsetMinutes + $windowMinutes) * 60);

    $sql = [];
    $sql['QUERY'] = "SELECT
            s.id,
            s.session_title AS title,
            CONCAT(DATE(d.conf_date), ' ', s.session_start_time) AS session_datetime
        FROM rcg_program_schedule_session s
        INNER JOIN rcg_program_schedule_date d ON d.id = s.session_date_id
        WHERE s.status = 'A'
          AND d.status = 'A'
        HAVING session_datetime >= ?
           AND session_datetime <  ?
        ORDER BY session_datetime ASC";

    $sql['PARAM'][] = ['FILD' => 'from_offset', 'DATA' => $windowStart, 'TYP' => 's'];
    $sql['PARAM'][] = ['FILD' => 'to_offset',   'DATA' => $windowEnd,   'TYP' => 's'];

    return $mycms->sql_select($sql);
}

// Automatically route if accessed directly — no auth, GET only
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    sendQuizReminders();
}
