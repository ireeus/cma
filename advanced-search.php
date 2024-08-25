<?php
session_start();

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
        mark {
    color: BLACK; /* Using hexadecimal */
    background-color: #f5beba;
}
    </style>
    <style>
        #searchResults {
            margin-top: 20px;
        }
.thumbnail {
    max-width: 150px;
    max-height: 150px;
    margin-bottom: 10px;
    background-color: #fff;
    padding: 5px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out; /* Add transition for smooth scaling */
}

.thumbnail:hover {
    transform: scale(3); /* Scale the image by 10% on hover */
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
  <h1>Advanced Search</h1>

  <input
    type="text"
    id="searchTerm"
    placeholder="Enter search term"
    value="<?php echo date('y.m.d'); ?>"
    onkeydown="handleKeyPress(event)"
    onfocus="clearDateValue(this)"
    onclick="clearDateValue(this)"
  />

  <button onclick="searchFiles()" style="display: block; margin-top: 7px;">Search</button>

  <div id="searchResults"></div>

  <script>
    function searchFiles() {
      var searchTerm = document.getElementById("searchTerm").value.trim();

      if (searchTerm === "") {
        alert("Please enter a search term.");
        return;
      }

      var xhr = new XMLHttpRequest();
      xhr.open(
        "GET",
        "search.php?term=" + encodeURIComponent(searchTerm),
        true
      );
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          document.getElementById("searchResults").innerHTML = xhr.responseText;
        }
      };
      xhr.send();
    }

    function handleKeyPress(event) {
      if (event.keyCode === 13) {
        // Check if Enter key is pressed
        event.preventDefault(); // Prevent form submission
        searchFiles(); // Trigger the searchFiles function
      }
    }

    function clearDateValue(input) {
      input.value = "";
    }
  </script>
</div>
</body>

</html>
