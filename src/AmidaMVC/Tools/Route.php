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
    private static $_routes = array();
    // +-------------------------------------------------------------+
    /**
     * sets route patterns to match.
     * @static
     * @param $routes
     */
    static function set( $routes ) {
        self::$_routes = self::compile( $routes );
    }
    // +-------------------------------------------------------------+
    /**
     * prepare routes for preg_match method.
     * @static
     * @param $routes
     * @return array
     */
    static function compile( $routes ) {
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
    static function match( $path ) {
        if( substr( $path, 0, 1 ) !== '/' ) {
            $path = '/' . $path;
        }
        foreach( self::$_routes as $pattern => $match ) {
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
    static function scan( $root, $path )
    {
        $loadInfo = array(
            'file' => FALSE,
        );
        // find a file to load. 
        // ex: file_name = /path/to/file_name.
        $file_name = $root . '/' . $path;
        if( file_exists( $file_name ) ) {
            $loadInfo[ 'file' ] = $path;
            if( is_dir( $file_name ) ) {
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
            if( file_exists( $file_name ) ) {
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
     * @static
     * @param string $root
     * @param string $path
     * @param array $files
     * @return array|bool
     */
    static function index( $root, $path, $files ) {
        $lists = '{' . implode( ',', $files ) . '}';
        $pattern = $root . '/' . $path . $lists;
        $found = glob( $pattern, GLOB_BRACE );
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