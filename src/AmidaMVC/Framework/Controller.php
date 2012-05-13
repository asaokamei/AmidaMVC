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
    protected $ctrl_root = NULL;
    /**
     * @var string   prefixAct to specify command.
     */
    protected $prefixCmd = '_';
    /**
     * @var array   list of folders to look for modules.
     */
    protected $loadFolder = array();
    /**
     * @var array   command list, starts with prefixCmd.
     */
    protected $commands = array();
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
    protected $_requestClass = '\AmidaMVC\Tools\Request';
    /**
     * @var \AmidaMVC\Framework\PageObj
     */
    protected $_pageObjClass = '\AmidaMVC\Framework\PageObj';
    /**
     * @var \AmidaMVC\Framework\Container
     */
    protected $_diContainer = '\AmidaMVC\Framework\Container';
    /**
     * @var \AmidaMVC\Tools\Load
     */
    protected $_loadClass = '\AmidaMVC\Tools\Load';
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     */
    function __construct( $option=array() ) 
    {
        // set up DI Container.
        if( isset( $option[ '_DIContainerClass' ] ) ) {
            $class = $option[ '_DIContainerClass' ];
            $this->_diContainer = new $class();
            unset( $option[ '_DIContainerClass' ] );
        }
        else {
            /** @var $class \AmidaMVC\Framework\Container */
            $class = $this->_diContainer;
            $this->_diContainer = $class::start();
        }
        // set up modules for AmidaChain, and DI Container.
        if( isset( $option[ 'modules' ] ) ) {
            $this->addModule( $option[ 'modules' ] );
            foreach( $option[ 'modules' ] as $info ) {
                $this->_diContainer->setModule( $info[1], $info[0] );
            }
        }
        // set up moduleInfo for DI Container.
        // TODO: not to setup config in DI at this point; routes maybe added later on.
        $this->options = $option;
        // get request object.
        $this->_requestClass = $this->_diContainer->get( '\AmidaMVC\Tools\Request' );
        $this->_loadClass = $this->_diContainer->get( '\AmidaMVC\Tools\Load', 'static' );

        // set ctrl root folder.
        if( !isset( $option[ 'ctrl_root' ] ) ) {
            $option[ 'ctrl_root' ] = getcwd();
        }
        $this->ctrl_root    = $option[ 'ctrl_root' ];
        // set loadFolder as ctrl_root and appDefault.
        $this->setFileLocation( $this->ctrl_root );
        if( isset( $option[ 'appDefault' ] ) ) {
            $this->setFileLocation( $option[ 'appDefault' ] );
        }
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
        $path = call_user_func( array( $this->_requestClass, 'truePath' ), $this->ctrl_root . $path );
        return $path;
    }
    // +-------------------------------------------------------------+
    /**
     * starts the Amida-chain loop. 
     * @param \AmidaMVC\Framework\PageObj $pageObj     page info.
     * @return bool|mixed|null  returned value from the last module.
     */
    function start( $pageObj=NULL ) {
        if( !isset( $pageObj ) ) {
            $class   = $this->_pageObjClass;
            $pageObj = $this->_diContainer->get( $class );
        }
        $this->pageObj = $pageObj;
        $action = $this->defaultAct();
        return $this->dispatch( $action, $this->pageObj );
    }
    // +-------------------------------------------------------------+
    function fireStart() {
        Event::fire(
            'Controller::start', $this
        );
    }
    // +-------------------------------------------------------------+
    function fireDispatch() {
        Event::fire(
            'Controller::dispatch', $this
        );
    }
    // +-------------------------------------------------------------+
    function setFileLocation( $folder ) {
        if( substr( $folder, 0, 1 ) !== '/' ) {
            $folder = $this->getLocation( $folder );
        }
        $exec = array( $this->_loadClass, 'setFileLocation' );
        return call_user_func( $exec, $folder );
    }
    // +-------------------------------------------------------------+
    /**
     * find file_name from $this->loadFolder list and returns the
     * full path.
     * @param string $file_name
     * @return string
     */
    function findFile( $file_name ) {
        $exec = array( $this->_loadClass, 'findFile' );
        return call_user_func( $exec, $file_name );
    }
    // +-------------------------------------------------------------+
    /**
     * @param mixed $module
     * @param string $name
     * @throws \RuntimeException
     * @return Controller|bool
     */
    function loadModule( &$module, $name )
    {
        $option = array();
        $name   = $this->makeModuleOptionName( $name );
        if( isset( $this->options[ $name ] ) ) {
            $option = $this->options[ $name ];
        }
        if( is_object( $module ) ) {
            // good. it's an object.
        }
        else {
            if( !class_exists( $module ) ) {
                $exec = array( $this->_loadClass, 'loadClassFile' );
                if( !call_user_func( $exec, $module ) ) {
                    throw new \RuntimeException( "Module: {$module} not found." );
                }
            }
            $module = new $module();

        }
        if( !empty( $option ) && ( $module instanceof \AmidaMVC\Framework\IModule ) ) {
            call_user_func( array( $module, '_init' ), $option );
        }
        return TRUE;
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
        $url = $this->getBaseUrl( $path );
        header( "Location: {$url}" );
        exit;
    }
    // +-------------------------------------------------------------+
    /**
     * returns base url containing mode (_dev etc.).
     *
     * @param null|string $url
     * @return string   base url where your web site is at.
     */
    function getBaseUrl( $url=NULL ) {
        $base_url = $this->_requestClass->getBaseUrl( $url );
        return $base_url;
    }
    // +-------------------------------------------------------------+
    /**
     * get path info
     * @return null|string
     */
    function getPathInfo() {
        return $this->_requestClass->getPathInfo();
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
    function getOption( $name ) {
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
    /**
     * make name for module option.
     * @param $name
     * @return string
     */
    function makeModuleOptionName( $name ) {
        return "_{$name}";
    }
    // +-------------------------------------------------------------+
    /**
     * set option for each module.
     * @param string $name        name of the module to set.
     * @param string $key         key name of option.
     * @param mixed $value       option value.
     * @return Controller
     */
    function setModuleOption( $name, $key, $value ) {
        $name   = $this->makeModuleOptionName( $name );
        if( !isset( $this->options[ $name ] ) ) {
            $this->options[ $name ] = array();
        }
        $this->options[ $name ][ $key ] = $value;
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * get option for module
     * @param string $name
     * @param string $key
     * @return mixed
     */
    function getModuleOption( $name, $key ) {
        $name   = $this->makeModuleOptionName( $name );
        if( isset( $this->options[ $name ] ) &&
            is_array( $this->options[ $name ] ) &&
            isset( $this->options[ $name ][ $key ] ) ) {
            return $this->options[ $name ][ $key ];
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * set routes for Router.
     * @param string $route
     * @param string $file
     * @param array $option
     * @return Controller
     */
    function get( $route, $file, $option=array() ) {
        // default is router
        $name = 'router';
        $key  = 'routes';
        $routeList = $this->getModuleOption( $name, $key );
        if( !$routeList ) {
            $routeList = array();
        }
        $routeList[ $route ] = array_merge( $option, array( 'file' => $file ) );
        $this->setModuleOption( $name, $key, $routeList );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * separates commands in path_info to $this->cmd.
     * @return Controller
     */
    function separateCommands() {
        $this->commands = $this->_requestClass->separateCommands( $this->prefixCmd );
        return $this;
    }
    // +-------------------------------------------------------------+
    function getCommands() {
        return $this->commands;
    }
    // +-------------------------------------------------------------+
}