
<?php
include('config.php');
    // Check the account type and assign to $myType
    $username = $_POST['username'];
    $userDataFile = $dir."users/" . $username . ".php";
    $userData = getUserData($userDataFile);
    $myType = isset($userData['Type']) ? $userData['Type'] : ''; // Assuming 'Type' is the correct key in your user data
    //echo$myType;


$userDataFile = $dir."users/" . $username . ".php";

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

// Function to save user data to file
function saveUserData($userDataFile, $userData) {
    // Preserve existing 'Type' line
    $existingUserData = getUserData($userDataFile);
    $userData['Type'] = $existingUserData['Type'];

    $iniContent = "";
    foreach ($userData as $key => $value) {
        $iniContent .= "$key: $value\n";
    }
    file_put_contents($userDataFile, $iniContent);
    header('Location: index.php');
    die;
}

// Retrieve and update user data on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission and update user data
    // Example: Assuming the form field is 'new_password'
    $userData = getUserData($userDataFile);
    $userData['Password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    saveUserData($userDataFile, $userData);
}

// logfile record
include("lib/logfile.php");

?>
