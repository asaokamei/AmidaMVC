<?php
namespace AmidaMVC\AppSimple;

class Controller extends \AmidaMVC\Framework\Controller
{
    var $siteObj;
    // +-------------------------------------------------------------+
    function __construct( $option=array() ) {
        $default = array(
        );
        $option = array_merge( $default, $option );
        parent::__construct( $option );
        $this
            ->addComponent( '\AmidaMVC\AppSimple\Router', 'router' )
            ->addComponent( '\AmidaMVC\AppSimple\Loader', 'loader' )
            ->addComponent( '\AmidaMVC\AppSimple\Render', 'render' )
        ;
        $this->siteObj = $this->newSiteObj();
        return $this->start( $this->siteObj );
    }
    // +-------------------------------------------------------------+
    function newSiteObj() {
        return new \AmidaMVC\Component\ResponseObj();
    }
    // +-------------------------------------------------------------+
    function getSiteObj() {
        return $this->siteObj;
    }
    // +-------------------------------------------------------------+
}