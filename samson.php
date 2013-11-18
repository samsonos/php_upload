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
			
}