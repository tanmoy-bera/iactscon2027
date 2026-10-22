<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("../includes/init.php");

$question_id = (int)($_GET['question_id'] ?? 0);
if ($question_id <= 0) {
    echo "<i>No options found</i>";
    exit;
}

$sql = [];
$sql['QUERY'] = "SELECT option_text, is_correct 
                 FROM rcg_quiz_question_options
                 WHERE question_id = ?
                 ORDER BY id ASC";
$sql['PARAM'][] = [
    'FILD' => 'question_id',
    'DATA' => $question_id,
    'TYP'  => 'i'
];

$data = $mycms->sql_select($sql);

if (!empty($data)) {
    foreach ($data as $opt) {
        echo '<div class="mb-1">'
            . htmlspecialchars($opt['option_text'])
            . ($opt['is_correct'] == 1 ? ' <span class="badge bg-success ms-2">✔ Correct</span>' : '')
            . '</div>';
    }
} else {
    echo "<i>No options found</i>";
}
exit;
