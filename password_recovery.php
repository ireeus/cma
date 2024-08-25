<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .verification-fail {
            color: red;
            margin-top: 10px; /* Adjust the margin as needed */
        }
    </style>
</head>
<body>
    <?php
    // Start or resume a session
    session_start();
    include('config.php');
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
    // Function to get predefined security questions
    function getSecurityQuestions() {
        return array(
            "Where did you go for your most memorable vacation?",
            "What is your mother's maiden name?",
            "What is the name of your childhood best friend?"
        );
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the provided username, answer, and question number
        $username = $_POST['username'];
        $email = $_POST['email'];
        $providedAnswer = $_POST['answer'];
        $questionNumber = $_POST['question_number'];

        // Read the encrypted answers from the corresponding file and from userfile
        $filePath = $dir."recovery/$username.php";

if (file_exists($filePath)) {
    // Read the content of the PHP file and remove unnecessary parts
    $fileContent = file_get_contents($filePath);
    $fileContent = str_replace(['<?php/*', '*/'], '', $fileContent);
    $fileContent = trim($fileContent);
    
    // Convert the content of the file to an array of lines
    $fileLines = explode("\n", $fileContent);
    
    // Now you can use $fileLines instead of $fileContent in the rest of the code
    $userDataFile = $dir."users/" . $username . ".php";
    $userData = getUserData($userDataFile);
    $Email = $userData['Email'];

    // Ensure the question number is within the bounds of the array
    if ($questionNumber >= 1 && $questionNumber <= count($fileLines)) {
        $index = $questionNumber - 1;
        list($question, $encryptedAnswer) = explode(": ", $fileLines[$index]);

        // Decrypt the answer using OpenSSL (modify as needed)
        $decryptedAnswer = openssl_decrypt($encryptedAnswer, 'aes-256-cbc', $secretkey, 0, $iv);

        // Check if the provided answer matches the decrypted answer (case-insensitive)
        if (strcasecmp($providedAnswer, $decryptedAnswer) === 0) {
            if ($Email === $email) {
                echo '<form method="post" action="password_change.php" onsubmit="return validatePasswords();">
                    <input type="text" name="username" value="' . $username . '" id="username" hidden>

                    <label for="new_password">New Password:</label>
                    <input type="password" name="new_password" id="new_password" required>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>

                    <button type="submit">Change Password</button>
                </form>

                <script>
                    function validatePasswords() {
                        var newPassword = document.getElementById("new_password").value;
                        var confirmPassword = document.getElementById("confirm_password").value;

                        if (newPassword !== confirmPassword) {
                            alert("Passwords do not match. Please check and try again.");
                            return false; // Prevent form submission
                        }

                        return true; // Allow form submission
                    }
                </script>';

                // You may redirect or provide further actions here
                exit();
            }
        }
    }
}


        // If the verification fails
        $verification= '<p class="verification-fail">Verification failed. <br>Please make sure the username, email and security answer are correct.</p>';
    }

    // Get predefined security questions
    $questions = getSecurityQuestions();
session_start();


//retriving user info
$username = $_SESSION['username'];
$userDataFile = $dir."users/" . $username . ".php";
$userData = getUserData($userDataFile);




    // Generate a random security question for the session and save the question number
    $randomQuestionIndex = array_rand($questions);
    $randomQuestion = $questions[$randomQuestionIndex];
    $_SESSION['security_question'] = $randomQuestion;

    // Find the question number based on the randomly selected question
    $questionNumber = $randomQuestionIndex + 1;
if(isset($_SESSION['username'])){$type='readonly';}
    echo '   <form method="post" action="">     
                        <h2>Password Recovery</h2>
       
    <input placeholder="Username:" type="text" '.$type.'  value="'.$_SESSION['username'].'" name="username" id="username" required>
    
    <input placeholder="Email:" type="text" value="" name="email" id="email" required>

            <label for="question">Security Question:</label>
            <p>';
    
    echo $_SESSION['security_question'].'</p>
    
            <!-- Add a hidden input to store the question number -->
            <input type="hidden" name="question_number" value="';
    
    echo $questionNumber.'">
    
            <input placeholder="Your Answer:" type="text" name="answer" id="answer" required>
    
            <button type="submit">Verify</button>
            '.$verification;
           // if($Email!==$email){echo'Incorect details provided';}

            echo'
        </form>';
        include("lib/logfile.php");

    ?>

</body>
</html>
