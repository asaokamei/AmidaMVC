<?php
namespace AmidaMVC\AppSimple;

class Controller extends \AmidaMVC\Framework\Controller
{
    /**
     * @var \AmidaMVC\Component\ResponseObj
     */
    var $siteObj;
    // +-------------------------------------------------------------+
    /**
     * @param array $option   options to set
     */
    function __construct( $option=array() )
    {
        $default = array(
            'site_title' => 'AmidaMVC/Simple Web Site',
            'template_file' => __DIR__ . '/template.php',
            'components' => array(
                array( '\AmidaMVC\AppSimple\Router', 'router' ),
                array( '\AmidaMVC\AppSimple\Loader', 'loader' ),
                array( '\AmidaMVC\AppSimple\Render', 'render' ),
            ),
        );
        $option = array_merge( $default, $option );
        parent::__construct( $option );
        $this->addComponent( $option[ 'components' ] );
    }
    // +-------------------------------------------------------------+
    function serve( $siteObj=NULL ) {
        if( !isset( $siteObj ) ) {
            $siteObj = new \AmidaMVC\Component\ResponseObj();
        }
        $this->siteObj = $siteObj;
        $return = $this->start( $this->siteObj );
        return $return;
    }
    // +-------------------------------------------------------------+
    /**
     * @return \AmidaMVC\Component\ResponseObj
     */
    function getSiteObj() {
        return $this->siteObj;
    }
    // +-------------------------------------------------------------+
}