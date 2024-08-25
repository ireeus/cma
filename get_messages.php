<?php
session_start();
include('config.php');

// Get the selected session from the session variable
$selectedSession = isset($_SESSION['selected_session']) ? $_SESSION['selected_session'] : '';

// Get the chat session file path based on the selected session
$sessionFolder = "sessions/";
$chatFilePath = $sessionFolder . $selectedSession . "_session.txt";

// Function to get chat messages from the chat session file
function getChatMessages($filePath) {
    // Check if the file exists
    if (file_exists($filePath)) {
        // Read the content of the file
        $content = file_get_contents($filePath);
        return explode("\n", $content);
    }

    return [];
}

$username = $_SESSION['username'];

// Getting account status
$userFilePath = 'users/'.$username.'.php';

// Read the content of the user file
$userContent = file_get_contents($userFilePath);

// Define a regular expression pattern to extract the type
$typeRegex = '/Type:\s*(.+)/';

// Search for the type information in the content
if (preg_match($typeRegex, $userContent, $matches)) {
    // Extracted type value
    $userType = trim($matches[1]);
}

// Function to replace emoticons in the message content
function replaceEmoticons($text) {
    $emoticonMapping = array(
        ":D" => "😁",
        ":)" => "😃",
        ":(" => "😢",
        ";)" => "😉",
        ":p" => "😛",
        ":o" => "😲",
        ":|" => "😐",
        ":*" => "😘",
        ";/" => "😕",
        "@)" => "😏",
        "@W" => "😔",
        ":^)" => "😄",
        ":,(" => "😢",
        "XD" => "😆",
        ":-D" => "😃",
        "@D" => "😂",
        ";o)" => "😇",
        ":K" => "😺",
        "@h" => "❤️",
        "*(" => "😥",
        "@rrr" => "😡",
        ":ii" => "😖",
        ":S" => "😖",
        "O:)" => "😇",
        "o.O" => "😳",
        ":|" => "😐",
        "(y)" => "👍",
        "@b" => "🐦",
        "(H)" => "🏠",
        "(C)" => "©️",
        "(R)" => "®️",
        "(T)" => "™️",
        ":+1" => "👍",
        ":-1" => "👎",
        "(A)" => "🅰️",
        "(B)" => "🅱️",
        "(AB)" => "🆎",
        "(O)" => "🅾️",
        "**help" => "<a href='help.html' target='blank'>help</a>"
    );

    foreach ($emoticonMapping as $key => $value) {
        $text = str_replace($key, $value, $text);
    }
    return $text;
}

// Function to replace image links with HTML image tags
function replaceImageLinks($text) {
    $selected_session = $_SESSION['selected_session'];
    include('config.php');
    $pattern = '/\[file: ([^\]]+)\]/';
    $replacement = '<a href="get_data.php?data=$1&sessionName='.$selected_session.'" target="blank"><img src="images/$1"
    style="margin-top: 0px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); border-radius: 5px; margin-bottom: 0px;" width="'.$imgSize.'" 
    alt="Image"></a>';
    return preg_replace($pattern, $replacement, $text);
}

// Function to replace pdf links with HTML image tags
function replacePdfLinks($text) {
    include('config.php');
    $pattern = '/\[pdf: ([^\]]+)\]/';
    $replacement = '<br><a href="pdfs/$1" target="blank"><img src="lib/img/pdf.png" style="padding: 10px; margin-top: 0px; 
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3); border-radius: 5px; margin-bottom: 0px;" width="'.$pdfIcon.'" alt="pdf"></a>';
    return preg_replace($pattern, $replacement, $text);
}

// Get chat messages from the chat session file and not reverse the order
$chatMessages = getChatMessages($chatFilePath);

// Function to generate a background color based on the username
function getUsernameColor($username) {
    include('config.php');
    // Generate a hash from the username
    $hash = md5($username);
    // Extract RGB values from the hash
    $red = hexdec(substr($hash, 0, 2));
    $green = hexdec(substr($hash, 2, 2));
    $blue = hexdec(substr($hash, 4, 2));
    // Return the formatted RGB color
    return sprintf('rgba(%d, %d, %d, %f)', $red, $green, $blue, $opacity);
}

// Function to replace URLs with hyperlinks
function replaceUrls($text) {
    // Define the pattern for matching URLs
    $urlPattern = '/(https?:\/\/[^\s]+)/';
    // Replace URLs with hyperlinks
    $text = preg_replace($urlPattern, '<a href="$1" target="_blank">$1</a>', $text);
    return $text;
}

