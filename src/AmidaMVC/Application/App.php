<?php
namespace AmidaMVC\Application;

class App
{
    /** @var \AmidaMVC\Framework\Services */
    static $dic;
    /** @var array   configuration for DiContainer */
    static $dicConfig = array();

    // +-------------------------------------------------------------+
    /**
     * @return \AmidaMVC\Framework\Controller
     */
    static function app()
    {
        $sl = static::_setupDiC();
        /** @var $ctrl \AmidaMVC\Framework\Controller */
        $ctrl = $sl->get( 'controller' );

        $modules = array(
            'config',
            'router', 'loader', 'menus', 'emitter',
        );
        $ctrl->setModules( $modules );
        $ctrl->separateCommands();

        return $ctrl;
    }

    /**
     * @static
     * @return \AmidaMVC\Framework\Controller
     */
    static function std()
    {
        static::config( 'config', array(
            'onPathInfo' => '/',
            'evaluate'   => function( $config ) {
                $config->_ctrl->addModule( 'lang', 'lang' );
            },
        ) );
        return static::app();
    }
    /**
     * @static
     * @return \AmidaMVC\Framework\Controller
     */
    static function cms()
    {
        static::config( 'config', array(
            'onPathInfo' => '/',
            'evaluate'   => function( $config ) {
                $config->_ctrl->addModule( 'lang', 'lang' );
                $config->_ctrl->addModule( 'template', 'template' );
                $config->_ctrl->addModule( 'configDev', 'configDev' );
            },
        ) );
        return static::app();
    }

    // +-------------------------------------------------------------+
    /**
     * @static
     * @param string $service
     * @param array $config
     * @param bool $replace
     */
    static function config( $service, $config, $replace=FALSE )
    {
        static::defaultDicConfig();
        if( isset( static::$dicConfig[ $service ] ) )
        {
            if( $replace ) {
                static::$dicConfig[ $service ][ 'config' ] = $config;
            }
            else {
                static::$dicConfig[ $service ][ 'config' ] = array_merge(
                    static::$dicConfig[ $service ][ 'config' ], $config
                );
            }
        }
    }

    /**
     * @static
     * @param string $service
     * @param string $name
     * @param null|string $name2
     */
    static function inject( $service, $name, $name2=NULL )
    {
        static::defaultDicConfig();
        $name2 = ( isset( $name2 ) ) ? $name2: $name;
        if( isset( static::$dicConfig[ $service ] ) )
        {
            if( !empty( static::$dicConfig[ $service ][ 'inject' ] ) )
            foreach( static::$dicConfig[ $service ][ 'inject' ] as $key => $inject ) {
                if( $inject[0] == $name ) {
                    static::$dicConfig[ $service ][ 'inject' ][ $key ][1] = $name2;
                    return;
                }
            }
            static::$dicConfig[ $service ][ 'inject' ][] = array( $name, $name2 );
        }
    }

