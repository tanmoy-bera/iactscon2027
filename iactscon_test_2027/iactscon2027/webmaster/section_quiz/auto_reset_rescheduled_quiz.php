<?php
/**
 * auto_reset_rescheduled_quiz.php
 *
 * Cron entry point — cPanel hits this file directly on a schedule.
 * No auth key, no time window: every run just checks current state
 * and acts on whatever matches.
 *
 * TRIGGER CONDITION
 * ------------------
 * A quiz is considered "the admin went inactive / left the live quiz
 * screen mid-run, and the date got changed" when:
 *   row_status = 'A'  AND  status = 'active'  AND  start_time > NOW()
 *
 * That combination can only occur if a quiz that was already live
 * ('active') had its start_time edited to a future date/time while
 * still running — i.e. the admin stepped away and the quiz got
 * rescheduled mid-run. This is the only condition the current schema
 * can detect automatically (there is no session/presence table to
 * know the admin specifically went idle), so this script resets the
 * whole quiz attempt whenever that combination is found, using the
 * exact same steps as the existing manual "reset_quiz" action.
 *
 * WHAT IT DOES (identical to the manual reset_quiz case):
 *   1. Reset all questions for the quiz back to status = 'active'
 *   2. Delete all recorded answers for the quiz
 *   3. Delete guest users associated with the quiz
 *   4. Set quiz status back to 'active' (same as manual reset_quiz)
 *   5. Clear the live question from Firebase
 *
 * DIFFERENCE FROM THE MANUAL RESET:
 *   The manual reset leaves start_time untouched. This cron ALSO
 *   sets start_time = NULL. That's not cosmetic — it's what stops
 *   the same quiz from being reset again on every subsequent cron
 *   run: since status goes back to 'active' (matching reset_quiz),
 *   the trigger condition above would otherwise still match as long
 *   as start_time stays in the future. With start_time NULL, the
 *   condition `start_time > NOW()` can never be true again, so the
 *   quiz is left alone until someone deliberately sets a new date.
 *
 * Suggested cPanel cron line (every 5 minutes, adjust as needed):
 *  *   [asterisk]/5 * * * * curl -s "https://ruedakolkata.com/ccicon_2025/webmaster/section_quiz/auto_reset_rescheduled_quiz.php" >/dev/null 2>&1
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

function autoResetRescheduledQuizzes()
{
    global $mycms, $cfg;

    // $logLine = date('Y-m-d H:i:s') . " | Triggered | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'cli') . "\n";
    // file_put_contents(__DIR__ . '/cron_hits_reset.log', $logLine, FILE_APPEND);

    try {
        // --- Find quizzes marked 'active' but whose start_time is now in the future ---
        $sql = [];
        $sql['QUERY'] = "SELECT id, title, start_time
            FROM rcg_quizzes
            WHERE row_status = 'A'
              AND status = 'active'
              AND start_time > NOW()";

        $quizzes = $mycms->sql_select($sql);

        if (empty($quizzes)) {
            sendJson(200, 'No rescheduled in-progress quizzes found', []);
            return;
        }

        $resetResults = [];

        foreach ($quizzes as $quiz) {
            $quizId = (int)$quiz['id'];
            resetSingleQuiz($mycms, $cfg, $quizId);

            $resetResults[] = [
                'quiz_id'            => $quizId,
                'title'              => $quiz['title'],
                'previous_start_time' => $quiz['start_time'],
            ];
        }

        sendJson(200, 'Rescheduled in-progress quizzes auto-reset', $resetResults);

    } catch (Exception $e) {
        sendJson(422, 'Failed to auto-reset rescheduled quizzes: ' . $e->getMessage());
    }
}

/**
 * Performs the same reset steps as the manual "reset_quiz" action,
 * plus setting start_time = NULL (see file header note above).
 */
function resetSingleQuiz($mycms, $cfg, $quizId)
{
    $updatedBy = 0; // no logged-in user in a cron context

    // 1. Reset all questions status to 'active' for this quiz
    $resetQuestions = [];
    $resetQuestions['QUERY'] = "UPDATE `rcg_quiz_questions`
                                SET status = 'active',
                                    updated_at = NOW(),
                                    updated_by = ?
                                WHERE quiz_id = ? AND row_status != 'D'";
    $resetQuestions['PARAM'] = [
        ['FILD' => 'updated_by', 'DATA' => $updatedBy, 'TYP' => 'i'],
        ['FILD' => 'quiz_id',    'DATA' => $quizId,     'TYP' => 'i']
    ];
    $mycms->sql_update($resetQuestions);

    // 2. Delete all answers for this quiz
    $deleteAnswers = [];
    $deleteAnswers['QUERY'] = "DELETE FROM `rcg_quiz_answers` WHERE quiz_id = ?";
    $deleteAnswers['PARAM'] = [
        ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i']
    ];
    $mycms->sql_delete($deleteAnswers);

    // 3. Delete guest users associated with this quiz
    $deleteGuestUsers = [];
    $deleteGuestUsers['QUERY'] = "DELETE FROM `quiz_guest_users`
                                   WHERE latest_visit_quiz_id = ?";
    $deleteGuestUsers['PARAM'] = [
        ['FILD' => 'latest_visit_quiz_id', 'DATA' => $quizId, 'TYP' => 'i']
    ];
    $mycms->sql_delete($deleteGuestUsers);

    // 4. Set quiz status back to 'active' (same as manual reset_quiz),
    //    AND set start_time = NULL (see file header note — this is
    //    what stops the quiz from being reset again on every run)
    $updateQuiz = [];
    $updateQuiz['QUERY'] = "UPDATE `rcg_quizzes`
                            SET status = 'active',
                                start_time = NULL,
                                updated_at = NOW(),
                                updated_by = ?
                            WHERE id = ? AND row_status != 'D'";
    $updateQuiz['PARAM'] = [
        ['FILD' => 'updated_by', 'DATA' => $updatedBy, 'TYP' => 'i'],
        ['FILD' => 'id',         'DATA' => $quizId,     'TYP' => 'i']
    ];
    $mycms->sql_update($updateQuiz);

    // 5. Clear Firebase data for this quiz
    try {
        if (!class_exists('FirebaseService')) {
            require_once __DIR__ . '/includes/FirebaseService.php';
        }

        $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);
        $firebase->delete("live_quiz/$quizId/current_question");
        $firebase->delete("live_quiz/$quizId");
    } catch (Exception $e) {
        // Log but don't stop the reset — DB state is more important
        error_log("Firebase reset error for quiz {$quizId}: " . $e->getMessage());
    }
}

// Automatically route if accessed directly — no auth, GET only
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    autoResetRescheduledQuizzes();
}