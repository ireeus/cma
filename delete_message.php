<?php
session_start();

// Check if the user is logged in, redirect to chat.php
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$userDataFile = "users/" . $username . ".php";

// Function to get user data from file
function getUserData($userDataFile) {
    $userData = array();

    if (file_exists($userDataFile)) {
        $lines = file($userDataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            list($key, $value) = explode(':', $line, 2);
            $userData[trim($key)] = trim($value);
        }
    }

    return $userData;
}

$userData = getUserData($userDataFile);
include("lib/logfile.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Register</title>
<style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        #login-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            text-align: center;
        }

        ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        li {
            margin-bottom: 10px;
        }

        .error {
            color: #ff0000;
            text-align: center;
            margin: 10px 0;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        #sessionSelect {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #newSessionInput {
            display: none;
        }

        button {
            background-color: #3498db;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #007bb5;
        }
    </style>
</head>

<body>
    <div id="login-container">
 <?php
session_start();

if (isset($_GET['file']) && isset($_GET['delete'])) {
    $file = $_GET['file'];
    $delete = $_GET['delete'];

    echo '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px; background-color: #f8d7da; color: #721c24;">
            <p>Are you sure you want to delete this message?</p>
            <form method="get" action="">
                <button type="submit" name="deleteConfirmed" style="background-color: darkred; color: white;">Delete</button>
                  <input type="text" hidden name="fileConfirmed"  value="'.$file.'">
                  <input type="text" hidden name="messageConfirmed" value="'.$delete.'">
              <button><a href="chat.php" style="color: white;">Cancel</a> </button>
            </form>
          </div>';
}

if (isset($_GET['fileConfirmed']) && isset($_GET['messageConfirmed'])) {
    $fileToDelete = $_GET['fileConfirmed'];
    $messageToDelete = urldecode($_GET['messageConfirmed']);

    // Construct the file path
    $filePath = $fileToDelete;

    // Read the content of the file
    $content = file_get_contents($filePath);

    // Extract file names from the message to be deleted
    preg_match_all('/\[(pdf|file): ([^\]]+)\]/', $messageToDelete, $matches);

    // Remove the message from the content
    $updatedContent = preg_replace('/' . preg_quote($messageToDelete, '/') . '\s*/', '', $content);

    // Write the updated content back to the file
    file_put_contents($filePath, $updatedContent);

    // Delete associated files
    foreach ($matches[2] as $index => $fileName) {
        $fileType = $matches[1][$index];

        // Construct the file path based on the file type
        $filePathToDelete = ($fileType == 'pdf') ? "pdfs/" : "images/";
        $filePathToDelete .= $fileName;

        if (file_exists($filePathToDelete)) {
            unlink($filePathToDelete);
        }
    }

    // Redirect back to chat.php
    header("Location: chat.php");
    exit();
}

?>

 </div>
</body>
</html>
