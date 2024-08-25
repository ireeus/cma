<?php
// Define the temp directory
$tempDir = __DIR__ . '/temp';

// Function to list temporary files
function listTempFiles($tempDir) {
    $files = [];
    if (is_dir($tempDir)) {
        if ($dh = opendir($tempDir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    $files[] = $file;
                }
            }
            closedir($dh);
        } else {
            return ["Unable to open temp directory."];
        }
    } else {
        return ["Temp directory does not exist."];
    }
    return $files;
}

// Handle file deletion
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $fileToDelete = $_POST['delete_file'];
    $filePath = $tempDir . '/' . $fileToDelete;
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            $message = "File '$fileToDelete' deleted successfully.";
        } else {
            $message = "Failed to delete file '$fileToDelete'.";
        }
    } else {
        $message = "File '$fileToDelete' not found.";
    }
}

// Fetch and list temporary files
$tempFiles = listTempFiles($tempDir);



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="favicon-256x256.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        #output-area {
            width: 100%;
            max-width: 800px;
            resize: vertical;
        }
        @media (max-width: 600px) {
            #output-area {
                max-width: 100%;
            }
        }
        .progress-bar {
            width: 0;
            height: 20px;
            background-color: #4caf50;
            text-align: center;
            color: white;
            line-height: 20px;
            transition: width 0.4s;
        }
    </style>
</head>
<body>
    <?php 
    include('lib/nav.php');   
    include('lib/sus_re_dir.php');
    ?>

    <div id="options-container">
        <a href="profile.php"><i class="fa fa-arrow-circle-left" style="font-size:36px"></i></a>
        <h3>System Updates</h3>
Current
        <?php
       include('ver.txt');
       echo"<br>";
        // Define paths and URLs
        include('config.php');
        $tempDir = __DIR__ . '/temp';
        $websiteDir = __DIR__; // Adjust as necessary

        // Ensure temp directory exists
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Function to fetch update files
        function fetchUpdateFiles($url) {
            $files = [];
            $html = file_get_contents($url);
            if ($html === false) {
                return $files;
            }

            preg_match_all('/href="([^"]*\.zip)"/i', $html, $matches);
            if (isset($matches[1])) {
                foreach ($matches[1] as $file) {
                    $files[] = $file;
                }
            }
            return $files;
        }

        // Function to list updates and find the latest one
        function listUpdates($files) {
            if (empty($files)) {
                return ["No updates available.", ''];
                $flag=1;
            }

            // Sort files to find the latest based on timestamp in filename
            usort($files, function($a, $b) {
                return strnatcmp($b, $a); // Sort descending by filename
            });

            $latest = $files[0];
            $output = "<b>Update Ver:</b> ";
            foreach ($files as $file) {
                if ($file === $latest) {
                    include('config.php');
                    $details = file_get_contents( $updatesUrl .'log.php');
                    $parts = explode('.', $file);
                    $fileTrim = $parts[0];
                    $output .= $fileTrim.' <br><i>- '.$details.'</i>
                    ';        

                            

                } else {
                    //$output .= "  $file<br>";
                }
            }

            return [$output, $latest];
        }

        // Function to read the timestamp from ver.txt
        function getVerTimestamp() {
            $verFile = 'ver.txt';
            if (file_exists($verFile)) {
                $content = file_get_contents($verFile);
                if (preg_match('/Ver: (\d+)/', $content, $matches)) {
                    return $matches[1];
                }
            }
            return null;
        }

        // Function to download and install update with progress
        function downloadAndInstallUpdate($fileUrl, $tempDir, $websiteDir) {
            $tempFile = $tempDir . '/' . basename($fileUrl);
            $progressFile = $tempDir . '/progress.txt'; // For storing progress info

            $ch = curl_init($fileUrl);
            $fp = fopen($tempFile, 'w+');

            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($downloadTotal, $downloadNow, $uploadTotal, $uploadNow) use ($progressFile) {
                if ($downloadTotal > 0) {
                    $progress = ($downloadNow / $downloadTotal) * 100;
                    //file_put_contents($progressFile, sprintf("%.2f", $progress));
                }
                return 0; // Continue the download
            });
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);

            $result = curl_exec($ch);
            if ($result === false) {
                fclose($fp);
                curl_close($ch);
                return "Failed to download the file from $fileUrl: " . curl_error($ch);
            }

            fclose($fp);
            curl_close($ch);

            // Check if download was successful
            if (filesize($tempFile) === 0) {
                return "Downloaded file is empty.";
            }

            // Unzip the file into the website directory
            $zip = new ZipArchive();
            if ($zip->open($tempFile) === TRUE) {
                if ($zip->extractTo($websiteDir)) {
                    $zip->close();
                    echo "Update downloaded successfully.";
                    
                } else {
                    $zip->close();
                    return "Failed to extract the zip file.";
                }
            } else {
                return "Failed to open the zip file.";
            }
        }

        // Handle form submission
        $message = '';
        $progress = '0';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $selectedFile = $_POST['update'];
            $fileUrl = $updatesUrl . $selectedFile;
            $message = downloadAndInstallUpdate($fileUrl, $tempDir, $websiteDir);

            // Read progress if available
            $progressFile = $tempDir . '/progress.txt';
            if (file_exists($progressFile)) {
                $progress = file_get_contents($progressFile);
                unlink( $progress);
            }
        }

        // Main logic
        $files = fetchUpdateFiles($updatesUrl);
        list($updateList, $latestFile) = listUpdates($files);
        
        // Get the timestamp from ver.txt
        $verTimestamp = getVerTimestamp();
        $isLatest = $verTimestamp && strpos($latestFile, $verTimestamp) !== false;

        // Display the list of updates only if it's not the latest update
        if (!$isLatest) {
            echo "<p>$updateList</p>";
            ?>
            <form method="post">
                <input type="hidden" name="update" value="<?php echo htmlspecialchars($latestFile); ?>">
                <input type="submit" value="Download and Install Latest Update">
            </form>
            <?php
        } else {
            echo "<p>This is the latest update.</p>";
        }
        ?>
