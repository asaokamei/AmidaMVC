<?php
namespace AmidaMVC\Tests\AppSimple\testEmitter;
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

class TestEmitter extends \AmidaMVC\AppSimple\Emitter
{
    function getEmitClass() {
        return $this->_emitClass;
    }
}

class testCtrl
{
    var $loadInfo = array();
    var $redirectPath = NULL;
    var $commands = array();
    var $action = 'defaultAction';
    var $myAction = NULL;
    var $pageNotFoundFile = '';
    function getPathInfo() {
        return 'test';
    }
    function getLocation( $file=NULL ) {
        if( isset( $file ) ) return 'loc/' . $file;
        return 'loc';
    }
    function redirect( $path ) {
        $this->redirectPath = $path;
    }
    function getCommands() {
        return $this->commands;
    }
    function setAction( $action ) {
        $this->action = $action;
    }
    function getOption( $name ) {
        if( $name == 'pageNotFound_file' ) {
            return $this->pageNotFoundFile;
        }
        return FALSE;
    }
    function defaultAct() {
        return 'default';
    }
    function setMyAction( $action ) {
        $this->myAction = $action;
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
     * @var \AmidaMVC\AppSimple\Emitter
     */
    var $emitter;
    /**
     * @var TestEmitter
     */
    var $tEmitter;
    /**
     * @var testCtrl
     */
    var $_ctrl;
    /**
     * @var testPageObj
     */
    var $_pageObj;
    // +----------------------------------------------------------------------+
    public function setUp()
    {
        $this->emitter = new \AmidaMVC\AppSimple\Loader();
        $this->tEmitter = new TestEmitter();
        $this->_ctrl = new testCtrl();
        $this->_pageObj = new testPageObj();
    }
    // +----------------------------------------------------------------------+
    function test_init() {
        $orig_loaderClass = $this->tEmitter->getEmitClass();

        $test_loaderClass = 'dummyClass';
        $option = array(
            'emitClass' => $test_loaderClass,
        );
        $this->tEmitter->_init( $option );
        $this->assertEquals(    $test_loaderClass, $this->tEmitter->getEmitClass() );
        $this->assertNotEquals( $orig_loaderClass, $this->tEmitter->getEmitClass() );
    }
    // +----------------------------------------------------------------------+
}