<?php
namespace samson\upload;

/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 09.02.2015
 * Time: 14:37
 */

class ServerHandler
{
    public function name()
    {
        return urldecode($_SERVER['HTTP_X_FILE_NAME']);
    }

    public function size()
    {
        return $_SERVER['HTTP_X_FILE_SIZE'];
    }

    public function type()
    {
        return $_SERVER['HTTP_X_FILE_TYPE'];
    }

    public function file()
    {
        return file_get_contents('php://input');
    }

    public function write($file, $fileName, $uploadDir)
    {
        /** @var $fs \samsonphp\fs\FileService Pointer to module controller */
        $fs = & m('fs');

        return $fs->write($file, $fileName, $uploadDir);
    }
}
