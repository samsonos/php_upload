<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 17.11.2014
 * Time: 14:12
 */

namespace samson\upload;

/**
 * Local file system adapter implementation
 * @package samson\upload
 */
class LocalAdapter implements iAdapter {


    /** @see \samson\upload\iAdapter::init() */
    public function init($uploadDir)
    {
        // If upload path does not exists - create it
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
    }

    /** @see \samson\upload\iAdapter::write() */
    public function write($data, $filename = '', $uploadDir = '')
    {
        // Build path to writing file
        $path = $uploadDir.'/'.$filename;

        // Put file and return true if at least one byte is written
        if (file_put_contents($path, $data) !== false) {
            return $uploadDir.'/';
        } else { // We have failed my lord..
            return false;
        }
    }
}
