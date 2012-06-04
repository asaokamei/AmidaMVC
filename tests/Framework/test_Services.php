<?php
namespace tests\Framework;
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

class testClass1
{
    function __construct( $config=NULL ) {
    }
    function _init( $option=NULL ) {
    }
    function injectSome( $obj ) {
    }
}

class testClass2
{
    var $config;
    var $option;
    var $someObj;
    function __construct( $config=NULL ) {
        $this->config = $config;
    }
    function _init( $option=NULL ) {
        $this->option = $option;
    }
    function injectSome( $obj ) {
        $this->someObj = $obj;
    }
}

class test_FrameworkServices extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AmidaMVC\Framework\Services
     */
    var $di;
    // +----------------------------------------------------------------------+
    public function setUp()
    {
        $this->sl = '\AmidaMVC\Framework\Services';
        $this->di = \AmidaMVC\Framework\Services::getInstance();
    }
    // +----------------------------------------------------------------------+
    function tearDown() {
        $this->di->clean();
    }
    // +----------------------------------------------------------------------+
    function test_first() {
    }
    // +----------------------------------------------------------------------+
    function test_start() {
        $className = '\tests\Framework\testClass1';
        $this->di->setService( 'modTest', $className, 'new' );
        $obj1 = $this->di->get( 'modTest', 'new', 'test' );
        $obj2 = $this->di->get( 'modTest', 'new', 'test' );
        $this->assertTrue( $obj1 === $obj2 );

        /** @var $di \AmidaMVC\Framework\Services */
        $sl = $this->sl;
        $di = $sl::start();
        $obj3 = $di->get( 'modTest', 'new', 'test' );
        $this->assertTrue( $obj1 !== $obj3 );
    }
    // +----------------------------------------------------------------------+
    function test_setModules_set_basic_info() {
        $className = '\tests\Framework\testClass1';
        $this->di->setService( 'modTest', $className, 'get' );
        $obj1 = $this->di->get( 'modTest' );
        $this->assertEquals( $className, '\\' . get_class( $obj1 ) );

        $obj2 = $this->di->get( $className );
        $this->assertEquals( $obj1, $obj2 );

        $obj3 = $this->di->get( 'modTest' );
        $this->assertEquals( $obj1, $obj3 );
    }
    // +----------------------------------------------------------------------+
    function test_Object_are_preserved() {
        $className = '\tests\Framework\testClass1';
        $obj1 = $this->di->get( $className );
        $this->assertEquals( $className, '\\' . get_class( $obj1 ) );

        $obj2 = $this->di->get( $className );
        $this->assertEquals( $obj1, $obj2 );

        $obj3 = $this->di->get( $className, 'GET', 'test' );
        $this->assertTrue( $obj1 !== $obj3 );

        $obj4 = $this->di->get( $className, 'GET', 'test' );
        $this->assertTrue( $obj4 === $obj3 );

        $obj5 = $this->di->get( $className, 'test' );
        $this->assertTrue( $obj5 === $obj3 );

        $static1 = $this->di->get( $className, 'static' );
        $this->assertEquals( $className, $static1 );
    }
    // +----------------------------------------------------------------------+
    function test_get_static_Object() {
        $className = '\tests\Framework\testClass1';
        $static1 = $this->di->get( $className, 'static' );
        $this->assertEquals( $className, $static1 );
    }
    // +----------------------------------------------------------------------+
    function test_get_with_config() {
        $className = '\tests\Framework\testClass2';
        $config1 = array( 'hello' => 'world' );
        $obj1 = $this->di->get( $className, $config1 );
        $this->assertEquals( $className, '\\' . get_class( $obj1 ) );
        $this->assertEquals( $config1, $obj1->config );
    }
    // +----------------------------------------------------------------------+
    function test_get_with_config_inject_object() {
        $className1 = '\tests\Framework\testClass1';
        $className2 = '\tests\Framework\testClass2';
        $config1 = array(
            'config' => 'testing config',
        );
        $this->di
            ->setService( 'test',     $className2 )
            ->setService( 'injected', $className1 );
        $obj2 = $this->di->get( 'test', 'get', $config1 );
        $this->di->inject( 'some', 'injected' );
        $this->assertEquals( $className2, '\\' . get_class( $obj2 ) );
        $this->assertEquals( $config1, $obj2->config );
        $this->assertEquals( $className1, '\\' . get_class( $obj2->someObj ) );
    }
    // +----------------------------------------------------------------------+
}
