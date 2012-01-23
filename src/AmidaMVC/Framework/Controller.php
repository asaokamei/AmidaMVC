<?php
namespace AmidaMVC\Framework;

/**
 * Controller for AmidaMVC.
 *
 */
class Controller extends Chain
{
    /**
     * @var null    control root where MVC controls.
     */
    var $ctrl_root = NULL;
    /**
     * @var null  base url where AmidaMVC application starts.
     */
    var $base_url = NULL;
    /** TODO: rename from $path to $path_info.
     * @var null     path info starting from the root dir.
     */
    var $path = NULL;
    /**
     * @var null     path info without command (modified path for Route match).
     */
    var $path_info = NULL;
    /**
     * @var array    command map from URI.
     */
    var $command = array();
    /**
     * @var array    route map from URI, without _*.
     */
    var $routes = array();
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
            'path_info'  => FALSE,
            'base_url'   => FALSE,
        );
        $option = $option + $default;
        // set ctrl root folder.
        if( !$option[ 'ctrl_root' ] ) {
            $traces = debug_backtrace(false);
            $option[ 'ctrl_root' ] = dirname( $traces[0]['file'] );
        }
        $this->ctrl_root    = $option[ 'ctrl_root' ];
        // set loadFolder as ctrl_root and appDefault.
        $this->loadFolder[] = $this->ctrl_root;
        $this->loadFolder[] = $option[ 'appDefault' ];
        // set path_info
        if( !$option[ 'path_info' ] ) {
            $option[ 'path_info' ] = self::getPathInfo();
        }
        $this->path = $option[ 'path_info' ];
        // set base url
        if( !$option[ 'base_url' ] ) {
            $option[ 'base_url' ] = self::getBaseUrl();
        }
        $this->base_url = $option[ 'base_url' ];
    }
    // +-------------------------------------------------------------+
    function getLocation() {
        return $this->ctrl_root;
    }
    // +-------------------------------------------------------------+
    function start( &$view ) {
        $this->getRoute();
        if( isset( $this->routes[0] ) && "" !== "{$this->routes[0]}" ) {
            $action = $this->routes[0];
        }
        else {
            $action = $this->defaultAct;
        }
        return $this->dispatch( $action, $view );
    }
    // +-------------------------------------------------------------+
    function getPathInfo() {
        return \AmidaMVC\Framework\Request::getPathInfo();
    }
    // +-------------------------------------------------------------+
    function getBaseUrl() {
        return \AmidaMVC\Framework\Request::getBaseUrl();
    }
    // +-------------------------------------------------------------+
    function getRoute() {
        $paths = explode( '/', $this->path );
        $this->command = array();
        $this->routes = array();
        foreach( $paths as $cmd ) {
            if( empty( $cmd ) ) continue;
            if( $cmd === '..' ) continue;
            if( substr( $cmd, 0, 1 ) === $this->prefixCmd ) {
                $this->command[] = $cmd;
            }
            else {
                $this->routes[] = $cmd;
            }
        }
        $this->path_info = implode( '/', $this->routes );
        if( empty( $this->path_info ) ) {
            $this->path_info = '/';
        }
    }
    // +-------------------------------------------------------------+
    function fireStart() {
        Event::fire(
            'Controller::start',
            $this->command, 'command list'
        );
    }
    // +-------------------------------------------------------------+
    function fireDispatch() {
        Event::fire(
            'Controller::dispatch',
            "model={$this->modelName} action={$this->currAct}"
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