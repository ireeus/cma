<?php
session_start();
$phpSelf = basename($_SERVER['PHP_SELF']);
if ($phpSelf !== 'log-view.php') {

    // Function to log activity to CSV file
    function logActivity($username, $currentPage, $selectedSession, $postData, $ipAddress) {
        // Define maximum file size in bytes (3 MB)
        $maxFileSize = 10 * 1024 * 1024; // 3 MB in bytes

        // Define CSV file base path
        $baseLogfilePath = 'log/logfile';

        // Check current logfile size
        $currentLogfile = findAvailableLogfile($baseLogfilePath, $maxFileSize);

        // Prepare data array for CSV
        $data = array(
            date('Y-m-d H:i:s'),
            $username,
            $currentPage,
            $selectedSession,
            $postData,
            $ipAddress
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

    // Function to get the WAN IP address
    function getWANIP() {
        $ip = file_get_contents('http://api.ipify.org');
        return $ip;
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
        logActivity($username, $currentPage, $selectedSession, '', '');
    } else {
        // User is not logged in
        $username = 'guest'; // Or you can leave it as an empty string

        // Get current page (using $_SERVER['PHP_SELF'] to get current script name)
        $currentPage = basename($_SERVER['PHP_SELF']);

        // Append query string (GET parameters) if exists
        if (!empty($_SERVER['QUERY_STRING'])) {
            $currentPage .= '?' . $_SERVER['QUERY_STRING'];
        }

        // Get selected session from session or set it to an empty string
        $selectedSession = isset($_SESSION['selected_session']) ? $_SESSION['selected_session'] : '';

        // Get POST data and remove the password field
        $postData = $_POST; // This will be an associative array of POST data
        if (isset($postData['password'])) {
            unset($postData['password']);
        }

        // Convert POST data to a string for logging (e.g., JSON encode)
        $postDataString = json_encode($postData);

        // Get WAN IP address of the user
        $ipAddress = getWANIP();

        // Log activity to CSV file
        logActivity($username, $currentPage, $selectedSession, $postDataString, $ipAddress);
    }

    // Create the folder if it doesn't exist
    $folder = 'online';
    if (!file_exists($folder)) {
        mkdir($folder);
    }

    // Generate filename based on username
    $filename = $folder . '/' . (isset($username) ? $username : 'guest');

    // Create or overwrite the file with new timestamp
    $timestamp = date('y.m.d H:i:s');
    $content = "$timestamp";
    file_put_contents($filename, $content);

    return true;
}
?>
