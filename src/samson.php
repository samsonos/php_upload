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

    public $adapterType;

    public $accessKey;

    public $secretKey;

    public $bucket;

    public $handler;

    public function init(array $params = array())
    {
        AwsAdapter::$accessKey = $this->accessKey;
        AwsAdapter::$secretKey = $this->secretKey;
        AwsAdapter::$bucket = $this->bucket;
        AwsAdapter::$handler = $this->handler;
        Upload::$type = $this->adapterType;
        parent::init($params);
    }
}