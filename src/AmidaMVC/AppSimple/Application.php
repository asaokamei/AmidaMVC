<?php
namespace AmidaMVC\AppSimple;

class Application extends \AmidaMVC\Framework\Controller
{
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     * @return \AmidaMVC\AppSimple\Application
     */
    function __construct( $option=array() )
    {
        $moduleDefault = array(
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
        );
        $ctlDefault = array(
            'site_title' => 'AppSimple Web Site',
            'template_file' => NULL,
            'pageNotFound_file' => FALSE,
            'appDefault' => NULL,
        );
        $default = array(
            'site_title' => 'AppSimple Web Site',
            'template_file' => NULL,
            'pageNotFound_file' => FALSE,
            'appDefault' => NULL,
            'modules' => array(
                array( '\AmidaMVC\AppSimple\Router',  'router' ),
                array( '\AmidaMVC\AppSimple\Loader',  'loader' ),
                array( '\AmidaMVC\AppSimple\Emitter', 'emitter' ),
            ),
            '_init' => array(
                '_router'  => array(),
                '_loader'  => array(),
                '_emitter' => array(),
            ),
        );
        $option = array_merge( $default, $option );
        parent::__construct( $option );
        $this->separateCommands();
    }
    // +-------------------------------------------------------------+
}