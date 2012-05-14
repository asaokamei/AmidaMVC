<?php
namespace AmidaMVC\Application;

class Application extends \AmidaMVC\Framework\Controller
{
    static $diContainerStart = array( '\AmidaMVC\Framework\Container', 'start' );
    static $controllerStart = array( '\AmidaMVC\Framework\Controller', 'getInstance' );
    // +-------------------------------------------------------------+
    /**
     * @static
     * @param array $option
     * @return \AmidaMVC\Framework\Controller
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
        $diContainer = self::setupDiContainer( $option, $diDefault  );
        // create AmidaMVC Controller.
        $controller = self::setupController( $option, $ctlDefault, $moduleDefault  );
        // inject dependencies.
        $controller->injectDiContainer( $diContainer );
        $controller->injectRequest( $diContainer->get( '\AmidaMVC\Tools\Request' ) );
        $controller->injectLoad( $diContainer->get( '\AmidaMVC\Tools\Load', 'static' ) );
        // setup container and controller.


        $controller->separateCommands();

        return $controller;
    }
    // +-------------------------------------------------------------+
    /**
     * @static
     * @param array $option
     * @param array $di
     * @return \AmidaMVC\Framework\Container
     */
    static function setupDiContainer( &$option, $di ) {
        $diContainer = call_user_func( self::$diContainerStart );
        if( isset( $option[ 'diContainer' ] ) && is_array( $option[ 'diContainer' ] ) ) {
            $di = array_merge( $di, $option[ 'diContainer' ] );
            unset( $option[ 'diContainer' ] );
        }
        foreach( $di as $moduleInfo ) {
            call_user_func_array( array( $diContainer, 'setModule' ), $moduleInfo );
        }
        return $diContainer;
    }

    /**
     * @static
     * @param array $option
     * @param array $ctl
     * @param array $mod
     * @return \AmidaMVC\Framework\Controller
     */
    static function setupController( &$option, &$ctl, &$mod ) {
        if( isset( $option[ 'modules' ] ) && is_array( $option[ 'modules' ] ) ) {
            $mod = array_merge( $mod, $option[ 'modules' ] );
            unset( $option[ 'modules' ] );
        }
        $ctl = array_merge( $ctl, $option );
        $controller = call_user_func( self::$controllerStart, $ctl );
        $controller->setModules( $mod );
        return $controller;
    }
    // +-------------------------------------------------------------+
}