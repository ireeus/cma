<?php

// Start or resume the session
session_start();

// Assuming you have a function to check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Check if POST data containing the filename is received
if (isset($_POST['filename'])) {
    $filename = $_POST['filename'];
    // Perform any necessary validation on the filename

    // Check if the file has less than 2 lines of content
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lineCount = count($lines);

    if ($lineCount < 2) {
        // Delete the file (adjust the path as needed)
        if (file_exists($filename)) {
            unlink($filename);

            // Log out all sessions
           // session_destroy();

            // Redirect to the login page after logout
            header("Location: index.php");
            exit();
        } else {
            // Handle file not found or other error
            echo "Error: File $filename not found or could not be deleted.";
        }
    } else {
                    header("Location: chat.php");
            exit();
    }
} else {
    // Handle missing filename in POST data
    echo "Error: Filename not provided.";
}

?>
