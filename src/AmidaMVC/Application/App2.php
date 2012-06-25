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
            'router',       'loader',        'menus',      'emitter',
        );
        $ctrl->setModules( $modules );
        $ctrl->separateCommands();

        return $ctrl;
    }
    function diConfigSample() {
        $diConfigJa = array(
            'configJa' => array(
                'din' => array( '\AmidaMVC\Module\Config', 'new' ),
                'config' => array(
                    'evaluateOn' => array(
                        'onPathInfo'  => '/',
                        'variable' => '_lang',
                        'session'  => TRUE,
                        'value'    => 'ja',
                    ),
                    'onSuccess' => array(
                        'ctrl_root' => 'docs.ja',
                        'set_option' => array(
                            'template_file' => '_Config/template.ja.php',
                            'language' => 'ja',
                        ),
                    ),
                ),
            )
        );
        $diConfigSmartPhone = array(
            'configSf' => array(
                'din' => array( '\AmidaMVC\Module\Config', 'new' ),
                'config' => array(
                    'evaluateOn' => array(
                        'onPathInfo'  => '/',
                        'user_agent'  => 'mobile',
                    ),
                    'onSuccess' => array(
                        'set_option' => array(
                            'template_file' => '_Config/template.small.php',
                        ),
                    ),
                ),
            )
        );
    }
    function diCms() {
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
                'din' => array( '\AmidaMVC\Tools\Request', 'new' ),
            ),
            'session' => array(
                'din' => array( '\AmidaMVC\Tools\Session', 'new' ),
            ),
            'i18n' => array(
                'din'    => array( '\AmidaMVC\Tools\i18n', 'new' ),
                'config' => array(
                    'language'         => 'ja',
                    'directory'        => '_Config',
                    'file_amidaMVC'    => 'amidaMVC',
                    'file_application' => 'application',
                ),
                'inject' => array(
                    array( 'load', 'load' ),
                ),
            ),
            'navBar' => array(
                'din' => array( '\AmidaMVC\Tools\NavBar', 'new' ),
            ),
            // Modules
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
                'din' => array( '\AmidaMVC\Module\Menus', 'new' ),
                'inject' => array(
                    array( 'nav', 'navBar' )
                ),
            ),
            // Controller
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
                    array( 'i18n', 'i18n' ),
                    array( 'diContainer', '_self' ),
                ),
            ),
            // for _Dev mode
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
                    array( 'request', 'request' ),
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