<?php
include('config.php');
// Path to the 'recovery' directory
$directory = $dir.'recovery/';

// Check if the directory exists
if (!is_dir($directory)) {
    die("The 'recovery' directory does not exist.");
}

// Open the directory
if ($handle = opendir($directory)) {
    $txtFiles = [];
    
    // Search for files in the directory
    while (false !== ($entry = readdir($handle))) {
        
        // Check if the file has a .txt extension
        if (pathinfo($entry, PATHINFO_EXTENSION) == 'txt') {
            $txtFiles[] = $entry;
            $txtFilePath = $directory . $entry;
            $phpFilePath = $directory . pathinfo($entry, PATHINFO_FILENAME) . '.php';

            // Read the contents of the .txt file
            $content = file_get_contents($txtFilePath);

            // Create the contents of the .php file
            $phpContent = "<?php/*\n$content\n*/";

            // Write the contents to the .php file
            file_put_contents($phpFilePath, $phpContent);

            //echo "File $entry has been converted to " . pathinfo($entry, PATHINFO_FILENAME) . ".php\n";
        }
    }

    // Close the directory
    closedir($handle);

    // Delete all .txt files after successful conversion
    foreach ($txtFiles as $txtFile) {
        unlink($directory . $txtFile);
        //echo "File $txtFile has been deleted.\n";
    }
}

?>




<?php
session_start();

// Constants for lockout mechanism
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes in seconds

// Check if the "Exit Session" button is clicked
if (isset($_GET['logout']) and !isset($_COOKIE[$usernameCookieName])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to index page
    header('Location: login.php');
    exit();
}

if (isset($_GET['logout']) and isset($_COOKIE[$usernameCookieName])) { 
    header('Location: profile.php');
    die();
}

if (isset($_SESSION['username']) and (isset($_GET['session']))) {
    $_SESSION['selected_session'] = $_GET['session'];
    header('Location: chat.php');
    exit;
}
function trackLoginAttempt($username, $success) {
    if ($success) {
        // If login is successful, clear any existing attempts
        $attemptsFile = $dir."login_attempts/{$username}_attempts.php";
        if (file_exists($attemptsFile)) {
            unlink($attemptsFile);
        }
        return 0;
    }

    $attemptsFile = $dir."login_attempts/{$username}_attempts.php";
    $currentTime = time();

    if (!file_exists($attemptsFile)) {
        $attempts = [];
    } else {
        $attempts = unserialize(file_get_contents($attemptsFile));
    }

    // Remove old attempts
    $attempts = array_filter($attempts, function($timestamp) use ($currentTime) {
        return ($currentTime - $timestamp) < LOCKOUT_TIME;
    });

    // Add new attempt only for failed logins
    $attempts[] = $currentTime;

    file_put_contents($attemptsFile, serialize($attempts));

    return count($attempts);
}

function authenticateUser($username, $password) {
    if (isUserLocked($username)) {
        return 'locked'; // User is locked out
    }

    $success = false; // Initialize success flag
    $filePath = $dir."users/" . $username . ".php";

    // Check if the user file exists
    if (file_exists($filePath)) {
        // Read the content of the user file
        $userContent = file_get_contents($filePath);

        // Extract hashed password from user file
        preg_match("/Password: (.*)/", $userContent, $matches);
        $hashedPassword = isset($matches[1]) ? $matches[1] : null;

        // Verify password
        if ($hashedPassword !== null && password_verify($password, $hashedPassword)) {
            $success = true; // Authentication successful
            
            // Create the folder if it doesn't exist
            $folder = 'online';
            if (!file_exists($folder)) {
                mkdir($folder);
            }
            
            // Generate filename based on username
            $filename = $folder . '/' . $username;
            
            // Create or overwrite the file with new timestamp
            $timestamp = date('y.m.d H:i:s');
            $content = "$timestamp";
            file_put_contents($filename, $content);

            // Clear login attempts on successful login
            trackLoginAttempt($username, true);

            return true;
        }
    }

    // Only track failed attempts
    $attemptCount = trackLoginAttempt($username, false);

    if ($attemptCount >= MAX_LOGIN_ATTEMPTS) {
        return 'locked'; // Indicate that the user is now locked out
    }

    return false; // Authentication failed
}

