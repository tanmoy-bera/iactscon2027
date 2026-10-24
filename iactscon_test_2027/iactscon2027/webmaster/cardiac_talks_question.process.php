<?php
include_once('./includes/init.php');

$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];
date_default_timezone_set('Asia/Kolkata');

/*
 * TABLES
 *  rcg_cardiac_talk_quizzes               : quiz master
 *  rcg_cardiac_talk_quiz_questions        : questions (option_a..d + correct_option kept in sync for the first 4 options)
 *  rcg_cardiac_talk_quiz_question_options : every option of a question (option_text, is_correct)
 *
 * CALLED BY cardiac_talks_quiz_questions.php (AJAX, always answers JSON)
 *  act = add_question | edit_question | Remove
 *
 * IMAGE UPLOADS
 *  Saved under CQ_UPLOAD_DIR (filesystem) and stored as CQ_UPLOAD_URL . filename (relative to the webmaster folder).
 *  Change the two constants below if your uploads live somewhere else.
 */
define('CQ_UPLOAD_DIR', __DIR__ . '/uploads/cardiac_quiz/');
define('CQ_UPLOAD_URL', 'uploads/cardiac_quiz/');

function cqJson($arr)
{
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit();
}

/* ---------- Save an uploaded image, return its relative URL or null ---------- */
function cqSaveImage()
{
    if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $f = $_FILES['image'];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        throw new Exception("Only JPG, PNG, GIF or WEBP images are allowed.");
    }
    if (@getimagesize($f['tmp_name']) === false) {
        throw new Exception("The uploaded file is not a valid image.");
    }
    if ($f['size'] > 5 * 1024 * 1024) {
        throw new Exception("Image must be smaller than 5 MB.");
    }
    if (!is_dir(CQ_UPLOAD_DIR) && !@mkdir(CQ_UPLOAD_DIR, 0755, true)) {
        throw new Exception("Upload folder could not be created.");
    }
    $name = 'cq_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], CQ_UPLOAD_DIR . $name)) {
        throw new Exception("Image could not be saved.");
    }
    return CQ_UPLOAD_URL . $name;
}

/* ---------- Collect + validate the question fields from POST ---------- */
function cqCollect()
{
    $quizId = (int)($_POST['quiz_id'] ?? 0);
    if ($quizId <= 0) throw new Exception("Quiz ID is required.");

    $text = trim($_POST['question_text'] ?? '');
    if ($text === '') throw new Exception("Question text is required.");

    // Options: keep the non-empty ones, remember the position they had on the page
    $raw = (isset($_POST['options']) && is_array($_POST['options'])) ? $_POST['options'] : [];
    $options = [];
    foreach ($raw as $idx => $o) {
        $t = trim($o['text'] ?? '');
        if ($t === '') continue;
        $options[] = [
            'orig'       => (int)$idx,
            'text'       => $t,
            'is_correct' => !empty($o['is_correct']) ? 1 : 0,
        ];
    }
    if (count($options) < 2) throw new Exception("At least 2 options are required.");

    // exactly one correct option (fall back to the first one)
    $correctPos = -1;
    foreach ($options as $i => $o) {
        if ($o['is_correct'] && $correctPos === -1) $correctPos = $i;
    }
    if ($correctPos === -1) $correctPos = 0;
    foreach ($options as $i => $o) {
        $options[$i]['is_correct'] = ($i === $correctPos) ? 1 : 0;
    }

    $mediaType = strtolower(trim($_POST['media_type'] ?? ''));
    if (!in_array($mediaType, ['image', 'video', 'audio', 'none'])) $mediaType = '';

    return [
        'quiz_id'          => $quizId,
        'question_text'    => $text,
        'round'            => max(1, (int)($_POST['round'] ?? 1)),
        'level'            => max(1, (int)($_POST['level'] ?? 1)),
        'timer_override'   => max(0, (int)($_POST['timer_override'] ?? 0)),
        'is_last_question' => (strtolower(trim($_POST['is_last_question'] ?? '')) === 'yes') ? 1 : 0,
        'media_type'       => $mediaType,
        'media_url'        => trim($_POST['media_url'] ?? ''),
        'media_desc'       => trim($_POST['media_description'] ?? ''),
        'options'          => $options,
        'correct_pos'      => $correctPos,
    ];
}

