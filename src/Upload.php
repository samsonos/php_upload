<?php
namespace samson\upload;

/**
 * Generic file uploader
 * @package samson\upload
 * @author Vitaly Iegorov <egorov@samsonos.com>
 * @author Nikita Kotenko <kotenko@samsonos.com>
 */
class Upload
{
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

    /** @var int File size */
    private $size;

    /** @var UploadController Pointer to module controller */
    public $parent;

    /** Upload server path */
    public $uploadDir = 'upload/';

    /** @var array Parameters for callable handlers */
    protected $relPathParameters = array();

    /**
     * Constructor
     * @param mixed $extensions Collection or single excepted extension
     * @param mixed $relPathParameters Data to be passed to external rel. path builder
     */
    public function __construct($extensions = array(), $relPathParameters = null)
    {
        // Set additional relative path parameters
        $this->relPathParameters = !is_array($relPathParameters) ? array($relPathParameters) : $relPathParameters;

        // Set file extension limitations, form array if isn't an array
        $this->extensions = is_array($extensions) ? $extensions : array($extensions);

        // Get current upload adapter
        $this->parent = & m('upload');

        // Build relative path for uploading
        $this->uploadDir = call_user_func_array($this->parent->uploadDirHandler, $this->relPathParameters);
    }

    /**
     * Perform file uploading logic
     * @param string $filePath Uploaded file path
     * @param string $uploadName Uploaded file name real name to return on success upload
     * @param string $fileName Uploaded file name on server to return on success upload
     * @return boolean True if file successfully uploaded
     */
    public function upload(& $filePath = '', & $uploadName = '', & $fileName = '')
    {
        // Try to get upload file with new upload method
        $this->realName = urldecode($_SERVER['HTTP_X_FILE_NAME']);

        // If upload data exists
        if (isset($this->realName)) {
            // Get file extension
            $this->extension = pathinfo($this->realName, PATHINFO_EXTENSION);

            // If we have no extension limitations or they are matched
            if (!sizeof($this->extensions) || in_array($this->extension, $this->extensions)) {
                // If we have callable handler for generating file name
                if (isset($this->parent->fileNameHandler) && is_callable($this->parent->fileNameHandler)) {
                    // Add file extension as last parameter
                    array_push($this->relPathParameters, $this->extension);
                    // Call handler and create fileName
                    $this->fileName = call_user_func_array($this->parent->fileNameHandler, $this->relPathParameters);
                }

                // If we have not created filename - generic generate it
                if (!isset($this->fileName)) {
                    $this->fileName = strtolower(md5(time().$this->realName).'.'.$this->extension);
                }

                /** @var string $file Read uploaded file */
                $file = file_get_contents('php://input');

                // Create file
                $this->filePath = $this->parent->fs->write($file, $this->fileName, $this->uploadDir);

                // Save size and mimeType
                $this->size = $_SERVER['HTTP_X_FILE_SIZE'];
                $this->mimeType = $_SERVER['HTTP_X_FILE_TYPE'];

                // store data for output
                $filePath = $this->fullPath();
                $uploadName = $this->name();
                $fileName = $this->realName();

                // Success
                return true;
            }
        }

        // Failure
        return false;
    }

    /** @return string Full path to file  */
    public function path()
    {
        return $this->parent->pathPrefix.$this->filePath;
    }

    /** @return string Full path to file with file name */
    public function fullPath()
    {
        return $this->filePath.$this->fileName;
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