    static function service( $service, $din, $config=array(), $inject=array() )
    {
        static::defaultDicConfig();
        if( !isset( static::$dicConfig[ $service ] ) ) {
            static::$dicConfig[ $service ] = array();
        }
        static::$dicConfig[ $service ][ 'din'    ] = $din;
        static::$dicConfig[ $service ][ 'config' ] = $config;
        static::$dicConfig[ $service ][ 'inject' ] = $inject;
    }
    // +-------------------------------------------------------------+
    /**
     * @return \AmidaMVC\Framework\Services
     */
    static function _setupDiC()
    {
        static::defaultDicConfig();
        static::$dic = \AmidaMVC\Framework\Services::start();
        foreach( static::$dicConfig as $service => $setup ) {
            $din = array_merge( array( $service ), $setup[ 'din' ] );
            call_user_func_array( array( static::$dic, 'setService' ), $din );
            if( isset( $setup[ 'config' ] ) ) {
                call_user_func( array( static::$dic, 'setConfig' ), $setup[ 'config' ] );
            }
            if( isset( $setup[ 'inject' ] ) && is_array( $setup[ 'inject' ] ) ) {
                foreach( $setup[ 'inject' ] as $inject ) {
                    call_user_func_array( array( static::$dic, 'setInject' ), $inject );
                }
            }
        }
    }
    /** set up configuration for DiContainer
     * @static
     */
    static function defaultDicConfig()
    {
        // setup with default DIC only once.
        if( !empty( static::$dicConfig ) ) return;

        // Tools
        static::$dicConfig[ 'load' ] = array(
            'din' => array( '\AmidaMVC\Tools\Load', 'get' ),
            'config' => array(
                realpath( __DIR__ . '/../../www/' )
            ),
        );
        static::$dicConfig[ 'route' ] = array(
            'din' => array( '\AmidaMVC\Tools\Route', 'get' ),
            'config' => array(),
            'inject' => array(
                array( 'load', 'load' ),
            )
        );
        static::$dicConfig[ 'request' ] = array(
            'din' => array( '\AmidaMVC\Tools\Request', 'get' ),
        );
        static::$dicConfig[ 'session' ] = array(
            'din' => array( '\AmidaMVC\Tools\Session', 'get' ),
        );
        static::$dicConfig[ 'i18n' ] = array(
            'din' => array( '\AmidaMVC\Tools\i18n', 'get' ),
            'config' => array(
                'language' => 'en',
                'directory' => '_Config',
                'file_amidaMVC' => 'amidaMVC',
                'file_application' => 'application',
            ),
            'inject' => array(
                array( 'load', 'load' ),
            ),
        );
        static::$dicConfig[ 'navBar' ] = array(
            'din' => array( '\AmidaMVC\Tools\NavBar', 'get' ),
        );
        static::$dicConfig[ 'section' ] = array(
            'din' => array( '\AmidaMVC\Tools\Section', 'get' ),
        );
        // Modules
        static::$dicConfig[ 'config' ] = array(
            'din' => array( '\AmidaMVC\Module\Config', 'new' ),
            'config' => array(
                'onPathInfo' => '/',
                'evaluate'   => function( $config ) {
                    $config->_ctrl->addModule( 'lang', 'lang' );
                    $config->_ctrl->addModule( 'template', 'template' );
                    $config->_ctrl->addModule( 'configDev', 'configDev' );
                },
            ),
        );
        static::$dicConfig[ 'lang' ] = array(
            'din' => array( '\AmidaMVC\Module\Lang', 'new' ),
            'config' => array(),
            'inject' => array(
                array( 'i18n', 'i18n' ),
                array( 'session', 'session' )
            ),
        );
        static::$dicConfig[ 'template' ] = array(
            'din' => array( '\AmidaMVC\Module\Template', 'new' ),
            'config' => array(),
            'inject' => array(
                array( 'i18n', 'i18n' ),
                array( 'session', 'session' )
            ),
        );
        static::$dicConfig[ 'configDev' ] = array(
            'din' => array( '\AmidaMVC\Module\Config', 'new' ),
            'config' => array(
                'onPathInfo' => '/common/',
                'evaluate'   => function( $config ) {
                    $config->_ctrl->addModule( 'authDev', 'authDev' );
                    $config->_ctrl->addModule( 'template', 'template' );
                },
            ),
        );
        static::$dicConfig[ 'router' ] = array(
            'din' => array( '\AmidaMVC\Module\Router', 'new' ),
            'config' => array(),
            'inject' => array(
                array( 'route', 'route' ),
            )
        );
        static::$dicConfig[ 'loader' ] = array(
            'din' => array( '\AmidaMVC\Module\Loader', 'new' ),
            'inject' => array(
                array( 'load', 'load' ),
            )
        );
        static::$dicConfig[ 'emitter' ] = array(
            'din' => array( '\AmidaMVC\Module\Emitter', 'new' )
        );
        static::$dicConfig[ 'menus' ] = array(
            'din' => array( '\AmidaMVC\Module\Menus', 'new' ),
            'inject' => array(
                array( 'nav', 'navBar' ),
                array( 'i18n', 'i18n' ),
            ),
        );

        // Page Object
        static::$dicConfig[ 'pageObj' ] = array(
            'din' => array( '\AmidaMVC\Framework\PageObj', 'new' ),
            'config' => array(),
            'inject' => array(
                array( 'section', 'section' ),
            ),
        );
        // Controller
        static::$dicConfig[ 'controller' ] = array(
            'din' => array( '\AmidaMVC\Framework\Controller', 'get' ),
            'config' => array(
                'site_title' => 'AppCMS Web Site',
                'template_file' => '_Config/template.base.php',
                'pageNotFound_file' => '_Config/pageNotFound.md',
                'template_dev_file' => '_Config/template._dev.php',
            ),
            'inject' => array(
                array( 'request', 'request' ),
                array( 'load', 'load' ),
                array( 'i18n', 'i18n' ),
                array( 'diContainer', '_self' ),
            ),
        );
        // for _Dev mode
        static::$dicConfig[ 'auth' ] = array(
            'din' => array( '\AmidaMVC\Tools\AuthBasic', 'get' ),
            'inject' => array(
                array( 'load', 'load' ),
            ),
            'config' => array(
                'password_file' => '_Config/.password',
            ),
        );
        static::$dicConfig[ 'AuthDev' ] = array(
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
        );
        static::$dicConfig[ 'filer' ] = array(
            'din' => array( '\AmidaMVC\Module\Filer', 'new' ),
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
        );
    }
}