/* ---------- Write the options table, return [original page index => option id] ---------- */
function cqSyncOptions($mycms, $questionId, $options, $userId)
{
    $sel = [];
    $sel['QUERY']   = "SELECT id FROM `rcg_cardiac_talk_quiz_question_options` WHERE question_id = ? ORDER BY id ASC";
    $sel['PARAM'][] = ['FILD' => 'question_id', 'DATA' => $questionId, 'TYP' => 'i'];
    $existing = $mycms->sql_select($sel) ?: [];

    $map = [];
    foreach ($options as $i => $o) {
        if (isset($existing[$i])) {
            $optId = (int)$existing[$i]['id'];
            $u = [];
            $u['QUERY'] = "UPDATE `rcg_cardiac_talk_quiz_question_options` SET option_text=?, is_correct=?, updated_by=? WHERE id=? AND question_id=?";
            $u['PARAM'] = [
                ['FILD' => 'option_text', 'DATA' => $o['text'],       'TYP' => 's'],
                ['FILD' => 'is_correct',  'DATA' => $o['is_correct'], 'TYP' => 'i'],
                ['FILD' => 'updated_by',  'DATA' => $userId,          'TYP' => 'i'],
                ['FILD' => 'id',          'DATA' => $optId,           'TYP' => 'i'],
                ['FILD' => 'question_id', 'DATA' => $questionId,      'TYP' => 'i'],
            ];
            $mycms->sql_update($u);
        } else {
            $n = [];
            $n['QUERY'] = "INSERT INTO `rcg_cardiac_talk_quiz_question_options` SET question_id=?, option_text=?, is_correct=?, created_by=?";
            $n['PARAM'] = [
                ['FILD' => 'question_id', 'DATA' => $questionId,      'TYP' => 'i'],
                ['FILD' => 'option_text', 'DATA' => $o['text'],       'TYP' => 's'],
                ['FILD' => 'is_correct',  'DATA' => $o['is_correct'], 'TYP' => 'i'],
                ['FILD' => 'created_by',  'DATA' => $userId,          'TYP' => 'i'],
            ];
            $optId = (int)$mycms->sql_insert($n);   // assumes sql_insert returns insert id
            if ($optId <= 0) {                       // fallback if it does not
                $f = [];
                $f['QUERY']   = "SELECT MAX(id) AS id FROM `rcg_cardiac_talk_quiz_question_options` WHERE question_id = ?";
                $f['PARAM'][] = ['FILD' => 'question_id', 'DATA' => $questionId, 'TYP' => 'i'];
                $r = $mycms->sql_select($f);
                $optId = (int)($r[0]['id'] ?? 0);
            }
        }
        $map[$o['orig']] = $optId;
    }

    // remove surplus options that are no longer submitted
    for ($i = count($options); $i < count($existing); $i++) {
        $x = [];
        $x['QUERY'] = "DELETE FROM `rcg_cardiac_talk_quiz_question_options` WHERE id=? AND question_id=?";
        $x['PARAM'] = [
            ['FILD' => 'id',          'DATA' => (int)$existing[$i]['id'], 'TYP' => 'i'],
            ['FILD' => 'question_id', 'DATA' => $questionId,              'TYP' => 'i'],
        ];
        $mycms->sql_delete($x);
    }
    return $map;
}

