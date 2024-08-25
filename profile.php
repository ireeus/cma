<?php
include('config.php');
session_start(); // Start the session

include('sessionCheck.php');
        
if (isset($_POST['remember_me'])) {
    // Set the cookie with the username and expiry date
    $username = $_POST['username'];
    $encryptedUsername= openssl_encrypt($username, 'aes-256-cbc', $secretkey, 0, $iv);
    $expiry = time() + (30 * 24 * 60 * 60); // 1 month from now
    setcookie($usernameCookieName, $encryptedUsername, $expiry, '/');
    header('Location: profile.php');

exit();
}

if (isset($_POST['cancel_me'])) {
    // Delete the cookie by setting its expiry time to a past value
    setcookie($usernameCookieName, '', time() - 3600, '/');
    // Redirect to the same page to ensure the cookie is unset
    header('Location: profile.php');
    exit;
}

// Check if redirection exists
if(isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']!=='https://cloudapps.zapto.org/cas/backup.php') {
    // createing redirection address session 
    $_SESSION['redirect_content'] = $_SERVER['HTTP_REFERER'];
} else {
    // if redirection doesn't exist, assign a message about redirection
    $_SESSION['redirect_content'] = 'index.php';
}

// Checking the user account type
if (isset($_SESSION['username'])) {
    // Check the account type and assign to $myType
    $username = $_SESSION['username'];
    $userDataFile = $dir."users/" . $username . ".php";
    $userData = getUserData($userDataFile);
    $myType = isset($userData['Type']) ? $userData['Type'] : ''; 
    $userDataFile = $dir."users/" . $username . ".php";
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

// Function to save user data to file
function saveUserData($userDataFile, $userData) {
    // Preserve existing 'Username' and 'Password' lines
    $existingUserData = getUserData($userDataFile);
    $userData['Username'] = $existingUserData['Username'];
    $userData['Password'] = $existingUserData['Password'];
    $userData['Email'] = $existingUserData['Email'];
    $iniContent = "";
    foreach ($userData as $key => $value) {
        $iniContent .= "$key: $value\n";
    }
    file_put_contents($userDataFile, $iniContent);
header('Location: chat.php');
die;
}

// Retrieve and update user data on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission and update user data
    // Example: Assuming the form field is 'type'
    $userData = getUserData($userDataFile);
    $userData['Type'] = $_POST['type'];
    saveUserData($userDataFile, $userData);
}

// Get and display user data
$userData = getUserData($userDataFile);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon-256x256.png">
    <link rel="manifest" href="/manifest.json">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
      .then(registration => {
        console.log('Service Worker registered with scope:', registration.scope);
      })
      .catch(error => {
        console.error('Service Worker registration failed:', error);
      });
  }
</script>


    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        #options-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        p {
            margin: 0;
        }

        input[type="text"] {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        a {
            text-decoration: none;
            color: #3498db;
        }
    </style>  <!-- Add your existing head content here -->
</head>
<body>
     <?php include('lib/nav.php');   ?>

    <div id="options-container">
        <a href="<?php echo $_SESSION['redirect_content']; ?>"><i class="fa fa-arrow-circle-left" style="font-size:36px"></i></a>
        <h2>Settings</h2>
        <ul>

           <table>
    <tr>
        <th>Account</th>
        <td><?php echo $username; ?></td>
    </tr>
    <tr>
        <th>Type</th>
        <td><?php 
        $userDataType=$userData['Type'];
        if($userDataType!=="limited" and $userDataType!=="standard" and $userDataType!=="suspended" ){echo'<a href="user_edit.php?userName='.$userData['Username'].'">';}
        echo $userData['Type']; 
        if($userDataType!=="limited" and $userDataType!=="standard" and $userDataType!=="suspended" ){echo'</a>';}
        ?>
        </td>
    </tr>
    <tr>
        <th><a href="questionnaire.php">Update security questions</a></th>
        <td><a href="password_recovery.php">Change password</a></td>
    </tr>
    
    <tr><td colspan="2">
    
    

<?php

    // Check if the cookie exists and display the username
    if (isset($_COOKIE[$usernameCookieName])) {
        echo '<center><form method="post" action="">
        
        <input type="text" value="'.$username.'" hidden name="username" id="username" required>

            <input type="checkbox" hidden name="cancel_me" checked value="1">

        <input type="submit" value="'.$username.' is locked on this device - Cancel">
    </form>
        
        </center>';
        // Display the expiry date/time
    }else{
        
        echo'<form method="post" action="">
        <input type="text" value="'.$username.'" hidden name="username" id="username" required>

            <input type="checkbox" hidden name="remember_me" checked value="1">


        <input type="submit" value="Remember me on this device">
    </form>
';
        
        }
    ?>
    
    
    
    </td></tr>
</table><br>



<?php 

$userDataType=$userData['Type'];
if($userDataType!=="suspended" and $userDataType!=="limited" and $userDataType!=="standard" and $userDataType!=="advanced"){

////////////////////UPDATE CHECK//////////////////


function getRemoteFiles($url) {
    $files = array();
    
    // Use cURL to get the content of the directory listing
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    
    // Match all .zip files from the directory listing
    if (preg_match_all('/href="([^"]+\.zip)"/', $result, $matches)) {
        foreach ($matches[1] as $file) {
            $files[] = $file;
        }
    }
    
    return $files;
}

function getLatestVersion($files) {
    $latestVersion = 0;
    
    foreach ($files as $file) {
        // Extract the version number from the file name
        if (preg_match('/(\d+)\.zip$/', $file, $matches)) {
            $version = (int) $matches[1];
            if ($version > $latestVersion) {
                $latestVersion = $version;
            }
        }
    }
    
    return $latestVersion;
}

function getLocalVersion($filePath) {
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        if (preg_match('/Ver:\s*(\d+)/', $content, $matches)) {
            return (int) $matches[1];
        }
    }
    return 0; // Default to 0 if the file does not exist or the version is not found
}

function showUpdateButton($remoteVersion, $localVersion) {
    if ($remoteVersion > $localVersion) {
        echo '<a href="update.php"><button style="background-color: blue;"><i class="fa fa-shield"> UPDATE AVAILABLE</i></button></a><br>';
        
    } else {
        echo '<a href="update.php"><button style="background-color: green;"><i class="fa fa-shield"> SYSTEM UP TO DATE</i></button></a><br>';
    }
}

// Define the URL and local version file path
include('config.php');
$localVersionFilePath = 'ver.txt';

// Get the list of remote files
$remoteFiles = getRemoteFiles($updatesUrl);

// Get the latest version from the remote files
$remoteVersion = getLatestVersion($remoteFiles);

// Get the local version from the local file
$localVersion = getLocalVersion($localVersionFilePath);

// Show the appropriate update button
showUpdateButton($remoteVersion, $localVersion);

/////////////////////////////////////////////////////

    echo'<br><a href="config-edit.php"><button>App config</button></a><br>';}
