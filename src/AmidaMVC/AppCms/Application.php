<?php
namespace AmidaMVC\AppCms;

class Application extends \AmidaMVC\Framework\Controller
{
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     * @return \AmidaMVC\AppCms\Application
     */
    function __construct( $option=array() )
    {
        $moduleDefault = array(
            //array( 'authAdminOnly',    'authAdminOnly' ),
            array( 'authDevLogin',   'authDevLogin' ),
            array( 'authDevLogout',  'authDevLogout' ),
            array( 'authDevMode',    'authDevMode' ),
            array( 'router',         'router' ),
            array( 'loader',         'loader' ),
            array( 'emitter',        'emitter' ),
        );
        $diDefault = array(
            array( 'router',    '\AmidaMVC\AppSimple\Router', 'new',
                array( 'routes' => array() )
            ),
            array( 'loader',    '\AmidaMVC\AppSimple\Loader', 'new', array() ),
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
            array( 'authAdminOnly', '\AmidaMVC\AppCms\Auth', 'new',
                array(
                    'loginForm_file' => 'login_file.md',
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
            array( 'filer', '\AmidaMVC\AppSimple\Router', 'new',
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
        $ctlDefault = array(
            'site_title' => 'AppCMS Web Site',
            'template_file' => 'template.php',
            'pageNotFound_file' => FALSE,
            'appDefault' => NULL,
        );
        $default = array(
            'site_title' => 'AppCMS Web Site',
            'template_file' => 'template.php',
            'pageNotFound_file' => FALSE,
            'appDefault' => NULL,
            'modules' => array(
//                array( '\AmidaMVC\AppSimple\Config',  'config' ),
                array( 'Config',  'config' ),
//                array( '\AmidaMVC\AppCms\Auth',    'authAdminOnly' ),
                array( '\AmidaMVC\AppCms\Auth',    'authDevLogin' ),
                array( '\AmidaMVC\AppCms\Auth',    'authDevLogout' ),
                array( '\AmidaMVC\AppCms\Auth',    'authDevMode' ),
                array( '\AmidaMVC\AppSimple\Router',  'router' ),
                array( '\AmidaMVC\AppSimple\Loader',  'loader' ),
                array( '\AmidaMVC\AppSimple\Emitter', 'emitter' ),
            ),
            '_diContainer' => array(
            ),
            '_init' => array(
                'authAdminOnly' => array(),
                'authDevLogin' => array(
                    'authArea' => 'authDev',
                    'authClass' => '\AmidaMVC\Tools\AuthNot',
                    'password_file' => '.dev.password',
                    'loginForm_file' => 'login_file.md',
                    'evaluateOn' => array(
                        'onPathInfo' => array( '/dev_login' ),
                        'onFail' => array(
                            'setLoginForm' => 'login for develop mode',
                        ),
                        'onSuccess' => array(
                            'redirect' => '/',
                        ),
                    ),
                ),
                'authDevLogout' => array(
                    'authArea' => 'authDev',
                    'authClass' => '\AmidaMVC\Tools\AuthNot',
                    'password_file' => '.dev.password',
                    'loginForm_file' => 'login_file.md',
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
                'authDevMode' => array(
                    'authArea' => 'authDev',
                    'authClass' => '\AmidaMVC\Tools\AuthNot',
                    'password_file' => '.dev.password',
                    'evaluateOn' => array(
                        'onPathInfo' => array( '/' ),
                        'onFail' => array(),
                        'onSuccess' => array(
                            'addModuleAfter' => array(
                                'router', '\AmidaMVC\AppCms\Filer', 'filer',
                            ),
                        ),
                    ),
                ),
                'filer' => array(
                    'template_file' => NULL,
                    'listJs' => array(
                        '../bootstrap/js/jquery-1.7.1.js',
                        '../bootstrap/js/bootstrap.js',
                        '../bootstrap/js/bootstrap-modal.js',
                    ),
                    'listCss' => array(
                        '../bootstrap/css/bootstrap.css',
                    ),
                ),
                'router'  => array(),
                'loader'  => array(),
                'emitter' => array(),
            ),
        );
        $option = array_merge( $default, $option );
        parent::__construct( $option );
        $this->separateCommands();
    }
    // +-------------------------------------------------------------+
}