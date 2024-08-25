<?php
session_start();
$username=$_SESSION['username'];
$newSessionName = $_POST['newSession']; // Using timestamp for uniqueness


// Remove all special characters except -!@#$%&
$newSessionName = preg_replace('/[^a-zA-Z0-9\s\-!@]/', '', $newSessionName);

$lastChar = substr($newSessionName, -1);

if ($lastChar === ' ') {
    $newSessionName = substr($newSessionName, 0, -1);
}



    // Create a new session 
    $info = '[' . $username . '-' . date("Y-m-d H.i.s") . ']
';
    $sessionFolder = "sessions/";
    $chatFilePath = $sessionFolder . $newSessionName . "_session.txt";

    // Check if the file already exists
    $counter = 1;
    while (file_exists($chatFilePath)) {
        $newSessionName = $_POST['newSession'] . " ($counter)";
        $chatFilePath = $sessionFolder . $newSessionName . "_session.txt";
        $counter++;
    }

    // Create the new session file
    file_put_contents($chatFilePath, $info); // Start with an empty file
include("lib/logfile.php");

    $_SESSION['selected_session'] = $newSessionName;
    header("Location: chat.php?session=$newSessionName");

