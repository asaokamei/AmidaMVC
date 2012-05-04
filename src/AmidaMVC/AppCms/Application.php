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
                array( '\AmidaMVC\AppSimple\Auth',    'auth' ),
                array( '\AmidaMVC\AppSimple\Router',  'router' ),
                array( '\AmidaMVC\AppSimple\Loader',  'loader' ),
                array( '\AmidaMVC\AppSimple\Emitter', 'emitter' ),
            ),
            '_auth' => array(
                '_admin' => array(
                    'login' => '_Config/auth.admin.php',
                    'onFail' => array(
                        '_denyAccess' => array(
                            '/admin/', '/adminTest/',
                        ),
                    ),
                    'onSuccess' => array(),
                ),
                '_dev' => array(
                    'login' => '_Config/auth.dev.php',
                    'onFail' => array(),
                    'onSuccess' => array(
                        '_loadModules' => array(
                            '\AmidaMVC\AppSimple\Filer' => 'fFiles',
                        )
                    ),
                ),
                'login_file' => '_Config/login_file.php',
            ),
            '_fFiles' => array(
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