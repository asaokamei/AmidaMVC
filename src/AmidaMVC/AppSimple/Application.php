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
        $default = array(
            'site_title' => 'AppSimple Web Site',
            'template_file' => __DIR__ . '/template.php',
            'components' => array(
                array( '\AmidaMVC\AppSimple\Router',  'router' ),
                array( '\AmidaMVC\AppSimple\Loader',  'loader' ),
                array( '\AmidaMVC\AppSimple\Emitter', 'emitter' ),
            ),
        );
        $option = array_merge( $default, $option );
        parent::__construct( $option );
    }
    // +-------------------------------------------------------------+
}