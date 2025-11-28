<?php 
error_reporting(E_ERROR | E_PARSE);

function log_file($message, $log_name) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = uniqid();
    }

    $logFile = __DIR__ . '/'.$log_name.'.log';
    $maxSize = 1024 * 1024 * 10; // 10 MB

    date_default_timezone_set('Asia/Hong_Kong');

    $entry = date('Ymd His').' '.$_SESSION['user_id'].' '."$message\n";

    // If file exists and will exceed max size, trim from top
    if (file_exists($logFile)) {
        $currentSize = filesize($logFile);
        $newSize = $currentSize + strlen($entry);

        if ($newSize > $maxSize) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES);
            while ($newSize > $maxSize && count($lines) > 0) {
                $removed = array_shift($lines);
                $newSize -= strlen($removed) + 1; // +1 for newline
            }
            if (!empty($lines)) {
                file_put_contents($logFile, implode("\n", $lines) . "\n");
            } else {
                file_put_contents($logFile, "");
            }
        }
    }

    file_put_contents($logFile, $entry, FILE_APPEND);
}

function m_log($message) {
    log_file($message, 'app');
}

function t_log($message) {
    log_file($message, 'time');
}

// Example usage
?>