<?php
namespace AmidaMVC\Application;

class App2
{
    static $sl;
    function app() {
        $di = self::diCms();
        $sl = self::setUpDi( $di );
        /** @var $ctrl \AmidaMVC\Framework\Controller */
        $ctrl = $sl->get( 'controller' );

        $modules = array(
            //'authAdminOnly',
            'authDevLogin', 'authDevLogout', 'authDevFiler',
            'router',       'loader',        'emitter',
        );
        $ctrl->setModules( $modules );
        $ctrl->separateCommands();

        return $ctrl;
    }
    function diCms() {
        $www = realpath( __DIR__ . '/../www/' );
        $di = array(
            'load' => array(
                'din'    => array( '\AmidaMVC\Tools\Load', 'get' ),
                'config' => array(
                    $www
                ),
            ),
            'route' => array(
                'din'    => array( '\AmidaMVC\Tools\Route', 'get' ),
                'config' => array(),
                'inject' => array(
                    array( 'load', 'load' ),
                )
            ),
            'request' => array(
                'din' => array( '\AmidaMVC\Tools\Request', 'new' ),
            ),
            'session' => array(
                'din' => array( '\AmidaMVC\Tools\Session', 'new' ),
            ),
            'router' => array(
                'din'    => array( '\AmidaMVC\Module\Router',  'new' ),
                'config' => array(),
                'inject' => array(
                    array( 'route', 'route' ),
                )
            ),
            'loader' => array(
                'din'    => array( '\AmidaMVC\Module\Loader',  'new' ),
                'inject' => array(
                    array( 'load', 'load' ),
                )
            ),
            'emitter' => array(
                'din' => array( '\AmidaMVC\Module\Emitter', 'new' )
            ),
            'controller' => array(
                'din'    => array( '\AmidaMVC\Framework\Controller', 'get' ),
                'config' => array(
                    'site_title'        => 'AppCMS Web Site',
                    'template_file'     => '_Config/template.php',
                    'pageNotFound_file' => '_Config/pageNotFound.md',
                    'template_dev_file' => '_Config/template._dev.php',
                ),
                'inject' => array(
                    array( 'request', 'request' ),
                    array( 'load', 'load' ),
                    array( 'diContainer', '_self' ),
                ),
            ),
            'authAdmin' => array(
                'din' => array( '\AmidaMVC\Tools\AuthNot',  'new', 'authAdmin' ),
                'config' => array(
                    'password_file' => 'admin.password',
                    'authArea'      => 'authAdmin'
                )
            ),
            'authDev' => array(
                'din'    => array( '\AmidaMVC\Tools\AuthNot',    'new', 'authDev' ),
                'config' => array(
                    'password_file' => 'dev.password',
                    'authArea'      => 'authDev'
                ),
                'inject' => array(
                    array( 'session', 'session' ),
                ),
            ),
            'authDevLogin' => array(
                'din'    => array( '\AmidaMVC\Module\Auth', 'new' ),
                'config' => array(
                    'authArea' => 'authDev',
                    'evaluateOn' => array(
                        'onPathInfo' => array( '/dev_login' ),
                        'onFail' => array(
                            'setLoginForm' => '_Config/login_file.md',
                        ),
                        'onSuccess' => array(
                            'redirect' => '/',
                        ),
                    ),
                )
            ),
            'authDevFiler' => array(
                'din'    => array( '\AmidaMVC\Module\Auth', 'new' ),
                'config' => array(
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
            'authDevLogout' => array(
                'din'    => array( '\AmidaMVC\Module\Auth', 'new' ),
                'config' => array(
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
                )
            ),
            'filer' => array(
                'din'    => array( '\AmidaMVC\Module\Filer', 'new' ),
                'config' => array(
                    'template_file' => NULL,
                    'listJs' => array(
                        '/common/js/jquery-1.7.1.js',
                        '/common/js/bootstrap.js',
                        '/common/js/bootstrap-modal.js',
                    ),
                    'listCss' => array(
                        '/common/css/bootstrap.css',
                    ),
                ),
                'inject' => array(
                    array( 'load', 'load' ),
                ),
            )
        );
        return $di;
    }
    function setUpDi( $di ) {
        static::$sl = $sl = \AmidaMVC\Framework\Services::start();
        foreach( $di as $service => $setup ) {
            $din = array_merge( array( $service ), $setup[ 'din' ] );
            call_user_func_array( array( $sl, 'setService' ), $din );
            if( isset( $setup[ 'config' ] ) ) {
                call_user_func( array( $sl, 'setConfig' ), $setup[ 'config' ] );
            }
            if( isset( $setup[ 'inject' ] ) && is_array( $setup[ 'inject' ] ) ) {
                foreach( $setup[ 'inject' ] as $inject ) {
                    call_user_func_array( array( $sl, 'setInject' ), $inject );
                }
            }
        }
        return $sl;
    }
}