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
	}
	
	/**
	 * Perform file uploading logic
     * @param string $filePath Uloaded file path
     * @param string $uploadName Uploaded file name real name to return on success upload
	 * @param string $fileName Uploaded file name on server to return on success upload
	 * @return boolean True if file succesfully uploaded
	 */
	public function upload( & $filePath = '', & $uploadName = '', & $fileName = '' )
	{
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
                $this->size = $_SERVER['HTTP_X_FILE_SIZE'];
                $this->mimeType = $_SERVER['HTTP_X_FILE_TYPE'];

                // store data for output
                $filePath = $this->filePath;
                $uploadName = $this->realName;
                $fileName = $this->fileName;
                //

				// Success
				return true;
			}
		}

		// Failure
		return false; 
	}

    /**
     * Returns full file path to file.
     * @return string File path.
     */
    public function realPath()
    {
        return $this->filePath;
    }

    /**
     * Returns uploaded file name
     * @return string File name
     */
    public function realName()
    {
        return $this->realName;
    }

    /**
     * Returns stored file name
     * @return string File name
     */
    public function name()
    {
        return $this->fileName;
    }

    /**
     * Returns MIME type of uploaded file
     * @return string MIME type
     */
    public function mimeType()
    {
        return $this->mimeType;
    }

    /**
     * If $extension is set, tries to compare file extension to input extension and return a result
     * Otherwise returns file extension
     * @param string $extension Supposed file extension
     * @return bool|string Result of extension comparison or extension by itself.
     */
    public function extension($extension = null)
    {
        return isset($extension) ? ($extension === $this->extension ? true : false) : $this->extension;
    }

    /**
     * Returns file size
     * @return int File size
     */
    public function size()
    {
        return $this->size;
    }
}
