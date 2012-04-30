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
    function _init( $option=array() ) {
        if( isset( $option[ 'routeClass' ] ) ) {
            static::$_route = $option[ 'routeClass' ];
        }
        if( isset( $option[ 'routes' ] ) ) {
            $route = static::$_route;
            $route::set( $option[ 'routes' ] );
        }
    }
    // +-------------------------------------------------------------+
    /**
     * default is to use route map first, then scan the file system
     * to determine which file to load.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @return array   $loadInfo for Loader.
     */
    function actionDefault( $_ctrl, $_pageObj )
    {
        $route = static::$_route;
        $path  = $_ctrl->getPathInfo();
        $root  = $_ctrl->getLocation();
        if( $loadInfo = $route::match( $path ) ) {
            // found by route map.
            $loadInfo[ 'foundBy' ] = 'route';
            return $loadInfo;
        }
        if( $loadInfo = $route::scan( $root, $path ) ) {
            $loadInfo[ 'foundBy' ] = 'scan';
            $loadInfo[ 'action'  ] = $_ctrl->defaultAct();
            return $loadInfo;
        }
        $_ctrl->setAction( '_pageNotFound' );
        $loadInfo = array();
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
}