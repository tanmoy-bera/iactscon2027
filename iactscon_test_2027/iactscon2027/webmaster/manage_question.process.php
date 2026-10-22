<?php
include_once('includes/init.php');

$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];
$fr  = @$_REQUEST['fr'];
date_default_timezone_set('Asia/kolkata');

// ─────────────────────────────────────────────────────────────────────────────
//  HELPERS
// ─────────────────────────────────────────────────────────────────────────────

function sendJsonResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
    exit();
}

function assertQuizIsDraft($quiz_id, $mycms) {
    if ($quiz_id <= 0) throw new Exception("Invalid quiz ID.");

    $sql          = [];
    $sql['QUERY'] = "SELECT status FROM rcg_quizzes WHERE id = ? AND row_status = 'A' LIMIT 1";
    $sql['PARAM'] = [['FILD' => 'id', 'DATA' => $quiz_id, 'TYP' => 'i']];
    $result       = $mycms->sql_select($sql);

    if (empty($result)) {
        throw new Exception("Quiz not found or inactive.");
    }
    if ($result[0]['status'] !== 'draft') {
        throw new Exception("Questions can only be added, edited, or deleted when the quiz is in Draft status.");
    }
}

// ─────────────────────────────────────────────────────────────────────────────
//  ROUTER
// ─────────────────────────────────────────────────────────────────────────────

