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
     * @param $path        path to match.
     * @return array|bool  returns matched result, or FALSE if not found.
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
}