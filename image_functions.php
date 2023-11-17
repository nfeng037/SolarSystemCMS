<?php
// image_functions.php
require_once './lib/ImageResize.php';
require_once './lib/ImageResizeException.php';

use \Gumlet\ImageResize;
use \Gumlet\ImageResizeException;

function file_upload_path($original_filename, $upload_subfolder_name = 'uploads', $return_relative_path = false) {
    $current_folder = dirname(__FILE__);
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
    $absolute_path = join(DIRECTORY_SEPARATOR, $path_segments);

    if ($return_relative_path) {
        $relative_path = $upload_subfolder_name . '/' . basename($original_filename);
        return str_replace('\\', '/', $relative_path); 
    }

    return $absolute_path;
}

function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $imageInfo = getimagesize($temporary_path);

    if ($imageInfo === false) {
        error_log('Error: getimagesize() failed for file: ' . $temporary_path);
        return false;
    }

    $actual_mime_type = $imageInfo['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    error_log('Extension check: ' . ($file_extension_is_valid ? 'Passed' : 'Failed'));
    error_log('MIME type check: ' . ($mime_type_is_valid ? 'Passed' : 'Failed'));

    return $file_extension_is_valid && $mime_type_is_valid;
}



function resize_image($original_path, $resized_path, $max_width) {
    try {
        $image = new ImageResize($original_path);
        $image->resizeToWidth($max_width);
        $image->save($resized_path);
    } catch (ImageResizeException $e) {
        error_log($e->getMessage());
        return false;
    }
    return true;
}