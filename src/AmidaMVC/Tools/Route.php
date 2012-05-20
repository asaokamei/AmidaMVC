<?php
namespace AmidaMVC\Tools;

/**
 * Router class
 * from Perfect PHP.
 */

class Route
{
    /**
     * @var array   contains pattern to parameter array.
     */
    private $_routes = array();
    private $_loadClass = NULL;
    // +-------------------------------------------------------------+
    function __construct() {

    }
    function injectLoad( $load ) {
        $this->_loadClass = $load;
    }
    function _init( $option=NULL ) {
        if( !isset( $this->_loadClass ) ) {
            $di = \AmidaMVC\Framework\Container::start();
            $this->_routeClass = $di->get( '\AmidaMVC\Tools\Load' );
        }
    }
    // +-------------------------------------------------------------+
    /**
     * sets route patterns to match.
     * @static
     * @param $routes
     */
    function set( $routes ) {
        $this->_routes = self::compile( $routes );
    }
    // +-------------------------------------------------------------+
    /**
     * prepare routes for preg_match method.
     * @static
     * @param $routes
     * @return array
     */
    function compile( $routes ) {
        $_routes = array();
        foreach( $routes as $url => $match ) {
            $tokens = explode( '/', ltrim( $url, '/' ) );
            foreach( $tokens as $i => $token ) {
                if( strpos( $token, ':' ) === 0 ) {
                    $name  = substr( $token, 1 );
                    $token = "(?P<{$name}>[^/]+)";
                }
                $tokens[$i] = $token;
            }
            $pattern = '/' . implode( '/', $tokens );
            $_routes[ $pattern ] = $match;
        }
        return $_routes;
    }
    // +-------------------------------------------------------------+
    /**
     * matches $path against route patterns.
     * @static
     * @param string $path    path to match.
     * @return array|bool     returns matched result, or FALSE if not found.
     */
    function match( $path ) {
        if( substr( $path, 0, 1 ) !== '/' ) {
            $path = '/' . $path;
        }
        foreach( $this->_routes as $pattern => $match ) {
            if( preg_match( "#^{$pattern}$#", $path, $matches ) ) {
                $match = array_merge( $match, $matches );
                return $match;
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * search file system for path info.
     * @static
     * @param string $root   root of the web document
     * @param string $path   path of url
     * @return array|bool    return $loadInfo or FALSE if not found
     */
    function scan( $root, $path )
    {
        $di = \AmidaMVC\Framework\Container::start();
        /** @var $loadClass \AmidaMVC\Tools\Load */
        $loadClass = $di->get( '\AmidaMVC\Tools\Load' );
        $loadInfo = array(
            'file' => FALSE,
        );
        // find a file to load. 
        // ex: file_name = /path/to/file_name.
        $file_name = $root . '/' . $path;
        if( $loadClass->exists( $file_name ) ) {
            $loadInfo[ 'file' ] = $path;
            if( $loadClass->isDir( $file_name ) ) {
                if( substr( $file_name, -1, 1 ) !== '/' ) {
                    $loadInfo[ 'reload' ] = $path . '/';
                }
                $loadInfo[ 'is_dir' ] = TRUE;
            }
            return $loadInfo;
        }
        // find an app to load.
        // ex: path_info = path/to/_App.php/action.
        $routes = explode( '/', $path );
        if( empty( $routes ) ) {
            $routes = array( '' );
        }
        $folder = '';
        $found  = FALSE;
        foreach( $routes as $loc ) {
            if( $found ) {
                // found an _App to load. next loc is the action.
                $loadInfo[ 'action' ] = $loc;
                return $loadInfo;
            }
            $folder .= $loc . '/';
            $file_name = $root . '/' . $folder . '_App.php';
            if( $loadClass->exists( $file_name ) ) {
                $loadInfo[ 'file' ] = $folder . '_App.php';
                $found = TRUE;
            }
        }
        if( $found ) {
            return $loadInfo;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * search for an index file in the given folder.
     * @static
     * @param string $root     root of the folder.
     * @param string $path     path to the directory.
     * @param array $files     possible index file names.
     * @return array|bool      found loadInfo.
     */
    function index( $root, $path, $files )
    {
        $di = \AmidaMVC\Framework\Container::start();
        /** @var $loadClass \AmidaMVC\Tools\Load */
        $loadClass = $di->get( '\AmidaMVC\Tools\Load' );
        $found = $loadClass->search( $root . '/' . $path, $files );
        if( empty( $found ) ) return FALSE;
        $found_names = array();
        foreach( $found as $list ) {
            $found_names[] = basename( $list );
        }
        foreach( $files as $index ) {
            if( in_array( $index, $found_names ) ) {
                return array(
                    'file' => $path . $index,
                    'action' => NULL,
                );
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}