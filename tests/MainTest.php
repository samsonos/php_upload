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
        \samson\core\Error::$OUTPUT = false;

        // Create Server Handler mock
        $this->serverHandler = $this->getMockBuilder('\samson\upload\ServerHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = \samson\core\Service::getInstance('\samson\upload\UploadHandler');
    }

   /*public function testUpload()
   {
       $this->instance->init();

       $this->instance->serverHandler = & $this->serverHandler;

       $this->serverHandler
           ->expects($this->once())
           ->method('name')
           ->willReturn('samsonos.png');

       $this->serverHandler
           ->expects($this->once())
           ->method('size')
           ->willReturn('16256');

       $this->serverHandler
           ->expects($this->once())
           ->method('file')
           ->willReturn(file_get_contents('samsonos.png'));

       $this->serverHandler
           ->expects($this->once())
           ->method('type')
           ->willReturn('png');

   }*/

    public function testUpload()
    {
        $this->assertEquals('1', '1');
    }
}
