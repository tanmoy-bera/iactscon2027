<?php
include_once('./includes/init.php');

$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];
date_default_timezone_set('Asia/Kolkata');

/*
 * TABLES
 *  rcg_cardiac_talks_polls        : id, group_id, title, is_active, is_published,
 *                                   start_time, end_time, created_by, created_at, updated_at (auto), updated_by,
 *                                   is_deleted, deleted_by, deleted_at
 *  rcg_cardiac_talks_poll_options : id, poll_id, option_text, vote_count, created_at
 *
 * FORM FIELDS (add/edit):
 *  poll_id (edit only), group_id, title, start_time, end_time, is_published (0/1),
 *  options[]  and  option_ids[]  (option_ids[] = existing option id, or empty/0 for a new option)
 */

/* ---------- Helper: collect + validate poll fields from POST ---------- */
function pollCollect()
{
    $groupId = (int)($_POST['group_id'] ?? 0);
    if ($groupId <= 0) throw new Exception("Please select a topic.");

    $title = trim($_POST['title'] ?? '');
    if ($title === '') throw new Exception("Poll title is required.");

    $opts = (isset($_POST['options']) && is_array($_POST['options'])) ? $_POST['options'] : [];
    $oids = (isset($_POST['option_ids']) && is_array($_POST['option_ids'])) ? $_POST['option_ids'] : [];
    $options = [];
    foreach ($opts as $i => $text) {
        $text = trim($text);
        if ($text === '') continue;
        $options[] = ['id' => (int)($oids[$i] ?? 0), 'text' => $text];
    }
    if (count($options) < 2) throw new Exception("At least 2 options are required.");

    $start = !empty($_POST['start_time']) ? date('Y-m-d H:i:s', strtotime($_POST['start_time'])) : date('Y-m-d H:i:s');
    if (empty($_POST['end_time'])) throw new Exception("End time is required.");
    $end   = date('Y-m-d H:i:s', strtotime($_POST['end_time']));
    if (strtotime($end) <= strtotime($start)) {
        throw new Exception("End time must be after start time.");
    }

    return [
        'group_id'       => $groupId,
        'title'          => $title,
        'start_time'     => $start,
        'end_time'       => $end,
        'is_published'   => !empty($_POST['is_published']) ? 1 : 0,
        'options'        => $options,
    ];
}

