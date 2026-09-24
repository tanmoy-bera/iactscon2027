<?php
/**
 * IACTSCON 2027 — Delegate lookup by email.
 * Called via GET from the art exhibition form when the email field
 * loses focus. Returns whether a matching _DB_USER_REGISTRATION_ row
 * exists, and if so, its id + registration number.
 */

header('Content-Type: application/json');
include_once('includes/frontend.init.php');

function respond($status, $extra = array()) {
    echo json_encode(array_merge(array('status' => $status), $extra));
    exit;
}

$email = trim($_GET['email'] ?? $_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond('error', array('message' => 'Invalid email address.'));
}

$sqlLookup = array();
$sqlLookup['QUERY']   = "SELECT `id`, `user_registration_id`, `user_full_name`
                          FROM " . _DB_USER_REGISTRATION_ . "
                          WHERE `user_email_id` = ? AND `status` = ?  AND `isRegistration` = 'Y'
                          ORDER BY `id` DESC
                          LIMIT 1";
$sqlLookup['PARAM'][] = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
$sqlLookup['PARAM'][] = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

$result = $mycms->sql_select($sqlLookup);

if (!empty($result)) {
    respond('found', array(
        'delegate_id'      => $result[0]['id'],
        'registration_id'  => $result[0]['user_registration_id'],
        'full_name'        => $result[0]['user_full_name'],
    ));
}

respond('not_found');
