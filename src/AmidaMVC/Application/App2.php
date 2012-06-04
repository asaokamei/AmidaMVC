<?php
namespace AmidaMVC\Application;

class App2
{
    static $sl;
    function app() {
        $di = self::diCms();
        $sl = self::setUpDi( $di );
        $ctrl = $sl->get( 'controller' );

        $modules = array(
            //'authAdminOnly',
            'authDevLogin', 'authDevLogout', 'authDevFiler',
            'router',       'loader',        'emitter',
        );
        $modules = array(
            'router', 'loader', 'emitter',
        );
        $ctrl->setModules( $modules );

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
            'router' => array(
                'din' => array( '\AmidaMVC\Module\Router',  'new' ),
                'config' => array(),
                'inject' => array(
                    array( 'route', 'route' ),
                )
            ),
            'loader' => array(
                'din' => array( '\AmidaMVC\Module\Loader',  'new' ),
                'inject' => array(
                    array( 'load', 'load' ),
                )
            ),
            'emitter' => array(
                'din' => array( '\AmidaMVC\Module\Emitter', 'new' )
                ),
            'controller' => array(
                'din' => array( '\AmidaMVC\Framework\Controller', 'get' ),
                'config' => array(
                    'site_title'        => 'AppCMS Web Site',
                    'template_file'     => '_Config/template.php',
                    'pageNotFound_file' => '_Config/pageNotFound.md',
                ),
                'inject' => array(
                    array( 'request', 'request' ),
                    array( 'load', 'load' ),
                    array( 'diContainer', '_self' ),
                ),
            ),
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