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
    /**
     * @var array   options for this site. use it freely.
     */
    var $options = array();
    /**
     * @var array   loaded file's information.
     */
    var $loadInfo = array();
    /**
     * @var \AmidaMVC\Framework\PageObj
     */
    var $pageObj;
    /**
     * @var \AmidaMVC\Tools\Request
     */
    var $_requestClass = '\AmidaMVC\Tools\Request';
    /**
     * @var \AmidaMVC\Framework\PageObj
     */
    var $_pageObjClass = '\AmidaMVC\Framework\PageObj';
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     */
    function __construct( $option=array() ) 
    {
        // set path_info and base_url. 
        $class   = $this->_requestClass;
        $this->path_info = $class::getPathInfo();
        if( substr( $this->path_info, 0, 1 ) === '/' ) {
            $this->path_info = substr( $this->path_info, 1 );
        }
        $this->base_url = $class::getBaseUrl();
        
        // set ctrl root folder.
        if( !$option[ 'ctrl_root' ] ) {
            $option[ 'ctrl_root' ] = getcwd();
        }
        $this->ctrl_root    = $option[ 'ctrl_root' ];
        
        // set loadFolder as ctrl_root and appDefault.
        $this->loadFolder[] = $this->ctrl_root;
        $this->loadFolder[] = $option[ 'appDefault' ];

        if( isset( $option[ 'components' ] ) ) {
            $this->addComponent( $option[ 'components' ] );
        }
        $this->options = $option;
    }
    // +-------------------------------------------------------------+
    /**
     * @static
     * @param array $option
     * @return \AmidaMVC\Framework\Controller
     */
    static function getInstance( $option=array() ) {
        static $self = NULL;
        if( !isset( $self ) ) {
            $self = new static( $option );
        }
        return $self;
    }
    // +-------------------------------------------------------------+
    /**
     * get location of the document/control root.
     * @param null $path
     * @return null
     */
    function getLocation( $path=NULL ) {
        if( isset( $path ) ) {
            if( substr( $path, 0, 1 ) !== DIRECTORY_SEPARATOR ) {
                $path = DIRECTORY_SEPARATOR . $path;
            }
        }
        return $this->ctrl_root . $path;
    }
    // +-------------------------------------------------------------+
    /**
     * starts the Amida-chain loop. 
     * @param \AmidaMVC\Framework\PageObj $pageObj     page info.
     * @return bool|mixed|null  returned value from the last component.
     */
    function start( $pageObj=NULL ) {
        if( !isset( $pageObj ) ) {
            $class   = $this->_pageObjClass;
            $pageObj = new $class();
        }
        $this->pageObj = $pageObj;
        $action = $this->_defaultAct;
        return $this->dispatch( $action, $this->pageObj );
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
    function loadComponent( $component )
    {
        if( is_object( $component ) ) return TRUE;
        if( class_exists( $component, FALSE ) ) return TRUE;
        $base_name = $this->prefixCmd . $component . '.php';
        foreach( $this->loadFolder as $folder )
        {
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
    /**
     * get path info
     * @return null|string
     */
    function getPathInfo() {
        return $this->path_info;
    }
    // +-------------------------------------------------------------+
    /**
     * to catch uncaught exceptions just in case.
     * @param null $e
     * @return Controller
     */
    function actionFatal( $e=NULL ) {
        if( !isset( $e ) ) {
            set_exception_handler( array( $this, 'actionFatal' ) );
            return $this;
        }
        echo "Sorry, terrible thing has happened...";
        exit;
    }
    // +-------------------------------------------------------------+
    /**
     * gets values in option.
     * @param $name
     * @return string
     */
    function get( $name ) {
        if( isset( $this->options[ $name ] ) ) {
            return $this->options[ $name ];
        }
        return '';
    }
    // +-------------------------------------------------------------+
    /**
     * @return \AmidaMVC\Framework\PageObj
     */
    function getPageObj() {
        return $this->pageObj;
    }
    // +-------------------------------------------------------------+
}