<?php

// Sprawdź, czy plik install.php istnieje
$install_file = __DIR__ . DIRECTORY_SEPARATOR . 'install.php';

if (file_exists($install_file)) {
    
    

// Sprawdź, czy istnieje plik ZIP w folderze temp
$temp_folder = __DIR__ . DIRECTORY_SEPARATOR . 'temp';
$zip_files = glob($temp_folder . DIRECTORY_SEPARATOR . '*.zip');

// Funkcja pobierająca zawartość URL
function get_remote_files($url) {
    $html = file_get_contents($url);
    preg_match_all('/<a href="([^"]+\.zip)">/', $html, $matches);
    return $matches[1];
}
    
if (!empty($zip_files)) {
} else {
    // Pobierz pliki z podanego URL
    $url = $updatesUrl;
    $zip_files_remote = get_remote_files($url);

    if (!empty($zip_files_remote)) {
        // Znajdź plik z największym numerem w nazwie
        usort($zip_files_remote, function($a, $b) {
            return preg_replace('/\D/', '', basename($b)) - preg_replace('/\D/', '', basename($a));
        });
        $largest_zip_file = $zip_files_remote[0];

        // Pobierz plik
        $file_content = file_get_contents($url . '/' . $largest_zip_file);
        $destination = $temp_folder . DIRECTORY_SEPARATOR . basename($largest_zip_file);
        file_put_contents($destination, $file_content);

        echo 'update file downloaded';
    } else {
        echo 'No ZIP files found on the server.';
    }
}
    $install="active";

    echo '<br><a href="install.php">Click here to complete the installation</a><br>';
} else {
    echo '';
}


?>


        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?><br><br>Updates history:
  <textarea id="output-area" rows="3" cols="50" readonly><?php
// Read the content of verlog.php
$filePath = 'verlog.php';
$content = file_get_contents($filePath);

// Split the content into lines
$lines = explode("\n", $content);

// Reverse the order of the lines
$reversedLines = array_reverse($lines);

// Join the reversed lines back into a single string
$reversedContent = implode("\n", $reversedLines);

// Output the reversed content
echo $reversedContent;
?>

   </textarea>

        <?php if (!empty($tempFiles) and !$install=="active"): ?>
            <!-- Display the temp files -->
            <h3>Temporary Files</h3>
            <ul>
                <?php foreach ($tempFiles as $file): ?>
                    <li><?php echo htmlspecialchars($file); ?>
                        <form style="display:inline;" method="post" onsubmit="return confirm('Are you sure you want to delete this file?');">
                            <input type="hidden" name="delete_file" value="<?php echo htmlspecialchars($file); ?>">
                            <input type="submit" value="Delete">
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>                                                                   

    </div>
    <script src="js/nav.js"></script>
</body>
</html>
