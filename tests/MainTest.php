<?php
namespace samson\upload;

/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 04.08.14 at 16:42
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var \samson\upload\UploadController */
    public $instance;

    /** @var \samson\upload\ServerHandler */
    public $serverHandler;

    public function fileNameHandler($name)
    {
        return $name;
    }

    public function setUp()
    {
        \samson\core\Error::$OUTPUT = false;

        // Create Server Handler mock
        $this->serverHandler = $this->getMockBuilder('\samson\upload\ServerHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = \samson\core\Service::getInstance('\samson\upload\UploadController');
        $this->instance->fs = \samson\core\Service::getInstance('\samsonphp\fs\FileService');
        $this->instance->fs->loadExternalService('\samsonphp\fs\LocalFileService');
        $this->instance->serverHandler = & $this->serverHandler;
    }

    // Test main method
    public function testUpload()
    {
        $this->instance->init();

        $this->serverHandler
           ->expects($this->once())
           ->method('name')
           ->willReturn('tests/samsonos.png');

        $this->serverHandler
           ->expects($this->once())
           ->method('size')
           ->willReturn('1003');

        $this->serverHandler
           ->expects($this->once())
           ->method('file')
           ->willReturn(file_get_contents('tests/samsonos.png'));

        $this->serverHandler
           ->expects($this->once())
           ->method('type')
           ->willReturn('png');

        $upload = new Upload(array(), null, $this->instance);

        $upload->upload($filePath, $uploadName, $fileName);

        $this->assertTrue($upload->extension('png'));
        $this->assertEquals($upload->extension(), 'png');
        $this->assertEquals($upload->mimeType(), 'png');
        $this->assertEquals($upload->size(), 1003);
        $this->assertEquals($fileName, 'tests/samsonos.png');
        $this->assertEquals($upload->realName(), 'tests/samsonos.png');
        $this->assertNotNull($filePath);
        $this->assertNotNull($uploadName);
        $this->assertNotNull($upload->path());
        $this->assertNotNull($upload->name());
        $this->assertNotNull($upload->fullPath());
    }

    // Test help functions after uploading
    public function testUploadFunctions()
    {
        $this->instance->init();
        $this->instance->serverHandler = & $this->serverHandler;

        $this->serverHandler
            ->expects($this->once())
            ->method('name')
            ->willReturn('');

        $upload = new Upload(array(), null, $this->instance);

        $this->assertFalse($upload->upload());
    }

    // Test upload file name handler
    public function testHandler()
    {
        $this->instance->init();
        $this->instance->fileNameHandler = array($this, 'fileNameHandler');
        $this->instance->serverHandler = & $this->serverHandler;

        $this->serverHandler
            ->expects($this->once())
            ->method('name')
            ->willReturn('tests/samsonos.png');

        $this->serverHandler
            ->expects($this->once())
            ->method('size')
            ->willReturn('16256');

        $this->serverHandler
            ->expects($this->once())
            ->method('file')
            ->willReturn(file_get_contents('tests/samsonos.png'));

        $this->serverHandler
            ->expects($this->once())
            ->method('type')
            ->willReturn('png');

        $upload = new Upload(array(), 'myFile.png', $this->instance);

        $upload->upload($filePath, $uploadName, $fileName);
    }

    // Test upload with extension error
    public function testExtension()
    {
        $this->instance->init();
        $this->instance->serverHandler = & $this->serverHandler;

        $this->serverHandler
            ->expects($this->once())
            ->method('name')
            ->willReturn('tests/samsonos.png');

        $upload = new Upload(array('xls', 'gif'), null, $this->instance);

        $this->assertFalse($upload->upload());
    }

    // Test Class ServerHandler
    public function testServerHandler()
    {
        // Create fs mock
        $fs = $this->getMockBuilder('\samsonphp\fs\FileService')
            ->disableOriginalConstructor()
            ->getMock();

        $serverHandler = new ServerHandler($fs);

        $serverHandler->name();
        $serverHandler->size();
        $serverHandler->file();
        $serverHandler->type();
        $serverHandler->write('fileName', 'fileDir', 'uploadDir');
    }
}
