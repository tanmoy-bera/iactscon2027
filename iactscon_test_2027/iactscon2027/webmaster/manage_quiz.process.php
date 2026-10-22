<?php
include_once('./includes/init.php');
include_once('./includes/fcm-helper.php');

// Default page to go back to after every action
const QUIZ_LIST_PAGE = 'manage_quiz.php';

$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];
date_default_timezone_set('Asia/Kolkata');

switch ($act) {

    /* ------------------------------------------------------------------ */
    case 'add_quiz':
        try {
            $title         = addslashes(trim($_POST['title']));
            $description   = isset($_POST['description']) ? addslashes(trim($_POST['description'])) : null;
            $type          = 'main';
            $mode          = 'manual';
            $quiz_type     = isset($_POST['quiz_type']) ? trim($_POST['quiz_type']) : 'default';
            $hall          = isset($_POST['hall']) ? (int)$_POST['hall'] : null;
            $rounds        = isset($_POST['rounds']) ? (int)$_POST['rounds'] : 1;
            $levels        = isset($_POST['levels']) ? (int)$_POST['levels'] : 1;
            $start_time    = !empty($_POST['start_time']) ? trim($_POST['start_time']) : null;
            $answer_time   = isset($_POST['answer_time']) ? (int)$_POST['answer_time'] : 20;
            $buffer_time   = isset($_POST['buffer_time']) ? (int)$_POST['buffer_time'] : 5;
            $parent_id     = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $status        = !empty($_POST['quiz_status']) ? trim($_POST['quiz_status']) : 'draft';
            $view_answer   = !empty($_POST['view_answer']) ? trim($_POST['view_answer']) : 'manually';
            $enable_qr     = (!empty($_POST['enable_qr']) && strtolower(trim($_POST['enable_qr'])) === 'yes') ? 'yes' : 'no';
            $enable_instruction_bar = 'no';
            $created_by    = $loggedUserID;
            $seat_limit             = isset($_POST['seat_limit']) ? (int)$_POST['seat_limit'] : 1;
            $application_close_time = isset($_POST['application_close_time']) ? $_POST['application_close_time'] : date('Y-m-d H:i:s');
            $application_status     = isset($_POST['application_status']) ? trim($_POST['application_status']) : 'default';
            $marks_correct          = isset($_POST['marks_correct']) ? $_POST['marks_correct'] : 0;
            $marks_incorrect        = isset($_POST['marks_incorrect']) ? $_POST['marks_incorrect'] : 0;
            $participation_type     = isset($_POST['participation_type']) ? trim($_POST['participation_type']) : 'solo';

            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_quizzes`
                            SET title=?, description=?, type=?, mode=?, rounds=?, levels=?, 
                                start_time=?, answer_time=?, buffer_time=?, parent_id=?, 
                                status=?, created_by=?, row_status=?, hall_id=?, view_answer=?, 
                                enable_qr=?, enable_instruction_bar=?, quiz_type=?, seat_limit=?, application_status=?, application_close_time=?, marks_correct=?, marks_incorrect=?, participation_type=?";

            $sql['PARAM'] = [
                ['FILD' => 'title',                     'DATA' => $title,                  'TYP' => 's'],
                ['FILD' => 'description',               'DATA' => $description,            'TYP' => 's'],
                ['FILD' => 'type',                      'DATA' => $type,                   'TYP' => 's'],
                ['FILD' => 'mode',                      'DATA' => $mode,                   'TYP' => 's'],
                ['FILD' => 'rounds',                    'DATA' => $rounds,                 'TYP' => 'i'],
                ['FILD' => 'levels',                    'DATA' => $levels,                 'TYP' => 'i'],
                ['FILD' => 'start_time',                'DATA' => $start_time,             'TYP' => 's'],
                ['FILD' => 'answer_time',               'DATA' => $answer_time,            'TYP' => 'i'],
                ['FILD' => 'buffer_time',               'DATA' => $buffer_time,            'TYP' => 'i'],
                ['FILD' => 'parent_id',                 'DATA' => $parent_id,              'TYP' => 'i'],
                ['FILD' => 'status',                    'DATA' => $status,                 'TYP' => 's'],
                ['FILD' => 'created_by',                'DATA' => $created_by,             'TYP' => 'i'],
                ['FILD' => 'row_status',                'DATA' => 'A',                     'TYP' => 's'],
                ['FILD' => 'hall_id',                   'DATA' => $hall,                   'TYP' => 'i'],
                ['FILD' => 'view_answer',               'DATA' => $view_answer,            'TYP' => 's'],
                ['FILD' => 'enable_qr',                 'DATA' => $enable_qr,              'TYP' => 's'],
                ['FILD' => 'enable_instruction_bar',    'DATA' => $enable_instruction_bar, 'TYP' => 's'],
                ['FILD' => 'quiz_type',                 'DATA' => $quiz_type,              'TYP' => 's'],
                ['FILD' => 'seat_limit',                'DATA' => $seat_limit,             'TYP' => 'i'],
                ['FILD' => 'application_status',        'DATA' => $application_status,     'TYP' => 's'],
                ['FILD' => 'application_close_time',    'DATA' => $application_close_time, 'TYP' => 's'],
                ['FILD' => 'marks_correct',             'DATA' => $marks_correct,          'TYP' => 's'],
                ['FILD' => 'marks_incorrect',           'DATA' => $marks_incorrect,        'TYP' => 's'],
                ['FILD' => 'participation_type',        'DATA' => $participation_type,     'TYP' => 's'],
            ];

            $mycms->sql_insert($sql);
            pageRedirection(QUIZ_LIST_PAGE, 1);
        } catch (Exception $e) {
            pageRedirection(QUIZ_LIST_PAGE, "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------------ */
    case 'edit_quiz':
        try {
            $id       = (int)$_POST['quiz_id'];
            $title         = addslashes(trim($_POST['title']));
            $hall          = isset($_POST['hall']) ? (int)$_POST['hall'] : null;
            $description   = isset($_POST['description']) ? addslashes(trim($_POST['description'])) : null;
            $type          = 'main';
            $mode          = 'manual';
            $quiz_type     = isset($_POST['quiz_type']) ? trim($_POST['quiz_type']) : 'default';
            $rounds        = isset($_POST['rounds']) ? (int)$_POST['rounds'] : 1;
            $levels        = isset($_POST['levels']) ? (int)$_POST['levels'] : 1;
            $start_time    = !empty($_POST['start_time']) ? trim($_POST['start_time']) : null;
            $answer_time   = isset($_POST['answer_time']) ? (int)$_POST['answer_time'] : 20;
            $buffer_time   = isset($_POST['buffer_time']) ? (int)$_POST['buffer_time'] : 5;
            $parent_id     = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $status        = !empty($_POST['quiz_status']) ? trim($_POST['quiz_status']) : 'draft';
            $view_answer   = !empty($_POST['view_answer']) ? trim($_POST['view_answer']) : 'manually';
            $enable_qr     = (!empty($_POST['enable_qr']) && strtolower(trim($_POST['enable_qr'])) === 'yes') ? 'yes' : 'no';
            $enable_instruction_bar = 'no';
            $updated_by    = $loggedUserID;
            $seat_limit             = isset($_POST['seat_limit']) ? (int)$_POST['seat_limit'] : 1;
            $application_close_time = isset($_POST['application_close_time']) ? $_POST['application_close_time'] : date('Y-m-d H:i:s');
            $application_status     = isset($_POST['application_status']) ? trim($_POST['application_status']) : 'default';
            $marks_correct          = isset($_POST['marks_correct']) ? $_POST['marks_correct'] : 0;
            $marks_incorrect        = isset($_POST['marks_incorrect']) ? $_POST['marks_incorrect'] : 0;
            $participation_type     = isset($_POST['participation_type']) ? trim($_POST['participation_type']) : 'solo';

            // Fetch current start_time BEFORE update, to detect a change
            $existingSql = [];
            $existingSql['QUERY'] = "SELECT start_time FROM `rcg_quizzes` WHERE id = ? AND row_status != 'D'";
            $existingSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $id, 'TYP' => 'i'];
            $existingData = $mycms->sql_select($existingSql);
            $existingStartTime = $existingData[0]['start_time'] ?? null;

            $normalizedExisting = $existingStartTime ? date('Y-m-d H:i:s', strtotime($existingStartTime)) : null;
            $normalizedNew      = $start_time ? date('Y-m-d H:i:s', strtotime($start_time)) : null;
            $startTimeChanged   = ($normalizedExisting !== $normalizedNew);

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_quizzes` 
                            SET title=?, description=?, type=?, mode=?, rounds=?, levels=?, 
                                start_time=?, answer_time=?, buffer_time=?, parent_id=?,
                                status=?, updated_by=?, hall_id=?, view_answer=?, 
                                enable_qr=?, enable_instruction_bar=?, quiz_type=?, seat_limit=?, application_status=?, application_close_time=?, marks_correct=?, marks_incorrect=?, participation_type=?
                         WHERE id=? AND row_status!='D'";

            $sql['PARAM'] = [
                ['FILD' => 'title',                     'DATA' => $title,                  'TYP' => 's'],
                ['FILD' => 'description',               'DATA' => $description,            'TYP' => 's'],
                ['FILD' => 'type',                      'DATA' => $type,                   'TYP' => 's'],
                ['FILD' => 'mode',                      'DATA' => $mode,                   'TYP' => 's'],
                ['FILD' => 'rounds',                    'DATA' => $rounds,                 'TYP' => 'i'],
                ['FILD' => 'levels',                    'DATA' => $levels,                 'TYP' => 'i'],
                ['FILD' => 'start_time',                'DATA' => $start_time,             'TYP' => 's'],
                ['FILD' => 'answer_time',               'DATA' => $answer_time,            'TYP' => 'i'],
                ['FILD' => 'buffer_time',               'DATA' => $buffer_time,            'TYP' => 'i'],
                ['FILD' => 'parent_id',                 'DATA' => $parent_id,              'TYP' => 'i'],
                ['FILD' => 'status',                    'DATA' => $status,                 'TYP' => 's'],
                ['FILD' => 'updated_by',                'DATA' => $updated_by,             'TYP' => 'i'],
                ['FILD' => 'hall_id',                   'DATA' => $hall,                   'TYP' => 'i'],
                ['FILD' => 'view_answer',               'DATA' => $view_answer,            'TYP' => 's'],
                ['FILD' => 'enable_qr',                 'DATA' => $enable_qr,              'TYP' => 's'],
                ['FILD' => 'enable_instruction_bar',    'DATA' => $enable_instruction_bar, 'TYP' => 's'],
                ['FILD' => 'quiz_type',                 'DATA' => $quiz_type,              'TYP' => 's'],
                ['FILD' => 'seat_limit',                'DATA' => $seat_limit,             'TYP' => 'i'],
                ['FILD' => 'application_status',        'DATA' => $application_status,     'TYP' => 's'],
                ['FILD' => 'application_close_time',    'DATA' => $application_close_time, 'TYP' => 's'],
                ['FILD' => 'marks_correct',             'DATA' => $marks_correct,          'TYP' => 's'],
                ['FILD' => 'marks_incorrect',           'DATA' => $marks_incorrect,        'TYP' => 's'],
                ['FILD' => 'participation_type',        'DATA' => $participation_type,     'TYP' => 's'],
                ['FILD' => 'id',                        'DATA' => $id,                     'TYP' => 'i'],
            ];

            $mycms->sql_update($sql);

            // If start_time changed, reset the quiz exactly like reset_quiz does
            if ($startTimeChanged) {
                // 1. Reset all questions status to 'active'
                $resetQuestions = [];
                $resetQuestions['QUERY'] = "UPDATE `rcg_quiz_questions` 
                                            SET status = 'active', 
                                                updated_at = NOW(), 
                                                updated_by = ? 
                                            WHERE quiz_id = ? AND row_status != 'D'";
                $resetQuestions['PARAM'] = [
                    ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                    ['FILD' => 'quiz_id',    'DATA' => $id,         'TYP' => 'i']
                ];
                $mycms->sql_update($resetQuestions);

                // 2. Delete all answers
                $deleteAnswers = [];
                $deleteAnswers['QUERY'] = "DELETE FROM `rcg_quiz_answers` WHERE quiz_id = ?";
                $deleteAnswers['PARAM'] = [
                    ['FILD' => 'quiz_id', 'DATA' => $id, 'TYP' => 'i']
                ];
                $mycms->sql_delete($deleteAnswers);

                // 2.5. Delete guest users associated with this quiz
                $deleteGuestUsers = [];
                $deleteGuestUsers['QUERY'] = "DELETE FROM `quiz_guest_users` 
                                               WHERE latest_visit_quiz_id = ?";
                $deleteGuestUsers['PARAM'] = [
                    ['FILD' => 'latest_visit_quiz_id', 'DATA' => $id, 'TYP' => 'i']
                ];
                $mycms->sql_delete($deleteGuestUsers);

                // 3. Update quiz status to 'active'
                $updateQuizStatus = [];
                $updateQuizStatus['QUERY'] = "UPDATE `rcg_quizzes` 
                                        SET status = 'active', 
                                            updated_at = NOW(), 
                                            updated_by = ? 
                                        WHERE id = ? AND row_status != 'D'";
                $updateQuizStatus['PARAM'] = [
                    ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                    ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
                ];
                $mycms->sql_update($updateQuizStatus);

                // 4. Clear Firebase data
                try {
                    if (!class_exists('FirebaseService')) {
                        require_once __DIR__ . '/includes/FirebaseService.php';
                    }
                    $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);
                    $firebase->delete("live_quiz/$id/current_question");
                    $firebase->delete("live_quiz/$id");
                } catch (Exception $e) {
                    error_log("Firebase reset error for quiz {$id}: " . $e->getMessage());
                }
            }

            pageRedirection(QUIZ_LIST_PAGE, 2);
        } catch (Exception $e) {
            pageRedirection(QUIZ_LIST_PAGE, "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------------ */
    case 'Remove':
        try {
            if (empty($_GET['id'])) {
                throw new Exception("Quiz ID is required.");
            }

            $id         = (int)$_GET['id'];
            $deleted_by = $loggedUserID;
            $deleted_at = date('Y-m-d H:i:s');

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_quizzes` 
                            SET row_status=?, deleted_by=?, deleted_at=? 
                         WHERE id=? AND row_status!='D'";
            $sql['PARAM'] = [
                ['FILD' => 'row_status', 'DATA' => 'D',         'TYP' => 's'],
                ['FILD' => 'deleted_by', 'DATA' => $deleted_by, 'TYP' => 'i'],
                ['FILD' => 'deleted_at', 'DATA' => $deleted_at, 'TYP' => 's'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];

            $mycms->sql_update($sql);
            pageRedirection(QUIZ_LIST_PAGE, 3);
        } catch (Exception $e) {
            pageRedirection(QUIZ_LIST_PAGE, "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------------ */
    case 'Active':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing Quiz ID to activate.");

            $quiz_id    = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_quizzes` 
                            SET row_status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ?";
            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'A',        'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $quiz_id,    'TYP' => 'i'];

            $mycms->sql_update($sql);
            pageRedirection(QUIZ_LIST_PAGE, 2);
        } catch (Exception $e) {
            pageRedirection(QUIZ_LIST_PAGE, "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------------ */
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing Quiz ID to deactivate.");

            $quiz_id    = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_quizzes` 
                            SET row_status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ?";
            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'I',        'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $quiz_id,    'TYP' => 'i'];

            $mycms->sql_update($sql);
            pageRedirection(QUIZ_LIST_PAGE, 2);
        } catch (Exception $e) {
            pageRedirection(QUIZ_LIST_PAGE, "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------------ */
    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No quizzes selected.");
            }
            if (empty($_POST['action'])) {
                throw new Exception("Please choose an action.");
            }

            $ids        = array_map('intval', $_POST['ids']);
            $action     = $_POST['action'];
            $updated_by = $loggedUserID;

            foreach ($ids as $id) {
                $sql = [];

                switch ($action) {
                    case 'Active':
                        $sql['QUERY'] = "UPDATE `rcg_quizzes` 
                                        SET row_status=?, updated_at = NOW(), updated_by = ?  
                                     WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'row_status', 'DATA' => 'A',         'TYP' => 's'],
                            ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
                        ];
                        break;

                    case 'Inactive':
                        $sql['QUERY'] = "UPDATE `rcg_quizzes` 
                                        SET row_status=?, updated_at = NOW(), updated_by = ?  
                                     WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'row_status', 'DATA' => 'I',         'TYP' => 's'],
                            ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
                        ];
                        break;

                    case 'Remove':
                        $sql['QUERY'] = "UPDATE `rcg_quizzes` 
                                        SET row_status=?, deleted_at=NOW(), deleted_by=? 
                                     WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'row_status', 'DATA' => 'D',         'TYP' => 's'],
                            ['FILD' => 'deleted_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
                        ];
                        break;

                    default:
                        throw new Exception("Invalid action selected.");
                }

                $mycms->sql_update($sql);
            }

            switch ($action) {
                case 'Active':
                case 'Inactive':
                    pageRedirection(QUIZ_LIST_PAGE, 2);
                    break;
                case 'Remove':
                    pageRedirection(QUIZ_LIST_PAGE, 3);
                    break;
            }
        } catch (Exception $e) {
            pageRedirection(QUIZ_LIST_PAGE, "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------------ */
    case 'notify_all':
        try {
            if (empty($_REQUEST['quiz_id'])) {
                throw new Exception("Quiz ID is required");
            }

            $quizId = (int)$_REQUEST['quiz_id'];

            // Get quiz details
            $quiz = [];
            $quiz['QUERY'] = "SELECT id, title 
                          FROM rcg_quizzes 
                          WHERE id = ? AND row_status = 'A'";
            $quiz['PARAM'][] = ['FILD' => 'id', 'DATA' => $quizId, 'TYP' => 'i'];

            $quizData = $mycms->sql_select($quiz);
            if (empty($quizData)) {
                throw new Exception("Quiz not found");
            }
            $quizName = $quizData[0]['title'];

            // Get all tokens
            $tokens = [];
            $tokens['QUERY'] = "SELECT token FROM rcg_user_fcm_tokens";
            $tokensData = $mycms->sql_select($tokens);
            if (empty($tokensData)) {
                throw new Exception("No users found");
            }

            // Send notifications
            foreach ($tokensData as $row) {
                $token = $row['token'];

                $message = [
                    "token" => $token,
                    "notification" => [
                        "title" => "Quiz Enrolment Open",
                        "body"  => "Enroll now for " . $quizName
                    ],
                    "data" => [
                        "type"    => "quiz_enrolment",
                        "quiz_id" => (string)$quizId
                    ]
                ];

                $response = sendFCM($message);

                // Remove invalid token
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

            pageRedirection(QUIZ_LIST_PAGE, 2);
        } catch (Exception $e) {
            pageRedirection(QUIZ_LIST_PAGE, "Error: " . $e->getMessage());
        }
        exit();

    /* ------------------------------------------------------------------ */
    case 'reset_quiz':
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        try {
            if (empty($_REQUEST['quiz_id'])) {
                throw new Exception("Quiz ID is required.");
            }

            $quiz_id    = (int)$_REQUEST['quiz_id'];
            $updated_by = $loggedUserID;

            // 1. Reset all questions status to 'active'
            $resetQuestions = [];
            $resetQuestions['QUERY'] = "UPDATE `rcg_quiz_questions` 
                                        SET status = 'active', 
                                            updated_at = NOW(), 
                                            updated_by = ? 
                                        WHERE quiz_id = ? AND row_status != 'D'";
            $resetQuestions['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'quiz_id',    'DATA' => $quiz_id,    'TYP' => 'i']
            ];
            $mycms->sql_update($resetQuestions);

            // 2. Delete all answers
            $deleteAnswers = [];
            $deleteAnswers['QUERY'] = "DELETE FROM `rcg_quiz_answers` WHERE quiz_id = ?";
            $deleteAnswers['PARAM'] = [
                ['FILD' => 'quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i']
            ];
            $mycms->sql_delete($deleteAnswers);

            // 2.5. Delete guest users associated with this quiz
            $deleteGuestUsers = [];
            $deleteGuestUsers['QUERY'] = "DELETE FROM `quiz_guest_users` 
                                           WHERE latest_visit_quiz_id = ?";
            $deleteGuestUsers['PARAM'] = [
                ['FILD' => 'latest_visit_quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i']
            ];
            $mycms->sql_delete($deleteGuestUsers);

            // 3. Update quiz status to 'active'
            $updateQuiz = [];
            $updateQuiz['QUERY'] = "UPDATE `rcg_quizzes` 
                                    SET status = 'active', 
                                        updated_at = NOW(), 
                                        updated_by = ? 
                                    WHERE id = ? AND row_status != 'D'";
            $updateQuiz['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $quiz_id,    'TYP' => 'i']
            ];
            $mycms->sql_update($updateQuiz);

            // 4. Clear Firebase data
            try {
                if (!class_exists('FirebaseService')) {
                    require_once __DIR__ . '/includes/FirebaseService.php';
                }
                $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);
                $firebase->delete("live_quiz/$quiz_id/current_question");
                $firebase->delete("live_quiz/$quiz_id");
            } catch (Exception $e) {
                error_log("Firebase reset error for quiz {$quiz_id}: " . $e->getMessage());
            }

            $message = "Quiz has been reset successfully. Questions status reset to active, all answers cleared, quiz status set to active, and live question cleared from Firebase.";

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 1, 'message' => $message]);
                exit();
            } else {
                pageRedirection(QUIZ_LIST_PAGE, 2);
            }
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 0, 'message' => $e->getMessage()]);
                exit();
            } else {
                pageRedirection(QUIZ_LIST_PAGE, "Error: " . $e->getMessage());
            }
        }
        exit();
}

/* ---------- Decide which page to go back to ----------
 * Only an explicit return_to param (whitelisted) can override the default.
 * The HTTP Referer is NOT used any more: the form lives on
 * manage_quiz_polls.php, so Referer was always sending you back there.
 */
function getReturnPage($default = QUIZ_LIST_PAGE)
{
    $allowed = ['manage_quiz.php', 'manage_quiz.php'];

    if (!empty($_REQUEST['return_to'])) {
        $page = basename((string)$_REQUEST['return_to']);
        if (in_array($page, $allowed, true)) {
            return $page;
        }
    }

    return $default;
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey      = "_pgn_";
    $pageKeyVal   = (isset($_REQUEST[$pageKey]) && $_REQUEST[$pageKey] !== "") ? (int)$_REQUEST[$pageKey] : 0;
    $searchString = "";
    $searchArray  = array();

    $searchArray['tab']       = 'quiz';
    $searchArray[$pageKey]    = $pageKeyVal;
    $searchArray['src_title'] = isset($_REQUEST['src_title']) ? urlencode(trim($_REQUEST['src_title'])) : '';

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    // Error messages are text, so URL-encode them; numeric codes stay as-is
    if (!is_numeric($messageCode)) {
        $messageCode = urlencode((string)$messageCode);
    }

    $page = getReturnPage($fileName);   // $fileName is only the default
    $mycms->redirect($page . "?m=" . $messageCode . $additionalString . $searchString);
}