<?php

include_once('../includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];
switch ($act) {
    case 'add_poll':
        try {
            // Sanitize & Assign
            $title          = addslashes(trim($_REQUEST['title']));
            $description    = isset($_REQUEST['description']) ? addslashes(trim($_REQUEST['description'])) : null;
            // $is_active      = isset($_REQUEST['is_active']) ? (int)$_REQUEST['is_active'] : 1;
            $is_published   = isset($_REQUEST['is_published']) ? (int)$_REQUEST['is_published'] : 0;
            $allow_multiple = isset($_REQUEST['allow_multiple']) ? (int)$_REQUEST['allow_multiple'] : 0;
            $max_choices    = isset($_REQUEST['max_choices']) ? (int)$_REQUEST['max_choices'] : 2;
            $start_time     = !empty($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : date('Y-m-d H:i:s');
            $end_time       = !empty($_REQUEST['end_time']) ? trim($_REQUEST['end_time']) : null;
            $created_by     = $loggedUserID; //(int)$_SESSION['user_id']; // Assuming session is active

            date_default_timezone_set('Asia/Kolkata');

            $current_time = time(); // current timestamp in seconds

            $is_active = 1;
            
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

            $poll_id = $mycms->sql_insert($sql);

            $options = $_REQUEST['options'] ?? null;
            // Insert options
            foreach ($options as $opt) {
                $opt = trim($opt);
                if (!empty($opt)) {
                    $opt_sql = [];
                    $opt_sql['QUERY'] = "INSERT INTO `rcg_poll_options`
                                        SET poll_id=?, option_text=?, vote_count=?";
                    $opt_sql['PARAM'] = [
                        ['FILD' => 'poll_id',     'DATA' => $poll_id, 'TYP' => 'i'],
                        ['FILD' => 'option_text', 'DATA' => $opt,     'TYP' => 's'],
                        ['FILD' => 'vote_count',  'DATA' => 0,        'TYP' => 'i']
                    ];
                    $mycms->sql_insert($opt_sql);
                }
            }
            pageRedirection("manage_polls.php", 1);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
    case 'edit_poll':
        try {
            // Sanitize inputs
            $poll_id        = (int)$_POST['poll_id'];
            $title          = addslashes(trim($_POST['title']));
            $description    = isset($_POST['description']) ? addslashes(trim($_POST['description'])) : null;
            $is_published   = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;
            $allow_multiple = isset($_POST['allow_multiple']) ? (int)$_POST['allow_multiple'] : 0;
            $max_choices    = isset($_POST['max_choices']) ? (int)$_POST['max_choices'] : 2;
            $start_time     = !empty($_POST['start_time']) ? trim($_POST['start_time']) : date('Y-m-d H:i:s');
            $end_time       = !empty($_POST['end_time']) ? trim($_POST['end_time']) : null;
            $updated_by     = $loggedUserID; //(int)$_SESSION['user_id'];

            date_default_timezone_set('Asia/Kolkata');
            $current_time = time();
            $is_active = 1;

            // if ($start_time !== null && $end_time !== null) {
            //     if ($current_time >= strtotime($start_time) && $current_time <= strtotime($end_time)) {
            //         $is_active = 1;
            //     }
            // }

            // Update poll details
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
                ['FILD' => 'id',             'DATA' => $poll_id,        'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            // Handle poll options
            $options = $_REQUEST['options'] ?? [];

            foreach ($options as $opt) {
                $option_id   = isset($opt['id']) ? (int)$opt['id'] : 0;
                $option_text = trim($opt['text']);

                if (!empty($option_text)) {
                    if ($option_id > 0) {
                        // Update existing option
                        $opt_sql = [];
                        $opt_sql['QUERY'] = "UPDATE `rcg_poll_options` 
                                        SET option_text=? 
                                        WHERE id=? AND poll_id=?";
                        $opt_sql['PARAM'] = [
                            ['FILD' => 'option_text', 'DATA' => $option_text, 'TYP' => 's'],
                            ['FILD' => 'id',          'DATA' => $option_id,  'TYP' => 'i'],
                            ['FILD' => 'poll_id',     'DATA' => $poll_id,    'TYP' => 'i']
                        ];
                        $mycms->sql_update($opt_sql);
                    } else {
                        // Insert new option
                        $opt_sql = [];
                        $opt_sql['QUERY'] = "INSERT INTO `rcg_poll_options`
                                        SET poll_id=?, option_text=?, vote_count=?";
                        $opt_sql['PARAM'] = [
                            ['FILD' => 'poll_id',     'DATA' => $poll_id,     'TYP' => 'i'],
                            ['FILD' => 'option_text', 'DATA' => $option_text, 'TYP' => 's'],
                            ['FILD' => 'vote_count',  'DATA' => 0,            'TYP' => 'i']
                        ];
                        $mycms->sql_insert($opt_sql);
                    }
                }
            }

            pageRedirection("manage_polls.php", 2); // 2 = updated successfully

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
    case 'Remove':
        try {
            // Validation
            if (empty($_GET['id'])) {
                throw new Exception("Poll ID is required.");
            }

            // Sanitize
            $id         = (int)$_GET['id'];
            $deleted_by = $loggedUserID; //(int)$_SESSION['user_id']; // assuming session
            $deleted_at = date('Y-m-d H:i:s');

            // Build query
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_polls`
                         SET is_deleted=?, deleted_by=?, deleted_at=?
                         WHERE id=? AND is_deleted='0'";

            $sql['PARAM'] = [
                ['FILD' => 'is_deleted', 'DATA' => 1, 'TYP' => 'i'],
                ['FILD' => 'deleted_by', 'DATA' => $deleted_by, 'TYP' => 'i'],
                ['FILD' => 'deleted_at', 'DATA' => $deleted_at, 'TYP' => 's'],
                ['FILD' => 'id', 'DATA' => $id, 'TYP' => 'i']
            ];

            // Execute update
            $mycms->sql_update($sql);

            // Redirect with message code 3 (deleted)
            pageRedirection("manage_polls.php", 3);
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
            $updated_by = $loggedUserID; //(int)$_SESSION['user_id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_polls` 
                         SET row_status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ?";

            $sql['PARAM'][] = ['FILD' => 'row_status',  'DATA' => 'A',         'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by',  'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',          'DATA' => $poll_id,    'TYP' => 'i'];

            $mycms->sql_update($sql);

            pageRedirection("manage_polls.php", 2); // message code 2 = success
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing Poll ID to inactivate.");

            $poll_id    = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID; //(int)$_SESSION['user_id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_polls` 
                         SET row_status = ?, updated_at = NOW(), updated_by = ? 
                         WHERE id = ?";

            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'I',           'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by,   'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $poll_id,      'TYP' => 'i'];

            $mycms->sql_update($sql);
            pageRedirection("manage_polls.php", 2);  // 2 = success
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit();
   case 'Reset':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing Poll ID to reset.");

            $poll_id    = (int)$_REQUEST['id'];
            $updated_by = $loggedUserID;

            // ==========================
            // 1️⃣ Reset all vote counts
            // ==========================
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_poll_options`
                            SET vote_count = 0
                            WHERE poll_id = ?";
            $sql['PARAM'][] = ['FILD' => 'poll_id',    'DATA' => $poll_id,    'TYP' => 'i'];
            $mycms->sql_update($sql);

            // ==========================
            // 2️⃣ Delete all votes for this poll
            // ==========================
            $sql = [];
            $sql['QUERY'] = "DELETE FROM `rcg_poll_votes` WHERE poll_id = ?";
            $sql['PARAM'][] = ['FILD' => 'poll_id', 'DATA' => $poll_id, 'TYP' => 'i'];
            $mycms->sql_delete($sql);

            // ==========================
            // 3️⃣ Set poll as inactive
            // ==========================
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_polls`
                            SET is_active = 0, updated_by = ?
                            WHERE id = ?";
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $poll_id,    'TYP' => 'i'];
            $mycms->sql_update($sql);

            // Redirect after successful reset
            pageRedirection("manage_polls.php", 2);  // 2 = success
        } catch (Exception $e) {
            pageRedirection("manage_polls.php", "Error: " . $e->getMessage());
        }
        exit();
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey = "_pgn_";
    $pageKeyVal = ($_REQUEST[$pageKey]=="") ? 0 : $_REQUEST[$pageKey];

    @$searchString = "";
    $searchArray  = array();

    $searchArray[$pageKey]                 = $pageKeyVal;

    $searchArray = [
        'src_title'          => isset($_REQUEST['src_title']) ? urlencode(trim($_REQUEST['src_title'])) : '',
    ];

    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&" . $key . "=" . $val;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}
