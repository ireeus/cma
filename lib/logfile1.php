<?php
session_start();

// Function to log activity to CSV file
function logActivity($username, $currentPage, $selectedSession) {
    // Define maximum file size in bytes (3 MB)
    $maxFileSize = 10 * 1024 * 1024; // 3 MB in bytes

    // Define CSV file base path
    $baseLogfilePath = '../log/logfile';

    // Check current logfile size
    $currentLogfile = findAvailableLogfile($baseLogfilePath, $maxFileSize);

    // Prepare data array for CSV
    $data = array(
        date('Y-m-d H:i:s'),
        $username,
        $currentPage,
        $selectedSession
    );

    // Open or create CSV file in append mode
    $file = fopen($currentLogfile, 'a');

    // Write data to CSV file
    fputcsv($file, $data);

    // Close the file
    fclose($file);
}

// Function to find an available logfile and manage rotation if needed
function findAvailableLogfile($baseLogfilePath, $maxFileSize) {
    $fileIndex = 0;
    $currentLogfile = $baseLogfilePath . '';

    // Check if current logfile exists and its size
    while (file_exists($currentLogfile) && filesize($currentLogfile) > $maxFileSize) {
        $fileIndex++;
        $currentLogfile = $baseLogfilePath . "($fileIndex)";
    }

    return $currentLogfile;
}

// Check if username is set in session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Get current page (using $_SERVER['PHP_SELF'] to get current script name)
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Append query string (GET parameters) if exists
    if (!empty($_SERVER['QUERY_STRING'])) {
        $currentPage .= '?' . $_SERVER['QUERY_STRING'];
    }

    // Get selected session from session or set it to an empty string
    $selectedSession = isset($_SESSION['selected_session']) ? $_SESSION['selected_session'] : '';

    // Log activity to CSV file
    logActivity($username, $currentPage, $selectedSession);
}
?>