?><br>

            
<?php
             
if ($userData['Type']==="admin") {
    // Directory path to the users' folder
    $usersFolder = $dir.'users/';

    // Check if the folder exists
    if (is_dir($usersFolder)) {
        // Open the users' folder
        if ($handle = opendir($usersFolder)) {
            // Initialize an array to store user names
            $usernames = array();

            // Read all entries in the users' folder
            while (($entry = readdir($handle)) !== false) {
                // Exclude the current directory (.) and parent directory (..)
                if ($entry != "." && $entry != ".." && $entry != "index.php" && $entry != ".htaccess") {
                    // Extract the username from the filename
                    $username = pathinfo($entry, PATHINFO_FILENAME);

                    // Add the username to the array
                    $usernames[] = $username;
                }
            }

            // Close the directory handle
            closedir($handle);

            // Check if any users were found
            if (!empty($usernames)) {
                // Display the dropdown list
                echo '<br><form action="delete_user.php" method="get">  ';
                echo '<label for="userToDelete">Delete User:</label>';
                echo '<select name="delete" id="userToDelete" required>';
                echo'<option value="">Select user</option>';
                
                // Generate option elements for each username
                foreach ($usernames as $username) {
                    echo '<option value="' . $username . '">' . $username . '</option>';
                }

                echo '</select>';
                echo '<button type="submit">Delete</button>';
                echo '</form><br>';
            } else {
                echo 'No users found.';
            }
        } else {
            echo 'Error opening the users folder.';
        }
    } else {
        echo 'Users folder does not exist.';
    }
}
////////////////////////////////////////



if ($userData['Type'] !== "limited" && $userData['Type'] !== "suspended" ) {
    // Define the folder where user files are stored
    $folder = 'online';

    // Check if the folder exists
    if (file_exists($folder) && is_dir($folder)) {
        $sessionTime = ini_get('session.gc_maxlifetime');

        // Get the list of files in the folder
        $files = scandir($folder);

        // Sort the files based on the last modified time in descending order
        usort($files, function($a, $b) use ($folder) {
            $aTime = filemtime($folder . '/' . $a);
            $bTime = filemtime($folder . '/' . $b);
            return $bTime - $aTime;
        });

        echo "<br><form></form><b>Currently online: </b><br>";

        // Loop through each file
        foreach ($files as $file) {
            // Skip . and .. directory entries
            if ($file == '.' || $file == '..') {
                continue;
            }

            // Get the full path of the file
            $filePath = $folder . '/' . $file;

            // Get the last modification time of the file
            $fileTimestamp = filemtime($filePath);

            // Calculate the difference in seconds between the current time and the file timestamp
            $currentTime = time();
            $timeDifference = $currentTime - $fileTimestamp;

            // Check if the file was created less than the session time ago
            if ($timeDifference < $sessionTime) {
                // Display the username (file name)
                echo $file . "<br>";
            }
        }
    } else {
        echo "Error 'online' unable to retrieve data.";
    }
}

if ($userData['Type'] === "admin") {
    // Define the folder where user files are stored
    $folder = 'online';

    // Check if the folder exists
    if (file_exists($folder) && is_dir($folder)) {
        // Get the list of files in the folder
        $files = scandir($folder);

        // Sort the files based on the last modified time in descending order
        usort($files, function($a, $b) use ($folder) {
            $aTime = filemtime($folder . '/' . $a);
            $bTime = filemtime($folder . '/' . $b);
            return $bTime - $aTime;
        });

        echo "<br><form></form><b>Last seen: </b><br>";

        // Loop through each file
        foreach ($files as $file) {
            // Skip . and .. directory entries
            if ($file == '.' || $file == '..') {
                continue;
            }

            // Get the full path of the file
            $filePath = $folder . '/' . $file;

            // Read the content of the file
            $fileContent = file_get_contents($filePath);

            // Extract the date from the file content
            $datePattern = '/(\\d{2}\\.\\d{2}\\.\\d{2} \\d{2}:\\d{2}:\\d{2})/';
            if (preg_match($datePattern, $fileContent, $matches)) {
                $lastSeenDate = $matches[1];

                // Display the last seen date and the username (file name)
                echo $lastSeenDate . " - " . $file . "<br>";
            } else {
                // If the date couldn't be extracted from the file content, display an error message
                echo "Error: Unable to extract last seen date from file: " . $file . "<br>";
            }
        }
    } else {
        echo "Error: 'online' folder unable to retrieve data.";
    }
}
//echo $sessionTime;
?>


</ul>
    </div>
                <script src="js/nav.js"></script>

    
</body>
</html>
