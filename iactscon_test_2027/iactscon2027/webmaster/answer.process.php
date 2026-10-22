<?php

include_once('includes/init.php');
$act = @$_REQUEST['act'];

function sendResponse(int $code, string $message, array $data = [])
{
    http_response_code($code);
    echo json_encode([
        'status' => $code === 200 ? 'success' : 'failed',
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

switch ($act) {
    case 'submit_answer':
        // Accept JSON too
        $rawInput = file_get_contents("php://input");
        $input = json_decode($rawInput, true);
        if (!$input) {
            $input = $_POST; // fallback
        }

        $quizId      = isset($input['quiz_id']) ? (int)$input['quiz_id'] : 0;
        $questionId  = isset($input['question_id']) ? (int)$input['question_id'] : 0;
        $selectedOptionId = isset($input['selected_option']) ? (int)$input['selected_option'] : 0; // Now it's option ID
        // $timeTaken   = isset($input['time_taken']) ? (int)$input['time_taken'] : 0;
        $timeTaken = isset($input['time_taken']) 
                    ? round((float)$input['time_taken'], 3) 
                    : 0.000;


        // Get userId from auth (if logged in)
        // $auth = authenticate();
        // $userId = $auth['user_id'] ?? 0;
        $userId = 0; // Set to 0 for guest users

        if (!$quizId || !$questionId || !$selectedOptionId) {
            sendResponse(400, 'Missing required fields');
        }

        // ✅ Check quiz active
        $sql = [];
        $sql['QUERY'] = "SELECT id FROM rcg_quizzes 
                         WHERE id=? AND row_status='A' AND status='active'";
        $sql['PARAM'][] = ['FILD' => 'id', 'DATA' => $quizId, 'TYP' => 'i'];
        $quizExists = $mycms->sql_select($sql);
        if (!$quizExists) {
            sendResponse(404, 'Quiz not found or inactive');
        }

        // ✅ Validate question
        $sql = [];
        $sql['QUERY'] = "SELECT id FROM rcg_quiz_questions 
                         WHERE id=? AND quiz_id=?";
        $sql['PARAM'] = [
            ['FILD' => 'id',      'DATA' => $questionId, 'TYP' => 'i'],
            ['FILD' => 'quiz_id', 'DATA' => $quizId,     'TYP' => 'i']
        ];
        $questionExists = $mycms->sql_select($sql);
        if (!$questionExists) {
            sendResponse(404, 'Question not found in this quiz');
        }

        // ✅ Validate selected option and check if correct
        $sql = [];
        $sql['QUERY'] = "SELECT id, is_correct FROM rcg_quiz_question_options 
                         WHERE id=? AND question_id=?";
        $sql['PARAM'] = [
            ['FILD' => 'id',          'DATA' => $selectedOptionId, 'TYP' => 'i'],
            ['FILD' => 'question_id', 'DATA' => $questionId,       'TYP' => 'i']
        ];
        $optionData = $mycms->sql_select($sql);
        if (!$optionData) {
            sendResponse(404, 'Selected option not found for this question');
        }
        $isCorrect = (int)$optionData[0]['is_correct'];

        $userLoggedIn = !empty($userId);
        $sessionDeviceId = $_COOKIE['session_based_device_id'] ?? null;

        // ---------- CHECK DOUBLE ANSWER ----------
        $sql = [];
        if ($userLoggedIn) {
            $sql['QUERY'] = "SELECT id FROM rcg_quiz_answers 
                            WHERE quiz_id=? AND question_id=? AND user_id=?";
            $sql['PARAM'] = [
                ['FILD' => 'quiz_id',     'DATA' => $quizId,     'TYP' => 'i'],
                ['FILD' => 'question_id', 'DATA' => $questionId, 'TYP' => 'i'],
                ['FILD' => 'user_id',     'DATA' => $userId,     'TYP' => 'i']
            ];
        } else {
            if (!$sessionDeviceId) {
                sendResponse(400, 'Session device ID not found');
            }
            $sql['QUERY'] = "SELECT id FROM rcg_quiz_answers 
                            WHERE quiz_id=? AND question_id=? AND device_id=?";
            $sql['PARAM'] = [
                ['FILD' => 'quiz_id',     'DATA' => $quizId,         'TYP' => 'i'],
                ['FILD' => 'question_id', 'DATA' => $questionId,     'TYP' => 'i'],
                ['FILD' => 'device_id',   'DATA' => $sessionDeviceId, 'TYP' => 's']
            ];
        }

        $alreadyAnswered = $mycms->sql_select($sql);
        if ($alreadyAnswered) {
            sendResponse(409, 'You have already answered this question');
        }

        // ---------- INSERT ANSWER ----------

        // Fetch quiz marking rules
        $markSql = [];
        $markSql['QUERY'] = "SELECT marks_correct, marks_incorrect FROM rcg_quizzes WHERE id=?";
        $markSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $quizId, 'TYP' => 'i'];
        $quizData = $mycms->sql_select($markSql);

        $marksCorrect   = abs((float)($quizData[0]['marks_correct'] ?? 0));
        $marksIncorrect = abs((float)($quizData[0]['marks_incorrect'] ?? 0));

        if ($isCorrect == 1) {
            // Correct → add marks
            $marksObtained = $marksCorrect;
        } else {
            // Incorrect → minus only if incorrect marking exists
            $marksObtained = ($marksIncorrect > 0) ? -$marksIncorrect : 0;
        }




        $sql = [];
        if ($userLoggedIn) {
            $sql['QUERY'] = "INSERT INTO rcg_quiz_answers 
                            SET quiz_id=?, question_id=?, user_id=?,
                                selected_option=?, is_correct=?, time_taken=?, marks_obtained=?, answered_at=NOW()";
            $sql['PARAM'] = [
                ['FILD' => 'quiz_id',           'DATA' => $quizId,          'TYP' => 'i'],
                ['FILD' => 'question_id',       'DATA' => $questionId,      'TYP' => 'i'],
                ['FILD' => 'user_id',           'DATA' => $userId,          'TYP' => 'i'],
                ['FILD' => 'selected_option', 'DATA' => $selectedOptionId, 'TYP' => 'i'],
                ['FILD' => 'is_correct',        'DATA' => $isCorrect,       'TYP' => 'i'],
                ['FILD' => 'time_taken',        'DATA' => $timeTaken,       'TYP' => 'd'],
                ['FILD' => 'marks_obtained',    'DATA' => $marksObtained, 'TYP' => 's']
            ];
        } else {
            $sql['QUERY'] = "INSERT INTO rcg_quiz_answers 
                            SET quiz_id=?, question_id=?, device_id=?,
                                selected_option=?, is_correct=?, time_taken=?, marks_obtained=?, answered_at=NOW()";
            $sql['PARAM'] = [
                ['FILD' => 'quiz_id',           'DATA' => $quizId,          'TYP' => 'i'],
                ['FILD' => 'question_id',       'DATA' => $questionId,      'TYP' => 'i'],
                ['FILD' => 'device_id',         'DATA' => $sessionDeviceId, 'TYP' => 's'],
                ['FILD' => 'selected_option', 'DATA' => $selectedOptionId, 'TYP' => 'i'],
                ['FILD' => 'is_correct',        'DATA' => $isCorrect,       'TYP' => 'i'],
                ['FILD' => 'time_taken',        'DATA' => $timeTaken,       'TYP' => 'd'],
                ['FILD' => 'marks_obtained',    'DATA' => $marksObtained, 'TYP' => 's']
            ];
        }

        $answerId = $mycms->sql_insert($sql);

        if ($answerId) {
            sendResponse(200, "Answer saved successfully", [
                'answer_id' => $answerId,
                'is_correct' => $isCorrect
            ]);
        } else {
            sendResponse(500, "Failed to save answer");
        }

        exit();
}