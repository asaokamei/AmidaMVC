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
    var $loadFolder = array();
    // +-------------------------------------------------------------+
    function __construct( $option=array() ) {
        $default = array(
            'ctrl_root'  => FALSE,
            'appDefault' => realpath( __DIR__ . '/../AppDefault' ),
        );
        $option = $option + $default;
        
        // set path_info and base_url. 
        $this->path_info = $this->getPathInfo();
        $this->base_url = $this->getBaseUrl();
        
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
    function start( &$view ) {
        if( isset( $this->routes[0] ) && "" !== "{$this->routes[0]}" ) {
            $action = $this->routes[0];
        }
        else {
            $action = $this->_defaultAct;
        }
        return $this->dispatch( $action, $view );
    }
    // +-------------------------------------------------------------+
    function getPathInfo() {
        $path = \AmidaMVC\Tools\Request::getPathInfo();
        if( substr( $path, 0, 1 ) === '/' ) {
            $path = substr( $path, 1 );
        }
        return $path;
    }
    // +-------------------------------------------------------------+
    function getBaseUrl() {
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
    function loadModel( $model ) {
        if( is_object( $model ) ) return TRUE;
        if( class_exists( $model, FALSE ) ) return TRUE;
        $base_name = $this->prefixCmd . $model . '.php';
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
}