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
        return urldecode( $_SERVER[ 'REQUEST_URI' ] );
    }
    // +-------------------------------------------------------------+
    static function getScriptName() {
        return urldecode( $_SERVER[ 'SCRIPT_NAME' ] );
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
    /**
     * This function is to replace PHP's extremely buggy realpath().
     * from http://stackoverflow.com/questions/4049856/replace-phps-realpath
     * @param string $path   The original path, can be relative etc.
     * @return string        The resolved path, it might not exist.
     */
    static function truePath($path)
    {
        // whether $path is unix or not
        $unipath=strlen($path)==0 || $path{0}!='/';
        /** @var $lastSlash string */
        $lastSlash = ( substr($path, -1, 1 ) === '/' ) ? '/' : '';
        // attempts to detect if path is relative in which case, add cwd
        if(strpos($path,':')===false && $unipath)
            $path=getcwd().DIRECTORY_SEPARATOR.$path;
        // resolve path parts (single dot, double dot and double delimiters)
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.'  == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $path=implode(DIRECTORY_SEPARATOR, $absolutes);
        // resolve any symlinks
        //if(file_exists($path) && linkinfo($path)>0)$path=readlink($path);
        // put initial separator that could have been lost
        $path=!$unipath ? '/'.$path : $path;
        return $path . $lastSlash;
    }
    // +-------------------------------------------------------------+
}
