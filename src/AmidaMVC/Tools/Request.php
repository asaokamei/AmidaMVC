<?php
namespace AmidaMVC\Tools;

/**
 * Request class
 * from Perfect PHP.
 */
class Request
{
    /**
     * @var array   holds server info.
     */
    protected $_server = array();
    /**
     * @var string|bool   path info
     */
    protected $path_info = NULL;
    /**
     * @var string\null   base URL
     */
    protected $base_url = NULL;
    // +-------------------------------------------------------------+
    /**
     * @param array $config    alternative to $_SERVER info.
     */
    function __construct( $config=array() ) {
        if( !empty( $config ) ) {
            $this->_server = $config;
        }
        else {
            $this->_server = & $_SERVER;
        }
    }
    /**
     * check if request method is POST.
     * @static
     * @return bool
     */
    function isPost() {
        return ( $this->_server[ 'REQUEST_METHOD' ] === 'POST' ) ? TRUE: FALSE;
    }
    /**
     * get host name of server.
     * @static
     * @return bool
     */
    function getHost() {
        if( !empty( $this->_server[ 'HTTP_HOST' ] ) ) {
            return $this->_server[ 'HTTP_HOST' ];
        }
        return $this->_server[ 'SERVER_NAME' ];
    }
    /**
     * @static
     * @return string
     */
    function getRequestUri() {
        return urldecode( $this->_server[ 'REQUEST_URI' ] );
    }
    /**
     * @return string
     */
    function getScriptName() {
        return urldecode( $this->_server[ 'SCRIPT_NAME' ] );
    }
    // +-------------------------------------------------------------+
    /**
     * @param null|string $url
     * @return string
     */
    function getBaseUrl( $url=NULL ) {
        if( !isset( $this->base_url ) ) {
            $this->base_url = $this->calBaseUrl();
        }
        if( $url && substr( $url, 0, 1 ) !== '/' ) {
            $url = '/' . $url;
        }
        $base = "{$this->base_url}{$url}";
        $base = $this->truePath( $base );
        return $base;
    }
    /**
     * @param string $url
     */
    function setBaseUrl( $url ) {
        $this->base_url = $url;
    }
    /**
     * get base url of this application.
     * @return string
     */
    function calBaseUrl() {
        $script_name = $this->getScriptName();
        $request_uri = $this->getRequestUri();
        $baseUrl = '';
        if( strpos( $request_uri, $script_name ) === 0 ) {
            $baseUrl = $script_name;
        }
        else if( strpos( $request_uri, dirname( $script_name ) ) === 0 ) {
            $baseUrl = rtrim( dirname( $script_name ), '/' );
        }
        if( substr( $baseUrl, -1 ) !== '/' ) {
            $baseUrl .= '/';
        }
        return $baseUrl;
    }
    // +-------------------------------------------------------------+
    /**
     * @return bool|string
     */
    function getPathInfo() {
        if( !isset( $this->path_info ) ) {
            $this->path_info = $this->calPathInfo();
        }
        return $this->path_info;
    }
    /**
     * @param $path
     * @return mixed
     */
    function setPathInfo( $path ) {
        $this->path_info = $path;
        return $path;
    }
    /**
     * get path info, url from the base url to the end.
     * @return string
     */
    function calPathInfo() {
        $base_url    = $this->getBaseUrl();
        $request_uri = $this->getRequestUri();
        if( ( $pos = strpos( $request_uri, '?' ) ) !== FALSE ) {
            $request_uri = substr( $request_uri, 0, $pos );
        }
        $path_info = (string) substr( $request_uri, strlen( $base_url ) );
        if( substr( $path_info, 0, 1 ) === '/' ) {
            $path_info = substr( $path_info, 1 );
        }
        return $path_info;
    }
    // +-------------------------------------------------------------+
    /**
     * This function is to replace PHP's extremely buggy realpath().
     * from http://stackoverflow.com/questions/4049856/replace-phps-realpath
     * @param string $path   The original path, can be relative etc.
     * @return string        The resolved path, it might not exist.
     */
    function truePath($path)
    {
        // whether $path is unix or not
        $unipath=strlen($path)==0 || $path{0}!='/';
        /** @var $lastSlash string */
        $lastSlash = ( substr($path, -1, 1 ) === '/' ) ? '/' : '';
        // attempts to detect if path is relative in which case, add cwd
        if(strpos($path,':')===FALSE && $unipath)
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
