<?php
session_start();
include('sessionCheck.php');
include('config.php');


$loggedInUsername = $_SESSION['username'];
$loggedInUserFile = $dir."users/$loggedInUsername.php";
$loggedInUserType = getUserType($loggedInUserFile);

// Function to get the user type
function getUserType($userFile) {
    $content = file_get_contents($userFile);
    preg_match('/Type: (\w+)/', $content, $matches);
    return $matches[1];
}

// Function to get the list of users and their types
function getUsersData() {
    $users = [];
    foreach (glob($dir."users/*.php") as $file) {
        $username = basename($file, ".php");
        if ($username === 'index') {
            continue; // Skip the index.php file
        }
        $type = getUserType($file);
        $users[$username] = $type;
    }
    return $users;
}


// Function to count the number of admin users
function countAdminUsers() {
    $count = 0;
    foreach (glob($dir."users/*.php") as $file) {
        if (getUserType($file) == 'admin') {
            $count++;
        }
    }
    return $count;
}

// Function to update the user type
function updateUserType($username, $newType) {
    $userFile = $dir."users/$username.php";
    $content = file_get_contents($userFile);
    $content = preg_replace('/Type: \w+/', "Type: $newType", $content);
    file_put_contents($userFile, $content);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon-256x256.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

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

        th, td {
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
    </style>  <!-- Add your existing head content here -->
</head>
<body>
         <?php include('lib/nav.php');   ?>

    <div id="options-container">
         <a href="chat.php"><i class="fa fa-arrow-circle-left" style="font-size:36px"></i></a>
    
        
        
    <?php 
    
    
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $newType = $_POST['type'];
    $currentType = getUserType("users/$username.php");

    // Check if trying to downgrade the last admin
    if ($currentType == 'admin' && $newType != 'admin' && countAdminUsers() <= 1) {
        echo "<center><font color='red'>You cannot downgrade the last remaining admin.</font></center><br>";
    } else if ($loggedInUserType == 'admin' || 
               ($loggedInUserType == 'advanced' && in_array($newType, ['standard', 'limited', 'suspended']))) {
        updateUserType($username, $newType);
        echo "<center><font color='blue'>User type for $username has been updated to $newType.</font></center><br>";
    } else {
        echo "<center><font color='red'>You do not have permission to change the user type to $newType.</font></center><br>";
    }
}

$usersData = getUsersData();
    
    
    if (in_array($loggedInUserType, ['admin', 'advanced'])): ?>
        <form method="POST">
<p>            <label for="username">Select User:</label>
            <select name="username" id="username" onchange="updateAccountType()">
                <?php
                foreach ($usersData as $user => $type) {
                    echo "<option value=\"$user\">$user</option>";
                }
                ?>
            </select>
</p><p>
            <label for="type">Select New Account Type:</label>
            <select name="type" id="type">
                <?php
                $accountTypes = ['suspended', 'limited', 'standard', 'advanced', 'admin'];
                foreach ($accountTypes as $type) {
                    // Restrictions for "advanced" user
                    if ($loggedInUserType == 'advanced' && !in_array($type, ['standard', 'limited', 'suspended'])) {
                        continue;
                    }
                    echo "<option value=\"$type\">$type</option>";
                }
                ?>
            </select>

</p><p>            <input type="submit" value="Update Account Type"></p>
        </form>

        <script>
            // Load user data into a JavaScript object
            const usersData = <?php echo json_encode($usersData); ?>;

            function updateAccountType() {
                const usernameSelect = document.getElementById('username');
                const typeSelect = document.getElementById('type');
                const selectedUser = usernameSelect.value;
                const userType = usersData[selectedUser];

                // Set the type dropdown to the user's current type
                for (let i = 0; i < typeSelect.options.length; i++) {
                    if (typeSelect.options[i].value === userType) {
                        typeSelect.selectedIndex = i;
                        break;
                    }
                }
            }

            // Initialize the account type dropdown on page load
            window.onload = function() {
                updateAccountType();
            };
        </script>
    <?php else: ?>
        <p>You do not have permission to change user account types.</p>
    <?php endif; ?>

        
        </h2>
        <ul>
            


                        
        <font size="2"><br><br><br>
Suspended - No access to any information<br>
Limited - View only with limited content<br>
Standard - Add and Delete your own messages<br>
Advanced - Change privileges<br>

Admin - Delete everyone's messages

</font></ul>
    </div>
            <script src="js/nav.js"></script>

</body>
</html>

