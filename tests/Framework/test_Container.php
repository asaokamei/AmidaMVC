<?php
namespace tests\Framework;
error_reporting( E_ALL );
require( '../../src/AmidaMVC/bootstrap.php' );

class testClass1
{
    var $config;
    var $option;
    var $someObj;
    function __construct( $config=NULL ) {

    }
    function _init( $option=NULL ) {
    }
    function injectSome( $obj ) {
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
        $test_dispatch  = FALSE;
        $this->di = new \AmidaMVC\Framework\Container();
    }
    // +----------------------------------------------------------------------+
    function test_getObject() {
        $className = '\tests\Framework\testClass1';
        $obj1 = $this->di->get( $className );
        $this->assertEquals( $className, '\\' . get_class( $obj1 ) );
    }
    // +----------------------------------------------------------------------+
}
