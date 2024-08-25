<?php
session_start();
include('config.php');
include('sessionCheck.php');


// Check if a session name is provided in the GET parameter
if (isset($_GET['session'])) {
    $newSessionName = $_GET['session'];
    //$newSessionName = str_replace('&quot;', ' in ', $_GET['session']);

    // Update the selected session in the session variable
    $_SESSION['selected_session'] = $newSessionName;

    // Redirect to the updated session
    header("Location: chat.php");
    exit();
}


// Get the username and selected session
$username = $_SESSION['username'];
$selectedSession = $_SESSION['selected_session'];
//echo $selectedSession;
$file_path = "recovery/$username.php";

// Get user info
$userFilePath = 'users/'.$username.'.php';

// Read the content of the user file
$userContent = file_get_contents($userFilePath);

// Define a regular expression pattern to extract the type
$typeRegex = '/Type:\s*(.+)/';

// Search for the type information in the content
if (preg_match($typeRegex, $userContent, $matches)) {
    // Extracted type value
    $userType = trim($matches[1]);

    // Display the extracted type
    $userData= '<center><font size="1"><b> 
<i class="fa-solid fa-id-card" style="font-size:10px"></i></b> '.$username.'<b> | </b> '.$userType.'</font></center>';
} else {
    // Handle case where type information is not found
    echo '<center><font size="1"><b>Error:<b> Type information not found in the user file.</font></center>';
}

// Get the chat session file path based on the selected session
$sessionFolder = "sessions/";
$chatFilePath = $sessionFolder . $selectedSession . "_session.txt";


// Function to save a new message to the chat session file
function saveMessage($filePath, $message) {
    // Append the new message to the file after sanitizing
    $sanitizedMessage = htmlspecialchars("$message", ENT_QUOTES, 'UTF-8');
    file_put_contents($filePath, $sanitizedMessage . "\n", FILE_APPEND);
}

