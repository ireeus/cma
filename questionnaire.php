<?php
// Start or resume a session
session_start();
include('config.php');
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the username from the session
    $username = $_SESSION['username'];

    // Retrieve the answers from the form
    $answer1 = $_POST['answer1'];
    $answer2 = $_POST['answer2'];
    $answer3 = $_POST['answer3'];


    // Encrypt the answers using OpenSSL
    $encryptedText1 = openssl_encrypt($answer1, 'aes-256-cbc', $secretkey, 0, $iv);
    $encryptedText2 = openssl_encrypt($answer2, 'aes-256-cbc', $secretkey, 0, $iv);
    $encryptedText3 = openssl_encrypt($answer3, 'aes-256-cbc', $secretkey, 0, $iv);
    
    // bypas of the Encryption
    //$encryptedText1 = $answer1;
    //$encryptedText2 = $answer2;
    //$encryptedText3 = $answer3;

// Create a string with the file content
$fileContent = "<?php/*
answer1: $encryptedText1\nanswer2: $encryptedText2\nanswer3: $encryptedText3
*/
    ";

    // Save the file in the recovery directory
    $filePath = $dir."recovery/$username.php";
    file_put_contents($filePath, $fileContent);
    $returnPath=$_SESSION['redirect_content']; 
    //echo $returnPath;
       if(!isset($_SESSION['redirect_content'])){$returnPath='index.php';}

header('Location: '.$returnPath);
exit;
}

// Set the username in the session (replace 'your_username' with the actual username)
$username=$_SESSION['username'];
include("lib/logfile.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire</title>
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

        input[type="hidden"] {
            display: none;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body> <form method="post" action="">
      <h2>Password Recovery Questions</h2>
     <label for="answer1">Where did you go for your most memorable vacation?</label>
        <input type="text" name="answer1" id="answer1" required>

        <label for="answer2">What is your mother's maiden name?</label>
        <input type="text" name="answer2" id="answer2" required>

        <label for="answer3">What is the name of your childhood best friend?</label>
        <input type="text" name="answer3" id="answer3" required>

        <input type="hidden" name="username" value="<?php echo $username; ?>">
<span style="font-size: 12px; color: darkred;">During recovery, ensure you remember all provided details, as you'll be asked one of the questions randomly.</span> <br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
