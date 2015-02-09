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

    public function setUp()
    {
        \samson\core\Error::$OUTPUT = true;

        // Create Server Handler mock
        $this->serverHandler = $this->getMockBuilder('\samson\upload\ServerHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = \samson\core\Service::getInstance('\samson\upload\UploadController');
        $this->instance->fs = \samson\core\Service::getInstance('\samsonphp\fs\FileService');
        $this->instance->fs->loadExternalService('\samsonphp\fs\LocalFileService');
    }

    public function testUpload()
    {
        $this->instance->init();
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

        $upload = new Upload(array(), null, $this->instance);

        $upload->upload($filePath, $uploadName, $fileName);

        $this->assertEquals($fileName, 'tests/samsonos.png');
        $this->assertNotNull($filePath);
        $this->assertNotNull($uploadName);
    }

    public function testUploadFunctions()
    {
        $this->instance->init();
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

        $upload = new Upload(array(), null, $this->instance);

        $upload->upload($filePath, $uploadName, $fileName);

        $upload->size();
        $upload->path();
        $upload->name();
        $upload->fullPath();
        $upload->realName();
        $upload->mimeType();

        $this->assertTrue($upload->extension('png'));
        $this->assertEquals($upload->extension(), 'png');
    }



   /* public function testUpload2()
    {
        $this->assertEquals('1', '1');
    }*/
}
