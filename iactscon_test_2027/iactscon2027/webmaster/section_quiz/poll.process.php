<?php

include_once('includes/init.php');

$act = @$_REQUEST['act'];
switch ($act) {
    case 'add_poll':
        try {
            // Sanitize & Assign
            $title          = addslashes(trim($_POST['title']));
            $description    = isset($_POST['description']) ? addslashes(trim($_POST['description'])) : null;
            $is_active      = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
            $is_published   = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;
            $allow_multiple = isset($_POST['allow_multiple']) ? (int)$_POST['allow_multiple'] : 0;
            $max_choices    = isset($_POST['max_choices']) ? (int)$_POST['max_choices'] : 1;
            $start_time     = !empty($_POST['start_time']) ? trim($_POST['start_time']) : date('Y-m-d H:i:s');
            $end_time       = !empty($_POST['end_time']) ? trim($_POST['end_time']) : null;
            $created_by     = (int)$_SESSION['user_id']; // Assuming session is active

            // Build query
            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_polls`
                        SET title=?, description=?, is_active=?, is_published=?, 
                            allow_multiple=?, max_choices=?, start_time=?, end_time=?, 
                            created_by=?";

            $sql['PARAM'] = [
                ['FILD' => 'title',          'DATA' => $title,          'TYP' => 's'],
                ['FILD' => 'description',    'DATA' => $description,    'TYP' => 's'],
                ['FILD' => 'is_active',      'DATA' => $is_active,      'TYP' => 'i'],
                ['FILD' => 'is_published',   'DATA' => $is_published,   'TYP' => 'i'],
                ['FILD' => 'allow_multiple', 'DATA' => $allow_multiple, 'TYP' => 'i'],
                ['FILD' => 'max_choices',    'DATA' => $max_choices,    'TYP' => 'i'],
                ['FILD' => 'start_time',     'DATA' => $start_time,     'TYP' => 's'],
                ['FILD' => 'end_time',       'DATA' => $end_time,       'TYP' => 's'],
                ['FILD' => 'created_by',     'DATA' => $created_by,     'TYP' => 'i']
            ];

            $mycms->sql_insert($sql);
            pageRedirection("manage_polls.php", 1);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
    case 'edit_poll':
        try {
            // Sanitize
            $id             = (int)$_POST['poll_id'];
            $title          = addslashes(trim($_POST['title']));
            $description    = isset($_POST['description']) ? addslashes(trim($_POST['description'])) : null;
            $is_active      = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
            $is_published   = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;
            $allow_multiple = isset($_POST['allow_multiple']) ? (int)$_POST['allow_multiple'] : 0;
            $max_choices    = isset($_POST['max_choices']) ? (int)$_POST['max_choices'] : 1;
            $start_time     = !empty($_POST['start_time']) ? trim($_POST['start_time']) : date('Y-m-d H:i:s');
            $end_time       = !empty($_POST['end_time']) ? trim($_POST['end_time']) : null;
            $updated_by     = (int)$_SESSION['user_id']; // Assuming session is active

            // Build query
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_polls` 
                        SET title=?, description=?, is_active=?, is_published=?, 
                            allow_multiple=?, max_choices=?, start_time=?, end_time=?, 
                            updated_by=? 
                        WHERE id=? AND (is_deleted IS NULL OR is_deleted=0)";

            $sql['PARAM'] = [
                ['FILD' => 'title',          'DATA' => $title,          'TYP' => 's'],
                ['FILD' => 'description',    'DATA' => $description,    'TYP' => 's'],
                ['FILD' => 'is_active',      'DATA' => $is_active,      'TYP' => 'i'],
                ['FILD' => 'is_published',   'DATA' => $is_published,   'TYP' => 'i'],
                ['FILD' => 'allow_multiple', 'DATA' => $allow_multiple, 'TYP' => 'i'],
                ['FILD' => 'max_choices',    'DATA' => $max_choices,    'TYP' => 'i'],
                ['FILD' => 'start_time',     'DATA' => $start_time,     'TYP' => 's'],
                ['FILD' => 'end_time',       'DATA' => $end_time,       'TYP' => 's'],
                ['FILD' => 'updated_by',     'DATA' => $updated_by,     'TYP' => 'i'],
                ['FILD' => 'id',             'DATA' => $id,             'TYP' => 'i']
            ];

            $mycms->sql_update($sql);
            pageRedirection("manage_polls.php", 2); // 2 = updated successfully

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
    case 'Remove':
        try {
            // Validation
            if (empty($_GET['id'])) {
                throw new Exception("Quiz ID is required.");
            }

            // Sanitize
            $id         = (int)$_GET['id'];
            $deleted_by = (int)$_SESSION['user_id']; // assuming session
            $deleted_at = date('Y-m-d H:i:s');

            // Build query
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_quizzes`
                         SET row_status=?, deleted_by=?, deleted_at=?
                         WHERE id=? AND row_status='A'";

            $sql['PARAM'] = [
                ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'],
                ['FILD' => 'deleted_by', 'DATA' => $deleted_by, 'TYP' => 'i'],
                ['FILD' => 'deleted_at', 'DATA' => $deleted_at, 'TYP' => 's'],
                ['FILD' => 'id', 'DATA' => $id, 'TYP' => 'i']
            ];

            // Execute update
            $mycms->sql_update($sql);

            // Redirect with message code 3 (deleted)
            pageRedirection("manage_quiz.php", 3);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
    case 'Active':
        try {
            if (empty($_REQUEST['id'])) {
                throw new Exception("Missing Poll ID to activate.");
            }

            $poll_id    = (int)$_REQUEST['id'];
            $updated_by = (int)$_SESSION['user_id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_polls` 
                         SET row_status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ?";

            $sql['PARAM'][] = ['FILD' => 'row_status',  'DATA' => 'A',         'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by',  'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',          'DATA' => $poll_id,    'TYP' => 'i'];

            $mycms->sql_update($sql);

            pageRedirection("manage_poll.php", 2); // message code 2 = success
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing Poll ID to inactivate.");

            $poll_id    = (int)$_REQUEST['id'];
            $updated_by = (int)$_SESSION['user_id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_polls` 
                         SET row_status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ?";

            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'I',           'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by,   'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $poll_id,      'TYP' => 'i'];

            $mycms->sql_update($sql);

            pageRedirection("manage_poll.php", 2);  // 2 = success
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey = "_pgn_";
    $pageKeyVal = (!empty($_REQUEST[$pageKey])) ? (int)$_REQUEST[$pageKey] : 0;

    $searchArray = [
        $pageKey         => $pageKeyVal,
        'title'          => isset($_REQUEST['title']) ? urlencode(trim($_REQUEST['title'])) : '',
        'description'    => isset($_REQUEST['description']) ? trim($_REQUEST['description']) : '',
        'is_active'      => isset($_REQUEST['is_active']) ? trim($_REQUEST['is_active']) : '',
        'is_published'   => isset($_REQUEST['is_published']) ? trim($_REQUEST['is_published']) : '',
        'start_time'     => isset($_REQUEST['start_time']) ? (int)$_REQUEST['start_time'] : '',
        'end_time'       => isset($_REQUEST['end_time']) ? (int)$_REQUEST['end_time'] : '',
        'created_by'     => isset($_REQUEST['created_by']) ? (int)$_REQUEST['created_by'] : '',
        'created_at'     => isset($_REQUEST['created_at']) ? (int)$_REQUEST['created_at'] : '',
    ];

    $searchString = "";
    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}