// Function to save a PDF file
function savePDF($file, $uploadDir)
{
    $fileName = time() . '_' . basename($file['name']);
    $targetFilePath = $uploadDir . $fileName;

    // Check if the file is a PDF
    if ($file['type'] == 'application/pdf') {
        // Save the PDF file
        move_uploaded_file($file['tmp_name'], $targetFilePath);
        return $fileName;
    }

    return false;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message'])) {
        $newMessage = $_POST['message'];
        $timestamp = date('y.m.d H:i:s'); // Add time to the message

        // Check if a file is uploaded
        if (!empty($_FILES['file']['name'])) {
            $uploadDir = "";

// Check if the uploaded file is a PDF
if ($_FILES['file']['type'] == 'application/pdf') {
    $uploadDir = "pdfs/";
    $fileTypeLabel = "pdf";
} elseif (in_array($_FILES['file']['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
    // Check if the file type is an image (JPEG, PNG, GIF)
    $uploadDir = "images/";
    $fileTypeLabel = "file";
} else {
    // If not a PDF or image, prevent the upload (you can handle this case as needed)
    echo "Invalid file type. Only PDFs and images are allowed.";
    exit();
}
            // Save the file using the appropriate function
            $uploadedFile = saveFile($_FILES['file'], $uploadDir);

            if ($uploadedFile) {
                // Include the file link in the message
                $newMessage .= " [$fileTypeLabel: $uploadedFile]";
            }
        }

        // Save the new message to the chat session file with the username after sanitizing
        $messageWithUsername = $username . ': (' . $timestamp . ') ' . $newMessage;
        saveMessage($chatFilePath, $messageWithUsername);

        // Redirect to avoid form resubmission on page refresh
        header('Location: chat.php');
        exit();
    }
}
/////////////////////////////ok///////////////////////////////////////
// Function to save a file (PDF or image)
function saveFile($file, $uploadDir)
{
include('config.php');
    $fileName = time() . '_' . basename($file['name']);
    $targetFilePath = $uploadDir . $fileName;

    // Save the file
    move_uploaded_file($file['tmp_name'], $targetFilePath);

    // If the uploaded file is a JPEG image, reduce its quality to 50%
    if ($file['type'] == 'image/jpeg') {
        // Load the original image
        $image = imagecreatefromjpeg($targetFilePath);
        
        // Save the image with reduced quality
        imagejpeg($image, $targetFilePath, $imgQuality);

        // Free up memory
        imagedestroy($image);
    }

    return $fileName;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
<link rel="icon" type="image/png" href="favicon.png">    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="lib/icons/css/all.css">
    <style>
        .styled-text {
            margin: 10px; /* Set the margin around the text */
            font-family: 'Arial', sans-serif; /* Set the font family (optional) */
            line-height: 1.5; /* Set the line height (optional) */
        }

    /* Hide the original file input button */
    input[type="file"] {
      display: none;
    }

    /* Style the custom button */
    .custom-upload-btn {
      display: inline-block;
      padding: 6px 10px;
      cursor: pointer;
      background-color: #3498db;
      color: #fff;
      border: none;
      border-radius: 4px;
    }

    /* Style the Font Awesome icon */
    .fa-icon {
      margin-right: 8px;
    }
  </style>
        <title>CMA - Collaboration Management Assistant</title>


    <!-- Include jQuery (you can download it or use a CDN) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="lib/icons/css/all.css">

<script>
    $(document).ready(function () {
        var chatWindow = $('#chat-window');

        function updateChat() {
            // Store the current scroll position
            var scrollPos = chatWindow.prop('scrollHeight') - chatWindow.scrollTop();

            // AJAX request to get the latest chat messages
            $.ajax({
                type: 'GET',
                url: 'get_messages.php',
                data: { session: '<?php echo $selectedSession; ?>' },
                success: function (data) {
                    // Update the chat window with the latest messages
                    chatWindow.html(data);

                    // Set the scroll position back to the stored value
                    chatWindow.scrollTop(chatWindow.prop('scrollHeight') - scrollPos);
                },
            });
        }

        // Update the chat every 2 seconds (adjust as needed)
        setInterval(updateChat, <?php echo $messageUpdate; ?>);
    });
</script>

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/js/offline.js');
  }
</script>
<script>
  if (typeof navigator.serviceWorker !== 'undefined') {
    navigator.serviceWorker.register('/js/offline.js')
  }
</script>
</head>
<body>
<?php 
echo $userData;
?>
    <div id="chat-container">
        <div id="chat-window">
<?php include('get_messages.php'); 
    include('lib/sus_re_dir.php');
?> 
        </div>
<?php
if($userType!="limited"){echo'<form id="chat-form" enctype="multipart/form-data">
           
  <!-- Custom-styled button with Font Awesome icon -->
  <label for="file" class="custom-upload-btn">
   <i class="fa-solid fa-upload" style="font-size:12px"></i> </label> <input type="text" id="message" name="message" placeholder="Message or select image to upload" required><input type="file" id="file" name="file" accept="image/*, application/pdf">





            <button type="button" onclick="sendMessage()">Submit</button>
        </form>
        ';}
?>
 <script>
    function sendMessage() {
        // Get the form data
        var formData = new FormData(document.getElementById('chat-form'));

        // Check if a file is being uploaded
        var fileInput = document.getElementById('file');
        if (fileInput.files.length > 0) {
            // Get the first file in the input
            var uploadedFile = fileInput.files[0];

            // Check if the file size exceeds 2MB (2097152 bytes)
            if (uploadedFile.size > <?php
$ImgSize1=$ImgSize*1000000;

 echo$ImgSize1.') {
                alert("Uploaded image should be no larger than '.$ImgSize.'MB.");';?>
                return;
            }
        }

        // Create an XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Configure it to perform a POST request
        xhr.open('POST', 'chat.php', true);

        // Set up a callback function to handle the response
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 400) {
                // Success! You can handle the response here if needed
                console.log(xhr.responseText);

                // Clear the input box after successful submission
                document.getElementById('message').value = '';

                // Clear the file input field
                document.getElementById('file').value = '';

                // Reset the form (optional)
                // document.getElementById('chat-form').reset();
            } else {
                // Error handling
                console.error(xhr.statusText);
            }
        };

        // Send the FormData object
        xhr.send(formData);
    }

    // Add an event listener to the form for submit events
    document.getElementById('chat-form').addEventListener('submit', function (event) {
        // Prevent the default form submission
        event.preventDefault();

        // Call the sendMessage function when the form is submitted
        sendMessage();
    });

    // Add an event listener to the input for keydown events
    document.getElementById('message').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            // Prevent the default Enter key behavior (e.g., new line)
            event.preventDefault();

            // Call the sendMessage function when Enter is pressed
            sendMessage();
        }
    });