/* ---------- option_a..d + correct_option on the question row ---------- */
function cqLetterCols($options, $correctPos)
{
    $cols = ['option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null];
    $keys = array_keys($cols);
    foreach ($options as $i => $o) {
        if ($i > 3) break;
        $cols[$keys[$i]] = mb_substr($o['text'], 0, 255);
    }
    $cols['correct_option'] = ($correctPos <= 3) ? ['A', 'B', 'C', 'D'][$correctPos] : null;
    return $cols;
}

switch ($act) {

    /* ============================ ADD ============================ */
    case 'add_question':
        try {
            $d = cqCollect();

            // the quiz must exist
            $q = [];
            $q['QUERY']   = "SELECT id FROM `rcg_cardiac_talk_quizzes` WHERE id = ? AND row_status != 'D'";
            $q['PARAM'][] = ['FILD' => 'id', 'DATA' => $d['quiz_id'], 'TYP' => 'i'];
            if (!$mycms->sql_select($q)) throw new Exception("Quiz not found.");

            // media
            $mediaType = null;
            $mediaUrl  = null;
            if ($d['media_type'] === 'image') {
                $up = cqSaveImage();
                $mediaUrl = $up ?: ($d['media_url'] !== '' ? $d['media_url'] : null);
                $mediaType = $mediaUrl ? 'image' : null;
            } elseif (in_array($d['media_type'], ['video', 'audio']) && $d['media_url'] !== '') {
                $mediaType = $d['media_type'];
                $mediaUrl  = $d['media_url'];
            }
            $mediaDesc = ($mediaType && $d['media_desc'] !== '') ? $d['media_desc'] : null;

            $L = cqLetterCols($d['options'], $d['correct_pos']);

            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_cardiac_talk_quiz_questions`
                             SET quiz_id=?, level=?, round=?, question_text=?, media_type=?, media_description=?, media_url=?,
                                 option_a=?, option_b=?, option_c=?, option_d=?, correct_option=?,
                                 timer_override=?, status='active', created_by=?, row_status='A', is_last_question=?";
            $sql['PARAM'] = [
                ['FILD' => 'quiz_id',           'DATA' => $d['quiz_id'],        'TYP' => 'i'],
                ['FILD' => 'level',             'DATA' => $d['level'],          'TYP' => 'i'],
                ['FILD' => 'round',             'DATA' => $d['round'],          'TYP' => 'i'],
                ['FILD' => 'question_text',     'DATA' => $d['question_text'],  'TYP' => 's'],
                ['FILD' => 'media_type',        'DATA' => $mediaType,           'TYP' => 's'],
                ['FILD' => 'media_description', 'DATA' => $mediaDesc,           'TYP' => 's'],
                ['FILD' => 'media_url',         'DATA' => $mediaUrl,            'TYP' => 's'],
                ['FILD' => 'option_a',          'DATA' => $L['option_a'],       'TYP' => 's'],
                ['FILD' => 'option_b',          'DATA' => $L['option_b'],       'TYP' => 's'],
                ['FILD' => 'option_c',          'DATA' => $L['option_c'],       'TYP' => 's'],
                ['FILD' => 'option_d',          'DATA' => $L['option_d'],       'TYP' => 's'],
                ['FILD' => 'correct_option',    'DATA' => $L['correct_option'], 'TYP' => 's'],
                ['FILD' => 'timer_override',    'DATA' => $d['timer_override'], 'TYP' => 'i'],
                ['FILD' => 'created_by',        'DATA' => $loggedUserID,        'TYP' => 'i'],
                ['FILD' => 'is_last_question',  'DATA' => $d['is_last_question'], 'TYP' => 'i'],
            ];
            $questionId = (int)$mycms->sql_insert($sql);   // assumes sql_insert returns insert id

            if ($questionId <= 0) {                        // fallback if it does not
                $f = [];
                $f['QUERY'] = "SELECT MAX(id) AS id FROM `rcg_cardiac_talk_quiz_questions` WHERE quiz_id = ? AND created_by = ?";
                $f['PARAM'] = [
                    ['FILD' => 'quiz_id',    'DATA' => $d['quiz_id'], 'TYP' => 'i'],
                    ['FILD' => 'created_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                ];
                $r = $mycms->sql_select($f);
                $questionId = (int)($r[0]['id'] ?? 0);
            }
            if ($questionId <= 0) throw new Exception("Question could not be created.");

            $optMap = cqSyncOptions($mycms, $questionId, $d['options'], $loggedUserID);

            cqJson([
                'status'     => 1,
                'id'         => $questionId,
                'message'    => 'Question added successfully.',
                'media_url'  => $mediaUrl,
                'option_ids' => $optMap,
            ]);
        } catch (Exception $e) {
            cqJson(['status' => 0, 'message' => $e->getMessage()]);
        }
        break;

    /* ============================ EDIT ============================ */
    case 'edit_question':
        try {
            $id = (int)($_POST['question_id'] ?? 0);
            if ($id <= 0) throw new Exception("Question ID is required.");
            $d = cqCollect();

            // existing media (kept when the page could not send the new image yet)
            $e = [];
            $e['QUERY']   = "SELECT media_type, media_url FROM `rcg_cardiac_talk_quiz_questions` WHERE id = ? AND quiz_id = ? AND row_status != 'D'";
            $e['PARAM'] = [
                ['FILD' => 'id',      'DATA' => $id,           'TYP' => 'i'],
                ['FILD' => 'quiz_id', 'DATA' => $d['quiz_id'], 'TYP' => 'i'],
            ];
            $cur = $mycms->sql_select($e);
            if (!$cur) throw new Exception("Question not found.");
            $curType = $cur[0]['media_type'] ?: null;
            $curUrl  = $cur[0]['media_url'] ?: null;

            $mediaType = null;
            $mediaUrl  = null;
            if ($d['media_type'] === 'none') {
                // media removed on the page
            } elseif ($d['media_type'] === 'image') {
                $up = cqSaveImage();
                if ($up) {
                    $mediaUrl = $up;
                } elseif ($d['media_url'] !== '') {
                    $mediaUrl = $d['media_url'];
                } elseif ($curType === 'image') {
                    $mediaUrl = $curUrl;            // new image not uploaded yet: keep the old one
                }
                $mediaType = $mediaUrl ? 'image' : null;
            } elseif (in_array($d['media_type'], ['video', 'audio']) && $d['media_url'] !== '') {
                $mediaType = $d['media_type'];
                $mediaUrl  = $d['media_url'];
            } else {
                // page sent no media info: leave the stored media untouched
                $mediaType = $curType;
                $mediaUrl  = $curUrl;
            }
            $mediaDesc = ($mediaType && $d['media_desc'] !== '') ? $d['media_desc'] : null;

            $L = cqLetterCols($d['options'], $d['correct_pos']);

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_cardiac_talk_quiz_questions`
                             SET level=?, round=?, question_text=?, media_type=?, media_description=?, media_url=?,
                                 option_a=?, option_b=?, option_c=?, option_d=?, correct_option=?,
                                 timer_override=?, updated_by=?, is_last_question=?
                             WHERE id=? AND quiz_id=? AND row_status!='D'";
            $sql['PARAM'] = [
                ['FILD' => 'level',             'DATA' => $d['level'],          'TYP' => 'i'],
                ['FILD' => 'round',             'DATA' => $d['round'],          'TYP' => 'i'],
                ['FILD' => 'question_text',     'DATA' => $d['question_text'],  'TYP' => 's'],
                ['FILD' => 'media_type',        'DATA' => $mediaType,           'TYP' => 's'],
                ['FILD' => 'media_description', 'DATA' => $mediaDesc,           'TYP' => 's'],
                ['FILD' => 'media_url',         'DATA' => $mediaUrl,            'TYP' => 's'],
                ['FILD' => 'option_a',          'DATA' => $L['option_a'],       'TYP' => 's'],
                ['FILD' => 'option_b',          'DATA' => $L['option_b'],       'TYP' => 's'],
                ['FILD' => 'option_c',          'DATA' => $L['option_c'],       'TYP' => 's'],
                ['FILD' => 'option_d',          'DATA' => $L['option_d'],       'TYP' => 's'],
                ['FILD' => 'correct_option',    'DATA' => $L['correct_option'], 'TYP' => 's'],
                ['FILD' => 'timer_override',    'DATA' => $d['timer_override'], 'TYP' => 'i'],
                ['FILD' => 'updated_by',        'DATA' => $loggedUserID,        'TYP' => 'i'],
                ['FILD' => 'is_last_question',  'DATA' => $d['is_last_question'], 'TYP' => 'i'],
                ['FILD' => 'id',                'DATA' => $id,                  'TYP' => 'i'],
                ['FILD' => 'quiz_id',           'DATA' => $d['quiz_id'],        'TYP' => 'i'],
            ];
            $mycms->sql_update($sql);

            $optMap = cqSyncOptions($mycms, $id, $d['options'], $loggedUserID);

            cqJson([
                'status'     => 1,
                'id'         => $id,
                'message'    => 'Question updated successfully.',
                'media_url'  => $mediaUrl,
                'option_ids' => $optMap,
            ]);
        } catch (Exception $e) {
            cqJson(['status' => 0, 'message' => $e->getMessage()]);
        }
        break;

    /* ============================ REMOVE ============================ */
    case 'Remove':
        try {
            $id = (int)($_REQUEST['id'] ?? 0);
            if ($id <= 0) throw new Exception("Question ID is required.");

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_cardiac_talk_quiz_questions`
                             SET row_status='D', deleted_by=?, deleted_at=NOW()
                             WHERE id=? AND row_status!='D'";
            $sql['PARAM'] = [
                ['FILD' => 'deleted_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i'],
            ];
            $mycms->sql_update($sql);

            cqJson(['status' => 1, 'message' => 'Question deleted.']);
        } catch (Exception $e) {
            cqJson(['status' => 0, 'message' => $e->getMessage()]);
        }
        break;

    default:
        cqJson(['status' => 0, 'message' => 'Invalid request.']);
}