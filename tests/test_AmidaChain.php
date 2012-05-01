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
    function test_prependInModel() {
        $chained = 'prep: ';
        $this->amida
            ->addModule( array(
                array( 'chainAuth',  'auth' ),
                array( 'chainView',  'view' ),
            )
        );
        $this->amida->dispatch( 'prepend', $chained );
        $this->assertEquals( 'prep: prependAuth prependModel prependView ', $chained );
    }
    // +----------------------------------------------------------------------+
    function test_chainSkip() {
        $chained = 'skip: ';
        $this->amida
            ->addModule( array(
                array( 'chainAuth',  'auth' ),
                array( 'chainModel', 'model' ),
                array( 'chainView',  'view' ),
            )
        );
        $this->amida->dispatch( 'skip', $chained );
        $this->assertEquals( 'skip: skipAuth skipView ', $chained );
    }
    // +----------------------------------------------------------------------+
    function test_chainModel() {
        $chained = 'chain: ';
        $this->amida
            ->addModule( array(
                array( 'chainAuth',  'auth' ),
                array( 'chainModel', 'model' ),
                array( 'chainView',  'view' ),
            )
        );
        $this->amida->dispatch( 'start', $chained );
        $this->assertEquals( 'chain: defaultAuth startModel normalView ', $chained );
    }
    // +----------------------------------------------------------------------+
    function test_SingleModel() {
        $model = 'oneModel';
        $this->amida->addModule( $model, 'oneModel' );

        // check if model is set.
        $check = $this->amida->getModule();
        $this->assertEquals( $model, $check );

        // dispatch simple function: test_func.
        $data = 'single: ';
        $this->amida->dispatch( 'start', $data );
        $this->assertEquals( 'single: oneStart oneMore oneDone ', $data );
    }
    // +----------------------------------------------------------------------+
    function test_prependComp() {
        // get empty Module.
        $no_comp = $this->amida->getModule();
        $this->assertFalse( $no_comp );
        $no_name = $this->amida->getModuleName();
        $this->assertFalse( $no_name );

        // add Modules in array/array.
        $more = array(
            array( 'more1', 'more1' ),
            array( 'more2', 'more2' ),
        );
        $this->amida->addModule( $more );
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'more1', $more_got );
        
        // ### now prepend a Module.
        $this->amida->prependModule( 'prep1', 'prep1' );
        $prep_got = $this->amida->getModule();
        $this->assertEquals( 'prep1', $prep_got );
        // next is more, right. 
        $this->amida->nextModule();
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'more1', $more_got );
        
        // ### prepend an array of Module
        $this->amida->prependModule( array( 'prep2', 'prep2' ) );
        $prep_got = $this->amida->getModule();
        $this->assertEquals( 'prep2', $prep_got );
        // next is more, right. 
        $this->amida->nextModule();
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'more1', $more_got );

        // ### add modules in array/array.
        $prep2 = array(
            array( 'prep1', 'prep1' ),
            array( 'prep2', 'prep2' ),
        );
        $this->amida->prependModule( $prep2 );
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'prep1', $more_got );

        $this->amida->nextModule();
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'prep2', $more_got );

        $this->amida->nextModule();
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'more1', $more_got );

    }
    // +----------------------------------------------------------------------+
    function test_addModule() {
        // get empty module.
        $no_comp = $this->amida->getModule();
        $this->assertFalse( $no_comp );
        $no_name = $this->amida->getModuleName();
        $this->assertFalse( $no_name );
        
        // add two modules in a row.
        $test1 = 'test1';
        $test2 = 'test2';
        $this->amida->addModule( $test1, 'test1' );
        $this->amida->addModule( $test2, 'test2' );
        
        // test if module is added AND get module correctly.
        $test1_got = $this->amida->getModule();
        $this->assertEquals( $test1, $test1_got );
        $test1_name = $this->amida->getModuleName();
        $this->assertEquals( 'test1', $test1_name );
        
        // test if next module.
        $this->amida->nextModule();
        $test2_got = $this->amida->getModule();
        $this->assertEquals( $test2, $test2_got );
        
        // add a module in array.
        $more = array( 'more', 'more');
        $this->amida->addModule( $more );
        $this->amida->nextModule();
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'more', $more_got );
        
        // add modules in array/array.
        $more2 = array(
            array( 'more1', 'more1' ),
            array( 'more2', 'more2' ),
        );
        $this->amida->addModule( $more2 );
        $this->amida->nextModule();
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'more1', $more_got );
        
        $this->amida->nextModule();
        $more_got = $this->amida->getModule();
        $this->assertEquals( 'more2', $more_got );

        $more2_name = $this->amida->getModuleName();
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

// ======================================================================= //
class chainAuth
{
    function actionDefault( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'defaultAuth ';
    }
    function actionSkip( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'skipAuth ';
        $ctrl->skipToModel( 'view' );
    }
    function actionPrepend( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'prependAuth ';
        $ctrl->prependModule( 'chainModel', 'model' );
    }
}

class chainModel
{
    function actionDefault( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'defaultModel ';
    }
    function actionStart( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'startModel ';
        $ctrl->setAction( 'normal' );
    }
    function actionSkip( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'skipModel ';
    }
    function actionPrepend( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'prependModel ';
    }
}

class chainView
{
    function actionDefault( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'defaultView ';
    }
    function actionNormal( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'normalView ';
    }
    function actionSkip( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'skipView ';
        $ctrl->setAction( 'normal' );
    }
    function actionPrepend( AmidaMVC\Framework\AmidaChain $ctrl, &$data ) {
        $data .= 'prependView ';
    }
}