function isUserLocked($username) {
    $attemptsFile = $dir."login_attempts/{$username}_attempts.php";
    if (!file_exists($attemptsFile)) {
        return false;
    }

    $attempts = unserialize(file_get_contents($attemptsFile));
    $currentTime = time();

    // Remove old attempts
    $attempts = array_filter($attempts, function($timestamp) use ($currentTime) {
        return ($currentTime - $timestamp) < LOCKOUT_TIME;
    });

    return count($attempts) >= MAX_LOGIN_ATTEMPTS;
}



// Function to send a message to the chat session file
function sendMessageToSession($sessionFileName, $message) {
    $sessionFolder = $dir."sessions/";
    $chatFilePath = $sessionFolder . $sessionFileName . "_session.txt";

    // Save the new message to the chat session file with a system username
    $systemUsername = 'System';
    $messageWithUsername = $systemUsername . ': ' . $message;
    file_put_contents($chatFilePath, $messageWithUsername . "\n", FILE_APPEND);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $selectedSession = $_POST['session'];

    // Authenticate the user
    $authResult = authenticateUser($username, $password);

    if ($authResult === true) {
        $_SESSION['username'] = $username;

        if ($selectedSession === 'list') { 
            header("Location: index.php"); 
            exit;
        }
        if ($selectedSession === 'new') {
            // Create a new session 
            $newSessionName = $_POST['newSession'];
            $info = '[' . $username . '-' . date("Y-m-d H.i.s") . ']
';
            $sessionFolder = $dir."sessions/";
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

            $_SESSION['selected_session'] = $newSessionName;
            header("Location: chat.php?session=$newSessionName");
        } elseif (!empty($selectedSession)) {
            // Log in to an existing session
            $_SESSION['selected_session'] = $selectedSession;
            header("Location: chat.php?session=$selectedSession");
            
            // Update online status
            $folder = 'online';
            if (!file_exists($folder)) {
                mkdir($folder);
            }
            
            $filename = $folder . '/' . $_SESSION['username'];
            $timestamp = date('y.m.d H:i:s');
            $content = "$timestamp";
            file_put_contents($filename, $content, FILE_APPEND);
        } else {
            // Handle the case where no session is selected
            $error_message = 'Please select a session.';
        }

        exit();
    } elseif ($authResult === 'locked') {
        $error_message = 'Account is locked due to too many failed attempts. Please try again later.';
    } else {
        $error_message = 'Invalid username or password.';
    }
}
include("lib/logfile.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="manifest" href="manifest.json">
    <link rel="icons" href="icons.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Login</title>
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

        .floating-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #f0f0f0;
            padding: 0px 22px;
            border-radius: 60px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
    </style>
    <script>
        if (window.matchMedia('(display-mode: standalone)').matches) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'update_pwa_status.php?pwa=true', true);
            xhr.send();
        }
    </script>
</head>
<body>
    <div id="login-container">        
        <center><img src="<?php echo $logo;?>" alt="Company Logo" style="max-width: 40%;"></center>
    
        <h2>Login <i class="fa fa-sign-in" style="font-size:25px"></i></h2>

        <?php
        if (isset($error_message)) {
            echo '<p class="error">' . $error_message . '</p>';
        }
        ?>

        <form method="post" action="" id="loginForm" onsubmit="return validateForm()">
            <div>
                <p>
                    <label for="username">Username:</label><br>
                    <input type="text" id="username" name="username" required>
                </p>

                <p>
                    <label for="password">Password: <font size="2"><a href="password_recovery.php">Forgot password?</a></font></label><br>
                    <input type="password" id="password" name="password" required>
                    <input type="text" id="hidden" hidden value="list" name="session" required>
                </p>

                <p>
                    <button type="submit">Submit</button>
                </p>
            </div>
        </form>
        <ul>
            <li><a href="register.php">Register</a></li>
        </ul>
        <div class="floating-message">
            <p><a href="CMA.apk"><img src="lib/img/apk.png" width="50"></a></p>
        </div>
    </div>
</body>
</html>
