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
            'template_file' => NULL,
            'pageNotFound_file' => FALSE,
            'appDefault' => FALSE,
            'modules' => array(
                array( '\AmidaMVC\AppSimple\Config',  'config' ),
                array( '\AmidaMVC\AppSimple\Auth',    'authAdmin' ),
                array( '\AmidaMVC\AppSimple\Auth',    'authDev' ),
                array( '\AmidaMVC\AppSimple\Router',  'router' ),
                array( '\AmidaMVC\AppSimple\Loader',  'loader' ),
                array( '\AmidaMVC\AppSimple\Emitter', 'emitter' ),
            ),
            '_authAdmin' => array(
                'authClass' => '\AmidaMVC\Tools\AuthBasic',
                'password_file' => '_Config/admin.password',
                'login_file' => '_Config/login_file.php',
                'evaluateOn' => array(
                    array(
                        'onPathInfo' => array( '/admin', '/admin2' ),
                        'onFail' => array(
                            'setLoginForm' => 'login for admin pages',
                        ),
                        'onSuccess' => array(),
                    ),
                ),
            ),
            '_authDev' => array(
                'authArea' => 'authDev',
                'authClass' => '\AmidaMVC\Tools\AuthBasic',
                'password_file' => '_Config/.dev.password',
                'login_file' => '_Config/login_file.php',
                'evaluateOn' => array(
                    array(
                        'onPathInfo' => array( '/dev_login' ),
                        'onFail' => array(
                            '_setLoginForm' => 'login for develop mode',
                        ),
                        'onSuccess' => array(
                            'addModuleAfter' => array(
                                'router', '\AmidaMVC\AppSimple\Filer', 'filer',
                            ),
                        ),
                    ),
                    array(
                        'onPathInfo' => array( '/' ),
                        'onFail' => array(),
                        'onSuccess' => array(
                            'addModuleAfter' => array(
                                'router', '\AmidaMVC\AppSimple\Filer', 'filer',
                            ),
                        ),
                    ),
                ),
            ),
            '_filer' => array(
                'template_file' => NULL,
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