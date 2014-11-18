<?php
/**
 * Created by Maxim Omelchenko <omelchenko@samsonos.com>
 * on 11.11.2014 at 17:17
 */

use \samson\upload\Upload;

/**
 * Perform file uploading.
 * This function is a shortcut that avoids new Upload() object creation.
 * @param Upload $upload Waiting for upload object to be returned
 * @param mixed $extensions Collection of supported extensions, one extension is also supported
 * @param mixed $relativePathParameters Collection of parameters to be passed to external rel. path builder
 * @return bool True if file was successfully uploaded
 */
function uploadFile(&$upload, $extensions = array(), $relativePathParameters = null)
{
    // Create new Upload object
    $upload = new Upload($extensions, $relativePathParameters);

    // Return upload result
    return $upload->upload();
}
