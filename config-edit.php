
<?php
session_start();
include('sessionCheck.php');



if(isset($_GET['exit'])){}
    // Check if $_SESSION['selected_session'] is set
    if (isset($_SESSION['selected_session'])) {
        // Unset (remove) $_SESSION['selected_session']
        unset($_SESSION['selected_session']);
    }

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

        input[type="text"], textarea {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 10px;
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

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="submit"] {
            width: 100px;
            align-self: flex-end;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
            <script>
            function confirmDeletion() {
                if (confirm('Are you sure you want to delete all files and folders in this directory? This action cannot be undone.')) {
                    document.getElementById('delete-form').submit();
                }
            }
        </script>
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
        <h2>Edit Configuration</h2>

        <?php
        $configFile = 'config.php';
include($configFile);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Values changeable by admin
            $accountLevel = $_POST['accountLevel'];
            $logo = $_POST['logo'];
            $icon = $_POST['icon'];
            $ImgSize = $_POST['ImgSize'];
            $imgQuality = $_POST['imgQuality'];
            $messageUpdate = $_POST['messageUpdate'];
            $opacity = $_POST['opacity'];
            $pdfIcon = $_POST['pdfIcon'];
            $dateFont = $_POST['dateFont'];
            $messageFont = $_POST['messageFont'];
            $imgSizePercentage = $_POST['imgSizePercentage'];
            $error = $_POST['error'];
            
            //Values not changeable by admin
            $secretkey = $secretkey;
            $iv = $iv;
            $usernameCookieName = $usernameCookieName;            
            $updatesUrl =$updatesUrl;
            
            $configData = "<?php\n";
            $configData .= "///////////////////Default account status after registration//////////////\n";
            $configData .= "\$accountLevel='$accountLevel';  //suspended, limited, standard, advanced or admin\n\n";
            $configData .= "///////////////////////////////////////////////////////////\n";
            $configData .= "////////////////Images upload /////////////////////////////\n";
            $configData .= "\$icon='$icon';                       //icon link\n";
            $configData .= "\$logo='$logo';                       //logo link\n";
            $configData .= "\$ImgSize='$ImgSize';                  //Megabytes\n";
            $configData .= "\$imgQuality='$imgQuality';               //% of the original quality 70% = 70\n\n";
            $configData .= "////////////////Messages///////////////////////////////////\n";
            $configData .= "\$messageUpdate='$messageUpdate';         //chat refresh interval in miliseconds\n";
            $configData .= "\$opacity = '$opacity';               //Color opacity\n\n";
            $configData .= "////////////////////////////////////////////////////////////\n";
            $configData .= "\$pdfIcon='$pdfIcon';                 ////pdf icon size////\n\n";
            $configData .= "////////////////////////////message font////////////////////////////////\n";
            $configData .= "\$dateFont='
size=\"2\"
style=\"
padding: 0px; 
\"';\n\n";
            $configData .= "\$messageFont='
size=\"3\"
style=\"
padding: 8px; 
\"';\n\n";
            $configData .= "\$imgSize='$imgSizePercentage';                 \n\n";
            $configData .= "///////////////////Repository//////////////////////////////\n";
            $configData .= "\$updatesUrl = '$updatesUrl';\n\n";
                             
            
            
            $configData .= "///////////////////////// encryption keys /////////////////////\n";
            $configData .= "\$secretkey='$secretkey';\n";
            $configData .= "\$iv='$iv';\n\n";
            $configData .= "\$usernameCookieName='$usernameCookieName';\n\n";
            $configData .= "/////////////////Error Reporting///////////////////////////\n\n";
            $configData .= "\$error='$error';\n\n";
            $configData .= "//hide errors\n";
            $configData .= "if(\$error==0){\n";
            $configData .= "    error_reporting(0);\n";
            $configData .= "    ini_set('display_errors', 0);\n";
            $configData .= "}\n\n";
            $configData .= "//show errors\n";
            $configData .= "if(\$error==1){\n";
            $configData .= "    error_reporting(E_ALL);\n";
            $configData .= "    ini_set('display_errors', 1);\n";
            $configData .= "}\n";

            file_put_contents($configFile, $configData);
            echo "Configuration updated successfully.";
        }

        // Load existing values from the config file
        include $configFile;
        ?>

        <form method="post" action="config-edit.php">
            <label for="accountLevel">Default Account Access Level:</label>
            <select id="accountLevel" name="accountLevel">
                <option value="suspended" <?php if ($accountLevel == 'suspended') echo 'selected'; ?>>Suspended</option>
                <option value="limited" <?php if ($accountLevel == 'limited') echo 'selected'; ?>>Limited</option>
                <option value="standard" <?php if ($accountLevel == 'standard') echo 'selected'; ?>>Standard</option>
                <option value="advanced" <?php if ($accountLevel == 'advanced') echo 'selected'; ?>>Advanced</option>
                <option value="admin" <?php if ($accountLevel == 'admin') echo 'selected'; ?>>Admin</option>
            </select>

            <label for="logo">Logo link:</label>
            <input type="text" id="logo" name="logo" value="<?php echo $logo; ?>">

            <label for="icon">Icon link:</label>
            <input type="text" id="icon" name="icon" value="<?php echo $icon; ?>"> <a href="icon_generator.php" target="blank"> install icons</a><br>

            <label for="ImgSize">Image Size (MB):</label>
            <input type="text" id="ImgSize" name="ImgSize" value="<?php echo $ImgSize; ?>">

            <label for="imgQuality">Image Quality (%):</label>
            <input type="text" id="imgQuality" name="imgQuality" value="<?php echo $imgQuality; ?>">

            <label for="messageUpdate">Message Update Interval (ms):</label>
            <input type="text" id="messageUpdate" name="messageUpdate" value="<?php echo $messageUpdate; ?>">

            <label for="opacity">Opacity:</label>
            <input type="text" id="opacity" name="opacity" value="<?php echo $opacity; ?>">

            <label for="pdfIcon">PDF Icon Size:</label>
            <input type="text" id="pdfIcon" name="pdfIcon" value="<?php echo $pdfIcon; ?>">

            <label for="dateFont">Date Font:</label>
            <textarea id="dateFont" name="dateFont"><?php echo $dateFont; ?></textarea>

            <label for="messageFont">Message Font:</label>
            <textarea id="messageFont" name="messageFont"><?php echo $messageFont; ?></textarea>

            <label for="imgSizePercentage">Image Preview Size (%):</label>
            <input type="text" id="imgSizePercentage" name="imgSizePercentage" value="<?php echo $imgSize; ?>">

            <label for="error">Error Reporting (0 or 1):</label>
            <input type="text" id="error" name="error" value="<?php echo $error; ?>">

 
            <label for="updatesUrl">Updates repository:</label>
            <input type="text" id="updatesUrl" disabled value="<?php echo $updatesUrl; ?>">
            
            <input type="submit" value="Save">
        </form>
    </div>
    <form action="uninstall.php" method="get" onsubmit="return validateForm();">
        <p> This action cannot be undone.</p>
        <div class="confirmation">
            <input type="checkbox" id="confirmCheckbox" name="confirm" value="yes">
            <label for="confirmCheckbox">I confirm that I want to delete all files and folders.</label>
        </div>
        <input type="hidden" name="action" value="delete">
        <button type="submit">Delete Everything</button>
    </form>

    <script>
        function validateForm() {
            var checkbox = document.getElementById('confirmCheckbox');
            if (!checkbox.checked) {
                alert('You must confirm the deletion by ticking the checkbox.');
                return false;
            }
            return true;
        }
    </script>
    </body>
    </html>