// Display chat messages
foreach ($chatMessages as $message) {
    // Split the message into username and content
    list($messageUsername, $messageContent) = explode(':', $message, 2);

    // Trim and check if the message is not empty
    $trimmedMessageContent = trim($messageContent);
    if (!empty($trimmedMessageContent)) {
        // Check if the message contains a date inside brackets
        if (preg_match('/\((\d{2}:\d{2}:\d{2})\)/', $trimmedMessageContent, $matches)) {
            // If a date is found, change the font size
            $fontSizeStyle = 'font-size: 12px;';
            $trimmedMessageContent = preg_replace('/\((\d{2}:\d{2}:\d{2})\)/', '<span style="' . $fontSizeStyle . '">$0</span>', $trimmedMessageContent);
        }

        // Replace emoticons in the message content
        $trimmedMessageContent = replaceEmoticons($trimmedMessageContent);

        // Replace image links in the message content
        $trimmedMessageContent = replaceImageLinks($trimmedMessageContent);

        // Replace pdf links in the message content
        $trimmedMessageContent = replacePdfLinks($trimmedMessageContent);

        // Replace URLs with hyperlinks
        $trimmedMessageContent = replaceUrls($trimmedMessageContent);

        // Get the background color based on the username
        $backgroundColor = getUsernameColor($messageUsername);

        // Display the message with a label for the username and dynamic background color
        echo '<div class="message-container" style="background-color: ' . $backgroundColor . ' "><p><span class="username-label">';
        
        if ($userType === "admin") {
            echo '<a href="user_edit.php?userName=' . htmlspecialchars(trim($messageUsername), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(trim($messageUsername), ENT_QUOTES, 'UTF-8') . '</a>';
        } elseif ($userType === "advanced" && $messageUsername !== 'admin') {
            echo '<a href="user_edit.php?userName=' . htmlspecialchars(trim($messageUsername), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(trim($messageUsername), ENT_QUOTES, 'UTF-8') . '</a>';
        } elseif ($userType !== "limited") {
            echo htmlspecialchars(trim($messageUsername), ENT_QUOTES, 'UTF-8');
        } elseif ($userType === "limited") {
            echo "hidden";
        }

        // Separate date from message
        $dateEndPos = strpos($trimmedMessageContent, ')');
        $dateOnly = substr($trimmedMessageContent, 1, $dateEndPos - 1);
        $messageOnly = substr($trimmedMessageContent, $dateEndPos + 2); // +2 to skip ') ' after the date

        echo ':</span><font '.$dateFont.'"> ' . $dateOnly . '</font><br><div><font '.$messageFont.'><i> ' . $messageOnly . '</i></font></div></p>';

        // Additional actions for the message based on user type
        $username = $_SESSION['username'];
        $messageUsername = htmlspecialchars(trim($messageUsername), ENT_QUOTES, 'UTF-8');
        $username = htmlspecialchars(trim($username), ENT_QUOTES, 'UTF-8');

        if ($messageUsername == $username && $userType != "limited" || $userType === "admin") {
            echo '<table>';
            echo '<tr>';
            echo '<td>';
            echo '<form method="post" action="delete_message.php?file=' . $chatFilePath . '&delete=' . $message . '">';
            echo '<button class="small-delete-button" style="font-size: 10px; padding: 6px; background-color: darkred; color: white;" onclick="deleteMessage()">Delete</button>';
            echo '</form>';
            echo '</td>';

            if (strpos($message, "[file:") !== false && $userType === "advanced") {
                echo '<td>';
                echo '<form method="post" action="img-icon.php?file=' . $chatFilePath . '&move=' . $message . '">';
                echo '<button style="font-size: 10px; padding: 6px; background-color: green; color: white;" onclick="deleteMessage()">^Top^</button>';
                echo '</form>';
                echo '</td>';
            }elseif ($userType === "admin") {
                echo '<td>';
                echo '<form method="post" action="img-icon.php?file=' . $chatFilePath . '&move=' . $message . '">';
                echo '<button style="font-size: 10px; padding: 6px; background-color: green; color: white;" onclick="deleteMessage()">^Top^</button>';
                echo '</form>';
                echo '</td>';
            }

            echo '</tr>';
            echo '</table>';
        }

        echo '</div>';
    }
}
?>
