<?php
namespace AmidaMVC\Tests\Application\AppSimple;
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

use \AmidaMVC\Application\Application as TestApp;

class pageObj extends \AmidaMVC\Framework\PageObj {
    function emit() {}
}

class test_ITest_AppSimple extends \PHPUnit_Framework_TestCase
{
    /** @var \AmidaMVC\Framework\Controller */
    var $app;
    /** @var \AmidaMVC\Framework\Container */
    var $di;
    /** @var array */
    var $req;
    /** @var array */
    var $files;
    /** @var array */
    var $appConfig;
    function setUp() {
        $this->files = array(
            '/path/to/' => NULL,
            '/path/to/index.md' => "#Top of Array Data\n hello from Array!",
            '' => '',
        );
        $this->req = array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'SCRIPT_NAME' => '/path/to/_indexTest.php',
        );
        $this->appConfig = array(
            'site_title' => "TEST/Simple/array",
            'template_file' => NULL,
            'ctrl_root' => '/path/to/',
        );
        $this->di = \AmidaMVC\Framework\Container::start();
        $this->di->setModule( '\AmidaMVC\Framework\PageObj', '\AmidaMVC\Tests\Application\AppSimple\PageObj' );
        $this->di->setModule( '\AmidaMVC\Tools\Load', '\AmidaMVC\Tools\LoadArray', 'static' );
        /** @var $load \AmidaMVC\Tools\LoadArray */
        $load = $this->di->get( '\AmidaMVC\Tools\LoadArray' );
        $load::setFiles( $this->files );
    }
    function test_1st() {
        $this->di->setModule( '\AmidaMVC\Tools\Request', '\AmidaMVC\Tools\Request', 'get', $this->req  );
        $this->app = \AmidaMVC\Application\Application::simple( $this->appConfig );
        $this->app->start();
        $this->assertTrue( TRUE );
        echo $this->app->pageObj->getContent();
    }
}
