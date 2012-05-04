<?php
namespace AmidaMVC\Tests\AppSimple\testLoader;
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

class TestLoader extends \AmidaMVC\AppSimple\Loader
{
    function getLoadClass() {
        return $this->_loadClass;
    }
}

class testCtrl
{
    var $redirectPath = NULL;
    var $commands = array();
    function getPathInfo() {
        return 'test';
    }
    function getLocation() {
        return 'loc';
    }
    function redirect( $path ) {
        $this->redirectPath = $path;
    }
    function getCommands() {
        return $this->commands;
    }
}

class testPageObj {
    var $content = '';
    var $contentType = 'test';
    function setContent( $content ) {
        $this->content = $content;
    }
    function getContent() {
        return $this->content;
    }
    function contentType( $type=NULL ) {
        if( isset( $type ) ) {
            $this->contentType = $type;
        }
        return $this->contentType;
    }
}


class test_AppSimple_Loader extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AmidaMVC\AppSimple\Loader
     */
    var $loader;
    /**
     * @var TestLoader
     */
    var $tLoader;
    /**
     * @var \AmidaMVC\Framework\Controller
     */
    var $_ctrl;
    /**
     * @var \AmidaMVC\Framework\PageObj
     */
    var $_pageObj;
    // +----------------------------------------------------------------------+
    public function setUp()
    {
        $this->loader = new \AmidaMVC\AppSimple\Loader();
        $this->tLoader = new TestLoader();
        $this->_ctrl = new testCtrl();
        $this->_pageObj = new testPageObj();
    }
    // +----------------------------------------------------------------------+
    function test_init() {
        $orig_loaderClass = $this->tLoader->getLoadClass();

        $test_loaderClass = 'dummyClass';
        $option = array(
            'loadClass' => $test_loaderClass,
        );
        $this->tLoader->_init( $option );
        $this->assertEquals(    $test_loaderClass, $this->tLoader->getLoadClass() );
        $this->assertNotEquals( $orig_loaderClass, $this->tLoader->getLoadClass() );
    }
    // +----------------------------------------------------------------------+
    function test_defaultClosure() {
        $loadInfo = array(
            'file' => function() {return '#test closure'; },
            'type' => 'markdown',
        );
        $return = $this->loader->actionDefault(
            $this->_ctrl,
            $this->_pageObj,
            $loadInfo );
        $this->assertTrue( $return );

        $closure = $loadInfo['file'];
        $orig_content = $closure();
        $test_content = $this->_pageObj->getContent();
        $this->assertEquals( $orig_content, $test_content );

        $loadMode = $this->_ctrl->loadInfo[ 'loadMode' ];
        $this->assertEquals( '_closure', $loadMode );
    }
    // +----------------------------------------------------------------------+
}