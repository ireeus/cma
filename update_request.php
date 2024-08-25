<?php

///////////////////UPDATE CHECK//////////////////
function getRemoteFiles($url) {
    $files = array();
    
    // Use cURL to get the content of the directory listing
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    
    // Match all .zip files from the directory listing
    if (preg_match_all('/href="([^"]+\.zip)"/', $result, $matches)) {
        foreach ($matches[1] as $file) {
            $files[] = $file;
        }
    }
    
    return $files;
}

function getLatestVersion($files) {
    $latestVersion = 0;
    
    foreach ($files as $file) {
        // Extract the version number from the file name
        if (preg_match('/(\d+)\.zip$/', $file, $matches)) {
            $version = (int) $matches[1];
            if ($version > $latestVersion) {
                $latestVersion = $version;
            }
        }
    }
    
    return $latestVersion;
}

function getLocalVersion($filePath) {
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        if (preg_match('/Ver:\s*(\d+)/', $content, $matches)) {
            return (int) $matches[1];
        }
    }
    return 0; // Default to 0 if the file does not exist or the version is not found
}

function showUpdateButton($remoteVersion, $localVersion) {
    if ($remoteVersion > $localVersion) {
        echo '<a href="profile.php"><i style="color: orange;" class="fa fa-shield"></i></a>';
        
    } else {
        echo '<i  style="color: black;" class="fa fa-shield"></i>';
    }
}

// Define the URL and local version file path
include('config.php');
$localVersionFilePath = 'ver.txt';

// Get the list of remote files
$remoteFiles = getRemoteFiles($updatesUrl);

// Get the latest version from the remote files
$remoteVersion = getLatestVersion($remoteFiles);

// Get the local version from the local file
$localVersion = getLocalVersion($localVersionFilePath);

// Show the appropriate update button
showUpdateButton($remoteVersion, $localVersion);

/////////////////////////////////////////////////////
include('config.php');
// Function to get the current URL of server 
function getServerUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    
    return $protocol . $domainName . $scriptPath;
}

// Get the current URL of server (a)
$serverAUrl = getServerUrl();

// Define the URL of server (b) where the GET request will be sent
$serverBUrl = $updatesUrl . 'update.php'; 

// Get the local version from the file for the GET request
$version = getLocalVersion($localVersionFilePath);

// Build the complete URL for the GET request
$requestUrl = $serverBUrl . '?serverAUrl=' . urlencode($serverAUrl) . '&version=' . $version;

// Send the GET request to server (b)
$response = file_get_contents($requestUrl);

// Check if the request was successful
if ($response === FALSE) {
    die('Updates not available - contact developer.');
}

// Output the response from server (b)
echo $response;

?>