switch ($act) {

    // ── search redirect ───────────────────────────────────────────────────────
    case 'search_question':
        pageRedirection('manage_quiz.php', 5);
        exit();

    // ── ADD QUESTION ──────────────────────────────────────────────────────────
    case 'add_question':
        try {
            $quiz_id = (int)($_POST['quiz_id'] ?? 0);
            assertQuizIsDraft($quiz_id, $mycms);

            $level            = (int)($_POST['level']  ?? 1);
            $round            = (int)($_POST['round']  ?? 1);
            $question_text    = addslashes(trim($_POST['question_text'] ?? ''));
            $correct_option   = strtoupper(trim($_POST['correct_option'] ?? 'A'));
            $timer_override   = (isset($_POST['timer_override']) && $_POST['timer_override'] !== '')
                                    ? (int)$_POST['timer_override'] : null;
            $question_status  = 'active';
            $is_last_question = (isset($_POST['is_last_question']) && $_POST['is_last_question'] === 'Yes') ? 1 : 0;
            $created_by       = $loggedUserID;

            // ── media type ────────────────────────────────────────────────────
            // FIX: always use $_POST (not $_REQUEST) to avoid GET param bleed-in
            $raw_media_type = strtolower(trim($_POST['media_type'] ?? ''));
            $media_type     = in_array($raw_media_type, ['image', 'video', 'audio'], true)
                                ? $raw_media_type : null;
            $media_url      = trim($_POST['media_url'] ?? '');
            $media_description = trim($_POST['media_description'] ?? '');

            // ── options validation ────────────────────────────────────────────
            $options = isset($_POST['options']) && is_array($_POST['options']) ? $_POST['options'] : [];
            if (count($options) < 2) {
                throw new Exception("Minimum 2 options are required.");
            }
            $hasCorrect = false;
            foreach ($options as $opt) {
                if (!empty($opt['is_correct'])) { $hasCorrect = true; break; }
            }
            if (!$hasCorrect) {
                throw new Exception("At least one option must be marked as correct.");
            }

            // ── reset last-question flag if needed ────────────────────────────
            if ($is_last_question === 1) {
                $reset          = [];
                $reset['QUERY'] = "UPDATE `rcg_quiz_questions` SET is_last_question=0 WHERE quiz_id=? AND row_status!='D'";
                $reset['PARAM'] = [['FILD' => 'quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i']];
                $mycms->sql_update($reset);
            }

            // ── media resolution ──────────────────────────────────────────────
            $final_media_type = null;
            $final_media_url  = null;
            $final_media_description = null;

            if ($media_type !== null) {
                if ($media_type === 'image') {
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        // New file uploaded
                        $uploadDir = __DIR__ . "/upload/";
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                        $ext        = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (!in_array($ext, $allowedExt, true)) {
                            throw new Exception("Invalid image format. Allowed: jpg, jpeg, png, gif, webp");
                        }
                        $newFilename = uniqid('quizimg_') . '.' . $ext;
                        $uploadPath  = $uploadDir . $newFilename;
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                            throw new Exception("Failed to upload image.");
                        }
                        $final_media_url  = "upload/" . $newFilename;
                        $final_media_type = 'image';

                    } elseif (!empty($media_url)) {
                        // Existing URL provided (no new file)
                        $final_media_url  = addslashes($media_url);
                        $final_media_type = 'image';
                    }
                    // FIX: if image type selected but no file and no url, store nothing
                    // (avoids orphaned media_type='image' with NULL media_url in DB)

                } elseif ($media_type === 'video' || $media_type === 'audio') {
                    if (empty($media_url)) {
                        throw new Exception("Media URL is required for {$media_type} type.");
                    }
                    $final_media_url  = addslashes($media_url);
                    $final_media_type = $media_type;
                }
            }

            // description only meaningful when media actually stored
            if ($final_media_type !== null && $media_description !== '') {
                $final_media_description = addslashes($media_description);
            }
            // ── insert question ───────────────────────────────────────────────
            $sql          = [];
            $sql['QUERY'] = "INSERT INTO `rcg_quiz_questions`
                             SET quiz_id=?, level=?, round=?, question_text=?, media_type=?, media_url=?, media_description=?,
                                 timer_override=?, status=?, is_last_question=?, created_by=?, row_status=?";
            $sql['PARAM'] = [
                ['FILD' => 'quiz_id',          'DATA' => $quiz_id,          'TYP' => 'i'],
                ['FILD' => 'level',            'DATA' => $level,            'TYP' => 'i'],
                ['FILD' => 'round',            'DATA' => $round,            'TYP' => 'i'],
                ['FILD' => 'question_text',    'DATA' => $question_text,    'TYP' => 's'],
                ['FILD' => 'media_type',       'DATA' => $final_media_type, 'TYP' => 's'],
                ['FILD' => 'media_url',        'DATA' => $final_media_url,  'TYP' => 's'],
                ['FILD' => 'media_description', 'DATA' => $final_media_description, 'TYP' => 's'],
                ['FILD' => 'timer_override',   'DATA' => $timer_override,   'TYP' => 'i'],
                ['FILD' => 'status',           'DATA' => $question_status,  'TYP' => 's'],
                ['FILD' => 'is_last_question', 'DATA' => $is_last_question, 'TYP' => 'i'],
                ['FILD' => 'created_by',       'DATA' => $created_by,       'TYP' => 'i'],
                ['FILD' => 'row_status',       'DATA' => 'A',               'TYP' => 's'],
            ];

            $newId = $mycms->sql_insert($sql);
            if (!$newId) throw new Exception("Failed to insert question.");

            // ── insert options ────────────────────────────────────────────────
            foreach ($options as $opt) {
                $optText   = addslashes(trim($opt['text'] ?? ''));
                $isCorrect = !empty($opt['is_correct']) ? 1 : 0;
                if ($optText === '') continue;

                $optSql          = [];
                $optSql['QUERY'] = "INSERT INTO `rcg_quiz_question_options`
                                    SET question_id=?, option_text=?, is_correct=?, created_by=?, updated_by=?";
                $optSql['PARAM'] = [
                    ['FILD' => 'question_id', 'DATA' => $newId,      'TYP' => 'i'],
                    ['FILD' => 'option_text', 'DATA' => $optText,    'TYP' => 's'],
                    ['FILD' => 'is_correct',  'DATA' => $isCorrect,  'TYP' => 'i'],
                    ['FILD' => 'created_by',  'DATA' => $created_by, 'TYP' => 'i'],
                    ['FILD' => 'updated_by',  'DATA' => $created_by, 'TYP' => 'i'],
                ];
                $mycms->sql_insert($optSql);
            }

            // FIX: return media_url so JS can sync its in-memory state
            sendJsonResponse(1, "Question added successfully", [
                'id'        => $newId,
                'media_url' => $final_media_url
            ]);

        } catch (Exception $e) {
            sendJsonResponse(0, $e->getMessage());
        }
        break;

    // ── EDIT QUESTION ─────────────────────────────────────────────────────────
    case 'edit_question':
        try {
            if (empty($_POST['question_id'])) {
                throw new Exception("Missing question ID.");
            }

            $question_id   = (int)$_POST['question_id'];
            $question_text = addslashes(trim($_POST['question_text'] ?? ''));
            if (empty($question_text)) {
                throw new Exception("Question text cannot be empty.");
            }

            // FIX: use $_POST exclusively — FormData and urlencoded bodies land here
            //      $_REQUEST also includes $_GET which could bleed in stale values
            $raw_media_type = strtolower(trim($_POST['media_type'] ?? ''));
            $media_url      = trim($_POST['media_url'] ?? '');
            $media_description = trim($_POST['media_description'] ?? '');
            $media_description_sent = array_key_exists('media_description', $_POST);

            $timer_override   = (isset($_POST['timer_override']) && $_POST['timer_override'] !== '')
                                    ? (int)$_POST['timer_override'] : null;
            $question_status  = 'active';
            $is_last_question = (isset($_POST['is_last_question']) && $_POST['is_last_question'] === 'Yes') ? 1 : 0;
            $updated_by       = $loggedUserID;
            $level            = (int)($_POST['level'] ?? 1);
            $round            = (int)($_POST['round'] ?? 1);

            // ── fetch quiz_id ─────────────────────────────────────────────────
            $qidSql          = [];
            $qidSql['QUERY'] = "SELECT quiz_id FROM `rcg_quiz_questions` WHERE id = ? LIMIT 1";
            $qidSql['PARAM'] = [['FILD' => 'id', 'DATA' => $question_id, 'TYP' => 'i']];
            $qidData         = $mycms->sql_select($qidSql);
            $quiz_id         = isset($qidData[0]['quiz_id']) ? (int)$qidData[0]['quiz_id'] : 0;

            assertQuizIsDraft($quiz_id, $mycms);

            // ── fetch existing media for fallback ─────────────────────────────
            $exSql          = [];
            $exSql['QUERY'] = "SELECT media_type, media_url,media_description FROM `rcg_quiz_questions` WHERE id = ? LIMIT 1";
            $exSql['PARAM'] = [['FILD' => 'id', 'DATA' => $question_id, 'TYP' => 'i']];
            $exRow          = $mycms->sql_select($exSql);
            $existing_media_type = isset($exRow[0]['media_type']) ? $exRow[0]['media_type'] : null;
            $existing_media_url  = isset($exRow[0]['media_url'])  ? $exRow[0]['media_url']  : null;
            $existing_media_description = isset($exRow[0]['media_description']) ? $exRow[0]['media_description'] : null;

            // ── reset last-question flag if needed ────────────────────────────
            if ($is_last_question === 1 && $quiz_id > 0) {
                $reset          = [];
                $reset['QUERY'] = "UPDATE `rcg_quiz_questions` SET is_last_question=0 WHERE quiz_id=? AND row_status!='D'";
                $reset['PARAM'] = [['FILD' => 'quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i']];
                $mycms->sql_update($reset);
            }

            // ── options validation ────────────────────────────────────────────
            $options = isset($_POST['options']) && is_array($_POST['options']) ? $_POST['options'] : [];
            if (count($options) < 2) {
                throw new Exception("Minimum 2 options are required.");
            }
            $hasCorrect = false;
            foreach ($options as $opt) {
                if (!empty($opt['is_correct'])) { $hasCorrect = true; break; }
            }
            if (!$hasCorrect) {
                throw new Exception("At least one option must be marked as correct.");
            }

            // ── fetch existing option IDs for delete-orphan logic ─────────────
            $optIdSql          = [];
            $optIdSql['QUERY'] = "SELECT id FROM `rcg_quiz_question_options` WHERE question_id = ?";
            $optIdSql['PARAM'] = [['FILD' => 'question_id', 'DATA' => $question_id, 'TYP' => 'i']];
            $existingOpts      = $mycms->sql_select($optIdSql);
            $existing_ids      = array_column($existingOpts, 'id');

            $posted_ids = [];
            foreach ($options as $opt) {
                if (!empty($opt['id'])) $posted_ids[] = (int)$opt['id'];
            }

            // ── upload dir ────────────────────────────────────────────────────
            $uploadDir = __DIR__ . "/upload/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // ── media resolution ──────────────────────────────────────────────
            //
            // JS signal contract (what the frontend sends for each case):
            //
            //   User explicitly removed media (removeMedia / removeImage):
            //     media_type = 'none'  →  NULL both DB columns
            //
            //   User set/kept a specific media type:
            //     media_type = 'image' | 'video' | 'audio'  →  process normally
            //
            //   User never touched media on this slide:
            //     media_type = '' (empty string)  →  keep whatever is in DB (no change)
            //
            $final_media_type = null;
            $final_media_url  = null;
            $final_media_description = null;

            if ($raw_media_type === 'none') {
                // Explicit removal — write NULLs
                $final_media_type = null;
                $final_media_url  = null;
                $final_media_description = null;

            } elseif (in_array($raw_media_type, ['image', 'video', 'audio'], true)) {
                // Media type was set / changed
                $final_media_type = $raw_media_type;

                if ($raw_media_type === 'image') {
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        // New file uploaded — move it
                        $fileTmp  = $_FILES['image']['tmp_name'];
                        $ext      = strtolower(pathinfo(basename($_FILES['image']['name']), PATHINFO_EXTENSION));
                        $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (!in_array($ext, $allowed, true)) {
                            throw new Exception("Invalid image format. Allowed: JPG, PNG, GIF, WEBP.");
                        }
                        $uniqueName = uniqid("media_") . "." . $ext;
                        if (!move_uploaded_file($fileTmp, $uploadDir . $uniqueName)) {
                            throw new Exception("Failed to upload image.");
                        }
                        $final_media_url = "upload/" . $uniqueName;

                    } elseif (!empty($media_url)) {
                        // No new file but a server path was sent — use it as-is
                        $final_media_url = addslashes($media_url);

                    } else {
                        // No new file, no URL sent → fall back to what DB already has.
                        // This covers the case where base64 preview was in JS memory
                        // but couldn't be sent (e.g. beacon/background save).
                        $final_media_url = $existing_media_url;
                    }

                    // FIX: if we still have no URL (e.g. first save with no file uploaded yet)
                    // don't store an orphaned media_type='image' with NULL url
                    if ($final_media_url === null || $final_media_url === '') {
                        $final_media_type = $existing_media_type; // keep whatever was there
                        $final_media_url  = $existing_media_url;
                    }

                } elseif ($raw_media_type === 'video' || $raw_media_type === 'audio') {
                    if (empty($media_url)) {
                        throw new Exception("Media URL is required for {$raw_media_type}.");
                    }
                    $final_media_url = addslashes($media_url);
                }

            } else {
                // Empty string → user never touched media → preserve DB values exactly
                $final_media_type = $existing_media_type;
                $final_media_url  = $existing_media_url;
                $final_media_description = $existing_media_description;
            }
            // description resolution for the set/changed branches
            if ($raw_media_type !== 'none' && in_array($raw_media_type, ['image', 'video', 'audio'], true)) {
                if ($media_description_sent) {
                    $final_media_description = ($media_description !== '') ? addslashes($media_description) : null;
                } else {
                    $final_media_description = $existing_media_description;
                }
                // if media itself got dropped (no url resolved), drop description too
                if ($final_media_type === null || $final_media_url === null || $final_media_url === '') {
                    $final_media_description = $existing_media_description;
                }
            }

            // ── update question ───────────────────────────────────────────────
            $sql          = [];
            $sql['QUERY'] = "UPDATE `rcg_quiz_questions`
                             SET level=?, round=?, question_text=?, media_type=?, media_url=?, media_description=?,
                                 timer_override=?, status=?, is_last_question=?, updated_by=?
                             WHERE id=?";
            $sql['PARAM'] = [
                ['FILD' => 'level',            'DATA' => $level,                                                                  'TYP' => 'i'],
                ['FILD' => 'round',            'DATA' => $round,                                                                  'TYP' => 'i'],
                ['FILD' => 'question_text',    'DATA' => $question_text,                                                          'TYP' => 's'],
                ['FILD' => 'media_type',       'DATA' => ($final_media_type !== null && $final_media_type !== '') ? $final_media_type : null, 'TYP' => 's'],
                ['FILD' => 'media_url',        'DATA' => ($final_media_url  !== null && $final_media_url  !== '') ? $final_media_url  : null, 'TYP' => 's'],
                ['FILD' => 'media_description', 'DATA' => ($final_media_description !== null && $final_media_description !== '') ? $final_media_description : null, 'TYP' => 's'],
                ['FILD' => 'timer_override',   'DATA' => $timer_override,                                                         'TYP' => 'i'],
                ['FILD' => 'status',           'DATA' => $question_status,                                                        'TYP' => 's'],
                ['FILD' => 'is_last_question', 'DATA' => $is_last_question,                                                       'TYP' => 'i'],
                ['FILD' => 'updated_by',       'DATA' => $updated_by,                                                             'TYP' => 'i'],
                ['FILD' => 'id',               'DATA' => $question_id,                                                            'TYP' => 'i'],
            ];
            $mycms->sql_update($sql);

            // ── delete removed options ────────────────────────────────────────
            $ids_to_delete = array_diff($existing_ids, $posted_ids);
            foreach ($ids_to_delete as $del_id) {
                $delSql          = [];
                $delSql['QUERY'] = "DELETE FROM `rcg_quiz_question_options` WHERE id = ?";
                $delSql['PARAM'] = [['FILD' => 'id', 'DATA' => $del_id, 'TYP' => 'i']];
                $mycms->sql_delete($delSql);
            }

            // ── upsert options ────────────────────────────────────────────────
            foreach ($options as $opt) {
                $text       = addslashes(trim($opt['text'] ?? ''));
                if ($text === '') continue;
                $is_correct = isset($opt['is_correct']) ? (int)$opt['is_correct'] : 0;

                if (!empty($opt['id'])) {
                    $oSql          = [];
                    $oSql['QUERY'] = "UPDATE `rcg_quiz_question_options`
                                      SET option_text=?, is_correct=?, updated_by=? WHERE id=?";
                    $oSql['PARAM'] = [
                        ['FILD' => 'option_text', 'DATA' => $text,           'TYP' => 's'],
                        ['FILD' => 'is_correct',  'DATA' => $is_correct,     'TYP' => 'i'],
                        ['FILD' => 'updated_by',  'DATA' => $updated_by,     'TYP' => 'i'],
                        ['FILD' => 'id',          'DATA' => (int)$opt['id'], 'TYP' => 'i'],
                    ];
                    $mycms->sql_update($oSql);
                } else {
                    $oSql          = [];
                    $oSql['QUERY'] = "INSERT INTO `rcg_quiz_question_options`
                                      SET question_id=?, option_text=?, is_correct=?, created_by=?, updated_by=?";
                    $oSql['PARAM'] = [
                        ['FILD' => 'question_id', 'DATA' => $question_id, 'TYP' => 'i'],
                        ['FILD' => 'option_text', 'DATA' => $text,        'TYP' => 's'],
                        ['FILD' => 'is_correct',  'DATA' => $is_correct,  'TYP' => 'i'],
                        ['FILD' => 'created_by',  'DATA' => $updated_by,  'TYP' => 'i'],
                        ['FILD' => 'updated_by',  'DATA' => $updated_by,  'TYP' => 'i'],
                    ];
                    $mycms->sql_insert($oSql);
                }
            }

            // FIX: return resolved media_url so JS can sync state
            sendJsonResponse(1, "Question updated successfully", [
                'id'        => $question_id,
                'media_url' => ($final_media_url !== null && $final_media_url !== '') ? $final_media_url : null
            ]);

        } catch (Exception $e) {
            sendJsonResponse(0, $e->getMessage());
        }
        break;

    // ── LIST QUESTIONS ────────────────────────────────────────────────────────
    case 'list_quiz_questions':
        $quiz_id = (int)(isset($_GET['quiz_id']) ? $_GET['quiz_id'] : 0);

        $sql            = [];
        $sql['QUERY']   = "SELECT * FROM `rcg_quiz_questions` WHERE quiz_id = ? AND row_status = ?";
        $sql['PARAM'][] = ['FILD' => 'quiz_id',    'DATA' => $quiz_id, 'TYP' => 'i'];
        $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'A',      'TYP' => 's'];
        $result         = $mycms->sql_select($sql);

        sendJsonResponse(1, "Success", ['questions' => $result]);
        break;

    // ── REMOVE (soft-delete) ──────────────────────────────────────────────────
    case 'Remove':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing ID to delete.");

            $question_id = (int)$_REQUEST['id'];
            $deleted_by  = $loggedUserID;

            $qSql          = [];
            $qSql['QUERY'] = "SELECT quiz_id FROM `rcg_quiz_questions` WHERE id = ? LIMIT 1";
            $qSql['PARAM'] = [['FILD' => 'id', 'DATA' => $question_id, 'TYP' => 'i']];
            $qData         = $mycms->sql_select($qSql);
            $quiz_id       = isset($qData[0]['quiz_id']) ? (int)$qData[0]['quiz_id'] : 0;

            assertQuizIsDraft($quiz_id, $mycms);

            $sql            = [];
            $sql['QUERY']   = "UPDATE `rcg_quiz_questions`
                               SET row_status=?, deleted_at=NOW(), deleted_by=? WHERE id=?";
            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D',          'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'deleted_by', 'DATA' => $deleted_by,  'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $question_id, 'TYP' => 'i'];
            $mycms->sql_update($sql);

            sendJsonResponse(1, "Question deleted successfully");
        } catch (Exception $e) {
            sendJsonResponse(0, $e->getMessage());
        }
        break;

    // ── ACTIVE ────────────────────────────────────────────────────────────────
    case 'Active':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing ID.");

            $question_id = (int)$_REQUEST['id'];
            $updated_by  = $loggedUserID;

            $sql            = [];
            $sql['QUERY']   = "UPDATE `rcg_quiz_questions`
                               SET row_status=?, updated_at=NOW(), updated_by=? WHERE id=?";
            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'A',          'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by,  'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $question_id, 'TYP' => 'i'];
            $mycms->sql_update($sql);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                sendJsonResponse(1, "Question activated successfully");
            } else {
                pageRedirection("manage_questions.php", 2);
            }
        } catch (Exception $e) {
            sendJsonResponse(0, $e->getMessage());
        }
        break;

    // ── INACTIVE ──────────────────────────────────────────────────────────────
    case 'Inactive':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing ID.");

            $question_id = (int)$_REQUEST['id'];
            $updated_by  = $loggedUserID;

            $sql            = [];
            $sql['QUERY']   = "UPDATE `rcg_quiz_questions`
                               SET row_status=?, updated_at=NOW(), updated_by=? WHERE id=?";
            $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'I',          'TYP' => 's'];
            $sql['PARAM'][] = ['FILD' => 'updated_by', 'DATA' => $updated_by,  'TYP' => 'i'];
            $sql['PARAM'][] = ['FILD' => 'id',         'DATA' => $question_id, 'TYP' => 'i'];
            $mycms->sql_update($sql);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                sendJsonResponse(1, "Question deactivated successfully");
            } else {
                pageRedirection("manage_questions.php", 2);
            }
        } catch (Exception $e) {
            sendJsonResponse(0, $e->getMessage());
        }
        break;

    // ── MULTI-ACTION ──────────────────────────────────────────────────────────
    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No questions selected.");
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
                        $sql['QUERY'] = "UPDATE `rcg_quiz_questions` SET row_status=?, updated_at=NOW(), updated_by=? WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'row_status', 'DATA' => 'A',         'TYP' => 's'],
                            ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i'],
                        ];
                        break;
                    case 'Inactive':
                        $sql['QUERY'] = "UPDATE `rcg_quiz_questions` SET row_status=?, updated_at=NOW(), updated_by=? WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'row_status', 'DATA' => 'I',         'TYP' => 's'],
                            ['FILD' => 'updated_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i'],
                        ];
                        break;
                    case 'Remove':
                        $sql['QUERY'] = "UPDATE `rcg_quiz_questions` SET row_status=?, deleted_at=NOW(), deleted_by=? WHERE id=?";
                        $sql['PARAM'] = [
                            ['FILD' => 'row_status', 'DATA' => 'D',         'TYP' => 's'],
                            ['FILD' => 'deleted_by', 'DATA' => $updated_by, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,         'TYP' => 'i'],
                        ];
                        break;
                    default:
                        throw new Exception("Invalid action selected.");
                }
                $mycms->sql_update($sql);
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                sendJsonResponse(1, "Action completed successfully");
            } else {
                $redirectCode = ($action === 'Remove') ? 3 : 2;
                pageRedirection("manage_questions.php", $redirectCode);
            }
        } catch (Exception $e) {
            sendJsonResponse(0, $e->getMessage());
        }
        break;

    // ── IMPORT FROM CSV ───────────────────────────────────────────────────────
    case 'import_questions':
        try {
            $quiz_id = (int)(isset($_POST['import_quiz_id']) ? $_POST['import_quiz_id'] : 0);
            if ($quiz_id <= 0) throw new Exception("Invalid quiz selected.");

            $quizCheck          = [];
            $quizCheck['QUERY'] = "SELECT * FROM rcg_quizzes WHERE id=? AND row_status='A'";
            $quizCheck['PARAM'] = [['FILD' => 'id', 'DATA' => $quiz_id, 'TYP' => 'i']];
            $quizData           = $mycms->sql_select($quizCheck);
            if (!$quizData) throw new Exception("Selected quiz does not exist or is inactive.");
            $quizData = $quizData[0];

            assertQuizIsDraft($quiz_id, $mycms);

            if (!isset($_FILES['question_file']) || $_FILES['question_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("CSV file is required.");
            }
            $fileExt = strtolower(pathinfo($_FILES['question_file']['name'], PATHINFO_EXTENSION));
            if ($fileExt !== 'csv') throw new Exception("Only CSV files are allowed.");

            $handle = fopen($_FILES['question_file']['tmp_name'], 'r');
            if (!$handle) throw new Exception("Cannot open uploaded CSV file.");

            $header = fgetcsv($handle);
            if (!$header) throw new Exception("CSV is empty or invalid.");
            $header = array_map(function ($h) {
                return strtolower(str_replace(' ', '_', trim($h)));
            }, $header);

            $expectedCols = ['rounds', 'levels', 'question', 'option_a', 'option_b', 'option_c', 'option_d',
                             'correct_option', 'extra_time', 'media_type', 'media_url'];
            foreach ($expectedCols as $col) {
                if (!in_array($col, $header, true)) throw new Exception("CSV missing required column: {$col}");
            }

            $created_by    = $loggedUserID;
            $validatedRows = [];
            $rowNum        = 1;
            $seenQuestions = [];

            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                $rowAssoc       = array_combine($header, $row);
                $round          = (int)(isset($rowAssoc['rounds'])   ? $rowAssoc['rounds']   : 1);
                $level          = (int)(isset($rowAssoc['levels'])   ? $rowAssoc['levels']   : 1);
                $question_text  = trim(isset($rowAssoc['question'])  ? $rowAssoc['question']  : '');
                $option_a       = trim(isset($rowAssoc['option_a'])  ? $rowAssoc['option_a']  : '');
                $option_b       = trim(isset($rowAssoc['option_b'])  ? $rowAssoc['option_b']  : '');
                $option_c       = trim(isset($rowAssoc['option_c'])  ? $rowAssoc['option_c']  : '');
                $option_d       = trim(isset($rowAssoc['option_d'])  ? $rowAssoc['option_d']  : '');
                $correct_option = strtoupper(trim(isset($rowAssoc['correct_option']) ? $rowAssoc['correct_option'] : ''));
                $timer_override = (isset($rowAssoc['extra_time']) && is_numeric($rowAssoc['extra_time']))
                                    ? (int)$rowAssoc['extra_time'] : null;
                $media_type     = strtolower(trim(isset($rowAssoc['media_type']) ? $rowAssoc['media_type'] : ''));
                $media_url      = trim(isset($rowAssoc['media_url'])  ? $rowAssoc['media_url']  : '');

                if ($round < 1 || $round > (int)$quizData['rounds']) {
                    throw new Exception("Row {$rowNum}: Invalid round {$round}. Max: {$quizData['rounds']}");
                }
                if ($level < 1 || $level > (int)$quizData['levels']) {
                    throw new Exception("Row {$rowNum}: Invalid level {$level}. Max: {$quizData['levels']}");
                }
                if (empty($question_text)) {
                    throw new Exception("Row {$rowNum}: Question text is required.");
                }

                $allowedTypes = ['image', 'audio', 'video'];
                if ($media_type !== '' && !in_array($media_type, $allowedTypes, true)) {
                    throw new Exception("Row {$rowNum}: Invalid media type '{$media_type}'.");
                }
                if ($media_type !== '') {
                    if (empty($media_url)) throw new Exception("Row {$rowNum}: Media URL required for '{$media_type}'.");
                    if (!filter_var($media_url, FILTER_VALIDATE_URL)) {
                        throw new Exception("Row {$rowNum}: Invalid media URL for '{$media_type}'.");
                    }
                    $allowedDomains = ['drive.google.com', 'firebasestorage.googleapis.com',
                                       'firebaseio.com', 'youtube.com', 'www.youtube.com', 'youtu.be'];
                    $host = strtolower(parse_url($media_url, PHP_URL_HOST) ?? '');
                    if (!in_array($host, $allowedDomains, true)) {
                        throw new Exception("Row {$rowNum}: Unsupported media host '{$host}'.");
                    }
                } else {
                    if (!empty($media_url)) {
                        throw new Exception("Row {$rowNum}: media_url provided without media_type.");
                    }
                }

                $qKey = strtolower($question_text);
                if (isset($seenQuestions[$qKey])) {
                    throw new Exception("Row {$rowNum}: Duplicate question in file: '{$question_text}'.");
                }
                $seenQuestions[$qKey] = true;

                $dupCheck          = [];
                $dupCheck['QUERY'] = "SELECT id FROM rcg_quiz_questions WHERE quiz_id=? AND TRIM(LOWER(question_text))=? AND row_status!='D' LIMIT 1";
                $dupCheck['PARAM'] = [
                    ['FILD' => 'quiz_id',       'DATA' => $quiz_id,                   'TYP' => 'i'],
                    ['FILD' => 'question_text', 'DATA' => strtolower($question_text), 'TYP' => 's'],
                ];
                if ($mycms->sql_select($dupCheck)) {
                    throw new Exception("Row {$rowNum}: Question already exists: '{$question_text}'.");
                }

                $validatedRows[] = [
                    'quiz_id'          => $quiz_id,
                    'level'            => $level,
                    'round'            => $round,
                    'question_text'    => addslashes($question_text),
                    'media_type'       => !empty($media_type) ? $media_type : null,
                    'media_url'        => !empty($media_url)  ? $media_url  : null,
                    'option_a'         => addslashes($option_a),
                    'option_b'         => addslashes($option_b),
                    'option_c'         => addslashes($option_c),
                    'option_d'         => addslashes($option_d),
                    'correct_option'   => $correct_option,
                    'timer_override'   => $timer_override,
                    'status'           => 'active',
                    'is_last_question' => 0,
                    'created_by'       => $created_by,
                    'row_status'       => 'A',
                ];
            }
            fclose($handle);

            foreach ($validatedRows as $data) {
                $sql          = [];
                $sql['QUERY'] = "INSERT INTO `rcg_quiz_questions`
                                 SET quiz_id=?, level=?, round=?, question_text=?, media_type=?, media_url=?,
                                     option_a=?, option_b=?, option_c=?, option_d=?,
                                     correct_option=?, timer_override=?, status=?,
                                     is_last_question=?, created_by=?, row_status=?";
                $sql['PARAM'] = [
                    ['FILD' => 'quiz_id',          'DATA' => $data['quiz_id'],          'TYP' => 'i'],
                    ['FILD' => 'level',            'DATA' => $data['level'],            'TYP' => 'i'],
                    ['FILD' => 'round',            'DATA' => $data['round'],            'TYP' => 'i'],
                    ['FILD' => 'question_text',    'DATA' => $data['question_text'],    'TYP' => 's'],
                    ['FILD' => 'media_type',       'DATA' => $data['media_type'],       'TYP' => 's'],
                    ['FILD' => 'media_url',        'DATA' => $data['media_url'],        'TYP' => 's'],
                    ['FILD' => 'option_a',         'DATA' => $data['option_a'],         'TYP' => 's'],
                    ['FILD' => 'option_b',         'DATA' => $data['option_b'],         'TYP' => 's'],
                    ['FILD' => 'option_c',         'DATA' => $data['option_c'],         'TYP' => 's'],
                    ['FILD' => 'option_d',         'DATA' => $data['option_d'],         'TYP' => 's'],
                    ['FILD' => 'correct_option',   'DATA' => $data['correct_option'],   'TYP' => 's'],
                    ['FILD' => 'timer_override',   'DATA' => $data['timer_override'],   'TYP' => 'i'],
                    ['FILD' => 'status',           'DATA' => $data['status'],           'TYP' => 's'],
                    ['FILD' => 'is_last_question', 'DATA' => $data['is_last_question'], 'TYP' => 'i'],
                    ['FILD' => 'created_by',       'DATA' => $data['created_by'],       'TYP' => 'i'],
                    ['FILD' => 'row_status',       'DATA' => $data['row_status'],       'TYP' => 's'],
                ];
                $mycms->sql_insert($sql);
            }

            sendJsonResponse(1, "All questions imported successfully.");
        } catch (Exception $e) {
            sendJsonResponse(0, "Import error: " . $e->getMessage());
        }
        break;

    // ── DEFAULT ───────────────────────────────────────────────────────────────
    default:
        sendJsonResponse(0, "Invalid action: " . htmlspecialchars($act));
        break;
}

// ─────────────────────────────────────────────────────────────────────────────
//  PAGE REDIRECTION UTILITY
// ─────────────────────────────────────────────────────────────────────────────
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms;

    $pageKey    = "_pgn_";
    $pageKeyVal = (isset($_REQUEST[$pageKey]) && $_REQUEST[$pageKey] !== "") ? $_REQUEST[$pageKey] : 0;

    $searchArray = [
        'src_question_text' => isset($_REQUEST['src_question_text'])
            ? urlencode(trim($_REQUEST['src_question_text'])) : '',
    ];

    $searchString = "&{$pageKey}={$pageKeyVal}";
    foreach ($searchArray as $key => $val) {
        if ($val !== '' && $val !== null) {
            $searchString .= "&{$key}={$val}";
        }
    }

    if (isset($_REQUEST['fr']) && $_REQUEST['fr'] == '1') {
        $fileName = 'quiz_details.php';
    }
    if (isset($_REQUEST['quiz_id'])) {
        $searchString .= "&quiz_id=" . (int)$_REQUEST['quiz_id'];
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}