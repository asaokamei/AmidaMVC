<?php
namespace AmidaMVC\Application;

class Application extends \AmidaMVC\Framework\Controller
{
    static $diContainerStart = array( '\AmidaMVC\Framework\Container', 'start' );
    static $controllerStart = array( '\AmidaMVC\Framework\Controller', 'getInstance' );
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     * @return \AmidaMVC\AppSimple\Application
     */
    static function simple( $option=array() )
    {
        // various default options
        $ctlDefault = array(
            'site_title' => 'AppSimple Web Site',
            'template_file' => NULL,
            'pageNotFound_file' => FALSE,
            'appDefault' => NULL,
        );
        $moduleDefault = array(
            'router', 'loader', 'emitter',
        );
        $diDefault = array(
            array( 'router',  '\AmidaMVC\AppSimple\Router',  'new', array() ),
            array( 'loader',  '\AmidaMVC\AppSimple\Loader',  'new', array() ),
            array( 'emitter', '\AmidaMVC\AppSimple\Emitter', 'new', array() ),
        );
        // create Dependency Injection Container.
        /** @var $diContainer \AmidaMVC\Framework\Container */
        $diContainer = call_user_func( self::$diContainerStart );
        // create AmidaMVC Controller.
        /** @var $controller \AmidaMVC\Framework\Controller */
        $controller = call_user_func( self::$controllerStart, $ctlDefault );
        $controller->injectDiContainer( $diContainer );
        $controller->injectRequest( $diContainer->get( '\AmidaMVC\Tools\Request' ) );
        $controller->injectLoad( $diContainer->get( '\AmidaMVC\Tools\Load', 'static' ) );
        self::setupDiContainer( $diContainer, $option, $diDefault  );
        self::setupController( $controller, $option, $ctlDefault, $moduleDefault  );
        $controller->separateCommands();

        return $controller;
    }
    // +-------------------------------------------------------------+
    static function setupDiContainer( $diContainer, &$option, $di ) {
        if( isset( $option[ 'diContainer' ] ) && is_array( $option[ 'diContainer' ] ) ) {
            $di = array_merge( $di, $option[ 'diContainer' ] );
            unset( $option[ 'diContainer' ] );
        }
        foreach( $di as $moduleInfo ) {
            call_user_func_array( array( $diContainer, 'setModule' ), $moduleInfo );
        }
    }
    static function setupController( $ctrl, &$option, &$ctl, &$mod ) {
        if( isset( $option[ 'modules' ] ) && is_array( $option[ 'modules' ] ) ) {
            $mod = array_merge( $mod, $option[ 'modules' ] );
            unset( $option[ 'modules' ] );
        }
        $ctl = array_merge( $ctl, $option );
        $ctrl->setModules( $mod );
    }
    // +-------------------------------------------------------------+
}