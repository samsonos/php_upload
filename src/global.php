<?php
/**
 * Created by Maxim Omelchenko <omelchenko@samsonos.com>
 * on 11.11.2014 at 17:17
 */

use \samson\upload\Upload;

/**
 * @param Upload $upload
 * @param array $extensions
 * @param string $userDir
 * @return bool
 */
function uploadFile(Upload &$upload, $extensions = array(), $userDir = '')
{
    // Form array if isn't an array
    $extensions = is_array($extensions) ? $extensions : array($extensions);

    // Create new Upload object
    $upload = new Upload($extensions, $userDir);

    // Return upload result
    return $upload->upload();

}