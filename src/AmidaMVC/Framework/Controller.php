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
     * @var array   command list, starts with prefixCmd.
     */
    protected $commands = array();
    /**
     * @var array   options for this site. use it freely.
     */
    var $options = array();
    /**
     * @var \AmidaMVC\Framework\PageObj
     */
    var $pageObj = NULL;
    /**
     * @var \AmidaMVC\Tools\Request
     */
    var $request = NULL;
    /**
     * @var \AmidaMVC\Framework\Services
     */
    var $_diContainer = NULL;
    /**
     * @var \AmidaMVC\Tools\Load
     */
    var $_loadClass = NULL;
    /**
     * @var null|\AmidaMVC\Framework\Container
     */
    static $_self = NULL;
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     */
    function __construct( $option=array() ) 
    {
        $this->options = $option;

        // set ctrl root folder.
        if( !isset( $option[ 'ctrl_root' ] ) ) {
            $option[ 'ctrl_root' ] = getcwd();
        }
        if( substr( $option[ 'ctrl_root' ], -1 ) === '/' ) {
            // no trailing slash.
            $option[ 'ctrl_root' ] = substr( $option[ 'ctrl_root' ], 0, -1 );
        }
        $this->ctrl_root    = $option[ 'ctrl_root' ];
    }
    /**
     * @param $modules
     */
    function setModules( $modules ) {
        if( is_array( $modules ) ) {
            foreach( $modules as $mod ) {
                $this->_modules[] = ( is_array( $mod ) ) ?
                    $this->_modules[] = array( $mod[0], $mod[1] ) :
                    array( $mod, $mod );
            }
        }
    }
    /**
     * @param $diContainer
     */
    function injectDiContainer( $diContainer ) {
        $this->_diContainer = $diContainer;
    }
    function injectRequest( $request ) {
        $this->request = $request;
    }
    function injectLoad( $load ) {
        $this->_loadClass = $load;
    }
    function getDi() {
        return $this->_diContainer;
    }
    // +-------------------------------------------------------------+
    /**
     * @static
     * @param array $option
     * @return \AmidaMVC\Framework\Controller
     */
    static function getInstance( $option=array() ) {
        if( !isset( static::$_self ) ) {
            static::$_self = new static( $option );
        }
        return static::$_self;
    }
    static function clean() {
        static::$_self = NULL;
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
        $path = call_user_func( array( $this->request, 'truePath' ), $this->ctrl_root . $path );
        return $path;
    }
    // +-------------------------------------------------------------+
    /**
     * starts the Amida-chain loop. 
     * @param \AmidaMVC\Framework\PageObj $pageObj     page info.
     * @return bool|mixed|null  returned value from the last module.
     */
    function start( $pageObj=NULL ) {
        $this->pageObj =
            ( $pageObj ) ?: ( $this->pageObj ) ?:
            $this->_diContainer->get( '\AmidaMVC\Framework\PageObj' );
        // set loadFolder as ctrl_root and appDefault.
        $this->setFileLocation( $this->ctrl_root );
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
     * @return \AmidaMVC\Framework\AmidaChain|bool
     */
    function loadModule( &$module, $name ) {
        if( !is_object( $module ) ) {
            $module = $this->_diContainer->get( $module );
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
        $base_url = $this->request->getBaseUrl( $url );
        return $base_url;
    }
    // +-------------------------------------------------------------+
    /**
     * get path info
     * @return null|string
     */
    function getPathInfo() {
        return $this->request->getPathInfo();
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
    function setOption( $name, $value ) {
        $this->options[ $name ] = $value;
        return $this;
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
     * set option for each module.
     * @param string $name        name of the module to set.
     * @param mixed $value       option value.
     * @return Controller
     */
    function setModuleOption( $name, $value ) {
        $this->_diContainer
            ->service( $name )
            ->setConfig( $value );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * get option for module
     * @param string $name
     * @return mixed
     */
    function getModuleOption( $name ) {
        $this->_diContainer->getService( $name, $din, $config, $inject );
        return $config;
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
        $moduleOption = $this->getModuleOption( $name );
        $routeList = $moduleOption[ $key ];
        if( !$routeList ) {
            $routeList = array();
        }
        $routeList[ $route ] = array_merge( $option, array( 'file' => $file ) );
        $moduleOption[ $key ] = $routeList;
        $this->setModuleOption( $name, $moduleOption );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * separates commands in path_info to $this->cmd.
     * @return Controller
     */
    function separateCommands() {
        $this->commands = $this->request->separateCommands( $this->prefixCmd );
        return $this;
    }
    // +-------------------------------------------------------------+
    function getCommands() {
        return $this->commands;
    }
    // +-------------------------------------------------------------+
}