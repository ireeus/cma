<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
    <title>Rename File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
        }


button {
    padding: 10px 12px;
    background-color: #d62020;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #d62020;
}

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

    </style>
</head>
<body>


<?php
session_start();
include('sessionCheck.php');
include('config.php');


function sanitizeInput($data) {
    // Remove all special characters except -!@#$%&
    $data = preg_replace('/[^a-zA-Z0-9\s\-!@,;]/', '', $data);
    return htmlspecialchars(trim($data));
}
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the input data
    $newFilename = sanitizeInput($_POST['newFilename']);
    $originalFilename = sanitizeInput($_POST['originalFilename']);

    // Construct the paths for the old and new filenames
    $oldFilePath = $dir."sessions/{$originalFilename}_session.txt";
    $newFilePath = $dir."sessions/{$newFilename}_session.txt";

    // Check if the old file exists
    if (file_exists($oldFilePath)) {
        // Rename the file
        if (rename($oldFilePath, $newFilePath)) {
            echo "File renamed successfully!";

            // Update $_SESSION['selected_session'] with the new filename
            $_SESSION['selected_session'] = $newFilename;
 header('Location: chat.php');
        } else {
            echo "Error renaming the file.";
        }
    } else {
        echo "The specified file does not exist.";
    }
}
    include('lib/sus_re_dir.php');

?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">
    <label for="newFilename">New Name:</label>
    <input type="text" id="newFilename" name="newFilename" value="<?php echo isset($_GET['filename']) ? htmlspecialchars($_GET['filename']) : ''; ?>">
    <input type="hidden" name="originalFilename" value="<?php echo isset($_GET['filename']) ? htmlspecialchars($_GET['filename']) : ''; ?>">
   <br> <input type="submit" value="Save"> <a href="chat.php"><button>Cancel </button></a>
</form>

<script>
function validateForm() {
    var newFilename = document.getElementById("newFilename").value;
    if (newFilename.includes('.')) {
        alert("Only the following characters are alowed a-z A-Z 0-9 - ! @ , ;");
        return false;
    }
    return true;
}
</script>


</body>
</html>
