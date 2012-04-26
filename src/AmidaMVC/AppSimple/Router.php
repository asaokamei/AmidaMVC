<?php
namespace AmidaMVC\AppSimple;

class Router
{
    /**
     * @var \AmidaMVC\Tools\Route   a static class name for match and scan.
     */
    static $_route = '\AmidaMVC\Tools\Route';
    // +-------------------------------------------------------------+
    /**
     * initialize class.
     * @static
     * @param array $option   options to initialize.
     */
    static function _init( $option=array() ) {
        if( isset( $option[ 'routeClass' ] ) ) {
            static::$_route = $option[ 'routeClass' ];
        }
    }
    // +-------------------------------------------------------------+
    /**
     * default is to use route map first, then scan the file system
     * to determine which file to load.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_siteObj
     * @return array   $loadInfo for Loader.
     */
    static function actionDefault( $_ctrl, $_siteObj )
    {
        $route = static::$_route;
        $path  = $_ctrl->getPathInfo();
        $base  = $_ctrl->getBaseUrl();
        if( $loadInfo = $route::match( $path ) ) {
            // found by route map.
            $loadInfo[ 'foundBy' ] = 'route';
        }
        else if( $loadInfo = $route::scan( $base, $path ) ) {
            $loadInfo[ 'foundBy' ] = 'scan';
        }
        else {
            $_ctrl->setAction( '_pageNotFound' );
            $loadInfo = array();
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
}