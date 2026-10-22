<?php
include_once('.includes/init.php');
include_once('.includes/fcm-helper.php');

$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];

switch ($act) {

    case 'Approve':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing participation ID to approve.");
            }

            $participation_id = (int)$_REQUEST['id'];
            $approved_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `quiz_participation`
                             SET status = ?, approved_by = ?, approved_at = NOW()
                             WHERE id = ?";

            $sql['PARAM'][] = ['FILD' => 'status',      'DATA' => 'Approved', 'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'approved_by', 'DATA' => $approved_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',          'DATA' => $participation_id, 'TYP' => 'i'];

            $mycms->sql_update($sql);

            notifyParticipationStatus($participation_id, 'Approved');

            pageRedirection("participations.php", 2);  // success message
        } catch (Exception $e) {
            pageRedirection("participations.php", "Error: " . $e->getMessage());
        }
        exit();

    case 'Reject':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing participation ID to reject.");
            }

            $participation_id = (int)$_REQUEST['id'];
            $rejected_by = $loggedUserID;

            $sql = [];
            $sql['QUERY'] = "UPDATE `quiz_participation`
                             SET status = ?, approved_by = ?, approved_at = NOW()
                             WHERE id = ?";

            $sql['PARAM'][] = ['FILD' => 'status',      'DATA' => 'Rejected', 'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'approved_by', 'DATA' => $rejected_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',          'DATA' => $participation_id, 'TYP' => 'i'];

            $mycms->sql_update($sql);

            notifyParticipationStatus($participation_id, 'Rejected');

            pageRedirection("participations.php", 2);
        } catch (Exception $e) {
            pageRedirection("participations.php", "Error: " . $e->getMessage());
        }
        exit();
    case 'MultiAction':
        try {
            // --- Validation ---
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No participations selected.");
            }
            if (empty($_POST['action'])) {
                throw new Exception("Please choose an action.");
            }

            // --- Sanitize ---
            $ids         = array_map('intval', $_POST['ids']);
            $action      = $_POST['action'];
            $approved_by = $loggedUserID;
            $now         = date('Y-m-d H:i:s');

            foreach ($ids as $id) {
                // --- Check current status first ---
                $checkSql = [];
                $checkSql['QUERY'] = "SELECT `status` FROM `quiz_participation` WHERE `id` = ?";
                $checkSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $id, 'TYP' => 'i'];
                $existing = $mycms->sql_select($checkSql);

                if (empty($existing)) {
                    continue; // skip if not found
                }

                $currentStatus = $existing[0]['status'];

                // --- Skip if already Approved or Rejected ---
                if (in_array($currentStatus, ['Approved', 'Rejected'])) {
                    continue;
                }

                $sql = [];

                switch ($action) {
                    case 'Approve':
                        $sql['QUERY'] = "UPDATE `quiz_participation`
                                     SET status = ?, approved_by = ?, approved_at = NOW()
                                   WHERE id = ?";
                        $sql['PARAM'] = [
                            ['FILD' => 'status',      'DATA' => 'Approved',   'TYP' => 's'],
                            ['FILD' => 'approved_by', 'DATA' => $approved_by, 'TYP' => 'i'],
                            ['FILD' => 'id',          'DATA' => $id,          'TYP' => 'i']
                        ];
                        break;

                    case 'Reject':
                        $sql['QUERY'] = "UPDATE `quiz_participation`
                                     SET status = ?, approved_by = ?, approved_at = NOW()
                                   WHERE id = ?";
                        $sql['PARAM'] = [
                            ['FILD' => 'status',      'DATA' => 'Rejected',   'TYP' => 's'],
                            ['FILD' => 'approved_by', 'DATA' => $approved_by, 'TYP' => 'i'],
                            ['FILD' => 'id',          'DATA' => $id,          'TYP' => 'i']
                        ];
                        break;

                    default:
                        throw new Exception("Invalid action selected.");
                }

                $mycms->sql_update($sql);

                notifyParticipationStatus($id, $action === 'Approve' ? 'Approved' : 'Rejected');
            }

            // --- Redirect with message ---
            pageRedirection("participations.php", 2); // success msg
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
    /*==================== SEARCH PARTICIPANTS =======================*/
    case'search_participants':
        pageRedirection('participations.php',5);	
        exit();
        break;
    default:
        echo "Invalid action.";
        exit();
}

// -------------------------------------------------------
// Reusable redirection function (same as your current one)
// -------------------------------------------------------
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey = "_pgn_";
    $pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];
    @$searchString = "";
    $searchArray = [];

    $searchArray[$pageKey] = $pageKeyVal;
    $searchArray = [
        'group_name'    => isset($_REQUEST['group_name']) ? trim($_REQUEST['group_name']) : '',
        'group_code'    => isset($_REQUEST['group_code']) ? trim($_REQUEST['group_code']) : '',
        'quiz_date'     => isset($_REQUEST['quiz_date']) ? trim($_REQUEST['quiz_date']) : '',
        'start_date'    => isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '',
        'end_date'      => isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : '',
    ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}

function notifyParticipationStatus($participationId, $status)
{
    global $mycms;

    // --- Get quiz name ---
    $quiz = [];
    $quiz['QUERY'] = "SELECT q.title
                      FROM quiz_participation p
                      JOIN rcg_quizzes q ON q.id = p.quiz_id
                      WHERE p.id = ?";

    $quiz['PARAM'][] = [
        'FILD' => 'id',
        'DATA' => $participationId,
        'TYP'  => 'i'
    ];

    $quizData = $mycms->sql_select($quiz);
    $quizName = $quizData[0]['title'] ?? 'Quiz';

    // --- Get all users in this participation ---
    $users = [];
    $users['QUERY'] = "SELECT user_id 
                       FROM rcg_quiz_participation_members 
                       WHERE participation_id = ? AND status = 'Active'";

    $users['PARAM'][] = [
        'FILD' => 'participation_id',
        'DATA' => $participationId,
        'TYP'  => 'i'
    ];

    $userList = $mycms->sql_select($users);

    if (empty($userList)) return;

    foreach ($userList as $u) {

        $userId = $u['user_id'];

        // --- Get tokens ---
        $tokens = [];
        $tokens['QUERY'] = "SELECT token FROM rcg_user_fcm_tokens WHERE user_id = ?";
        $tokens['PARAM'][] = [
            'FILD' => 'user_id',
            'DATA' => $userId,
            'TYP'  => 'i'
        ];

        $tokenList = $mycms->sql_select($tokens);

        foreach ($tokenList as $t) {

            $message = [
                "token" => $t['token'],
                "notification" => [
                    "title" => "Quiz Participation Update",
                    "body"  => "Your participation is " . $status . " for " . $quizName
                ],
                "data" => [
                    "type" => "participation_status",
                    "status" => $status,
                    "quiz_name" => $quizName
                ]
            ];

            $response = sendFCM($message);

            // --- Remove invalid token ---
            if (strpos($response, 'UNREGISTERED') !== false ||
                strpos($response, 'INVALID_ARGUMENT') !== false) {

                $del = [];
                $del['QUERY'] = "DELETE FROM rcg_user_fcm_tokens WHERE token = ?";
                $del['PARAM'][] = [
                    'FILD' => 'token',
                    'DATA' => $t['token'],
                    'TYP'  => 's'
                ];
                $mycms->sql_delete($del);
            }
        }
    }
}