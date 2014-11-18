<?php
namespace samson\upload;

use samson\core\CompressableExternalModule;
use samson\core\iModuleViewable;

/**
 * Интерфейс для подключения модуля в ядро фреймворка SamsonPHP
 *
 * @package SamsonPHP
 * @author Vitaly Iegorov <vitalyiegorov@gmail.com>
 * @author Nikita Kotenko <nick.w2r@gmail.com>
 * @version 0.1
 */
class UploadController extends CompressableExternalModule
{
    /** @var string Module identifier */
    protected $id = 'samson_upload';

    /** @var string FileSystem adapter class name */
    public $adapterType = 'LocalAdapter';

    /** @var  array Collection of adapter specific parameters */
    public $adapterParameters = array();

    /** @var iAdapter Pointer to current file system adapter */
    public $adapter;

    /** @var  callable External handler to build relative file path */
    public $handler;

    /**
     * Initialize module
     * @param array $params Collection of module parameters
     * @return bool True if module successfully initialized
     */
    public function init(array $params = array())
    {
        // Check if NOT current Adapter is supported
        if (!class_exists($this->adapter)) {
            // Use default adapter
            $this->adapterType = 'LocalAdapter';
        }

        // Create adapter instance and pass all its possible parameters
        $this->adapter = new $this->adapterType($this->adapterParameters);

        // Call parent initialization
        parent::init($params);
    }
}