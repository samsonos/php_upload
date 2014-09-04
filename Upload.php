<?php
namespace samson\upload;

use samson\core\iModuleViewable;
use samson\core\File;

/**
 * Generic file uploader
 *
 * @author Vitaly Iegorov <vitalyiegorov@gmail.com>
 * @author Nikita Kotenko <nick.w2r@gmail.com>
 *
 * @version 0.0.2
 */
class Upload
{	
	const UPLOAD_PATH = 'upload/';

// 	/** Name of form file field */
// 	protected $file_field;	

	/** Supported file extensions */
	protected $extensions = array();
	
	/** Upload file name */
	protected $upload_file;
	
	/** Upload server path */
	public $upload_dir = self::UPLOAD_PATH;	

	/**
	 * Constructor	  
	 * @param array $extensions
	 */
	public function __construct( array $extensions = array(), $user_dir = null )
	{			
		// Set file extension limitations
		$this->extensions = $extensions;

        if (isset($user_dir)) {
            $this->upload_dir = $user_dir;
        } else {
            // Build full path to upload dir
            $this->upload_dir = $this->upload_dir;
        }

		
		// If upload path does not exsits - create it
		if( !file_exists( $this->upload_dir ) ) mkdir( $this->upload_dir, 0775, true ); 
		
		// If form field specified - save it
		if( isset( $file_field ) ) $this->file_field = $file_field;
	}
	
	/**
	 * Perform file uploading logic
	 * @param string $file_name 	Uploaded file name on server to return on success upload
	 * @param string $upload_name 	Uploaded file name real name to return on success upload
	 * @return boolean True if file succesfully uploaded
	 */
	public function upload( & $file_path = '', & $upload_name = '', & $file_name = '' )
	{
		// File extension also a flag
		$this->file_type = FALSE;
		
		// Try to get upload file with new upload method
		$this->upload_file = urldecode($_SERVER['HTTP_X_FILE_NAME']);
		// If upload data exsists
		if( isset( $this->upload_file ) )
		{				
			// Get file extension
			$this->file_type = pathinfo( $this->upload_file, PATHINFO_EXTENSION );
			
			// If we have no extension limitations or they are matched
			if( !sizeof( $this->extensions ) || in_array( $this->file_type, $this->extensions ))
			{				
				// Save real fiel name
				$upload_name = $this->upload_file;
				
				// Generate filename
				$file_name = strtolower(md5(time().$this->upload_file).'.'.$this->file_type);
				// Generate unique hashed file name for storing on server
				$file_path = $this->upload_dir.$file_name;
				// Create file 
				file_put_contents( $file_path, file_get_contents('php://input') );	
				
				// Success
				return true;
			}
		}
// 		else if ( isset($_FILES[ $this->input_name ]) )
// 		{
// 			$file = $_FILES[ $this->input_name ];
			
// 			// Имя полученного файла
// 			$tmp_path = $file['tmp_name'];
			
// 			// Обработаем тип получаемого файла
// 			$this->file_type = File::getExtension( $file['type'] );
// 		}

// 		// Если нам подошел тип файла
// 		if( $this->file_type !== FALSE )
// 		{
// 			// Получим полный путь к файлу
// 			$this->file_path = $this->upload_dir.$this->file_name.'.'.$this->file_type;
			
// 			$this->file = getcwd().$this->file_path;
			
// 			$this->full_path = getcwd().$this->upload_dir;
			
// 			if (isset($_SERVER['HTTP_X_FILE_NAME']))
// 			{
// 				file_put_contents( $this->file, file_get_contents('php://input') );
				
// 				return true;
// 			}
// 			// Обработаем загрузку файла
// 			else if( move_uploaded_file( $tmp_path,  $this->file) )
// 			{
// 				return true;
// 			}
// 		}

		// Failure
		return false; 
	}
}