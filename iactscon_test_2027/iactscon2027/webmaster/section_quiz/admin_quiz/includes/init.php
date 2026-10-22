<?php
// Always load the global admin init
require_once __DIR__ . '/../../includes/admininit.php';

// Load quiz module config (shared by admin_quiz + section_quiz frontend)
require_once __DIR__ . '/includes/configure.php';
require_once __DIR__ . '/includes/configure.DB.Tables.override.php';

// Load registration or utility functions
require_once __DIR__ . '/../../../includes/function.registration.php';

// If admin_quiz has its own overrides, include them last
if (file_exists(__DIR__ . '/configure.php')) {
    require_once __DIR__ . '/configure.php';
}
if (file_exists(__DIR__ . '/configure.DB.Tables.override.php')) {
    require_once __DIR__ . '/configure.DB.Tables.override.php';
}
?>
