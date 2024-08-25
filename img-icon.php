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

if (isset($_GET['file']) && isset($_GET['move'])) {
    $file = $_GET['file'];
    $move = $_GET['move'];

    echo '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px; background-color: #f8d7da; color: #721c24;">
            <p>Do you realy want to move the message to the top?</p>
            <form method="get" action="">
                <button type="submit" name="deleteConfirmed" style="background-color: darkred; color: white;">Move</button>
                  <input type="text" hidden name="fileConfirmed"  value="'.$file.'">
                  <input type="text" hidden name="messageConfirmed" value="'.$move.'">
              <button><a href="chat.php" style="color: white;">Cancel</a> </button>
            </form>
          </div>';
}

if (isset($_GET['fileConfirmed']) && isset($_GET['messageConfirmed'])) {
    $fileToModify = $_GET['fileConfirmed'];
    $messageToMove = urldecode($_GET['messageConfirmed']);

    // Construct the file path
    $filePath = $fileToModify;

    // Read the content of the file
    $content = file_get_contents($filePath);

    // Extract file names from the message to be moved
    preg_match_all('/\[(pdf|file): ([^\]]+)\]/', $messageToMove, $matches);

    // Split the content into lines
    $lines = explode("\n", $content);

    // Find the line containing the message to be moved
    $messageLineIndex = array_search(preg_quote($messageToMove, '/'), array_map('preg_quote', $lines, array_fill(0, count($lines), '/')));

    if ($messageLineIndex !== false) {
        $movedMessage = $lines[$messageLineIndex];

        // Remove the message line from the array
        unset($lines[$messageLineIndex]);

        // Insert the message at line 2 (index 1)
        array_splice($lines, 1, 0, $movedMessage);

        // Reconstruct the file content
        $updatedContent = implode("\n", $lines);

        // Write the updated content back to the file
        file_put_contents($filePath, $updatedContent);
    }

    // Redirect back to chat.php
    header("Location: chat.php");
    exit();
}
?>

 </div>
</body>
</html>
