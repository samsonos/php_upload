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

    /** @var string real file path */
    private $filePath;

    /** @var string Name of uploaded file */
    private $realName;

    /** @var string Generated file name */
    private $fileName;

    /** @var string File MIME type */
    private $mimeType;

    /** @var string extension */
    private $extension;

    /** @var File size */
    private $size;
	
	/** Upload server path */
	public $uploadDir = self::UPLOAD_PATH;

	/**
	 * Constructor
	 * @param array $extensions Array of excepted extensions
     * @param string $userDir Directory to save file
	 */
	public function __construct(array $extensions = array(), $userDir = null)
	{			
		// Set file extension limitations
		$this->extensions = $extensions;

        // Try to reset directory
        $this->uploadDir = isset($userDir) ? $userDir : $this->uploadDir;
		
		// If upload path does not exsits - create it
		if (!file_exists($this->uploadDir)) mkdir($this->uploadDir, 0775, true);
		
		// If form field specified - save it
		if(isset($file_field)) $this->file_field = $file_field;
	}
	
	/**
	 * Perform file uploading logic
     * @param string $filePath
     * @param string $uploadName 	Uploaded file name real name to return on success upload
	 * @param string $fileName 	Uploaded file name on server to return on success upload
	 * @return boolean True if file succesfully uploaded
	 */
	public function upload( & $filePath = '', & $uploadName = '', & $fileName = '' )
	{
		// File extension also a flag
		$this->extension = FALSE;
		
		// Try to get upload file with new upload method
		$this->realName = urldecode($_SERVER['HTTP_X_FILE_NAME']);

		// If upload data exists
		if (isset($this->realName)) {

			// Get file extension
			$this->extension = pathinfo($this->realName, PATHINFO_EXTENSION);
			
			// If we have no extension limitations or they are matched
			if(!sizeof($this->extensions) || in_array($this->extension, $this->extensions)) {
				
				// Generate filename
				$this->fileName = strtolower(md5(time() . $this->realName) . '.' . $this->extension);
				// Generate unique hashed file name for storing on server
				$this->filePath = $this->uploadDir . '/' . $this->fileName;
                /** @var string $file */
                $file = file_get_contents('php://input');
				// Create file 
				file_put_contents($this->filePath, $file);
                $this->size = strlen($file);
                $this->mimeType = $_SERVER['HTTP_X_FILE_SIZE'];

                // deprecated section
                //
                $filePath = $this->filePath;
                $uploadName = $this->realName;
                $fileName = $this->fileName;
                //
                //*******************

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

    public function realPath()
    {
        return $this->filePath;
    }

    public function realName()
    {
        return $this->realName;
    }

    public function name()
    {
        return $this->fileName;
    }

    public function mimeType()
    {
        return $this->mimeType;
    }

    public function extension($extension = null)
    {
        return isset($extension) ? ($extension === $this->extension ? true : false) : $this->extension;
    }

    public function size()
    {
        return $this->size;
    }
}