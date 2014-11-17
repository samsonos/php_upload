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
class UploadConnector extends CompressableExternalModule
{
    /** Идентификатор модуля */
    protected $id = 'samsonupload';

    public $requirements = array('samsonjs');

    public $adapterType = 'local';

    public $accessKey;

    public $secretKey;

    public $bucket;

    public $handler;

    public $awsUrl;

    /** @var iAdapter  */
    public $adapter;

    public function init(array $params = array())
    {
        if (!isset($this->adapter)) {
            $this->adapter = new LocalAdapter();
        } elseif ($this->adapter->getID() == 'amazon') {
            AwsAdapter::$accessKey = $this->accessKey;
            AwsAdapter::$secretKey = $this->secretKey;
            AwsAdapter::$bucket = $this->bucket;
            AwsAdapter::$awsUrl = $this->awsUrl;
        }

        if (isset($this->handler) && is_callable($this->handler)) {
            Upload::$pathHandler = $this->handler;
        }

        parent::init($params);
    }
}