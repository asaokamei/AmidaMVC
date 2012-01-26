<?php
error_reporting( E_ALL );
require( '../src/AmidaMVC/bootstrap.php' );


class test_FrameworkAmidaChain extends PHPUnit_Framework_TestCase
{
    var $amida;
    // +----------------------------------------------------------------------+
    public function setUp()
    {
        global $test_dispatch;
        $test_dispatch  = FALSE;
        $this->amida = new AmidaMVC\Framework\AmidaChain();
    }
    // +----------------------------------------------------------------------+
    function test_component() {
        $test1 = 'test1';
        $test2 = 'test2';
        $this->amida->addComponent( $test1, 'test' );
        $this->amida->addComponent( $test2, 'test2' );
        
        // test if component is added AND get component correctly. 
        $test1_got = $this->amida->getComponent();
        $this->assertEquals( $test1, $test1_got );
        
        // test if next component. 
        $test2_got = $this->amida->getComponent();
        $this->assertEquals( $test2, $test2_got );
    }
    // +----------------------------------------------------------------------+
}
