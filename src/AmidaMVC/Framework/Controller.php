<?php
namespace AmidaMVC\Framework;

/**
 * Controller for AmidaMVC.
 * TODO: make new Controller
 *  and add data for controlling a web site, such as
 *  debug, admin, bread, etc.
 *
 */
class Controller extends Chain
{
    /**
     * @var null    control root where MVC controls.
     */
    var $ctrl_root = NULL;
    /**
     * @var null     path info starting from the root dir.
     */
    var $path = NULL;
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
    }
    // +-------------------------------------------------------------+
    function getLocation() {
        return $this->ctrl_root;
    }
    // +-------------------------------------------------------------+
    function start( &$view ) {
        $this->getRoute();
        if( isset( $this->routes[0] ) ) {
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
    function getRoute() {
        $this->command = explode( '/', $this->path );
        $this->routes = array();
        foreach( $this->command as $cmd ) {
            if( substr( $cmd, 0, 1 ) === $this->prefixCmd ) {
                continue; // ignore this cmd as route.
            }
            $this->routes[] = $cmd;
        }
        $this->debug( 'table', $this->command, 'getRoute command:' );
    }
    // +-------------------------------------------------------------+
    function loadDebug( $debug=NULL ) {
        if( !$debug ) $debug = 'Debug';
        if( $this->loadModel( $debug ) ) {
            $this->debug = new $debug;
        }
        return $this;
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
                return TRUE;
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}