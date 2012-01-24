<?php
namespace AmidaMVC\Tools;

/**
 * Request class
 * from Perfect PHP.
 */
class Request
{
    // +-------------------------------------------------------------+
    /**
     * check if request method is POST.
     * @static
     * @return bool
     */
    static function isPost() {
        return ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) ? TRUE: FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * get host name of server.
     * @static
     * @return bool
     */
    static function getHost() {
        if( !empty( $_SERVER[ 'HTTP_HOST' ] ) ) {
            return $_SERVER[ 'HTTP_HOST' ];
        }
        return $_SERVER[ 'SERVER_NAME' ];
    }
    // +-------------------------------------------------------------+
    static function getRequestUri() {
        return $_SERVER[ 'REQUEST_URI' ];
    }
    // +-------------------------------------------------------------+
    static function getScriptName() {
        return $_SERVER[ 'SCRIPT_NAME' ];
    }
    // +-------------------------------------------------------------+
    /**
     * get base url of this application.
     * @static
     * @return string
     */
    static function getBaseUrl() {
        $script_name = self::getScriptName();
        $request_uri = self::getRequestUri();
        if( strpos( $request_uri, $script_name ) === 0 ) {
            return $script_name;
        }
        else if( strpos( $request_uri, dirname( $script_name ) ) === 0 ) {
            return rtrim( dirname( $script_name ), '/' );
        }
        return '';
    }
    // +-------------------------------------------------------------+
    /**
     * get path info, url from the base url to the end.
     * @static
     * @return string
     */
    static function getPathInfo() {
        $base_url    = self::getBaseUrl();
        $request_uri = self::getRequestUri();
        if( ( $pos = strpos( $request_uri, '?' ) ) !== FALSE ) {
            $request_uri = substr( $request_uri, 0, $pos );
        }
        $path_info = (string) substr( $request_uri, strlen( $base_url ) );
        return $path_info;
    }
    // +-------------------------------------------------------------+
}
