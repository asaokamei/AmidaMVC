<?php
namespace AmidaMVC\AppSimple;

class Router implements \AmidaMVC\Framework\IModule
{
    /**
     * @var \AmidaMVC\Tools\Route   a static class name for match and scan.
     */
    var $_routeClass = '\AmidaMVC\Tools\Route';
    /**
     * @var array   list of index files when accessing a directory.
     */
    var $_indexes = array( 'index.md', 'index.html', 'index.php' );
    // +-------------------------------------------------------------+
    /**
     * initialize class.
     * @param array $option   options to initialize.
     */
    function _init( $option=array() ) {
        if( isset( $option[ 'routeClass' ] ) ) {
            $this->_routeClass = $option[ 'routeClass' ];
        }
        if( isset( $option[ 'routes' ] ) ) {
            $route = $this->_routeClass;
            $route::set( $option[ 'routes' ] );
        }
        if( isset( $option[ 'indexes' ] ) ) {
            if( is_array( $option[ 'indexes' ] ) ) {
                $this->_indexes = array_merge( $option[ 'indexes' ], $this->_indexes );
            }
            else {
                $this->_indexes[] = $option[ 'indexes' ];
            }
        }
    }
    // +-------------------------------------------------------------+
    /**
     * default is to use route map first, then scan the file system
     * to determine which file to load.
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return array   $loadInfo for Loader.
     */
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
    {
        $route = $this->_routeClass;
        $path  = $_ctrl->getPathInfo();
        $root  = $_ctrl->getLocation();
        if( $loadInfo = $route::match( $path ) ) {
            // found by route map.
            $loadInfo[ 'foundBy' ] = 'route';
        }
        else if( $loadInfo = $route::scan( $root, $path ) ) {
            // found (something) by scan.
            if( isset( $loadInfo[ 'reload' ] ) ) {
                // reload if it is a directory without trailing slash.
                $_ctrl->redirect( $loadInfo[ 'reload' ] );
            }
            else if( isset( $loadInfo[ 'is_dir' ] ) ) {
                if( $loadInfo = $route::index( $root, $loadInfo[ 'file' ], $this->_indexes ) ) {
                    // found an index file in the directory.
                    $loadInfo[ 'foundBy' ] = 'index';
                }
            }
            else {
                // normal file.
                $loadInfo[ 'foundBy' ] = 'scan';
            }
        }
        if( empty( $loadInfo ) ) {
            $_ctrl->setAction( '_pageNotFound' );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
}