switch ($act) {

    /* ============================ CREATE ============================ */
    case 'add_poll':
        try {
            $d = pollCollect();

            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_cardiac_talks_polls`
                             SET group_id=?, title=?, is_active=1, is_published=?,
                                 start_time=?, end_time=?, created_by=?, is_deleted=0";
            $sql['PARAM'] = [
                ['FILD' => 'group_id',       'DATA' => $d['group_id'],       'TYP' => 'i'],
                ['FILD' => 'title',         'DATA' => $d['title'],          'TYP' => 's'],
                ['FILD' => 'is_published',   'DATA' => $d['is_published'],   'TYP' => 'i'],
                ['FILD' => 'start_time',     'DATA' => $d['start_time'],     'TYP' => 's'],
                ['FILD' => 'end_time',       'DATA' => $d['end_time'],       'TYP' => 's'],
                ['FILD' => 'created_by',     'DATA' => $loggedUserID,        'TYP' => 'i'],
            ];
            $pollId = (int)$mycms->sql_insert($sql);   // assumes sql_insert returns insert id

            if ($pollId <= 0) {                         // fallback if it does not
                $f = [];
                $f['QUERY']   = "SELECT MAX(id) AS id FROM `rcg_cardiac_talks_polls` WHERE created_by = ?";
                $f['PARAM'][] = ['FILD' => 'created_by', 'DATA' => $loggedUserID, 'TYP' => 'i'];
                $r = $mycms->sql_select($f);
                $pollId = (int)($r[0]['id'] ?? 0);
            }
            if ($pollId <= 0) throw new Exception("Poll could not be created.");

            foreach ($d['options'] as $opt) {
                $o = [];
                $o['QUERY'] = "INSERT INTO `rcg_cardiac_talks_poll_options` SET poll_id=?, option_text=?, vote_count=0";
                $o['PARAM'] = [
                    ['FILD' => 'poll_id',     'DATA' => $pollId,      'TYP' => 'i'],
                    ['FILD' => 'option_text', 'DATA' => $opt['text'], 'TYP' => 's'],
                ];
                $mycms->sql_insert($o);
            }

            setAlert('success', 'Poll added successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 1);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0, "&mode=add");
        }
        exit();

    /* ============================ UPDATE ============================ */
    case 'edit_poll':
        $id = (int)($_POST['poll_id'] ?? 0);
        try {
            if ($id <= 0) throw new Exception("Poll ID is required.");
            $d = pollCollect();

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_cardiac_talks_polls`
                             SET group_id=?, title=?, is_published=?,
                                 start_time=?, end_time=?, updated_by=?
                             WHERE id=? AND is_deleted=0";
            $sql['PARAM'] = [
                ['FILD' => 'group_id',       'DATA' => $d['group_id'],       'TYP' => 'i'],
                ['FILD' => 'title',         'DATA' => $d['title'],          'TYP' => 's'],
                ['FILD' => 'is_published',   'DATA' => $d['is_published'],   'TYP' => 'i'],
                ['FILD' => 'start_time',     'DATA' => $d['start_time'],     'TYP' => 's'],
                ['FILD' => 'end_time',       'DATA' => $d['end_time'],       'TYP' => 's'],
                ['FILD' => 'updated_by',     'DATA' => $loggedUserID,        'TYP' => 'i'],
                ['FILD' => 'id',             'DATA' => $id,                  'TYP' => 'i'],
            ];
            $mycms->sql_update($sql);

            // Sync options: update existing (vote_count preserved), insert new, delete removed
            $keepIds = [];
            foreach ($d['options'] as $opt) {
                if ($opt['id'] > 0) {
                    $u = [];
                    $u['QUERY'] = "UPDATE `rcg_cardiac_talks_poll_options` SET option_text=? WHERE id=? AND poll_id=?";
                    $u['PARAM'] = [
                        ['FILD' => 'option_text', 'DATA' => $opt['text'], 'TYP' => 's'],
                        ['FILD' => 'id',          'DATA' => $opt['id'],   'TYP' => 'i'],
                        ['FILD' => 'poll_id',     'DATA' => $id,          'TYP' => 'i'],
                    ];
                    $mycms->sql_update($u);
                    $keepIds[] = $opt['id'];
                } else {
                    $n = [];
                    $n['QUERY'] = "INSERT INTO `rcg_cardiac_talks_poll_options` SET poll_id=?, option_text=?, vote_count=0";
                    $n['PARAM'] = [
                        ['FILD' => 'poll_id',     'DATA' => $id,          'TYP' => 'i'],
                        ['FILD' => 'option_text', 'DATA' => $opt['text'], 'TYP' => 's'],
                    ];
                    $mycms->sql_insert($n);
                }
            }
            // Delete options the admin removed (only the ids that were sent for this poll and not kept)
            $submitted = (isset($_POST['option_ids']) && is_array($_POST['option_ids'])) ? array_map('intval', $_POST['option_ids']) : [];
            $removed   = array_diff(array_filter($submitted), $keepIds);
            // Also catch options whose text was blanked out in the form
            $existing = [];
            $q = [];
            $q['QUERY']   = "SELECT id FROM `rcg_cardiac_talks_poll_options` WHERE poll_id = ?";
            $q['PARAM'][] = ['FILD' => 'poll_id', 'DATA' => $id, 'TYP' => 'i'];
            $rows = $mycms->sql_select($q);
            if ($rows) foreach ($rows as $r) $existing[] = (int)$r['id'];
            $toDelete = array_diff($existing, $keepIds);
            // Newly inserted options are not in $keepIds, so only delete ids that were part of the submitted form
            $toDelete = array_intersect($toDelete, $submitted);
            foreach ($toDelete as $delId) {
                $x = [];
                $x['QUERY'] = "DELETE FROM `rcg_cardiac_talks_poll_options` WHERE id=? AND poll_id=?";
                $x['PARAM'] = [
                    ['FILD' => 'id',      'DATA' => $delId, 'TYP' => 'i'],
                    ['FILD' => 'poll_id', 'DATA' => $id,    'TYP' => 'i'],
                ];
                $mycms->sql_delete($x);
            }

            setAlert('success', 'Poll updated successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            $back = ($id > 0) ? "&edit_poll=" . $id : "";
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0, $back);
        }
        exit();

    /* ============================ DELETE (soft) ============================ */
    case 'Remove':
        try {
            if (empty($_GET['id'])) throw new Exception("Poll ID is required.");
            $id = (int)$_GET['id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_cardiac_talks_polls` SET is_deleted=1, deleted_by=?, deleted_at=NOW()
                             WHERE id=? AND is_deleted=0";
            $sql['PARAM'] = [
                ['FILD' => 'deleted_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i'],
            ];
            $mycms->sql_update($sql);

            setAlert('success', 'Poll deleted successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 3);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
        }
        exit();

    /* ============================ ACTIVE / INACTIVE / PUBLISH / UNPUBLISH ============================ */
    case 'Active':
    case 'Inactive':
    case 'Publish':
    case 'Unpublish':
        try {
            if (empty($_REQUEST['id'])) throw new Exception("Missing Poll ID.");
            $id = (int)$_REQUEST['id'];

            $col = ($act == 'Active' || $act == 'Inactive') ? 'is_active' : 'is_published';
            $val = ($act == 'Active' || $act == 'Publish') ? 1 : 0;

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_cardiac_talks_polls` SET $col=?, updated_by=? WHERE id=? AND is_deleted=0";
            $sql['PARAM'] = [
                ['FILD' => $col,         'DATA' => $val,          'TYP' => 'i'],
                ['FILD' => 'updated_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i'],
            ];
            $mycms->sql_update($sql);

            $doneMap = ['Active' => 'marked as active', 'Inactive' => 'marked as inactive', 'Publish' => 'published', 'Unpublish' => 'unpublished'];
            setAlert('success', 'Poll ' . $doneMap[$act] . ' successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
        }
        exit();

    /* ============================ MULTI ACTION ============================ */
    case 'MultiAction':
        try {
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) throw new Exception("No polls selected.");
            if (empty($_POST['action'])) throw new Exception("Please choose an action.");

            $ids    = array_map('intval', $_POST['ids']);
            $action = $_POST['action'];

            $count = 0;
            foreach ($ids as $id) {
                if ($id <= 0) continue;
                $sql = [];
                switch ($action) {
                    case 'Active':
                    case 'Inactive':
                        $sql['QUERY'] = "UPDATE `rcg_cardiac_talks_polls` SET is_active=?, updated_by=? WHERE id=? AND is_deleted=0";
                        $sql['PARAM'] = [
                            ['FILD' => 'is_active',  'DATA' => ($action == 'Active' ? 1 : 0), 'TYP' => 'i'],
                            ['FILD' => 'updated_by', 'DATA' => $loggedUserID,                 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,                           'TYP' => 'i'],
                        ];
                        break;
                    case 'Publish':
                    case 'Unpublish':
                        $sql['QUERY'] = "UPDATE `rcg_cardiac_talks_polls` SET is_published=?, updated_by=? WHERE id=? AND is_deleted=0";
                        $sql['PARAM'] = [
                            ['FILD' => 'is_published', 'DATA' => ($action == 'Publish' ? 1 : 0), 'TYP' => 'i'],
                            ['FILD' => 'updated_by',   'DATA' => $loggedUserID,                  'TYP' => 'i'],
                            ['FILD' => 'id',           'DATA' => $id,                            'TYP' => 'i'],
                        ];
                        break;
                    case 'Remove':
                        $sql['QUERY'] = "UPDATE `rcg_cardiac_talks_polls` SET is_deleted=1, deleted_at=NOW(), deleted_by=? WHERE id=? AND is_deleted=0";
                        $sql['PARAM'] = [
                            ['FILD' => 'deleted_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                            ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i'],
                        ];
                        break;
                    default:
                        throw new Exception("Invalid action selected.");
                }
                $mycms->sql_update($sql);
                $count++;
            }

            $verb = ['Active' => 'marked as active', 'Inactive' => 'marked as inactive', 'Publish' => 'published', 'Unpublish' => 'unpublished', 'Remove' => 'removed'];
            setAlert('success', $count . ' poll(s) ' . $verb[$action] . ' successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", ($action == 'Remove' ? 3 : 2));
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
        }
        exit();

    /* ============================ RESET VOTES ============================ */
    case 'reset_poll':
        try {
            if (empty($_REQUEST['poll_id'])) throw new Exception("Poll ID is required.");
            $pollId = (int)$_REQUEST['poll_id'];

            $sql = [];
            $sql['QUERY']   = "UPDATE `rcg_cardiac_talks_poll_options` SET vote_count=0 WHERE poll_id=?";
            $sql['PARAM'][] = ['FILD' => 'poll_id', 'DATA' => $pollId, 'TYP' => 'i'];
            $mycms->sql_update($sql);

            // Optional: clear live poll data in Firebase, same as quiz reset (remove if you don't use it for polls)
            try {
                if (!class_exists('FirebaseService')) {
                    require_once __DIR__ . '/includes/FirebaseService.php';
                }
                $firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);
                $firebase->delete("live_poll/$pollId");
            } catch (Exception $e) {
                error_log("Firebase reset error for poll {$pollId}: " . $e->getMessage());
            }

            setAlert('success', 'Poll votes reset successfully.');
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 2);
        } catch (Exception $e) {
            setAlert('error', $e->getMessage());
            pageRedirection("cardiac_talks_discussion_quiz_poll.php", 0);
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

/* Only these pages are allowed as redirect target (security) */
function getReturnPage($default = "cardiac_talks_discussion_quiz_poll.php")
{
    $allowed = ['cardiac_talks_discussion_quiz_poll.php'];

    // 1) explicit return_to (hidden field / link param)
    $page = basename($_REQUEST['return_to'] ?? '');
    if (in_array($page, $allowed, true)) return $page;

    // 2) fallback: the page the request came from
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $page = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
        if (in_array($page, $allowed, true)) return $page;
    }

    // 3) default
    return $default;
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms;
    $pgn  = (isset($_REQUEST['_pgn_']) && $_REQUEST['_pgn_'] !== '') ? (int)$_REQUEST['_pgn_'] : 0;
    $page = getReturnPage($fileName);   // $fileName only used as default now
    $mycms->redirect($page . "?m=" . $messageCode . $additionalString . "&tab=poll&_pgn_=" . $pgn);
}