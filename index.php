<?php
include('config.php');
session_start(); // Start the session

include('sessionCheck.php');

if(isset($_GET['exit'])){}
    // Check if $_SESSION['selected_session'] is set
    if (isset($_SESSION['selected_session'])) {
        // Unset (remove) $_SESSION['selected_session']
        unset($_SESSION['selected_session']);
    }

//retriving user info
$username = $_SESSION['username'];
$userDataFile = "users/" . $username . ".php";
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
    <link rel="stylesheet" href="css/style.css">
    <script href="js/offline.js"></script>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="lib/icons/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
        .thumbnail {
    max-width: 150px;
    max-height: 150px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out; /* Add transition for smooth scaling */
}

.thumbnail:hover {
    transform: scale(3); /* Scale the image by 10% on hover */
}
    </style>

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

    <title>CMA - Collaboration Management Assistant</title>
</head>

<body>
    <?php 
    $pageName ='index.php';
    include('lib/nav.php'); 
    include('lib/sus_re_dir.php');

    ?>

    <div id="options-container">
        <ul>    

            <table border="1" id="recordsTable">
                <thead>
                    <tr>
                        <th>
                            <input type="text" id="searchInput" placeholder="Search Record" onkeyup="filterRecords()"><a href="advanced-search.php"><i class="fa-solid fa-magnifying-glass"> </i> Advanced search</a>
                        </th>
                    </tr>
                </thead>
            </table>

            <table border="1" id="itemsTable">
                <thead>
                    <tr>
                        <th>    
                            <font size="3"><i id="update-icon"></i> 
                            <i class="fa-solid fa fa-hdd-o"> </i> </font> <font size="2"><?php include('lib/size.php'); ?></font>
                            <script>
                                function fetchUpdateStatus() {
                                    $.ajax({
                                        url: 'update_request.php',
                                        method: 'GET',
                                        success: function(data) {
                                            $('#update-icon').html(data);
                                        },
                                        error: function() {
                                            $('#update-icon').html('<p>Error loading update status</p>');
                                        }
                                    });
                                }

                                // Fetch update status immediately
                                fetchUpdateStatus();
                                
                                // Set interval to fetch update status every 30 seconds
                                setInterval(fetchUpdateStatus, 30000);
                            </script>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Function to get all available chat sessions
                    function getAvailableSessions() {
                        $sessionFolder = "sessions/";
                        $sessions = glob($sessionFolder . "*_session.txt");
                        $result = [];
                        foreach ($sessions as $sessionFile) {
                            $content = file_get_contents($sessionFile);

                            // Use a regular expression to find the first image name in the file
                            $pattern = '/\[file: (.*?\.(?:jpg|png|gif))\]/i';
                            if (preg_match($pattern, $content, $matches)) {
                                $imageName = $matches[1];
                            } else {
                                $imageName = '../lib/img/folder-open.png';
                            }

                            $sessionName = basename($sessionFile, '_session.txt');
                            $result[] = [
                                'name' => $sessionName,
                                'image' => $imageName
                            ];
                        }

                        return $result;
                    }

                    $availableSessions = getAvailableSessions();

                    // Set caching headers for images
                    $cacheTime = 604800; // 1 week (604800 seconds)
                    $cacheExpire = gmdate('D, d M Y H:i:s', time() + $cacheTime) . ' GMT';
                    header('Cache-Control: public, max-age=' . $cacheTime . ', immutable');
                    header('Expires: ' . $cacheExpire);

                    foreach ($availableSessions as $session) {
                        echo '<tr><td>';
                        if (!empty($session['image'])) {
                            $imageModifiedTime = filemtime('images/' . $session['image']);
                            $imageUrl = 'images/' . $session['image'] . '?v=' . $imageModifiedTime;
                            echo '<img src="' . $imageUrl . '" 
                            class="thumbnail"
                            alt="Session Image" 
                            width="50" 
                            height="30" 
                            style="
                            object-fit: cover; 
                            margin-right: 10px; 
                            border-radius: 2px; 
                            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
                            
                            ">';
                        }
                        echo '<a href="login.php?session=' . $session['name'] . '">' . $session['name'] . '</a></td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </ul>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function (registration) {
                        console.log('Service Worker registered:', registration);
                    })
                    .catch(function (err) {
                        console.log('Service Worker registration failed:', err);
                    });
            });
        }
    </script>
    <script>
        function filterRecords() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("itemsTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>

    <script src="js/nav.js"></script>
</body>

</html>
