<?php
include_once('./includes/init.php');
include_once('./includes/fcm-helper.php');

$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];
date_default_timezone_set('Asia/Kolkata');

/*
 * TABLES
 *  rcg_cardiac_talk_quizzes               : quiz master (group_id, join_type, ...)
 *  rcg_cardiac_talk_quiz_questions        : questions
 *  rcg_cardiac_talk_quiz_question_options : question options
 *  rcg_cardiac_talk_quiz_answers          : answers
 *  rcg_cardiac_talk_quiz_participation    : applications (Applied / Approved / Rejected)
 */
switch ($act) {
    case 'add_quiz':
        try {
            // Sanitize
            $group_id      = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
            if ($group_id <= 0) {
                throw new Exception("Please select a topic.");
            }
            $title         = addslashes(trim($_POST['title']));
            $description   = isset($_POST['description']) ? addslashes(trim($_POST['description'])) : null;
            $type          = 'main'; // trim($_POST['type']);
            $mode          = 'manual'; // trim($_POST['mode']);
            $hall          = isset($_POST['hall']) ? (int)$_POST['hall'] : null;
            $start_time    = !empty($_POST['start_time']) ? trim($_POST['start_time']) : null;
            $parent_id     = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $status        = !empty($_POST['quiz_status']) ? trim($_POST['quiz_status']) : 'draft';
            $view_answer   = !empty($_POST['view_answer']) ? trim($_POST['view_answer']) : 'manually';
            $enable_qr     = (!empty($_POST['enable_qr']) && strtolower(trim($_POST['enable_qr'])) === 'yes') ? 'yes' : 'no';
            $enable_instruction_bar = 'no'; // !empty($_POST['enable_instruction_bar']) ? trim($_POST['enable_instruction_bar']) : 'no';
            $created_by             = $loggedUserID; //(int)$_SESSION['user_id'];  // Assuming session is active
            $seat_limit             = isset($_POST['seat_limit']) ? (int)$_POST['seat_limit'] : 1;
            $application_close_time = isset($_POST['application_close_time']) ? $_POST['application_close_time'] : date('Y-m-d H:i:s');
            $application_status     = isset($_POST['application_status']) ? trim($_POST['application_status']) : 'active';
            $marks_correct          = isset($_POST['marks_correct']) ? $_POST['marks_correct'] : 0;
            $marks_incorrect        = isset($_POST['marks_incorrect']) ? $_POST['marks_incorrect'] : 0;
            $join_type              = (isset($_POST['join_type']) && trim($_POST['join_type']) === 'approval') ? 'approval' : 'open';
            if ($join_type === 'open') { // open to all: application window does not apply
                $application_status     = 'active';
                $application_close_time = date('Y-m-d H:i:s');
            }

            // Build query
            $sql = [];
            $sql['QUERY'] = "INSERT INTO  `rcg_cardiac_talk_quizzes`
                            SET group_id=?, title=?, description=?, type=?, mode=?, 
                                start_time=?, parent_id=?, 
                                status=?, created_by=?, row_status=?, hall_id=?, view_answer=?, 
                                enable_qr=?, enable_instruction_bar=?, seat_limit=?, application_status=?, application_close_time=?, marks_correct=?, marks_incorrect=?, join_type=?";

            $sql['PARAM'] = [
                ['FILD' => 'group_id',     'DATA' => $group_id,     'TYP' => 'i'],
                ['FILD' => 'title',        'DATA' => $title,        'TYP' => 's'],
                ['FILD' => 'description',  'DATA' => $description,  'TYP' => 's'],
                ['FILD' => 'type',         'DATA' => $type,         'TYP' => 's'],
                ['FILD' => 'mode',         'DATA' => $mode,         'TYP' => 's'],
                ['FILD' => 'start_time',   'DATA' => $start_time,   'TYP' => 's'],
                ['FILD' => 'parent_id',    'DATA' => $parent_id,    'TYP' => 'i'],
                ['FILD' => 'status',       'DATA' => $status,       'TYP' => 's'],
                ['FILD' => 'created_by',   'DATA' => $created_by,   'TYP' => 'i'],
                ['FILD' => 'row_status',   'DATA' => 'A',           'TYP' => 's'],
                ['FILD' => 'hall_id',      'DATA' => $hall,         'TYP' => 'i'],
                ['FILD' => 'view_answer',  'DATA' => $view_answer,  'TYP' => 's'],
                ['FILD' => 'enable_qr',    'DATA' => $enable_qr,    'TYP' => 's'],
                ['FILD' => 'enable_instruction_bar',    'DATA' => $enable_instruction_bar,  'TYP' => 's'],
                ['FILD' => 'seat_limit',                'DATA' => $seat_limit,              'TYP' => 'i'],
                ['FILD' => 'application_status',        'DATA' => $application_status,      'TYP' => 's'],
                ['FILD' => 'application_close_time',    'DATA' => $application_close_time,  'TYP' => 's'],
                ['FILD' => 'marks_correct',             'DATA' => $marks_correct,           'TYP' => 's'],
                ['FILD' => 'marks_incorrect',           'DATA' => $marks_incorrect,         'TYP' => 's'],
                ['FILD' => 'join_type',                 'DATA' => $join_type,               'TYP' => 's'],
            ];

            $mycms->sql_insert($sql);

            setAlert('success', 'Quiz added successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 1);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0, "&mode=add");
        }
        exit();

    case 'edit_quiz':
        $id = (int)($_POST['quiz_id'] ?? 0);
        try {
            // Sanitize
            $group_id      = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
            if ($group_id <= 0) {
                throw new Exception("Please select a topic.");
            }
            $title         = addslashes(trim($_POST['title']));
            $hall          = isset($_POST['hall']) ? (int)$_POST['hall'] : null;
            $description   = isset($_POST['description']) ? addslashes(trim($_POST['description'])) : null;
            $type          = 'main'; //trim($_POST['type']);
            $mode          = 'manual'; //trim($_POST['mode']);
            $start_time    = !empty($_POST['start_time']) ? trim($_POST['start_time']) : null;
            $parent_id     = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $status        = !empty($_POST['quiz_status']) ? trim($_POST['quiz_status']) : 'draft';
            $view_answer   = !empty($_POST['view_answer']) ? trim($_POST['view_answer']) : 'manually';
            $enable_qr     = (!empty($_POST['enable_qr']) && strtolower(trim($_POST['enable_qr'])) === 'yes') ? 'yes' : 'no';
            $enable_instruction_bar = 'no'; // !empty($_POST['enable_instruction_bar']) ? trim($_POST['enable_instruction_bar']) : 'no';
            $updated_by    = $loggedUserID; //(int)$_SESSION['user_id'];  // Assuming session
            $seat_limit             = isset($_POST['seat_limit']) ? (int)$_POST['seat_limit'] : 1;
            $application_close_time = isset($_POST['application_close_time']) ? $_POST['application_close_time'] : date('Y-m-d H:i:s');
            $application_status     = isset($_POST['application_status']) ? trim($_POST['application_status']) : 'active';
            $marks_correct          = isset($_POST['marks_correct']) ? $_POST['marks_correct'] : 0;
            $marks_incorrect        = isset($_POST['marks_incorrect']) ? $_POST['marks_incorrect'] : 0;
            $join_type              = (isset($_POST['join_type']) && trim($_POST['join_type']) === 'approval') ? 'approval' : 'open';
            if ($join_type === 'open') { // open to all: application window does not apply
                $application_status     = 'active';
                $application_close_time = date('Y-m-d H:i:s');
            }

            if ($id <= 0) {
                throw new Exception("Quiz ID is required.");
            }

            // --- Fetch current start_time BEFORE update, to detect a change ---
            $existingSql = [];
            $existingSql['QUERY'] = "SELECT start_time FROM `rcg_cardiac_talk_quizzes` WHERE id = ? AND row_status != 'D'";
            $existingSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $id, 'TYP' => 'i'];
            $existingData = $mycms->sql_select($existingSql);
            $existingStartTime = $existingData[0]['start_time'] ?? null;

            // Normalize both values for a reliable comparison (avoid false positives from formatting differences)
            $normalizedExisting = $existingStartTime ? date('Y-m-d H:i', strtotime($existingStartTime)) : null;
            $normalizedNew      = $start_time ? date('Y-m-d H:i', strtotime($start_time)) : null;
            $startTimeChanged   = ($normalizedExisting !== $normalizedNew);

            // Build query
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_cardiac_talk_quizzes` 
                            SET group_id=?, title=?, description=?, type=?, mode=?, 
                                start_time=?, parent_id=?,
                                status=?, updated_by=?, hall_id=?, view_answer=?, 
                                enable_qr=?, enable_instruction_bar=?, seat_limit=?, application_status=?, application_close_time=?, marks_correct=?, marks_incorrect=?, join_type=?
                         WHERE id=? AND row_status!='D'";

            $sql['PARAM'] = [
                ['FILD' => 'group_id',     'DATA' => $group_id,     'TYP' => 'i'],
                ['FILD' => 'title',        'DATA' => $title,        'TYP' => 's'],
                ['FILD' => 'description',  'DATA' => $description,  'TYP' => 's'],
                ['FILD' => 'type',         'DATA' => $type,         'TYP' => 's'],
                ['FILD' => 'mode',         'DATA' => $mode,         'TYP' => 's'],
                ['FILD' => 'start_time',   'DATA' => $start_time,   'TYP' => 's'],
                ['FILD' => 'parent_id',    'DATA' => $parent_id,    'TYP' => 'i'],
                ['FILD' => 'status',       'DATA' => $status,       'TYP' => 's'],
                ['FILD' => 'updated_by',   'DATA' => $updated_by,   'TYP' => 'i'],
                ['FILD' => 'hall_id',      'DATA' => $hall,         'TYP' => 'i'],
                ['FILD' => 'view_answer',  'DATA' => $view_answer,  'TYP' => 's'],
                ['FILD' => 'enable_qr',    'DATA' => $enable_qr,    'TYP' => 's'],
                ['FILD' => 'enable_instruction_bar',    'DATA' => $enable_instruction_bar,  'TYP' => 's'],
                ['FILD' => 'seat_limit',                'DATA' => $seat_limit,              'TYP' => 'i'],
                ['FILD' => 'application_status',        'DATA' => $application_status,      'TYP' => 's'],
                ['FILD' => 'application_close_time',    'DATA' => $application_close_time,  'TYP' => 's'],
                ['FILD' => 'marks_correct',             'DATA' => $marks_correct,           'TYP' => 's'],
                ['FILD' => 'marks_incorrect',           'DATA' => $marks_incorrect,         'TYP' => 's'],
                ['FILD' => 'join_type',                 'DATA' => $join_type,               'TYP' => 's'],
                ['FILD' => 'id',                        'DATA' => $id,                      'TYP' => 'i'],
            ];

            $mycms->sql_update($sql);

            // --- If start_time changed, reset the quiz exactly like reset_quiz does ---
            if ($startTimeChanged) {
                // 1. Reset all questions status to 'active' for this quiz
                $resetQuestions = [];
                $resetQuestions['QUERY'] = "UPDATE `rcg_cardiac_talk_quiz_questions` 
                                            SET status = 'active', 
                                                updated_at = NOW(), 
                                                updated_by = ? 
                                            WHERE quiz_id = ? AND row_status != 'D'";
                $resetQuestions['PARAM'] = [
                    ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                    ['FILD' => 'quiz_id', 'DATA' => $id, 'TYP' => 'i']
                ];
                $mycms->sql_update($resetQuestions);

                // 2. Delete all answers for this quiz
                $deleteAnswers = [];
                $deleteAnswers['QUERY'] = "DELETE FROM `rcg_cardiac_talk_quiz_answers` WHERE quiz_id = ?";
                $deleteAnswers['PARAM'] = [
                    ['FILD' => 'quiz_id', 'DATA' => $id, 'TYP' => 'i']
                ];
                $mycms->sql_delete($deleteAnswers);

                // 3. Update quiz status to 'active'
                $updateQuizStatus = [];
                $updateQuizStatus['QUERY'] = "UPDATE `rcg_cardiac_talk_quizzes` 
                                        SET status = 'active', 
                                            updated_at = NOW(), 
                                            updated_by = ? 
                                        WHERE id = ? AND row_status != 'D'";
                $updateQuizStatus['PARAM'] = [
                    ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                    ['FILD' => 'id', 'DATA' => $id, 'TYP' => 'i']
                ];
                $mycms->sql_update($updateQuizStatus);

                // 4. Clear Firebase data for this quiz
                try {
                    if (!class_exists('FirebaseService')) {
                        require_once __DIR__ . '/includes/FirebaseService.php';
                    }

                    $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);

                    $firebase->delete("live_quiz/$id/current_question");
                    $firebase->delete("live_quiz/$id");
                } catch (Exception $e) {
                    error_log("Firebase reset error for quiz {$id}: " . $e->getMessage());
                    // Continue - database changes are more important
                }
            }

            setAlert('success', 'Quiz updated successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 2);  // success message code 2 = updated
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            $back = ($id > 0) ? "&edit_quiz=" . $id : "";
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0, $back);
        }
        exit();

    case 'Remove':
        try {
            // Validation
            if (empty($_GET['id'])) {
                throw new Exception("Quiz ID is required.");
            }

            // Sanitize
            $id          = (int)$_GET['id'];
            $deleted_by  = $loggedUserID; //(int)$_SESSION['user_id'];  // Assuming session is available
            $deleted_at  = date('Y-m-d H:i:s');

            // Build query
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_cardiac_talk_quizzes` 
                            SET row_status=?, deleted_by=?, deleted_at=? 
                         WHERE id=? AND row_status!='D'";

            $sql['PARAM'] = [
                ['FILD' => 'row_status', 'DATA' => 'D',         'TYP' => 's'],
                ['FILD' => 'deleted_by', 'DATA' => $deleted_by, 'TYP' => 'i'],
                ['FILD' => 'deleted_at', 'DATA' => $deleted_at, 'TYP' => 's'],
                ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i']
            ];

            $mycms->sql_update($sql);
            setAlert('success', 'Quiz deleted successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 3);  // message code 3 = deleted
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
        }
        exit();

    case 'Active':
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing Quiz ID.");

            $quiz_id    = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID; //(int)$_SESSION['user_id'];
            $newStatus  = ($act === 'Active') ? 'A' : 'I';

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_cardiac_talk_quizzes` 
                            SET row_status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ? AND row_status != 'D'";

            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => $newStatus,  'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $quiz_id,    'TYP' => 'i'];

            $mycms->sql_update($sql);
            setAlert('success', 'Quiz marked as ' . (($act === 'Active') ? 'active' : 'inactive') . ' successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 2);  // message code 2 = success
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
        }
        exit();

    case 'MultiAction':
        try {
            // Validation
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No quizzes selected.");
            }
            if (empty($_POST['action'])) {
                throw new Exception("Please choose an action.");
            }

            // Sanitize
            $ids         = array_map('intval', $_POST['ids']);
            $action      = $_POST['action'];
            $updated_by  = $loggedUserID; //(int)$_SESSION['user_id'];
            $updated_at  = date('Y-m-d H:i:s');

            $count = 0;
            foreach ($ids as $id) {
                if ($id <= 0) {
                    continue;
                }
                $sql = [];

                switch ($action) {
                    case 'Active':
                        $sql['QUERY'] = "UPDATE `rcg_cardiac_talk_quizzes` 
                                        SET row_status=?, updated_at = NOW(), updated_by = ?  
                                     WHERE id=? AND row_status != 'D'";
                        $sql['PARAM'] = [
                            ['FILD' => 'row_status', 'DATA' => 'A', 'TYP' => 's'],
                            ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',     'DATA' => $id,      'TYP' => 'i']
                        ];
                        break;

                    case 'Inactive':
                        $sql['QUERY'] = "UPDATE `rcg_cardiac_talk_quizzes` 
                                        SET row_status=?, updated_at = NOW(), updated_by = ?  
                                     WHERE id=? AND row_status != 'D'";
                        $sql['PARAM'] = [
                            ['FILD' => 'row_status', 'DATA' => 'I', 'TYP' => 's'],
                            ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',     'DATA' => $id,        'TYP' => 'i']
                        ];
                        break;

                    case 'Remove':
                        $sql['QUERY'] = "UPDATE `rcg_cardiac_talk_quizzes` 
                                        SET row_status=?, deleted_at=NOW(), deleted_by=? 
                                     WHERE id=? ";
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
                $count++;
            }

            $verb = ['Active' => 'marked as active', 'Inactive' => 'marked as inactive', 'Remove' => 'removed'];
            setAlert('success', $count . ' quiz(zes) ' . $verb[$action] . ' successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", ($action === 'Remove') ? 3 : 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
        }
        exit();

    case 'approve_applicants':
    case 'reject_applicants':
        $quizId = (int)($_POST['quiz_id'] ?? 0);
        try {
            if ($quizId <= 0) {
                throw new Exception("Quiz ID is required.");
            }
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No applicants selected.");
            }
            $ids     = array_values(array_filter(array_map('intval', $_POST['ids'])));
            $approve = ($act === 'approve_applicants');

            // Seat limit (0 = unlimited)
            $qSql = [];
            $qSql['QUERY']   = "SELECT seat_limit FROM `rcg_cardiac_talk_quizzes` WHERE id = ? AND row_status != 'D'";
            $qSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $quizId, 'TYP' => 'i'];
            $qRes = $mycms->sql_select($qSql);
            if (empty($qRes)) {
                throw new Exception("Quiz not found.");
            }
            $seatLimit = (int)$qRes[0]['seat_limit'];

            $cSql = [];
            $cSql['QUERY']   = "SELECT COUNT(*) AS c FROM `rcg_cardiac_talk_quiz_participation` WHERE quiz_id = ? AND status = 'Approved' AND row_status = 'A'";
            $cSql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
            $cRes        = $mycms->sql_select($cSql);
            $approvedNow = $cRes ? (int)$cRes[0]['c'] : 0;

            $done = 0;
            $skippedFull = 0;
            foreach ($ids as $pid) {
                // Row must belong to this quiz
                $rSql = [];
                $rSql['QUERY']   = "SELECT status FROM `rcg_cardiac_talk_quiz_participation` WHERE id = ? AND quiz_id = ? AND row_status = 'A'";
                $rSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $pid, 'TYP' => 'i'];
                $rSql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i'];
                $rRes = $mycms->sql_select($rSql);
                if (empty($rRes)) {
                    continue;
                }
                $current = $rRes[0]['status'];

                if ($approve) {
                    if ($current === 'Approved') {
                        continue;
                    }
                    if ($seatLimit > 0 && $approvedNow >= $seatLimit) {
                        $skippedFull++;
                        continue;
                    }
                    $u = [];
                    $u['QUERY'] = "UPDATE `rcg_cardiac_talk_quiz_participation`
                                   SET status = 'Approved', approved_by = ?, approved_at = NOW()
                                   WHERE id = ? AND quiz_id = ? AND row_status = 'A'";
                    $u['PARAM'] = [
                        ['FILD' => 'approved_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                        ['FILD' => 'id',          'DATA' => $pid,          'TYP' => 'i'],
                        ['FILD' => 'quiz_id',     'DATA' => $quizId,       'TYP' => 'i']
                    ];
                    $mycms->sql_update($u);
                    $approvedNow++;
                    $done++;
                } else {
                    if ($current === 'Rejected') {
                        continue;
                    }
                    $u = [];
                    $u['QUERY'] = "UPDATE `rcg_cardiac_talk_quiz_participation`
                                   SET status = 'Rejected', approved_by = NULL, approved_at = NULL
                                   WHERE id = ? AND quiz_id = ? AND row_status = 'A'";
                    $u['PARAM'] = [
                        ['FILD' => 'id',      'DATA' => $pid,    'TYP' => 'i'],
                        ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i']
                    ];
                    $mycms->sql_update($u);
                    $done++;
                }
            }

            if ($approve) {
                $msg = $done . ' applicant(s) approved.';
                if ($skippedFull > 0) {
                    $msg .= ' ' . $skippedFull . ' not approved because the seat limit (' . $seatLimit . ') is reached.';
                }
                setAlert(($done === 0 && $skippedFull > 0) ? 'error' : 'success', $msg);
            } else {
                setAlert('success', $done . ' applicant(s) rejected.');
            }
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
        }
        participantRedirect('cardiac_talks_quiz_applicants.php', $quizId);
        exit();

    case 'remove_participants':
        $quizId = (int)($_POST['quiz_id'] ?? 0);
        try {
            if ($quizId <= 0) {
                throw new Exception("Quiz ID is required.");
            }
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No participants selected.");
            }
            $ids  = array_values(array_filter(array_map('intval', $_POST['ids'])));
            $done = 0;
            foreach ($ids as $pid) {
                $u = [];
                $u['QUERY'] = "UPDATE `rcg_cardiac_talk_quiz_participation`
                               SET row_status = 'D'
                               WHERE id = ? AND quiz_id = ? AND row_status = 'A'";
                $u['PARAM'] = [
                    ['FILD' => 'id',      'DATA' => $pid,    'TYP' => 'i'],
                    ['FILD' => 'quiz_id', 'DATA' => $quizId, 'TYP' => 'i']
                ];
                $mycms->sql_update($u);
                $done++;
            }
            setAlert('success', $done . ' participant(s) removed.');
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
        }
        participantRedirect('cardiac_talks_quiz_participants.php', $quizId);
        exit();

    case 'notify_all':
        try {

            // --- Validate ---
            if (empty($_REQUEST['quiz_id'])) {
                throw new Exception("Quiz ID is required");
            }

            $quizId = (int)$_REQUEST['quiz_id'];

            // --- Get Quiz Details ---
            $quiz = [];
            $quiz['QUERY'] = "SELECT id, title 
                          FROM rcg_cardiac_talk_quizzes 
                          WHERE id = ? AND row_status = 'A'";

            $quiz['PARAM'][] = [
                'FILD' => 'id',
                'DATA' => $quizId,
                'TYP'  => 'i'
            ];

            $quizData = $mycms->sql_select($quiz);

            if (empty($quizData)) {
                throw new Exception("Quiz not found");
            }

            $quizName = $quizData[0]['title'];

            // --- Get All Tokens ---
            $tokens = [];
            $tokens['QUERY'] = "SELECT token FROM rcg_user_fcm_tokens";

            $tokensData = $mycms->sql_select($tokens);

            if (empty($tokensData)) {
                throw new Exception("No users found");
            }

            // --- Send Notifications ---
            foreach ($tokensData as $row) {

                $token = $row['token'];

                $message = [
                    "token" => $token,
                    "notification" => [
                        "title" => "Quiz Enrolment Open",
                        "body"  => "Enroll now for " . $quizName
                    ],
                    "data" => [
                        "type" => "quiz_enrolment",
                        "quiz_id" => (string)$quizId
                    ]
                ];

                $response = sendFCM($message);

                // --- Remove invalid token ---
                if (
                    strpos($response, 'UNREGISTERED') !== false ||
                    strpos($response, 'INVALID_ARGUMENT') !== false
                ) {

                    $del = [];
                    $del['QUERY'] = "DELETE FROM rcg_user_fcm_tokens WHERE token = ?";
                    $del['PARAM'][] = [
                        'FILD' => 'token',
                        'DATA' => $token,
                        'TYP'  => 's'
                    ];
                    $mycms->sql_delete($del);
                }
            }

            setAlert('success', 'Notification sent successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
        }

        exit();

    case 'reset_quiz':
        try {
            // Validate
            if (empty($_REQUEST['quiz_id'])) {
                throw new Exception("Quiz ID is required.");
            }

            $quiz_id = (int)$_REQUEST['quiz_id'];
            $updated_by = $loggedUserID;

            // 1. Reset all questions status to 'active' for this quiz
            $resetQuestions = [];
            $resetQuestions['QUERY'] = "UPDATE `rcg_cardiac_talk_quiz_questions` 
                                        SET status = 'active', 
                                            updated_at = NOW(), 
                                            updated_by = ? 
                                        WHERE quiz_id = ? AND row_status != 'D'";
            $resetQuestions['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i']

            ];
            $mycms->sql_update($resetQuestions);

            // 2. Delete all answers for this quiz
            $deleteAnswers = [];
            $deleteAnswers['QUERY'] = "DELETE FROM `rcg_cardiac_talk_quiz_answers` WHERE quiz_id = ?";
            $deleteAnswers['PARAM'] = [
                ['FILD' => 'quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i']
            ];
            $mycms->sql_delete($deleteAnswers);

            // 3. Update quiz status to 'active'
            $updateQuiz = [];
            $updateQuiz['QUERY'] = "UPDATE `rcg_cardiac_talk_quizzes` 
                                    SET status = 'active', 
                                        updated_at = NOW(), 
                                        updated_by = ? 
                                    WHERE id = ? AND row_status != 'D'";
            $updateQuiz['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                ['FILD' => 'id', 'DATA' => $quiz_id, 'TYP' => 'i']
            ];
            $mycms->sql_update($updateQuiz);

            // 4. Clear Firebase data for this quiz
            try {
                // Include Firebase service if not already included
                if (!class_exists('FirebaseService')) {
                    require_once __DIR__ . '/includes/FirebaseService.php';
                }

                $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);

                // Clear the current question in Firebase
                $firebase->delete("live_quiz/$quiz_id/current_question");

                // Optional: Also clear other quiz-related Firebase data if needed
                $firebase->delete("live_quiz/$quiz_id");
            } catch (Exception $e) {
                // Log Firebase error but don't stop the reset process
                error_log("Firebase reset error for quiz {$quiz_id}: " . $e->getMessage());
                // Continue with the reset - database changes are more important
            }

            $message = "Quiz has been reset successfully. Questions status reset to active, all answers cleared, quiz status set to active, and live question cleared from Firebase.";

            // For AJAX requests, return JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 1, 'message' => $message]);
                exit();
            } else {
                setAlert('success', 'Quiz reset successfully.');
                pageRedirection("cardiac_talks_discussion_quiz_poll.php", 2);
            }
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 0, 'message' => $e->getMessage()]);
                exit();
            } else {
                setAlert('error', $e->getMessage());
                pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
            }
        }
        exit();
}

/*============== HELPER: FLASH ALERT (shown once on the list page) ================*/
function setAlert($type, $message)
{
    $_SESSION['cardiac_alert'] = [
        'type'    => ($type === 'error') ? 'error' : 'success',
        'message' => $message
    ];
}

/* ---------- Decide which page to go back to (whitelist) ----------
 * 1) return_to param (hidden field / link param)
 * 2) page the request came from (Referer)
 * 3) default passed in by the caller
 */
function getReturnPage($default = "cardiac_talks_discussion_quiz_poll.php")
{
    $allowed = ['cardiac_talks_discussion_quiz_poll.php'];

    $page = basename($_REQUEST['return_to'] ?? '');
    if (in_array($page, $allowed, true)) {
        return $page;
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        $page = basename((string)parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
        if (in_array($page, $allowed, true)) {
            return $page;
        }
    }

    return $default;
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey        = "_pgn_";
    $pageKeyVal     = (isset($_REQUEST[$pageKey]) && $_REQUEST[$pageKey] !== "") ? (int)$_REQUEST[$pageKey] : 0;
    $searchString   = "";
    $searchArray    = array();

    $searchArray['tab']    = 'quiz';
    $searchArray[$pageKey] = $pageKeyVal;
    $searchArray['src_title']  = isset($_REQUEST['src_title']) ? urlencode(trim($_REQUEST['src_title'])) : '';
    $searchArray['start_date'] = isset($_REQUEST['start_date']) ? urlencode(trim($_REQUEST['start_date'])) : '';

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $page = getReturnPage($fileName);   // $fileName is only the default now
    $mycms->redirect($page . "?m=" . $messageCode . $additionalString . $searchString);
}

/* Redirect back to the participants / applicants page, keeping its filters */
function participantRedirect($page, $quizId)
{
    global $mycms;
    $qs = "?quiz_id=" . (int)$quizId;
    foreach (['f_status', 'f_search'] as $k) {
        if (isset($_POST[$k]) && $_POST[$k] !== '') {
            $qs .= "&" . $k . "=" . urlencode($_POST[$k]);
        }
    }
    $mycms->redirect($page . $qs);
}