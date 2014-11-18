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
 * @param string $userDir Custom path modifier
 * @return bool True if file was successfully uploaded
 */
function uploadFile(&$upload, $extensions = array(), $userDir = '')
{
    // Create new Upload object
    $upload = new Upload($extensions, $userDir);

    // Return upload result
    return $upload->upload();
}
