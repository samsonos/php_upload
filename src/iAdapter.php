<?php
namespace samson\upload;

/**
 * Generic file system adapter interface for writing/reading
 * data to a particular file system.
 *
 * @package samson\upload
 */
interface iAdapter {

    /**
     * Adapter initialization
     * @param mixed $params collection or parameter for initialization of adapter.
     *                      This depends on adapter implementation.
     * @return mixed True if adapter successfully initialized
     */
    public function init($params);

    /**
     * Write data to a specific relative location
     *
     * @param mixed $data Data to be written
     * @param string $filename File name
     * @param string $uploadDir Relative file path
     * @return string|boolean Relative path to created file, false if there were errors
     */
    public function write($data, $filename = '', $uploadDir = '');
}
