<?php
namespace AmidaMVC\Framework;

/**
 * Controller for AmidaMVC.
 *
 */
class Controller extends AmidaChain
{
    /**
     * @var null    control root where MVC controls.
     */
    var $ctrl_root = NULL;
    /**
     * @var null  base url where AmidaMVC application starts.
     */
    var $base_url = NULL;
    /**
     * @var null     path info without command (modified path for Route match).
     */
    var $path_info = NULL;
    /**
     * @var string   prefixAct to specify command.
     */
    var $prefixCmd = '_';
    /**
     * @var array   list of folders to look for components. 
     */
    var $loadFolder = array();
    /**
     * @var string  admin/dev mode of AmidaMVC. 
     */
    var $mode = '';
    // +-------------------------------------------------------------+
    function __construct( $option=array() ) 
    {
        // set path_info and base_url. 
        $this->path_info = $this->_obtainPathInfo();
        $this->base_url = $this->_obtainBaseUrl();
        
        // set ctrl root folder.
        if( !$option[ 'ctrl_root' ] ) {
            $option[ 'ctrl_root' ] = getcwd();
        }
        $this->ctrl_root    = $option[ 'ctrl_root' ];
        
        // set loadFolder as ctrl_root and appDefault.
        $this->loadFolder[] = $this->ctrl_root;
        $this->loadFolder[] = $option[ 'appDefault' ];
    }
    // +-------------------------------------------------------------+
    function getLocation() {
        return $this->ctrl_root;
    }
    // +-------------------------------------------------------------+
    /**
     * starts the Amida-chain loop. 
     * @param $view             parameter to pass through.
     * @return bool|mixed|null  returned value from the last component.
     */
    function start( &$view ) {
        $action = $this->_defaultAct;
        return $this->dispatch( $action, $view );
    }
    // +-------------------------------------------------------------+
    function _obtainPathInfo() {
        $path = \AmidaMVC\Tools\Request::getPathInfo();
        if( substr( $path, 0, 1 ) === '/' ) {
            $path = substr( $path, 1 );
        }
        return $path;
    }
    // +-------------------------------------------------------------+
    function _obtainBaseUrl() {
        return \AmidaMVC\Tools\Request::getBaseUrl();
    }
    // +-------------------------------------------------------------+
    function fireStart() {
        Event::fire(
            'Controller::start',
            $this->path_info, 'path info'
        );
    }
    // +-------------------------------------------------------------+
    function fireDispatch() {
        Event::fire(
            'Controller::dispatch',
            "model={$this->_components[0][0]} action={$this->_action}"
        );
    }
    // +-------------------------------------------------------------+
    /**
     * @param $component
     * @return Controller|bool
     */
    function loadComponent( $component ) {
        if( is_object( $component ) ) return TRUE;
        if( class_exists( $component, FALSE ) ) return TRUE;
        $base_name = $this->prefixCmd . $component . '.php';
        foreach( $this->loadFolder as $folder ) {
            $file_name = $folder. '/' . $base_name;
            if( file_exists( $file_name ) ) {
                require_once( $file_name );
                if( isset( $this ) ) {
                    return $this;
                }
                return TRUE;
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * redirect to base_url/mode/path. terminates by exit.
     * base_url is where your site is.  
     * mode is AmidaMVC's mode (_dev etc.)
     * path is path-info relative to base_url.
     * @param null $path 
     */
    function redirect( $path=NULL ) {
        $url = $this->getPath( $path );
        header( "Location: {$url}" );
        exit;
    }
    // +-------------------------------------------------------------+
    /**
     * gets path with base url. 
     * @param null $path
     * @return string
     */
    function getPath( $path=NULL ) {
        if( isset( $path ) && substr( $path, 0, 1 ) === '/' ) {
            $path = substr( $path, 1 );
        }
        $url = $this->getBaseUrl() . $path;
        return $url;
    }
    // +-------------------------------------------------------------+
    /**
     * returns base url containing mode (_dev etc.).
     * @return string   base url where your web site is at.
     */
    function getBaseUrl() {
        $base_url = $this->base_url;
        if( substr( $base_url, -1 ) !== '/' ) {
            $base_url .= '/';
        }
        $mode = $this->mode;
        if( $mode && substr( $mode, -1 ) !== '/' ) {
            $mode .= '/';
        }
        $base = "{$base_url}{$mode}";
        return $base;
    }
    // +-------------------------------------------------------------+
    function getPathInfo() {
        return $this->path_info;
    }
    // +-------------------------------------------------------------+
    function actionFatal( $e=NULL ) {
        if( !isset( $e ) ) {
            set_exception_handler( array( $this, 'actionFatal' ) );
            return $this;
        }
        \AmidaMVC\Component\Debug::format( 'table', $e->getTrace() );
    }
    // +-------------------------------------------------------------+
}