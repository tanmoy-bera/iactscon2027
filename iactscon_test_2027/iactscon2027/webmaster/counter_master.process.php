<?php
include_once('includes/init.php');

header('Content-Type: application/json');

define('CM_TABLE', 'rcg_id_print_box_master_for_delegate');
define('CM_BOXES_PER_COUNTER', 10);   // A-1 ... A-10, then B-1
define('CM_LAST_COUNTER', 'Z');

function cmJson($status, $message, $data = array())
{
    echo json_encode(array('status' => $status, 'message' => $message, 'data' => $data));
    exit;
}

function cmAudit()
{
    return array(
        'user' => isset($_SESSION['userId']) ? (int)$_SESSION['userId'] : (isset($_SESSION['USER_ID']) ? (int)$_SESSION['USER_ID'] : 1),
        'ip'   => $_SERVER['REMOTE_ADDR'],
        'sess' => session_id(),
        'now'  => date('Y-m-d H:i:s'),
    );
}

/**
 * Works out the next box from the table:
 * continues the last counter until it has 10 boxes, then moves to the next letter.
 * Returns array(box_number, counter_number, sequenceBy) or null when counters are exhausted.
 */
function cmNextBox()
{
    global $mycms;

    $sql = array();
    $sql['QUERY'] = "SELECT `box_number`, `counter_number`, `sequenceBy`
                       FROM `" . CM_TABLE . "`
                   ORDER BY `sequenceBy` DESC, `id` DESC
                      LIMIT 1";
    $res = $mycms->sql_select($sql);

    if (!$res) {
        return array('box_number' => 'A - 1', 'counter_number' => 'A', 'sequenceBy' => 1);
    }

    $lastCounter = strtoupper(trim($res[0]['counter_number']));
    $parts       = explode('-', $res[0]['box_number']);
    $lastNo      = (int) trim(end($parts));
    $seq         = (int) $res[0]['sequenceBy'] + 1;

    if ($lastNo < CM_BOXES_PER_COUNTER) {
        $counter = $lastCounter;
        $no      = $lastNo + 1;
    } else {
        if ($lastCounter >= CM_LAST_COUNTER) {
            return null;
        }
        $counter = chr(ord($lastCounter) + 1);
        $no      = 1;
    }

    return array('box_number' => $counter . ' - ' . $no, 'counter_number' => $counter, 'sequenceBy' => $seq);
}

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* ---------- next box for the Add popup ---------- */
if ($act === 'nextBox') {
    $next = cmNextBox();
    if (!$next) {
        cmJson('error', 'All counters (A - ' . CM_LAST_COUNTER . ') are already created.');
    }
    cmJson('success', 'OK', $next);
}

/* ---------- common validation ---------- */
$capacity = isset($_POST['card_capacity']) ? trim($_POST['card_capacity']) : '';
$venue    = isset($_POST['venue']) ? trim($_POST['venue']) : '';

if (in_array($act, array('add', 'edit'), true)) {
    if ($capacity === '' || !ctype_digit($capacity) || (int)$capacity < 1 || (int)$capacity > 100000) {
        cmJson('error', 'Please enter a valid card capacity.');
    }
    if ($venue === '') {
        cmJson('error', 'Please enter the venue.');
    }
    if (mb_strlen($venue) > 255) {
        cmJson('error', 'Venue is too long (max 255 characters).');
    }
}

/* ---------- add ---------- */
if ($act === 'add') {
    $next = cmNextBox();
    if (!$next) {
        cmJson('error', 'All counters (A - ' . CM_LAST_COUNTER . ') are already created.');
    }
    $a = cmAudit();

    $sqlIns = array();
    $sqlIns['QUERY'] = "INSERT INTO `" . CM_TABLE . "`
                            (`box_number`, `counter_number`, `venue`, `card_capacity`, `sequenceBy`, `status`,
                             `created_by`, `created_ip`, `created_sessionId`, `created_dateTime`)
                        VALUES (?, ?, ?, ?, ?, 'A', ?, ?, ?, ?)";
    $sqlIns['PARAM'][] = array('FILD' => 'box_number',        'DATA' => $next['box_number'],     'TYP' => 's');
    $sqlIns['PARAM'][] = array('FILD' => 'counter_number',    'DATA' => $next['counter_number'], 'TYP' => 's');
    $sqlIns['PARAM'][] = array('FILD' => 'venue',             'DATA' => $venue,                  'TYP' => 's');
    $sqlIns['PARAM'][] = array('FILD' => 'card_capacity',     'DATA' => (int)$capacity,          'TYP' => 's');
    $sqlIns['PARAM'][] = array('FILD' => 'sequenceBy',        'DATA' => $next['sequenceBy'],     'TYP' => 's');
    $sqlIns['PARAM'][] = array('FILD' => 'created_by',        'DATA' => $a['user'],              'TYP' => 's');
    $sqlIns['PARAM'][] = array('FILD' => 'created_ip',        'DATA' => $a['ip'],                'TYP' => 's');
    $sqlIns['PARAM'][] = array('FILD' => 'created_sessionId', 'DATA' => $a['sess'],              'TYP' => 's');
    $sqlIns['PARAM'][] = array('FILD' => 'created_dateTime',  'DATA' => $a['now'],               'TYP' => 's');
    $mycms->sql_insert($sqlIns, false);

    cmJson('success', 'Box ' . $next['box_number'] . ' added successfully.', $next);
}

/* ---------- edit (only capacity + venue; box / counter never change) ---------- */
if ($act === 'edit') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        cmJson('error', 'Invalid record.');
    }
    $a = cmAudit();

    $sqlUpd = array();
    $sqlUpd['QUERY'] = "UPDATE `" . CM_TABLE . "`
                           SET `card_capacity` = ?, `venue` = ?,
                               `modified_by` = ?, `modified_ip` = ?, `modified_sessionId` = ?, `modified_dateTime` = ?
                         WHERE `id` = ?";
    $sqlUpd['PARAM'][] = array('FILD' => 'card_capacity',     'DATA' => (int)$capacity, 'TYP' => 's');
    $sqlUpd['PARAM'][] = array('FILD' => 'venue',             'DATA' => $venue,         'TYP' => 's');
    $sqlUpd['PARAM'][] = array('FILD' => 'modified_by',       'DATA' => $a['user'],     'TYP' => 's');
    $sqlUpd['PARAM'][] = array('FILD' => 'modified_ip',       'DATA' => $a['ip'],       'TYP' => 's');
    $sqlUpd['PARAM'][] = array('FILD' => 'modified_sessionId','DATA' => $a['sess'],     'TYP' => 's');
    $sqlUpd['PARAM'][] = array('FILD' => 'modified_dateTime', 'DATA' => $a['now'],      'TYP' => 's');
    $sqlUpd['PARAM'][] = array('FILD' => 'id',                'DATA' => $id,            'TYP' => 's');
    $mycms->sql_update($sqlUpd, false);

    cmJson('success', 'Box updated successfully.');
}

cmJson('error', 'Invalid request.');
