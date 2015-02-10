<?php
namespace samson\upload;

use samson\core\CompressableExternalModule;
use samson\core\iModuleViewable;

/**
 * SamsonPHP Upload module
 *
 * @package SamsonPHP
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class UploadController extends CompressableExternalModule
{
    /** @var string Module identifier */
    public $id = 'upload';

    /** @var  callable External handler to build relative file path */
    public $uploadDirHandler;

    /** @var string Prefix for image path saving in db */
    public $pathPrefix = __SAMSON_BASE__;

    /** @var callable External handler to build file name */
    public $fileNameHandler;

    /** @var \samsonphp\fs\FileService Pointer to file system module */
    public $fs;

    /** @var ServerHandler Server functions handler */
    public $serverHandler;

    /**
     * Init current file system module and server requests handler
     */
    protected function initFields()
    {
        // Store pointer to file system module
        $this->fs = !isset($this->fs) ? m('fs') : $this->fs;

        // Set server handler object
        $this->serverHandler = !isset($this->serverHandler) ? new ServerHandler() : $this->serverHandler;
    }

    /**
     * Initialize module
     * @param array $params Collection of module parameters
     * @return bool True if module successfully initialized
     */
    public function init(array $params = array())
    {
        // Init FileSystem and ServerHandler
        $this->initFields();

        // If no valid handlers are passed - use generic handlers
        if (!isset($this->uploadDirHandler) || !is_callable($this->uploadDirHandler)) {
            $this->uploadDirHandler = array($this, 'defaultDirHandler');
        }

        // Call parent initialization
        parent::init($params);
    }

    /**
     * Default relative path builder handler
     * @return string Relative path for uploading
     */
    public function defaultDirHandler()
    {
        $path = 'upload';

        // Create upload dir if it does not present
        if (!$this->fs->exists($path)) {
            $this->fs->mkDir($path);
        }

        return $path;
    }
}