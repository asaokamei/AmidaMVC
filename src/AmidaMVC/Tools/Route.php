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
     * @param string $path   path of url
     * @param array $indexes
     * @return array|bool    return $loadInfo or FALSE if not found
     */
    function scan( $path, $indexes )
    {
        /** @var $loadClass \AmidaMVC\Tools\Load */
        $loadClass = $this->_loadClass;
        $loadInfo = array(
            'file' => FALSE,
        );
        // find a file to load. 
        // ex: file_name = /path/to/file_name.
        if( $file_name = $loadClass->findFile( $path ) ) {
            $loadInfo[ 'file' ] = $file_name;
            if( $loadClass->isDir( $file_name ) ) {
                $loadInfo[ 'is_dir' ] = TRUE;
                if( substr( $file_name, -1, 1 ) !== '/' ) {
                    $loadInfo[ 'reload' ] = $path . '/';
                }
                else {
                    $loadInfo = $this->index( $path, $indexes );
                }
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
            $file_name = $folder . '_App.php';
            if( $file_name = $loadClass->findFile( $file_name ) ) {
                $loadInfo[ 'file' ] = $file_name;
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
     * @param string $path     path to the directory.
     * @param array $indexes     possible index file names.
     * @return array|bool      found loadInfo.
     */
    function index( $path, $indexes )
    {
        /** @var $loadClass \AmidaMVC\Tools\Load */
        $loadClass = $this->_loadClass;
        $found = $loadClass->search( $path, $indexes );
        if( empty( $found ) ) return FALSE;
        $found_names = array();
        foreach( $found as $list ) {
            $found_names[ basename( $list ) ] = $list;
        }
        foreach( $indexes as $index ) {
            if( isset( $found_names[ $index ] ) ) {
                return array(
                    'file' => $found_names[ $index ],
                    'action' => NULL,
                    'foundBy' => 'index',
                );
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}