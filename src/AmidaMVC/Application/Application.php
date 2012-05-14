<?php
namespace AmidaMVC\Application;

class Application extends \AmidaMVC\Framework\Controller
{
    static $diContainerStart = array( '\AmidaMVC\Framework\Container', 'start' );
    static $controllerStart  = array( '\AmidaMVC\Framework\Controller', 'getInstance' );
    // +-------------------------------------------------------------+
    /**
     * @static
     * @param array $option
     * @return \AmidaMVC\Framework\Controller
     */
    static function cms( $option=array() )
    {
        // various default options
        $ctlDefault = array(
            'site_title' => 'AppCMS Web Site',
            'template_file' => 'template.php',
            'pageNotFound_file' => FALSE,
        );
        $moduleDefault = array(
            //'authAdminOnly',
            'authDevLogin', 'authDevLogout', 'authDevFiler',
            'router',       'loader',        'emitter',
        );
        $diDefault = array(
            array( 'router',    '\AmidaMVC\AppSimple\Router',  'new', array() ),
            array( 'loader',    '\AmidaMVC\AppSimple\Loader',  'new', array() ),
            array( 'emitter',   '\AmidaMVC\AppSimple\Emitter', 'new', array() ),
            array( 'authAdmin', '\AmidaMVC\Tools\AuthNot', 'get', 'authAdmin',
                array(
                    'password_file' => 'admin.password',
                    'authArea'      => 'authAdmin'
                )
            ),
            array( 'authDev', '\AmidaMVC\Tools\AuthNot', 'get', 'authDev',
                array(
                    'password_file' => 'dev.password',
                    'authArea'      => 'authDev'
                )
            ),
            array( 'authAdminOnly', '\AmidaMVC\AppCms\Auth', 'get',
                array(
                    'authClass'      => 'authAdmin',
                    'evaluateOn' => array(
                        'onPathInfo' => array( '/admin', '/admin2' ),
                        'onFail' => array(
                            'setLoginForm' => 'login for admin pages',
                        ),
                        'onSuccess' => array(),
                    ),
                )
            ),
            array( 'authDevLogin', '\AmidaMVC\AppCms\Auth', 'get',
                array(
                    'authArea' => 'authDev',
                    'evaluateOn' => array(
                        'onPathInfo' => array( '/dev_login' ),
                        'onFail' => array(
                            'setLoginForm' => 'login_file.md',
                        ),
                        'onSuccess' => array(
                            'redirect' => '/',
                        ),
                    ),
                ),
            ),
            array( 'authDevLogout', '\AmidaMVC\AppCms\Auth', 'get',
                array(
                    'authArea' => 'authDev',
                    'evaluateOn' => array(
                        'onPathInfo' => array( '/dev_logout' ),
                        'onFail' => array(
                            'redirect' => '/',
                        ),
                        'onSuccess' => array(
                            'logout' => '',
                            'redirect' => '/',
                        ),
                    ),
                ),
            ),
            array( 'authDevFiler', '\AmidaMVC\AppCms\Auth', 'get',
                array(
                    'authArea' => 'authDev',
                    'evaluateOn' => array(
                        'onPathInfo' => array( '/' ),
                        'onFail' => array(),
                        'onSuccess' => array(
                            'addModuleAfter' => array( 'router', 'filer', 'filer', ),
                        ),
                    ),
                )
            ),
            array( 'filer', '\AmidaMVC\AppCms\Filer', 'new',
                array(
                    'template_file' => NULL,
                    'listJs' => array(
                        '../bootstrap/js/jquery-1.7.1.js',
                        '../bootstrap/js/bootstrap.js',
                        '../bootstrap/js/bootstrap-modal.js',
                    ),
                    'listCss' => array(
                        '../bootstrap/css/bootstrap.css',
                    ),
                )
            ),
        );
        // create Dependency Injection Container.
        $diContainer = self::setupDiContainer( $option, $diDefault  );
        // create AmidaMVC Controller.
        $controller = self::setupController( $diContainer, $option, $ctlDefault, $moduleDefault  );

        return $controller;
    }
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
        $controller = self::setupController( $diContainer, $option, $ctlDefault, $moduleDefault  );

        return $controller;
    }
    // +-------------------------------------------------------------+
    /**
     * @static
     * @param array $option
     * @param array $diOption
     * @return \AmidaMVC\Framework\Container
     */
    static function setupDiContainer( &$option, $diOption ) {
        $diContainer = call_user_func( self::$diContainerStart );
        if( isset( $option[ 'diContainer' ] ) && is_array( $option[ 'diContainer' ] ) ) {
            $diOption = array_merge( $diOption, $option[ 'diContainer' ] );
            unset( $option[ 'diContainer' ] );
        }
        foreach( $diOption as $moduleInfo ) {
            call_user_func_array( array( $diContainer, 'setModule' ), $moduleInfo );
        }
        return $diContainer;
    }

    /**
     * @static
     * @param \AmidaMVC\Framework\Container $diContainer
     * @param array $option
     * @param array $ctlOption
     * @param array $modOption
     * @return \AmidaMVC\Framework\Controller
     */
    static function setupController( $diContainer, &$option, &$ctlOption, &$modOption ) {
        if( isset( $option[ 'modules' ] ) && is_array( $option[ 'modules' ] ) ) {
            $modOption = array_merge( $modOption, $option[ 'modules' ] );
            unset( $option[ 'modules' ] );
        }
        $ctlOption = array_merge( $ctlOption, $option );
        /** @var $controller \AmidaMVC\Framework\Controller */
        $controller = call_user_func( self::$controllerStart, $ctlOption );
        $controller->setModules( $modOption );
        // inject dependencies.
        $controller->injectDiContainer( $diContainer );
        $controller->injectRequest( $diContainer->get( '\AmidaMVC\Tools\Request' ) );
        $controller->injectLoad( $diContainer->get( '\AmidaMVC\Tools\Load', 'static' ) );
        // some more actions to do.
        $controller->separateCommands();
        return $controller;
    }
    // +-------------------------------------------------------------+
}