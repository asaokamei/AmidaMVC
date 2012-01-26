<?php
error_reporting( E_ALL );
require( '../src/AmidaMVC/bootstrap.php' );


class test_FrameworkAmidaChain extends PHPUnit_Framework_TestCase
{
    /**
     * @var AmidaMVC\Framework\AmidaChain
     */
    var $amida;
    // +----------------------------------------------------------------------+
    public function setUp()
    {
        global $test_dispatch;
        $test_dispatch  = FALSE;
        $this->amida = new AmidaMVC\Framework\AmidaChain();
    }
    // +----------------------------------------------------------------------+
    function test_SingleModel() {
        $model = 'oneModel';
        $this->amida->addComponent( $model, 'oneModel' );

        // check if model is set.
        $check = $this->amida->getComponent();
        $this->assertEquals( $model, $check );

        // dispatch simple function: test_func.
        $data = '';
        $this->amida->dispatch( 'start', $data );
        $this->assertEquals( 'oneStart oneMore oneDone ', $data );
    }
    // +----------------------------------------------------------------------+
    function test_prependComp() {
        // get empty component. 
        $no_comp = $this->amida->getComponent();
        $this->assertFalse( $no_comp );
        $no_name = $this->amida->getComponentName();
        $this->assertFalse( $no_name );

        // add components in array/array.
        $more = array(
            array( 'more1', 'more1' ),
            array( 'more2', 'more2' ),
        );
        $this->amida->addComponent( $more );
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'more1', $more_got );
        
        // ### now prepend a component.
        $this->amida->prependComponent( 'prep1', 'prep1' );
        $prep_got = $this->amida->getComponent();
        $this->assertEquals( 'prep1', $prep_got );
        // next is more, right. 
        $this->amida->nextComponent();
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'more1', $more_got );
        
        // ### prepend an array of component
        $this->amida->prependComponent( array( 'prep2', 'prep2' ) );
        $prep_got = $this->amida->getComponent();
        $this->assertEquals( 'prep2', $prep_got );
        // next is more, right. 
        $this->amida->nextComponent();
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'more1', $more_got );

        // ### add components in array/array.
        $prep2 = array(
            array( 'prep1', 'prep1' ),
            array( 'prep2', 'prep2' ),
        );
        $this->amida->prependComponent( $prep2 );
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'prep1', $more_got );

        $this->amida->nextComponent();
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'prep2', $more_got );

        $this->amida->nextComponent();
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'more1', $more_got );

    }
    // +----------------------------------------------------------------------+
    function test_addComponent() {
        // get empty component. 
        $no_comp = $this->amida->getComponent();
        $this->assertFalse( $no_comp );
        $no_name = $this->amida->getComponentName();
        $this->assertFalse( $no_name );
        
        // add two components in a row.
        $test1 = 'test1';
        $test2 = 'test2';
        $this->amida->addComponent( $test1, 'test1' );
        $this->amida->addComponent( $test2, 'test2' );
        
        // test if component is added AND get component correctly. 
        $test1_got = $this->amida->getComponent();
        $this->assertEquals( $test1, $test1_got );
        $test1_name = $this->amida->getComponentName();
        $this->assertEquals( 'test1', $test1_name );
        
        // test if next component.
        $this->amida->nextComponent();
        $test2_got = $this->amida->getComponent();
        $this->assertEquals( $test2, $test2_got );
        
        // add a component in array.
        $more = array( 'more', 'more');
        $this->amida->addComponent( $more );
        $this->amida->nextComponent();
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'more', $more_got );
        
        // add components in array/array.
        $more2 = array(
            array( 'more1', 'more1' ),
            array( 'more2', 'more2' ),
        );
        $this->amida->addComponent( $more2 );
        $this->amida->nextComponent();
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'more1', $more_got );
        
        $this->amida->nextComponent();
        $more_got = $this->amida->getComponent();
        $this->assertEquals( 'more2', $more_got );

        $more2_name = $this->amida->getComponentName();
        $this->assertEquals( 'more2', $more2_name );
    }
    // +----------------------------------------------------------------------+
}

// ======================================================================= //
class oneModel
{
    function actionStart( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'oneStart ';
        $ctrl->setMyAction( 'more' );
    }
    function actionMore( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'oneMore ';
        $ctrl->setMyAction( 'done' );
    }
    function actionDone( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'oneDone ';
    }
}

