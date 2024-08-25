<?php
function getFolderSize($dir) {
    $totalSize = 0;

    // Check if the path is a directory
    if (is_dir($dir)) {
        // Open the directory
        if ($dh = opendir($dir)) {
            // Iterate through each file and subdirectory
            while (($file = readdir($dh)) !== false) {
                // Skip . and .. directories
                if ($file != '.' && $file != '..') {
                    // Construct the full path of the file or subdirectory
                    $filePath = $dir . '/' . $file;

                    // Check if the path is a directory
                    if (is_dir($filePath)) {
                        // Recursively calculate the size of the subdirectory
                        $totalSize += getFolderSize($filePath);
                    } else {
                        // Get the size of the file and add to the total size
                        $totalSize += filesize($filePath);
                    }
                }
            }
            // Close the directory handle
            closedir($dh);
        }
    }

    return $totalSize;
}

// Specify the path to the directory whose size you want to calculate
$directory = './';

// Call the function to get the total size of the directory
$totalSizeInBytes = getFolderSize($directory);

// Format the size to a human-readable format (bytes, KB, MB, etc.)
function formatSize($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 0) . ' ' . $units[$i];
}

// Display the total size of the directory
echo formatSize($totalSizeInBytes);
?>
