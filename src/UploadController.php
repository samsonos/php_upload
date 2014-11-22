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
    public $id = 'samson_upload';

    /** @var string FileSystem adapter class name */
    public $adapterType = '\samson\fs\LocalAdapter';

    /** @var iAdapter Pointer to current file system adapter */
    public $adapter;

    /** @var  callable External handler to build relative file path */
    public $handler;

    /** @var string Prefix for image path saving in db */
    public $pathPrefix = __SAMSON_BASE__;

    /**
     * Initialize module
     * @param array $params Collection of module parameters
     * @return bool True if module successfully initialized
     */
    public function init(array $params = array())
    {
        // Check if NOT current Adapter is supported
        if (!class_exists($this->adapterType)) {
            // Use default adapter
            $this->adapterType = '\samson\fs\LocalAdapter';
        }

        // Create adapter instance and pass all its possible parameters
        $this->adapter = new $this->adapterType();

        if (!isset($this->handler) || !is_callable($this->handler)) {
            $this->handler = array($this, 'defaultHandler');
        }

        // Call parent initialization
        parent::init($params);
    }

    /**
     * Default relative path builder handler
     * @return string Relative path for uploading
     */
    public function defaultHandler()
    {
        return 'upload';
    }
}