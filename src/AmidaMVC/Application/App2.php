<?php
namespace AmidaMVC\Application;

class App2
{
    static $sl;

    /**
     * @return \AmidaMVC\Framework\Controller|
     */
    static function app() {
        $di = self::diCms();
        $sl = self::setUpDi( $di );
        /** @var $ctrl \AmidaMVC\Framework\Controller */
        $ctrl = $sl->get( 'controller' );

        $modules = array(
            //'authAdminOnly',
            'config', 'lang', 'template',
            'authDevLogin',
            'router',       'loader',        'menus',      'emitter',
        );
        $ctrl->setModules( $modules );
        $ctrl->separateCommands();

        return $ctrl;
    }
    /**
     *
     */
    static function diConfigSample() 
    {
        /**
         * set to false to turn of the module. 
         */
        $moduleList = array(
            //'authAdminOnly',
            'lang'          => TRUE, 
            'template'      => TRUE,
            'authDev'       => TRUE, 
            'router'        => TRUE,
            'loader'        => TRUE,
            'menus'         => TRUE,
            'emitter'       => TRUE,
        );
        $diConfigSmartPhone = array(
            'configSf' => array(
                'din'    => array( '\AmidaMVC\Module\Config', 'new' ),
                'inject' => array(),
                'config' => array(
                    'onPathInfo' => '/',
                    'evaluate'   => array( 'request', 'isMobile' ),
                    'onSuccess'  => array(
                        'set_option' => function( $_ctrl ) {
                            $_ctrl->options[ 'template_file' ] = '_Config/template.small.php';
                            return TRUE;
                        },
                    ),
                ),
            )
        );
        // wishful programming...
        // use closure, avoid array/config/settings.
        $diConfigAuth = array();

        $diConfigAuth[ 'Device' ] = array(
            'din'    => array( '\AmidaMVC\Module\Config', 'new' ),
            'inject' => array(),
            'config' => array(
                array(
                    'onPathInfo' => array( '/common/images/' ),
                    'evaluate'   => array( 'request', 'isRetina' ),
                    'onSuccess'  => function( $_ctrl ) {
                        $_ctrl->load->setFileLocation( '_docs.retina' );
                        return TRUE;
                    },
                ),
                array(
                    'onPathInfo' => array( '/' ),
                    'evaluate'   => array( 'request', 'isSmall' ),
                    'onSuccess'  => function( $_ctrl ) {
                        $_ctrl->options[ 'template_file' ] = '_Config/template.small.php';
                        return TRUE;
                    },
                ),
            ),
        );
    }
    /**
     * @return array
     */
    static function diCms() {
        $www = realpath( __DIR__ . '/../../www/' );
        $di = array(
            // Tools
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
                'din'    => array( '\AmidaMVC\Tools\Request', 'get' ),
            ),
            'session' => array(
                'din'    => array( '\AmidaMVC\Tools\Session', 'get' ),
            ),
            'i18n' => array(
                'din'    => array( '\AmidaMVC\Tools\i18n', 'get' ),
                'config' => array(
                    'language'         => 'en',
                    'directory'        => '_Config',
                    'file_amidaMVC'    => 'amidaMVC',
                    'file_application' => 'application',
                ),
                'inject' => array(
                    array( 'load', 'load' ),
                ),
            ),
            'navBar' => array(
                'din' => array( '\AmidaMVC\Tools\NavBar', 'get' ),
            ),
            'section' => array(
                'din' => array( '\AmidaMVC\Tools\Section', 'get' ),
            ),
            // Modules
            'config' => array(
                'din'    => array( '\AmidaMVC\Module\Config',    'new' ),
                'config' => array(
                    'onPathInfo' => '/',
                    'evaluate' => function( $config ) {
                        $config->test = 'test';
                    }
                ),
                'inject' => array(),
            ),
            'lang' => array(
                'din'    => array( '\AmidaMVC\Module\Lang',    'new' ),
                'config' => array(),
                'inject' => array(
                    array( 'i18n',    'i18n' ),
                    array( 'session', 'session' )
                ),
            ),
            'template' => array(
                'din'    => array( '\AmidaMVC\Module\Template',    'new' ),
                'config' => array(),
                'inject' => array(
                    array( 'i18n',    'i18n' ),
                    array( 'session', 'session' )
                ),
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
            'menus' => array(
                'din'    => array( '\AmidaMVC\Module\Menus', 'new' ),
                'inject' => array(
                    array( 'nav', 'navBar' ),
                    array( 'i18n', 'i18n' ),
                ),
            ),
            // Page Object
            'pageObj' => array(
                'din'    => array( '\AmidaMVC\Framework\PageObj', 'new' ),
                'config' => array(),
                'inject' => array(
                    array( 'section', 'section' ),
                ),
            ),
            // Controller
            'controller' => array(
                'din'    => array( '\AmidaMVC\Framework\Controller', 'get' ),
                'config' => array(
                    'site_title'        => 'AppCMS Web Site',
                    'template_file'     => '_Config/template.base.php',
                    'pageNotFound_file' => '_Config/pageNotFound.md',
                    'template_dev_file' => '_Config/template._dev.php',
                ),
                'inject' => array(
                    array( 'request', 'request' ),
                    array( 'load', 'load' ),
                    array( 'i18n', 'i18n' ),
                    array( 'diContainer', '_self' ),
                ),
            ),
            'authDev' => array(
                'din'    => array( '\AmidaMVC\Tools\AuthBasic',    'new', 'authDev' ),
                'config' => array(
                    'password_file' => '_Config/.dev.password',
                    'authArea'      => 'authDev'
                ),
                'inject' => array(
                    array( 'session', 'session' ),
                    array( 'request', 'request' ),
                    array( 'load', 'load' ),
                ),
            ),
            'authDevLogin' => array(
                'din'    => array( '\AmidaMVC\Module\Auth', 'new' ),
                'inject' => array(
                    array( 'i18n', 'i18n' ),
                ),
                'config' => array(
                    array(
                        'onPathInfo' => '/',
                        'evaluate' => array( 'authDev', 'getAuth' ),
                        'onSuccess' => function( $auth ) {
                            $auth->_ctrl->addModuleAfter( 'router', 'filer', 'filer' );
                            $auth->drawLogout();
                        },
                        'onFail' => function( $auth ) {
                            $auth->drawLogin();
                        },
                    ),
                    array(
                        'onPathInfo' => '/dev_logout',
                        'evaluate' => array( 'authDev', 'logout' ),
                        'onAny' => function( $auth ) {
                            $auth->_ctrl->redirect( '/' );
                        },
                    ),
                    array(
                        'onPathInfo' => '/dev_login',
                        'evaluate' => array( 'authDev', 'getAuth' ),
                        'onFail' => function( $auth ) {
                            $auth->_ctrl->setAction( '_loginForm' );
                            $auth->_ctrl->options[ 'loginForm_file' ] = '_Config/login_file.md';
                        },
                        'onSuccess' => function( $auth ) {
                            $auth->_ctrl->redirect( '/' );
                        },
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

    /**
     * @param $di
     * @return \AmidaMVC\Framework\Services
     */
    static function setUpDi( $di )
    {
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