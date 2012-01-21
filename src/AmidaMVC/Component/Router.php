<?php
namespace AmidaMVC\Component;

/**
 * Router class
 * from Perfect PHP.
 */

class Router
{
    // +-------------------------------------------------------------+
    /**
     * @var array   contains pattern to parameter array.
     */
    private static $_routes = array();
    // +-------------------------------------------------------------+
    static function set( $routes ) {
        self::$_routes = self::compile( $routes );
    }
    // +-------------------------------------------------------------+
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
    static function actionDefault( $ctrl, &$data ) {
        \AmidaMVC\Component\Debug::bug( 'wordy', $ctrl->path, 'ctrl path' );
        $loadInfo = self::match( $ctrl->path );
        if( !$loadInfo ) {
            $data .= 'Router::default route not found';
        }
        else {
            $data .= 'Router::default found route: '. $loadInfo['file'];
            $ctrl->nextModel( 'Load' );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
}