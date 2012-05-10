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
    function injectSomeObj( $obj ) {
        $this->someObj = $obj;
    }
}

class test_FrameworkContainer extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AmidaMVC\Framework\Container
     */
    var $di;
    // +----------------------------------------------------------------------+
    public function setUp()
    {
        $this->di = new \AmidaMVC\Framework\Container();
    }
    // +----------------------------------------------------------------------+
    function test_first() {

    }
    // +----------------------------------------------------------------------+
    function test_getObject() {
        $className = '\tests\Framework\testClass1';
        $obj1 = $this->di->get( $className );
        $this->assertEquals( $className, '\\' . get_class( $obj1 ) );
    }
    // +----------------------------------------------------------------------+
    function test_get_static_Object() {
        $className = '\tests\Framework\testClass1';
        $static1 = $this->di->get( $className, 'static' );
        $this->assertEquals( $className, $static1 );
    }
    // +----------------------------------------------------------------------+
    function test_Object_are_preserved() {
        $className = '\tests\Framework\testClass1';
        $obj1 = $this->di->get( $className );
        $this->assertEquals( $className, '\\' . get_class( $obj1 ) );

        $obj2 = $this->di->get( $className );
        $this->assertEquals( $obj1, $obj2 );

        $obj3 = $this->di->get( $className, 'test' );
        $this->assertFalse( $obj1 === $obj3 );

        $obj4 = $this->di->get( $className, 'test' );
        $this->assertTrue( $obj4 === $obj3 );

        $obj5 = $this->di->get( $className, 'get', 'test' );
        $this->assertTrue( $obj4 === $obj5 );

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
        $className = '\tests\Framework\testClass2';
        $config2 = array(
            'config' => 'testing config',
            'inject' => array(
                array( 'someObj', '\tests\Framework\testClass1' ),
            ),
            'option' => 'testing init',
        );
        $obj2 = $this->di->get( $className, 'new', $config2 );
        $this->assertEquals( $className, '\\' . get_class( $obj2 ) );
        $this->assertEquals( $config2['config'], $obj2->config );
        $this->assertEquals( $config2['option'], $obj2->option );
        $this->assertEquals( $config2['inject'][0][1], '\\' . get_class( $obj2->someObj ) );
    }
    // +----------------------------------------------------------------------+
    function test_get_with_config_inject_static_class() {
        $className = '\tests\Framework\testClass2';
        $config3 = array(
            'config' => 'testing config',
            'inject' => array(
                array( 'someObj', '\tests\Framework\testClass1', 'static' ),
            ),
            'option' => 'testing init',
        );
        $obj3 = $this->di->get( $className, 'new', $config3 );
        $this->assertEquals( $className, '\\' . get_class( $obj3 ) );
        $this->assertEquals( $config3['config'], $obj3->config );
        $this->assertEquals( $config3['option'], $obj3->option );
        $this->assertEquals( $config3['inject'][0][1], $obj3->someObj );
    }
    // +----------------------------------------------------------------------+
}
