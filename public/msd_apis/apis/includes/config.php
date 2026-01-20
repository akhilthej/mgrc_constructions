<?php
// config.php - Central configuration
define('DAILY_RATE', 0.0025);
define('TIMEZONE', 'Asia/Kolkata');
define('LOG_DIR', __DIR__ . '/logs/');
define('BATCH_SIZE', 100); // For batch processing

// Ensure log directory exists
if (!file_exists(LOG_DIR)) {
    mkdir(LOG_DIR, 0777, true);
}
?>