<?php
session_start();

include("lib/logfile.php");

?><!DOCTYPE html>
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
    <div id="login-container"><?php
session_start();

if (isset($_GET['delete'])){
$delete=$_GET['delete'];

echo '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px; background-color: #f8d7da; color: #721c24;">
            <p>Are you sure you want to delete <b>'.$delete.' </b>account?</p>
            <form method="get" action="">
                <button type="submit" name="deleteConfirmed" style="background-color: darkred; color: white;">Delete</button>
                  <input type="text" hidden name="userDelete" value="'.$delete.'">

              <button><a href="chat.php" style=" color: white;">Cancel</a> </button>
            </form>
          </div>';
}

// Check if the user is logged in, redirect to chat.php
if (!isset($_SESSION['username'])) {
        header('Location: chat.php');
            exit();
        } elseif (isset($_SESSION['username'])) {
        // Check the account type and assign to $myType
            $username = $_SESSION['username'];
            $userDataFile = "users/" . $username . ".php";
            $userData = getUserData($userDataFile);
            $myType = isset($userData['Type']) ? $userData['Type'] : ''; // Assuming 'Type' is the correct key in your user data
        //echo$myType;
}

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
// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Get the username from the query parameters
    if (isset($_GET['userDelete'])) {
        $deleteUsername = $_GET['userDelete'];

        // Check if the logged-in user has the authority to delete the user
        if ($userData['Type']==="admin") {
            // Define the path to the user's file
            $filePath = 'users/' . $deleteUsername . '.php';

            // Check if the user's file exists and delete it
            if (file_exists($filePath)) {
                unlink($filePath);
                echo 'User deleted successfully.';
                $filePath = 'online/' . $deleteUsername;
                unlink($filePath);
                $filePath = 'recovery/' . $deleteUsername .'.txt';
                unlink($filePath);
                header('Location: profile.php');
die();
            } else {
                echo 'User not found.';
            }
        } else {
            echo 'Unauthorized access: You do not have the authority to delete users.';
        }
    } 
    
} else {
    echo 'Unauthorized access: User not logged in.';
}

// Function to check if the logged-in user can delete the specified user
function canDeleteUser($loggedInUsername, $deleteUsername) {
    // Define the path to the logged-in user's file
    $loggedInUserFilePath = 'users/' . $loggedInUsername . '.txt';

    // Check if the logged-in user's file exists
    if (file_exists($loggedInUserFilePath)) {
        // Read the account type from the logged-in user's file
        $loggedInUserContent = file_get_contents($loggedInUserFilePath);
        $loggedInUserDetails = json_decode($loggedInUserContent, true);

        // Check if the account type is admin or advanced
        if ($loggedInUserDetails && in_array($loggedInUserDetails['type'], ['admin', 'advanced'])) {
            return true; // User has authority to delete
        }
    }

    return false; // User does not have authority to delete
}
?>

 </div>
</body>
</html>
