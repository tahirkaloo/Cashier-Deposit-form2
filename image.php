<?php
// Get the requested image file name from the URL
$image = $_GET['image'];

// Define the path to the images folder
$imageFolder = 'images/';

// Construct the full path to the image file
$imagePath = $imageFolder . $image;

// Check if the requested image file exists and is a valid file
if (file_exists($imagePath) && is_file($imagePath)) {
    // Set the appropriate content type header based on the file type
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $imagePath);
    finfo_close($fileInfo);
    header('Content-Type: ' . $mimeType);

    // Output the image file content
    readfile($imagePath);
} else {
    // If the image file doesn't exist or is not valid, return a 404 error
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
}
?>
