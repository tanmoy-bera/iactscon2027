<?php
header('Content-Type: application/json');
// include_once('includes/firebase_bootstrap.php');
// require_once __DIR__.'../';
require_once __DIR__.'/../includes/firebase_bootstrap.php';

$quizId = isset($_GET['quiz_id']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['quiz_id']) : null;
if (!$quizId) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing quiz_id']);
    exit;
}

try {
    
    $q = $firebase->get(fb_path_quiz($quizId, 'current_question')) ?: null;
    echo json_encode([
        'ok' => true,
        'data' => $q,
        'serverNowMs' => fb_get_server_ms()
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
