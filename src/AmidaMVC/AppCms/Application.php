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
        $default = array(
            'site_title' => 'AppCMS Web Site',
            'template_file' => 'template.php',
            'pageNotFound_file' => FALSE,
            'appDefault' => FALSE,
            'modules' => array(
//                array( '\AmidaMVC\AppSimple\Config',  'config' ),
                array( 'Config',  'config' ),
                array( '\AmidaMVC\AppCms\Auth',    'authAdmin' ),
                array( '\AmidaMVC\AppCms\Auth',    'authDevLogin' ),
                array( '\AmidaMVC\AppCms\Auth',    'authDevLogout' ),
                array( '\AmidaMVC\AppCms\Auth',    'authDevMode' ),
                array( '\AmidaMVC\AppSimple\Router',  'router' ),
                array( '\AmidaMVC\AppSimple\Loader',  'loader' ),
                array( '\AmidaMVC\AppSimple\Emitter', 'emitter' ),
            ),
            '_authAdmin' => array(
                'authArea' => 'authAdmin',
                'authClass' => '\AmidaMVC\Tools\AuthNot',
                'password_file' => 'admin.password',
                'loginForm_file' => 'login_file.md',
                'evaluateOn' => array(
                    'onPathInfo' => array( '/admin', '/admin2' ),
                    'onFail' => array(
                        'setLoginForm' => 'login for admin pages',
                    ),
                    'onSuccess' => array(),
                ),
            ),
            '_authDevLogin' => array(
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
            '_authDevLogout' => array(
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
            '_authDevMode' => array(
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
            '_filer' => array(
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
            '_router'  => array(),
            '_loader'  => array(),
            '_emitter' => array(),
        );
        $option = array_merge( $default, $option );
        parent::__construct( $option );
        $this->separateCommands();
    }
    // +-------------------------------------------------------------+
}