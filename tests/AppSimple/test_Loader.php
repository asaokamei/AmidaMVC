<?php
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

class TestRouter extends \AmidaMVC\AppSimple\Loader
{
    function getLoadClass() {
        return $this->_loadClass;
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


class test_AppSimple_Router extends PHPUnit_Framework_TestCase
{
    /**
     * @var \AmidaMVC\AppSimple\Router
     */
    var $loader;
    /**
     * @var TestRouter
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
        $this->tLoader = new TestRouter();
        $this->_ctrl = new testCtrl();
        $this->_pageObj = new testPageObj();
    }
    // +----------------------------------------------------------------------+
    function test_init() {
        $this->assertTrue( TRUE );

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
}