<?php
session_start();
include('config.php');
// Check if the user is logged in, redirect to chat.php
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

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

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
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
    <title>CAS - Database</title>
</head>

<body>
    <?php 
	include('lib/nav.php'); 
    include('lib/sus_re_dir.php');

?>
    <div id="options-container">

        <div id="filePreview">
            <?php
            function formatBytes($bytes, $precision = 2) {
                $units = array('B', 'KB', 'MB', 'GB', 'TB');
                $bytes = max($bytes, 0);
                $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                $pow = min($pow, count($units) - 1);
                $bytes /= pow(1024, $pow);
                return round($bytes, $precision) . ' ' . $units[$pow];
            }

            $folderPath = 'log/';
            $files = scandir($folderPath);

            if (isset($_GET['file'])) {
                $selectedFile = $_GET['file'];
                $filePath = $folderPath . $selectedFile;

                if (file_exists($filePath) && is_readable($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    $lines = array_filter(explode("\n", $fileContent));
                    $sortedLines = array_reverse($lines);
                    $sortedContent = implode("\n", $sortedLines);

                    $fileSize = formatBytes(filesize($filePath)); // Get the file size

                    echo '<font size="3">Preview: ' . htmlspecialchars($selectedFile) . ' (' . $fileSize . ')</font>    
<label>
        <input type="checkbox" id="tickCheckbox"> Live
    </label>';
                    echo '<textarea style="width: 100%; height: 350px;" readonly>';
                    echo htmlspecialchars($sortedContent);
                    echo '</textarea>';
                } else {
                    echo '<p>Selected file does not exist or cannot be read.</p>';
                }
            }
            ?>
        </div>
        <div id="searchContainer">
            <input type="text" id="searchInput" placeholder="Search...">
        </div>        <hr>

        <ul>
            <?php
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }

                echo '<li><a href="?file=' . urlencode($file) . '">' . htmlspecialchars($file) . '</a></li>';
            }
            ?>
        </ul>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const filePreview = document.getElementById('filePreview');
        const textarea = filePreview.querySelector('textarea');
        const originalContent = textarea.value;

        searchInput.addEventListener('input', filterFileContent);

        function filterFileContent() {
            const searchTerm = searchInput.value.trim().toLowerCase();
            const fileContent = textarea.value.toLowerCase();
            const lines = fileContent.split('\n');
            let filteredContent = '';

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                if (line.includes(searchTerm)) {
                    filteredContent += `${line}\n`;
                }
            }

            textarea.value = filteredContent;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tickCheckbox = document.getElementById('tickCheckbox');

            // Check the stored checkbox state on page load
            const storedCheckboxState = localStorage.getItem('tickCheckboxState');
            if (storedCheckboxState === 'checked') {
                tickCheckbox.checked = true;
            }

            // Function to reload the page after 10 seconds
            function reloadPage() {
                location.reload(); // Reload the current page
            }

            // Check if the checkbox is ticked every second
            setInterval(function() {
                if (tickCheckbox.checked) {
                    // Store the checkbox state in localStorage
                    localStorage.setItem('tickCheckboxState', 'checked');
                    setTimeout(reloadPage, 5000); // 10000 milliseconds = 10 seconds
                } else {
                    // Remove the stored checkbox state if not checked
                    localStorage.removeItem('tickCheckboxState');
                }
            }, 1000); // Check every 1 second (1000 milliseconds)
        });
    </script>
</body>
</html>
