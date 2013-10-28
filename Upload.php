<?php
namespace samson\upload;

use samson\core\iModuleViewable;
use samson\core\File;



/**
 * SamsonPager - Модуль для постраничного вывода и управления информацией
 *
 * @author Vitaly Iegorov <vitalyiegorov@gmail.com>
 * @author Nikita Kotenko <nick.w2r@gmail.com>
 *
 * @version 0.0.2
 */
class Upload
{	
	const UPLOAD_PATH = '/upload/';
	
	public $file_name;
	public $input_name;
	public $file_src;
	public $file_path;
	public $file_type;
	public $full_name;
	public $full_path;
	public $file;
	// Путь к папке с загрузками
	public $upload_dir = self::UPLOAD_PATH;
	
	
	
	/**
	 * Конструктор
	 *	 
	 * @param number 	$current_page 	Номер текущей строки данных ( от 1 до $rows_count )  	 
     * @param number 	$page_size 		Количество строк данных отображаемых на одной странице
     * @param number 	$rows_count 	Общее количество строк данных
	 */
	public function __construct( $input_name = '',$file_name = NULL )
	{	
		// Сгенерируем уникальное имя файла
		if (!isset($file_name)) $file_name = rand( 0, 9999999999 ).'_'.rand( 0, 9999999999 );
		$this->file_name = $file_name;
		$this->input_name = $input_name;
	}
	
	public function upload()
	{
		$filename = '';
		$tmp_path = '';
		$this->file_type = FALSE;
		if (isset($_SERVER['HTTP_X_FILE_NAME']))
		{
			$filename = $_SERVER['HTTP_X_FILE_NAME'];
			$file_info = pathinfo($filename);
			$this->file_type = $file_info['extension'];
		}
		else if (isset($_FILES[$this->input_name]))
		{
			$file = $_FILES[$this->input_name];
			// Имя полученного файла
			$tmp_path = $file['tmp_name'];
			// Обработаем тип получаемого файла
			$this->file_type = File::getExtension( $file['type'] );
		}

		// Если нам подошел тип файла
		if( $this->file_type !== FALSE )
		{
			// Получим полный путь к файлу
			$this->file_path = $this->upload_dir.$this->file_name.'.'.$this->file_type;
			$this->file = getcwd().$this->file_path;
			$this->full_path = getcwd().$this->upload_dir;
			
			if (isset($_SERVER['HTTP_X_FILE_NAME']))
			{
				file_put_contents($this->file, file_get_contents('php://input'));
				return true;
			}
			// Обработаем загрузку файла
			else if( move_uploaded_file( $tmp_path,  $this->file) )
			{
				return true;
			}
		}
		return false; 
	}

}

