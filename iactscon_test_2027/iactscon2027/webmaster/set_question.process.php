<?php
include_once('./includes/init.php');
include_once('./includes/fcm-helper.php');
$adminId = $mycms->getLoggedUserId();
if (!$adminId) {
    die('Access denied. Please login as admin.');
}

$act = $_REQUEST['act'] ?? '';

switch ($act) {

    case 'add_question':
    try {
        $questionText = trim($_REQUEST['question_text'] ?? '');
        $sessionId    = !empty($_REQUEST['session_id']) ? (int)$_REQUEST['session_id'] : null;
        $prefType     = !empty($_REQUEST['preference_type']) ? $_REQUEST['preference_type'] : null;
        $prefId       = !empty($_REQUEST['preference_id']) ? (int)$_REQUEST['preference_id'] : null;

        if ($questionText === '') {
            throw new Exception("Question text is required.");
        }
        if (($prefType && !$prefId) || (!$prefType && $prefId)) {
            throw new Exception("Please select both preference type and value, or leave both blank.");
        }

        $sql            = array();
        $sql['QUERY']   = "INSERT INTO `rcg_live_qa_questions`
                           SET session_id=?, preference_type=?, preference_id=?, question_text=?, status='A', created_by=?";
        $sql['PARAM'][] = array('FILD' => 'session_id',      'DATA' => $sessionId,    'TYP' => 'i');
        $sql['PARAM'][] = array('FILD' => 'preference_type', 'DATA' => $prefType,     'TYP' => 's');
        $sql['PARAM'][] = array('FILD' => 'preference_id',   'DATA' => $prefId,       'TYP' => 'i');
        $sql['PARAM'][] = array('FILD' => 'question_text',   'DATA' => $questionText, 'TYP' => 's');
        $sql['PARAM'][] = array('FILD' => 'created_by',      'DATA' => $adminId,      'TYP' => 'i');
        $questionId = $mycms->sql_insert($sql);

      
        $targetWhere  = " WHERE u.status = 'A' ";
        $targetParams = [];

        if ($sessionId) {
            $targetWhere .= " AND EXISTS (
                                SELECT 1 FROM rcg_user_saved_sessions ss
                                WHERE ss.user_id = u.id AND ss.session_id = ? AND ss.status = 'A'
                              ) ";
            $targetParams[] = ['FILD' => 'session_id', 'DATA' => $sessionId, 'TYP' => 'i'];
        }

        if ($prefId) {
            
            $prefRowSql = [];
            $prefRowSql['QUERY'] = "SELECT preference_name FROM rcg_group_preferences WHERE id = ? LIMIT 1";
            $prefRowSql['PARAM'] = [['FILD' => 'id', 'DATA' => $prefId, 'TYP' => 'i']];
            $prefRow = $mycms->sql_select($prefRowSql);

            if (!empty($prefRow)) {
                $prefName = trim($prefRow[0]['preference_name']);

               
                $targetWhere .= " AND u.user_interest IS NOT NULL
                                   AND LOWER(u.user_interest) LIKE CONCAT('%\"', LOWER(?), '\"%') ";
                $targetParams[] = ['FILD' => 'interest_match', 'DATA' => $prefName, 'TYP' => 's'];
            }
        }

        $targetSql = [];
        $targetSql['QUERY'] = "SELECT DISTINCT u.id FROM rcg_user_registration u $targetWhere";
        foreach ($targetParams as $tp) { $targetSql['PARAM'][] = $tp; }
        $targetUsers = $mycms->sql_select($targetSql);

        if ($targetUsers) {
            $title = "New Live Q&A Question";
            $body  = mb_strimwidth($questionText, 0, 100, '...');
            $dataPayload = [
                'type'        => 'live_qa_question',
                'question_id' => $questionId,
                'session_id'  => $sessionId
            ];

            foreach ($targetUsers as $tu) {
                $notifSql = [];
                $notifSql['QUERY'] = "INSERT INTO `rcg_notifications`
                                      SET user_id=?, type='live_qa_question', title=?, body=?, data=?";
                $notifSql['PARAM'] = [
                    ['FILD' => 'user_id', 'DATA' => $tu['id'], 'TYP' => 'i'],
                    ['FILD' => 'title',   'DATA' => $title,    'TYP' => 's'],
                    ['FILD' => 'body',    'DATA' => $body,     'TYP' => 's'],
                    ['FILD' => 'data',    'DATA' => json_encode($dataPayload), 'TYP' => 's']
                ];
                $mycms->sql_insert($notifSql);

                sendFcmToUser($mycms, $tu['id'], $title, $body, $dataPayload);
            }
        }

        pageRedirection("set_question.php");
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    exit();
    case 'edit_question':
        try {
            $questionId   = (int)($_REQUEST['question_id'] ?? 0);
            $questionText = trim($_REQUEST['question_text'] ?? '');
            $sessionId    = !empty($_REQUEST['session_id']) ? (int)$_REQUEST['session_id'] : null;
            $prefType     = !empty($_REQUEST['preference_type']) ? $_REQUEST['preference_type'] : null;
            $prefId       = !empty($_REQUEST['preference_id']) ? (int)$_REQUEST['preference_id'] : null;

            if ($questionId <= 0) {
                throw new Exception("Invalid question.");
            }
            if ($questionText === '') {
                throw new Exception("Question text is required.");
            }
            if (($prefType && !$prefId) || (!$prefType && $prefId)) {
                throw new Exception("Please select both preference type and value, or leave both blank.");
            }

            $sql            = array();
            $sql['QUERY']   = "UPDATE `rcg_live_qa_questions`
                               SET session_id=?, preference_type=?, preference_id=?, question_text=?
                               WHERE id=?";
            $sql['PARAM'][] = array('FILD' => 'session_id',      'DATA' => $sessionId,    'TYP' => 'i');
            $sql['PARAM'][] = array('FILD' => 'preference_type', 'DATA' => $prefType,     'TYP' => 's');
            $sql['PARAM'][] = array('FILD' => 'preference_id',   'DATA' => $prefId,       'TYP' => 'i');
            $sql['PARAM'][] = array('FILD' => 'question_text',   'DATA' => $questionText, 'TYP' => 's');
            $sql['PARAM'][] = array('FILD' => 'id',              'DATA' => $questionId,   'TYP' => 'i');
            $mycms->sql_update($sql);

            pageRedirection("set_question.php");
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    case 'delete_question':
        try {
            $id = (int)($_REQUEST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception("Invalid question.");
            }

            $sql            = array();
            $sql['QUERY']   = "UPDATE `rcg_live_qa_questions` SET status='D' WHERE id=?";
            $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $id, 'TYP' => 'i');
            $mycms->sql_update($sql);

            pageRedirection("set_question.php");
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();

    default:
        die('Invalid action.');
}

/*============== HELPER: PAGE REDIRECTION ================*/
function pageRedirection($fileName)
{
    global $mycms;

    $pageKey    = "_pgn_";
    $pageKeyVal = empty($_REQUEST[$pageKey]) ? 0 : (int)$_REQUEST[$pageKey];

    $searchString = "";
    $searchArray = [
        $pageKey      => $pageKeyVal,
        'session_id'  => isset($_REQUEST['session_id']) ? $_REQUEST['session_id'] : '',
    ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?" . ltrim($searchString, '&'));
}