<?php

// Check if the host is "https://cloudapps.zapto.org/cma/install.php"
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'cloudapps.zapto.org' && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/cma-development/install.php') {
    echo 'Executing this code is not allowed on this host.';
    exit;
}

function delete_files($dir, $exceptions = [], $exclude_folders = []) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path) && !in_array($file, $exceptions)) {
                unlink($path);
            } elseif (is_dir($path) && !in_array($file, $exclude_folders)) {
                delete_files($path, $exceptions, $exclude_folders);
                rmdir($path);
            }
        }
    }
}

function get_highest_numbered_zip($dir) {
    $zip_files = glob($dir . DIRECTORY_SEPARATOR . '*.zip');
    $highest_numbered_zip = '';
    $highest_number = -1;

    foreach ($zip_files as $zip_file) {
        preg_match('/(\d+)/', basename($zip_file), $matches);
        if ($matches) {
            $number = (int)$matches[1];
            if ($number > $highest_number) {
                $highest_number = $number;
                $highest_numbered_zip = $zip_file;
            }
        }
    }

    return $highest_numbered_zip;
}

$dir = __DIR__;
$exceptions = ['config.php'];
$exclude_folders = ['users', 'sessions', 'online', 'images', 'login_attempts', 'pdfs', 'temp', 'log', 'logo', 'recovery'];

delete_files($dir, $exceptions, $exclude_folders);

// Find the zip file with the highest number in the name in the "temp" folder
$temp_dir = $dir . DIRECTORY_SEPARATOR . 'temp';
$highest_numbered_zip = get_highest_numbered_zip($temp_dir);

if ($highest_numbered_zip) {
    $zip = new ZipArchive;
    if ($zip->open($highest_numbered_zip) === TRUE) {
        $zip->extractTo($dir);
        $zip->close();
    } else {
        echo 'Failed to open the zip file.';
    }

    // Remove install.php if it exists
    $install_file = $dir . DIRECTORY_SEPARATOR . 'install.php';
    if (file_exists($install_file)) {
        unlink($install_file);
        unlink($highest_numbered_zip);
        header('Location: profile.php');

    }
} else {
    echo 'No zip files found in the temp directory.';
}
header('Location: update.php');
?>
