<?php
// Get the requested image file name from the URL
$image = isset($_GET['image']) ? $_GET['image'] : ''; // Make sure $image is set

// Define the fixed path to the images folder
$imageFolder = 'images/';

// Check if the requested image file exists and is a valid file within the images folder
if (!empty($image) && file_exists($imageFolder . $image) && is_file($imageFolder . $image)) {
    // Set the appropriate content type header based on the file type
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $imageFolder . $image);
    finfo_close($fileInfo);
    header('Content-Type: ' . $mimeType);

    // Output the image file content
    readfile($imageFolder . $image);
} else {
    // If the image file doesn't exist or is not valid, return a 404 error
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
}
?>
