<?php
// include_once('../includes/FirebaseService.php');
// include_once('../includes/configure.php');
require_once __DIR__ . '/../../includes/FirebaseService.php';
require_once __DIR__ . '/../../includes/configure.php';
// bootstrap for Firebase in live_quiz

// TODO: set your values
// $FIREBASE_BASE_URL = 'https://YOUR_PROJECT-default-rtdb.firebaseio.com';
// $FIREBASE_SECRET   = 'YOUR_DB_SECRET';

$firebase = new FirebaseService($cfg['FIREBASE_URL'], $cfg['FIREBASE_SECRET']);

/**
 * Helpers for this app
 */
function fb_path_quiz($quizId, $sub='') {
    $base = "live_quiz/{$quizId}";
    return $sub ? "{$base}/{$sub}" : $base;
}

function fb_get_server_ms() {
    return (int) round(microtime(true) * 1000);
}
