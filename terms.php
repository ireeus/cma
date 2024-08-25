<?php
session_start();
include('config.php');

if(isset($_GET['exit'])){}
    // Check if $_SESSION['selected_session'] is set
    if (isset($_SESSION['selected_session'])) {
        // Unset (remove) $_SESSION['selected_session']
        unset($_SESSION['selected_session']);
    }

$username = $_SESSION['username'];
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

$userData = getUserData($userDataFile);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions</title>
 <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script></script>
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

        th,
        td {
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
    </style>	
</head>
<body>   
	    <?php include('lib/nav.php'); ?>
	     <div id="options-container">

    <h1>Terms and Conditions</h1>

<p>These Account Registration Terms and Conditions ("Terms") govern the registration and use of an account on https://cloudapps.zapto.org/cma 
("Service") provided by CMA ("Developer") for the benefit of user. By registering for an account, you agree to abide by these Terms. 
If you do not agree with these Terms, please do not proceed with account registration.</p>

    <h2>1. Account Registration</h2>

    <p><strong>1.1 Account Information:</strong></p>
    <ul>
        <li>You agree to provide necessary, and complete information during the registration process.</li>
        <li>You are responsible for maintaining the confidentiality of your account credentials (username and password).</li>
         <li>Your default access level after registration is <?php  include('config.php');echo '<b>"'.$accountLevel.'"</b>'; if($accountLevel=='suspended'){echo'. Your account will be reviewed and highier access level assigned.';}?></li>
    </ul>

    <h2>2. User Responsibilities</h2>

    <p><strong>2.1 Acceptable Use:</strong></p>
    <ul>
        <li>You agree that the Service will be used for your benefit without engaging in any activity that would be detrimental to the company's interests.</li>
        <li>You agree not to engage in any activity that may disrupt or interfere with the Service or its operation.</li>
    </ul>

    <p><strong>2.2 Prohibited Activities:</strong></p>
    <ul>
        <li>You agree not to:
            <ul>
                <li>Use the Service for any unlawful purpose or to transmit any harmful, defamatory, or otherwise objectionable content.</li>
                <li>Attempt to gain unauthorized access to any part of the Service or other users' accounts.</li>
                <li>Sell, resell, or exploit any aspect of the Service for commercial purposes without prior written consent from the Developer.</li>
            </ul>
        </li>
    </ul>

    <h2>3. Intellectual Property</h2>

    <p><strong>3.1 Ownership:</strong></p>
    <ul>
        <li>The Service and its original content, features, and functionality are owned by the Developer.</li>
    </ul>

    <p><strong>3.2 License:</strong></p>
    <ul>
        <li>By registering for an account, the Developer grants you a limited, non-exclusive, non-transferable license to use the Service for personal use in accordance with these Terms.</li>
    </ul>

<h2>4. Privacy</h2>

<p><strong>4.1 Data Collection:</strong></p>
<ul>
    <li>The Developer collects and processes only the following personal information required for the correct functionality of the system:</li>
    <ul>
        <li>Username and password: Necessary for user authentication and access to the Service.</li>
        <li>Email address: Used for account recovery purposes.</li>
    </ul>
    <li>No other personal data is collected or processed via the Service.</li>
</ul>

<p><strong>4.2 Communications:</strong></p>
<ul>
    <li>By registering an account, you consent to receive communications from the Developer related to your account and the Service.</li>
</ul>

    <h2>5. Termination</h2>

    <p><strong>5.1 Termination by Developer:</strong></p>
    <ul>
        <li>The Developer reserves the right to suspend or terminate your account at any time for violations of these Terms or for any other reason deemed necessary by the Developer.</li>
    </ul>

    <p><strong>5.2 Termination by User:</strong></p>
    <ul>
        <li>You may terminate your account at any time by contacting Developer.</li>
    </ul>

    <h2>6. Limitation of Liability</h2>

    <p><strong>6.1 Disclaimer:</strong></p>
    <ul>
        <li>The Service is provided on an "as is" and "as available" basis without warranties of any kind.</li>
        <li>The Developer shall not be liable for any indirect, incidental, special, or consequential damages arising out of or in connection with the use or inability to use the Service.</li>
    </ul>


    <h2>7. Changes to Terms</h2>

    <p><strong>7.1 Modification:</strong></p>
    <ul>
        <li>The Developer reserves the right to update or modify these Terms at any time. Continued use of the Service after changes to the Terms constitutes acceptance of the updated Terms.</li>
    </ul>
    <h2>8. Activity Logging</h2>

<p><strong>8.1 Logging for Statistics and Debugging:</strong></p>
<ul>
    <li>The Service may log user activity, including but not limited to account actions, usage patterns, and errors encountered.</li>
    <li>Logging is performed for statistical purposes and to aid in the identification and resolution of errors or issues within the Service.</li>
    <li>By using the Service, you consent to the logging of your activity for these purposes.</li>
</ul>

</div>
</body>
</html>
