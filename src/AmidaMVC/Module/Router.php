<?php
namespace AmidaMVC\Module;

class Router implements IfModule
{
    /**
     * @var \AmidaMVC\Tools\Route   a static class name for match and scan.
     */
    var $_routeClass = NULL;
    /**
     * @var array   list of index files when accessing a directory.
     */
    var $_indexes = array( 'index.md', 'index.html', 'index.php' );
    var $config = NULL;
    // +-------------------------------------------------------------+
    function __construct( $option=array() ) {
        $this->config = $option;
        if( isset( $option[ 'indexes' ] ) ) {
            if( is_array( $option[ 'indexes' ] ) ) {
                $this->_indexes = array_merge( $option[ 'indexes' ], $this->_indexes );
            }
            else {
                $this->_indexes[] = $option[ 'indexes' ];
            }
        }
    }
    /**
     * initialize class.
     * @param array $option   options to initialize.
     */
    function _init( $option=array() ) {
        if( !isset( $this->_routeClass ) ) {
            $di = \AmidaMVC\Framework\Container::start();
            $this->_routeClass = $di->get( '\AmidaMVC\Tools\Route', 'static' );
        }
        if( isset( $this->config[ 'routes' ] ) ) {
            call_user_func( array( $this->_routeClass, 'set' ), $this->config[ 'routes' ] );
        }
    }
    function injectRoute( $route ) {
        $this->_routeClass = $route;
    }
    // +-------------------------------------------------------------+
    /**
     * default is to use route map first, then scan the file system
     * to determine which file to load.
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return array   $loadInfo for Loader.
     */
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
    {
        $route = $this->_routeClass;
        $path  = $_ctrl->getPathInfo();
        if( $loadInfo = call_user_func( array( $this->_routeClass, 'match' ), $path ) ) {
            // found by route map.
            $loadInfo[ 'foundBy' ] = 'route';
        }
        else if( $loadInfo = $route->scan( $path ) ) {
            // found (something) by scan.
            if( isset( $loadInfo[ 'reload' ] ) ) {
                // reload if it is a directory without trailing slash.
                $_ctrl->redirect( $loadInfo[ 'reload' ] );
            }
            else if( isset( $loadInfo[ 'is_dir' ] ) ) {
                if( $loadInfo = $route->index( $loadInfo[ 'file' ], $this->_indexes ) ) {
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
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return array
     */
    function action_LoginForm( $_ctrl, &$_pageObj, $option=array() )
    {
        $loadInfo = array();
        if( isset( $_ctrl->options[ 'loginForm_file' ] ) ) {
            $loadInfo = array(
                'file' => $_ctrl->findFile( $_ctrl->options[ 'loginForm_file' ] ),
                'action' => 'default',
            );
            $_ctrl->setAction( $_ctrl->defaultAct() );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
}