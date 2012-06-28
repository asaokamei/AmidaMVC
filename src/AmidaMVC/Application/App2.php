<?php
namespace AmidaMVC\Application;

class App2
{
    static $sl;

    /**
     * @return \AmidaMVC\Framework\Controller|
     */
    function app() {
        $di = self::diCms();
        $sl = self::setUpDi( $di );
        /** @var $ctrl \AmidaMVC\Framework\Controller */
        $ctrl = $sl->get( 'controller' );

        $modules = array(
            //'authAdminOnly',
            'lang',
            'authDevLogin', 'authDevLogout', 'authDevFiler',
            'router',       'loader',        'menus',      'emitter',
        );
        $ctrl->setModules( $modules );
        $ctrl->separateCommands();

        return $ctrl;
    }
    /**
     *
     */
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
                        'ctrl_root' => '_docs.ja',
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
        // wishful programming...
        $diConfigAuth = array(
            'auth' => array(
                'din'    => array( '\AmidaMVC\Tools\AuthBasic', 'get' ),
                'config' => array(
                    'password_file' => '_Config/.password',
                ),
                'inject' => array(
                    array( 'load', 'load' ),
                ),
            ),
            'AuthDev' => array(
                'din'    => array( '\AmidaMVC\Module\Auth', 'new' ),
                'inject' => array(),
                'config' => array(
                    array(
                        'onPathInfo' => array( '/dev_login' ),
                        'evaluate' => array( 'auth', 'isLogin' ),
                        'onFail' => array(
                            'setLoginForm' => '_Config/login_file.md',
                        ),
                        'onSuccess' => array(
                            'redirect' => '/',
                        ),
                    ),
                    array(
                        'onPathInfo' => array( '/dev_login' ),
                        'evaluate' => array( 'auth', 'logOut' ),
                    ),
                    array(
                        'onPathInfo' => array( '/admin/' ),
                        'evaluate' => array( 'auth', 'isAdmin' ),
                        'onFail' => array(
                            'setLoginForm' => '_Config/login_file.md',
                        ),
                    ),
                    array(
                        'onPathInfo' => array( '/' ),
                        'evaluate' => array( 'auth', 'isLogin' ),
                        'onSuccess' => array(
                            'addModuleAfter' => array( 'router', 'filer', 'filer', ),
                        ),
                    ),
                ),
            ),
            'Device' => array(
                'din'    => array( '\AmidaMVC\Module\Config', 'new' ),
                'inject' => array(),
                'config' => array(
                    array(
                        'onPathInfo' => array( '/' ),
                        'evaluate' => array( 'request', 'isRetina' ),
                        'onSuccess' => array(
                            'setImageType' => 'retina'
                        ),
                    ),
                    array(
                        'onPathInfo' => array( '/' ),
                        'evaluate' => array( 'request', 'isSmall' ),
                        'onSuccess' => array(
                            'setOption' => array( 'template_file', 'template.small.php' ),
                        ),
                    ),
                ),
            ),
        );
    }
    /**
     * @return array
     */
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
            // Modules
            'lang' => array(
                'din'    => array( '\AmidaMVC\Module\Lang',    'new' ),
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
                'din'    => array( '\AmidaMVC\Tools\AuthNot',  'new', 'authAdmin' ),
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

    /**
     * @param $di
     * @return \AmidaMVC\Framework\Services
     */
    function setUpDi( $di )
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