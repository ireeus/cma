<?php
function createSession($sessionFolder, $sessionFileName) {
    $chatFilePath = $sessionFolder . $sessionFileName;
    
    // Create a new session file
    if (!file_exists($chatFilePath)) {
        file_put_contents($chatFilePath, ''); // Start with an empty file
        return true;
    }

    return false; // Session file already exists
}
?>
