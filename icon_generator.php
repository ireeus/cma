<?php
include('config.php');
// Check if GD library is installed
if (!extension_loaded('gd')) {
    die('Error: GD library is not installed. Please install the GD library to use this script.');
}

// Path to the original image
$originalImagePath = $icon;

// Sizes to generate
$sizes = [
    '512x512' => 'android-launchericon-512-512.png',
    '192x192' => 'android-launchericon-192-192.png',
    '144x144' => 'android-launchericon-144-144.png',
    '96x96'   => 'android-launchericon-96-96.png',
    '72x72'   => 'android-launchericon-72-72.png',
    '48x48'   => 'android-launchericon-48-48.png',
];

// Directory to save resized images
$saveDir = 'android/';

// Create directory if it doesn't exist
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0777, true);
}

// Function to resize image
function resizeImage($srcPath, $destPath, $width, $height) {
    list($origWidth, $origHeight) = getimagesize($srcPath);
    
    $imageRes = imagecreatetruecolor($width, $height);
    $imageSrc = imagecreatefrompng($srcPath);
    
    // Resample the original image to the new size
    imagecopyresampled($imageRes, $imageSrc, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
    
    // Save the resized image
    imagepng($imageRes, $destPath);
    
    // Free memory
    imagedestroy($imageRes);
    imagedestroy($imageSrc);
}

// Check if the original image exists
if (!file_exists($originalImagePath)) {
    die('Error: Original image file does not exist. Please check the file path.');
}

// Loop through each size and create resized images
foreach ($sizes as $size => $filename) {
    list($width, $height) = explode('x', $size);
    resizeImage($originalImagePath, $saveDir . $filename, $width, $height);
}

echo "Images have been resized and saved.";
?>
