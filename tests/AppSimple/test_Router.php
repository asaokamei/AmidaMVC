<?php
namespace AmidaMVC\Tests\AppSimple\testRouter;
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

class TestRouter extends \AmidaMVC\AppSimple\Router
{
    function getRouterClass() {
        return $this->_routeClass;
    }
    function getIndexes() {
        return $this->_indexes;
    }
}

class testCtrl
{
    var $redirectPath = NULL;
    function getPathInfo() {
        return 'test';
    }
    function getLocation() {
        return 'loc';
    }
    function redirect( $path ) {
        $this->redirectPath = $path;
    }
}

class testPageObj {
}

class routeMatchOK {
    static $matchResult = array( 'file' => 'OK', 'action' => 'test' );
    static function match( $path ) {
        return static::$matchResult;
    }
}

class routeScanFoundFile {
    static $scanResult = array( 'file' => 'OK', 'action' => 'test' );
    static function match( $path ) {
        return FALSE;
    }
    static function scan( $root, $path ) {
        return static::$scanResult;
    }
}

class routeScanFoundDir {
    static $scanResult = array( 'file' => 'OK', 'action' => 'test', 'is_dir' => TRUE );
    static function match( $path ) {
        return FALSE;
    }
    static function scan( $root, $path ) {
        return static::$scanResult;
    }
    static function index( $root, $path, $index ) {
        return static::$scanResult;
    }
}

class routeScanFoundDirWoSlash {
    static $scanResult = array( 'file' => 'OK', 'action' => 'test', 'reload' => 'reload' );
    static function match( $path ) {
        return FALSE;
    }
    static function scan( $root, $path ) {
        return static::$scanResult;
    }
}

class test_AppSimple_Router extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AmidaMVC\AppSimple\Router
     */
    var $router;
    /**
     * @var TestRouter
     */
    var $tRouter;
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
        $this->router = new \AmidaMVC\AppSimple\Router();
        $this->tRouter = new TestRouter();
        $this->_ctrl = new testCtrl();
        $this->_pageObj = new testPageObj();
    }
    // +----------------------------------------------------------------------+
    function test_init() {
        $this->assertTrue( TRUE );

        $orig_routerClass = $this->tRouter->getRouterClass();
        $orig_indexes = $this->tRouter->getIndexes();

        $test_routerClass = 'dummyClass';
        $test_indexes = array( 'testIndex' );
        $test_index2  = array_merge( $test_indexes, $orig_indexes );
        $option = array(
            'routeClass' => $test_routerClass,
            'indexes' => $test_indexes,
        );
        $this->tRouter->_init( $option );
        $this->assertEquals(    $test_routerClass, $this->tRouter->getRouterClass() );
        $this->assertNotEquals( $orig_routerClass, $this->tRouter->getRouterClass() );
        $this->assertEquals(    $test_index2,  $this->tRouter->getIndexes() );
        $this->assertNotEquals( $orig_indexes, $this->tRouter->getIndexes() );
    }
    // +----------------------------------------------------------------------+
    function test_default_route() {
        $option = array(
            'routeClass' => '\AmidaMVC\Tests\AppSimple\testRouter\routeMatchOK',
        );
        $this->router->_init( $option );
        $matchResult = routeMatchOK::$matchResult;
        $matchResult[ 'foundBy' ] = 'route';
        $return = $this->router->actionDefault( $this->_ctrl, $this->_pageObj );
        $this->assertEquals( $matchResult, $return );
    }
    // +----------------------------------------------------------------------+
    function test_default_scanNormal() {
        $option = array(
            'routeClass' => '\AmidaMVC\Tests\AppSimple\testRouter\routeScanFoundFile',
        );
        $this->router->_init( $option );
        $scanResult = routeScanFoundFile::$scanResult;
        $scanResult[ 'foundBy' ] = 'scan';
        $return = $this->router->actionDefault( $this->_ctrl, $this->_pageObj );
        $this->assertEquals( $scanResult, $return );
    }
    // +----------------------------------------------------------------------+
    function test_default_scanIsDir() {
        $option = array(
            'routeClass' => '\AmidaMVC\Tests\AppSimple\testRouter\routeScanFoundDir',
        );
        $this->router->_init( $option );
        $scanResult = routeScanFoundDir::$scanResult;
        $scanResult[ 'foundBy' ] = 'index';
        $return = $this->router->actionDefault( $this->_ctrl, $this->_pageObj );
        $this->assertEquals( $scanResult, $return );
    }
    // +----------------------------------------------------------------------+
    function test_default_scanReload() {
        $option = array(
            'routeClass' => '\AmidaMVC\Tests\AppSimple\testRouter\routeScanFoundDirWoSlash',
        );
        $this->router->_init( $option );
        $scanResult = routeScanFoundDirWoSlash::$scanResult;
        $redirect   = $scanResult[ 'reload' ];
        $this->router->actionDefault( $this->_ctrl, $this->_pageObj );

        $this->assertEquals( $redirect, $this->_ctrl->redirectPath );
    }
    // +----------------------------------------------------------------------+
}
