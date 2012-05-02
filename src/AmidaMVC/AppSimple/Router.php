<?php
namespace AmidaMVC\AppSimple;

class Router implements \AmidaMVC\Framework\IModule
{
    /**
     * @var \AmidaMVC\Tools\Route   a static class name for match and scan.
     */
    static $_route = '\AmidaMVC\Tools\Route';
    /**
     * @var array   list of index files when accessing a directory.
     */
    private static $_indecies = array( 'index.md', 'index.html', 'index.php' );
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
     * @param array $option
     * @return array   $loadInfo for Loader.
     */
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
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
            if( $loadInfo[ 'reload' ] ) {
                $_ctrl->redirect( $loadInfo[ 'reload' ] );
                exit;
            }
            else if( $loadInfo[ 'is_dir' ] ) {
                if( $loadInfo = $route::index( $root, $loadInfo[ 'file' ], static::$_indecies ) ) {
                    $loadInfo[ 'foundBy' ] = 'index';
                    return $loadInfo;
                }
            }
            else {
                $loadInfo[ 'foundBy' ] = 'scan';
                $loadInfo[ 'action'  ] = $_ctrl->defaultAct();
                return $loadInfo;
            }
        }
        $_ctrl->setAction( '_pageNotFound' );
        $loadInfo = array();
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
}