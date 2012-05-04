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

class loadTestClass {
    /**
     * @var \AmidaMVC\Tools\Load
     */
    static $_loadClass = '\AmidaMVC\Tools\Load';
    static function getFileType( $file ) {
        $class = self::$_loadClass;
        return $class::getFileType( $file );
    }
    static function isView( $file ) {
        $class = self::$_loadClass;
        return $class::isView( $file );
    }
    static function isAsIs( $file ) {
        $class = self::$_loadClass;
        return $class::isAsIs( $file );
    }
    static function getContentsByBuffer( $file ) {
        return 'got contents by buffer...';
    }
    static function getContentsByGet( $file ) {
        return 'got contntes by get...';
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
    function test_defaultFileNotSet() {
        $loadInfo = array(
            'type' => 'markdown',
            'file' => NULL, // testing when file is NOT SET.
        );
        $return = $this->loader->actionDefault(
            $this->_ctrl,
            $this->_pageObj,
            $loadInfo );
        $this->assertFalse( $return );
    }
    // +----------------------------------------------------------------------+
    function test_defaultClosure() {
        $loadInfo = array(
            // setting file to be a closure.
            'file' => function() {return '#test closure'; },
            'type' => 'markdown',
        );
        $return = $this->loader->actionDefault(
            $this->_ctrl,
            $this->_pageObj,
            $loadInfo );
        $this->assertTrue( $return );

        /** @var $closure closure */
        $closure = $loadInfo['file'];
        $orig_content = $closure();
        $test_content = $this->_pageObj->getContent();
        $this->assertEquals( $orig_content, $test_content );

        $loadMode = $this->_pageObj->loadInfo[ 'loadMode' ];
        $this->assertEquals( '_closure', $loadMode );
    }
    // +----------------------------------------------------------------------+
    function test_defaultFile() {
        $option = array(
            'loadClass' => '\AmidaMVC\Tests\AppSimple\testLoader\loadTestClass',
        );
        $this->loader->_init( $option );
        $loadInfo = array(
            'file' => 'file.md',
        );
        $return = $this->loader->actionDefault(
            $this->_ctrl,
            $this->_pageObj,
            $loadInfo );
        $this->assertTrue( $return );

        $orig_content = loadTestClass::getContentsByBuffer('file');
        $test_content = $this->_pageObj->getContent();
        $this->assertEquals( $orig_content, $test_content );

        $loadMode = $this->_pageObj->loadInfo[ 'loadMode' ];
        $this->assertEquals( '_view', $loadMode );

        $fileType = $this->_pageObj->contentType;
        $this->assertEquals( 'markdown', $fileType );
        $this->assertEquals( loadTestClass::getFileType( $loadInfo['file']), $fileType );
    }
    // +----------------------------------------------------------------------+
    function test_defaultFileSrc() {
        $option = array(
            'loadClass' => '\AmidaMVC\Tests\AppSimple\testLoader\loadTestClass',
        );
        $this->loader->_init( $option );
        $loadInfo = array(
            'file' => 'file.md',
        );
        // set _src commands to get content as is
        $this->_ctrl->commands = array( '_src' );
        $return = $this->loader->actionDefault(
            $this->_ctrl,
            $this->_pageObj,
            $loadInfo );
        $this->assertTrue( $return );

        // getting the contents by get, appropriate method for _src.
        $orig_content = loadTestClass::getContentsByGet('file');
        $test_content = $this->_pageObj->getContent();
        $this->assertEquals( $orig_content, $test_content );

        $loadMode = $this->_pageObj->loadInfo[ 'loadMode' ];
        $this->assertEquals( '_src', $loadMode );
    }
    // +----------------------------------------------------------------------+
    function test_defaultAsIs() {
        $option = array(
            'loadClass' => '\AmidaMVC\Tests\AppSimple\testLoader\loadTestClass',
        );
        $this->loader->_init( $option );
        $loadInfo = array(
            'file' => 'file.jpg',
        );
        $return = $this->loader->actionDefault(
            $this->_ctrl,
            $this->_pageObj,
            $loadInfo );
        $this->assertTrue( $return );

        $orig_content = loadTestClass::getContentsByGet('file');
        $test_content = $this->_pageObj->getContent();
        $this->assertEquals( $orig_content, $test_content );

        $loadMode = $this->_pageObj->loadInfo[ 'loadMode' ];
        $this->assertEquals( '_asIs', $loadMode );
    }
    // +----------------------------------------------------------------------+
    function test_defaultFileAndAction() {
        $option = array(
            'loadClass' => '\AmidaMVC\Tests\AppSimple\testLoader\loadTestClass',
        );
        $this->loader->_init( $option );
        $loadInfo = array(
            'file' => 'file.text',
            'action' => 'testingAction'
        );
        $orig_action  = $this->_ctrl->action;
        $return = $this->loader->actionDefault(
            $this->_ctrl,
            $this->_pageObj,
            $loadInfo );
        $this->assertTrue( $return );

        $orig_content = loadTestClass::getContentsByBuffer('file');
        $test_content = $this->_pageObj->getContent();
        $this->assertEquals( $orig_content, $test_content );

        $loadMode = $this->_pageObj->loadInfo[ 'loadMode' ];
        $this->assertEquals( '_view', $loadMode );
        $this->assertEquals( $loadInfo[ 'action' ], $this->_ctrl->action );
        $this->assertNotEquals( $orig_action, $this->_ctrl->action );
    }
    // +----------------------------------------------------------------------+
    function test_pageNotFound() {
        $this->_ctrl->pageNotFoundFile = 'pageNotFound.md';
        $return = $this->loader->action_PageNotFound(
            $this->_ctrl,
            $this->_pageObj );
        // when pageNotFound is called, should return loadInfo with
        // pageNotFound file set (if ctrl has option).
        $notFound = array(
            'file' => 'pageNotFound.md',
            'action' => 'default',
        );
        $this->assertEquals( $notFound, $return );
        // and should call itself by calling setMyAction with default as action.
        $this->assertEquals( 'default', $this->_ctrl->myAction );
    }
    // +----------------------------------------------------------------------+
    function test_pageNotFoundWoFile() {
        $return = $this->loader->action_PageNotFound(
            $this->_ctrl,
            $this->_pageObj );
        // when pageNotFound is called, should return FALSE
        // if pageNotFound file IS NOT set.
        $notFound = array(
            'file' => 'pageNotFound.md',
            'action' => 'default',
        );
        $this->assertFalse( $return );
        // make sure it just proceed to next module.
        $this->assertNull( $this->_ctrl->myAction );
    }
    // +----------------------------------------------------------------------+
}