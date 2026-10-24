<?php
/*==================================================================
   cardiac_talks_discussion.process.php
   Discussion post status handler (admin can only change status)

   Actions
     GET  act=PostActive   &id=<post id>&back=<return url>   -> post status = 'A'
     GET  act=PostInactive &id=<post id>&back=<return url>   -> post status = 'I'
     POST act=MultiAction  &action=Active|Inactive&ids[]=..  -> bulk status change

   Table: rcg_discussion_posts   (status: A = active, I = inactive, D = deleted)
==================================================================*/
include_once("./includes/source.php");
include_once('./includes/init.php');

/* ---------- Helpers ---------- */

/* Run an UPDATE through the $mycms wrapper. Adjust here if your wrapper's method name differs. */
function discRunUpdate($mycms, $sql)
{
    if (method_exists($mycms, 'sql_update')) {
        return $mycms->sql_update($sql, false);
    }
    if (method_exists($mycms, 'sql_query')) {
        return $mycms->sql_query($sql);
    }
    return false;
}

/* Status change for a list of post ids. Returns number of ids sent. */
function discSetPostStatus($mycms, array $ids, $status)
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    if (!$ids || !in_array($status, ['A', 'I'], true)) {
        return 0;
    }

    $sql = [];
    $sql['QUERY'] = "UPDATE `rcg_discussion_posts`
        SET `status` = ?, `updated_at` = NOW(), `updated_by_ip` = ?
        WHERE `id` IN (" . implode(',', $ids) . ") AND `status` != 'D'";
    $sql['PARAM'][] = ['FILD' => 'status',        'DATA' => $status,                      'TYP' => 's'];
    $sql['PARAM'][] = ['FILD' => 'updated_by_ip', 'DATA' => ($_SERVER['REMOTE_ADDR'] ?? ''), 'TYP' => 's'];
    discRunUpdate($mycms, $sql);

    return count($ids);
}

/* Only allow redirecting back to a page of this folder (e.g. "page.php?tab=discussion&...") */
function discSafeBack($back)
{
    $back = (string)$back;
    if (preg_match('#^[A-Za-z0-9_\-]+\.php\?#', $back)) {
        return $back;
    }
    return 'cardiac_talks_discussion_quiz_poll.php?tab=discussion';
}

function discFlash($type, $message)
{
    $_SESSION['cardiac_alert'] = ['type' => $type, 'message' => $message];
}

/* ---------- Router ---------- */
$act = $_REQUEST['act'] ?? '';

switch ($act) {

    /* ----- Single post: Make Active / Make Inactive ----- */
    case 'PostActive':
    case 'PostInactive':
        $postId = (int)($_REQUEST['id'] ?? 0);
        $status = ($act === 'PostActive') ? 'A' : 'I';

        if ($postId > 0 && discSetPostStatus($mycms, [$postId], $status)) {
            discFlash('success', 'Post #' . $postId . ' is now ' . ($status === 'A' ? 'active' : 'inactive') . '.');
        } else {
            discFlash('error', 'Invalid post.');
        }
        header('Location: ' . discSafeBack($_REQUEST['back'] ?? ''));
        exit;

    /* ----- Bulk: called by AJAX from the listing page ----- */
    case 'MultiAction':
        $action = $_POST['action'] ?? '';
        $ids    = (array)($_POST['ids'] ?? []);
        $map    = ['Active' => 'A', 'Inactive' => 'I'];

        if (!isset($map[$action]) || !$ids) {
            http_response_code(400);
            discFlash('error', 'Invalid bulk action.');
            echo 'invalid';
            exit;
        }

        $count = discSetPostStatus($mycms, $ids, $map[$action]);
        discFlash('success', $count . ' post(s) made ' . strtolower($action) . '.');
        echo 'ok';
        exit;

    default:
        header('Location: cardiac_talks_discussion_quiz_poll.php?tab=discussion');
        exit;
}