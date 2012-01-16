<?php
namespace AmidaMVC\Framework;
require_once( __DIR__ . '/Chain.php');
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
    /**
     * gets routes array. override this method to use other Router.
     * @static
     * @return array    routes.
     */
    function __construct() {
        // make this DI!
        $traces = debug_backtrace(false);
        $this->loadFolder[] = dirname( $traces[0]['file'] );
        $this->loadFolder[] = realpath( __DIR__ . '/../AppDefault' );
    }
    // +-------------------------------------------------------------+
    function getLocation() {
        return $this->loadFolder[0];
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
    function getRoute( $command=NULL ) {
        if( $command === NULL ) {
            $this->command = self::getUriList();
        }
        else {
            $this->command = $command;
        }
        foreach( $this->command as $key => $val ) {
            if( $val === '' ) {
                unset( $this->command[$key] );
            }
        }
        $this->routes = array();
        $this->command = array_values( $this->command );
        foreach( $this->command as $cmd ) {
            if( substr( $cmd, 0, 1 ) === $this->prefixCmd ) {
                // ignore this cmd as route.
                continue;
            }
            $this->routes[] = $cmd;
        }
        $this->debug( 'table', $this->command, 'getRoute command:' );
    }
    // +-------------------------------------------------------------+
    /**
     * @param null $uri
     * @param null $script
     * @return array     returns routes.
     */
    static function getUriList( $uri=NULL, $script=NULL ) {
        if( $uri === NULL ) {
            $uri = preg_replace('@[\/]{2,}@', '/', $_SERVER[ 'REQUEST_URI' ] );
            $uri = explode( '/', $uri );
        }
        if( $script === NULL ) {
            $script = explode( '/', $_SERVER[ 'SCRIPT_NAME' ] );
        }
        for( $i = 0; $i < sizeof( $script ); $i++ ) {
            if( $uri[$i] == $script[$i] ) {
                unset( $uri[$i] );
            }
        }
        return array_values( $uri );
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