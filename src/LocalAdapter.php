<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 17.11.2014
 * Time: 14:12
 */

namespace samson\upload;


class LocalAdapter implements iAdapter {
    /** @var string $adapterId Adapter identifier */
    public $adapterId = 'local';

    public function putContent($data, $filename = '', $uploadDir = '')
    {
        // Generate unique hashed file name for storing on server
        $filePath = $this->$uploadDir.'/'.$filename;

        // Put file
        file_put_contents($filePath, $data);

        return $filePath;
    }

    public function getId()
    {
        return $this->adapterId;
    }
}
