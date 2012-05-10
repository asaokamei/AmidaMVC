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

class testClass3
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
    function inject( $name, $obj ) {
        $this->$name = $obj;
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
    function test_clean() {
        /** @var $Container \AmidaMVC\Framework\Container */
        /** @var $di \AmidaMVC\Framework\Container */
        $Container = '\AmidaMVC\Framework\Container';
        $di1 = $Container::getInstance();
        $di2 = $Container::resume();
        $this->assertTrue( $di1 === $di2 );
        $Container::clean();
        $di4 = $Container::resume();
        $this->assertTrue( $di1 !== $di4 );
        $this->assertTrue( $di2 !== $di4 );
    }
    // +----------------------------------------------------------------------+
    function test_start() {
        /** @var $Container \AmidaMVC\Framework\Container */
        /** @var $di \AmidaMVC\Framework\Container */
        $Container = '\AmidaMVC\Framework\Container';
        $di = $Container::getInstance();
        $className = '\tests\Framework\testClass1';
        $di->setModule( 'modTest', $className, 'new' );
        $obj1 = $di->get( 'modTest', 'new', 'test' );
        $obj2 = $di->get( 'modTest', 'new', 'test' );
        $this->assertTrue( $obj1 === $obj2 );

        $di = $Container::start();
        $obj3 = $di->get( 'modTest', 'new', 'test' );
        $this->assertTrue( $obj1 !== $obj3 );
    }
    // +----------------------------------------------------------------------+
    function test_setModules_set_basic_info() {
        $className = '\tests\Framework\testClass1';
        $this->di->setModule( 'modTest', $className, 'get' );
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

        $obj3 = $this->di->get( $className, 'test' );
        $this->assertTrue( $obj1 !== $obj3 );

        $obj4 = $this->di->get( $className, 'test' );
        $this->assertTrue( $obj4 === $obj3 );

        $obj5 = $this->di->get( $className, 'get', 'test' );
        $this->assertTrue( $obj4 === $obj5 );

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
    function test_getObject_inject_by_inject_method() {
        $className = '\tests\Framework\testClass3';
        $config = array(
            'config' => array(),
            'inject' => array(
                array( 'someObj1', '\tests\Framework\testClass1', 'get' ),
                array( 'someObj2', '\tests\Framework\testClass1', 'get', 'id' ),
                array( 'someObj3', '\tests\Framework\testClass1', 'get' ),
                array( 'someObj4', '\tests\Framework\testClass1', 'get', 'id' ),
            ),
            'option' => array(),
        );
        $obj1 = $this->di->get( $className, $config );
        $this->assertEquals( $className, '\\' . get_class( $obj1 ) );
        $this->assertEquals( $config[ 'inject' ][0][1], '\\' . get_class( $obj1->someObj1 ) );
        $this->assertEquals( $config[ 'inject' ][1][1], '\\' . get_class( $obj1->someObj2 ) );
        $this->assertEquals( $config[ 'inject' ][2][1], '\\' . get_class( $obj1->someObj3 ) );
        $this->assertTrue( $obj1->someObj1 !== $obj1->someObj2 );
        $this->assertTrue( $obj1->someObj1 === $obj1->someObj3 );
        $this->assertTrue( $obj1->someObj2 === $obj1->someObj4 );
    }
    // +----------------------------------------------------------------------+
    function test_getObject_test_4_args() {
        // use 4 args to get an object with idName.
        $className = '\tests\Framework\testClass2';
        $config = array(
            'config' => 'testing config',
            'inject' => array(
                array( 'someObj', '\tests\Framework\testClass1' ),
            ),
            'option' => 'testing init',
        );
        $obj1 = $this->di->get( $className, 'get', 'id', $config );
        $this->assertEquals( $className, '\\' . get_class( $obj1 ) );
        $this->assertEquals( $config['config'], $obj1->config );
        $this->assertEquals( $config['option'], $obj1->option );
        $this->assertEquals( $config['inject'][0][1], '\\' . get_class( $obj1->someObj ) );
        // get the same idName with different config. must be the *SAME* object as $obj1.
        $config2 = array(
            'config' => 'testing config test2',
            'inject' => array(
                array( 'someObj', '\tests\Framework\testClass1' ),
            ),
            'option' => 'testing init 222',
        );
        $obj2 = $this->di->get( $className, 'get', 'id', $config2 );
        $this->assertTrue( $obj1 === $obj2 );
    }
    // +----------------------------------------------------------------------+
    function test_inject_object() {
        $className = '\tests\Framework\testClass2';
        $obj1 = $this->di->get( $className );
        $injectClass = '\tests\Framework\testClass1';
        $this->di->inject( 'someObj', $injectClass, 'get' );
        $this->assertEquals( $injectClass, '\\' . get_class( $obj1->someObj ) );
    }
    // +----------------------------------------------------------------------+
    /**
     * @expectedException RuntimeException
     */
    function test_inject_throws_RuntimeException() {
        $className = '\tests\Framework\testClass1';
        $obj1 = $this->di->get( $className );
        $injectClass = '\tests\Framework\testClass2';
        $this->di->inject( 'someObj', $injectClass, 'get' );
    }
    // +----------------------------------------------------------------------+
}