</script>
        <form method="post" action="index.php?exit">
 <table style="width: 100%;">
  <tr>    
   

<?php 
if($userType!="limited"){
echo' <td style="width: 15%;"><center><a href="rename.php?filename='.$selectedSession.'">
        <img src="lib/img/rename.png" width="25" alt="Profile Image">
      </a>  </td>';
}
?>
 
  
    <td style="width: 60%;"><center>
      <a href="index.php?exit"><button style="padding: 4px 8px; background-color: #777ce0; color: #fff; border: none; border-radius: 4px; cursor: pointer;" type="submit" name="exit-session">
        <i class="fa-solid fa-door-open" style="font-size:10px" > </i> 
        <b>Exit</b>
      </button></a>
    </td>

    <td style="width: 15%; text-align: right;"><center>
      <a href="profile.php">
          <i class="fa-solid fa-user-gear"style="font-size:25px"> </i> 
      </a>
    </td>
  </tr>
</table>

        </form>
      
<?php
// Display information in a table with two columns and one row
    echo '<table style="height: 3px;" border="0">';
    echo '<tr style="height: 3px;">';
 // Read the content of the file
$content = file_get_contents($chatFilePath);

// Extract the header
$headerRegex = '/^\[([^-\]]+)-(\d{4}-\d{2}-\d{2} \d{2}\.\d{2}\.\d{2})\]/';
if (preg_match($headerRegex, $content, $matches)) {
    $fileUsername = trim($matches[1]);
    $fileDate = trim($matches[2]);
// Check for lines with data (excluding the header line)
 // Split content into lines
    $lines = explode("\n", $content);   
    
     
// Count non-empty lines (excluding the header line)
$linesWithData = 0;
foreach ($lines as $line) {
    if (trim($line) !== "" && $line !== $headerRegex) {
        $linesWithData++;
    }
}
$linesWithData=$linesWithData-1;
if($userType!="limited" and $userType!="standard" ){
    echo '<td style="height: 3px;">';
    
     echo'<center><font size="1"><b><i class="fa-solid fa-file-lines" style="font-size:12px"> </i>  </b> '.$selectedSession.'</font> <br>
   | <font size="1"><b>
<i class="fa-solid fa-user-plus" style="font-size:12px"></i> </b> ' . $fileUsername . '</font> |
    <font size="1"><b>
<i class="fa-solid fa-calendar-days" style="font-size:12px"> </i></b> ' . $fileDate . '</font> |
    <font size="1"><b>
<i class="fa-solid fa-rectangle-list" style="font-size:12px"></i> </b> ' . $linesWithData . '</font> |
    </td></tr>';
}



    if ($linesWithData > 0) {
 } else {
        // Display delete button
if($fileUsername==$username and $userType!="limited" or $userType==="admin"){
    echo '<tr><td style="height: 3px;">';
    // Display the total number of lines
        echo '<form method="post" action="delete_session.php">'; // Replace "delete_script.php" with your actual delete script
        echo '<input style="    padding: 8px 12px;
    background-color: #e67e7e;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;" type="submit" value="! Delete '.$selectedSession.' !">';
        echo '<input type="text" name="filename" hidden value="'.$chatFilePath.'">';
        echo '</form>'; 
           echo'</td>';
    echo '</tr>';
    echo '</table>';}
    }
} else {
    // Invalid header format
    echo '
<script>
        // Optional: If you want an immediate redirect without the meta refresh tag
        window.onload = function() {
            window.location.href = "index.php";
        };
    </script>
';

}


///activity log
include("lib/logfile.php");

?>


        
    </div>
    <script src="js/main.js"></script>
</body>
</html>
