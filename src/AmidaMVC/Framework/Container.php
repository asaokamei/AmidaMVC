<?php
namespace AmidaMVC\Framework;

/*
 * about class's methods in calling order
 * __construct( $config=array() )
 * inject( 'name', $object ) or injectName( $object )
 * _init( $option )
 */
class Container
{
    /**
     * @var array     module info about how to build an object
     *
     * looks like this.
     * array(
     *   moduleName => [
     *     className=>'', loadType=>'', idName=>'', config=>[], inject=>[], option=>[]
     *   ],
     *   ...  )
     */
    protected $_modules = array();
    /**
     * @var array    list of folders to look for.
     */
    protected $_rootDir = array();
    /**
     * @var array    list of generated objects for singleton
     */
    protected $_objects = array();
    /**
     * @var object|null   keep the last instantiated object.
     */
    protected $_lastModule = NULL;
    /**
     * @var Container
     */
    static $self = FALSE;
    // +-------------------------------------------------------------+
    function __construct() {}
    // +-------------------------------------------------------------+
    /**
     * @static
     * @return Container
     */
    static function getInstance() {
        if( !static::$self ) {
            static::$self = new static();
        }
        return static::$self;
    }
    // +-------------------------------------------------------------+
    static function start() {
        $self = static::getInstance();
        $self->_objects[ 'new' ] = array();
        return $self;
    }
    // +-------------------------------------------------------------+
    static function resume() {
        return static::getInstance();
    }
    // +-------------------------------------------------------------+
    static function clean() {
        static::$self = FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     * @return Container
     */
    function _init( $option=array() ) {
        if( !is_array( $option ) ) return $this;
        if( isset( $option[ 'modules' ] ) && is_array( $option[ 'modules' ] ) ) {
            foreach( $option[ 'modules' ] as $moduleInfo ) {
                $className = $moduleInfo[0];
                $moduleInfo[0] = $moduleInfo[1];
                $moduleInfo[1] = $className;
                call_user_func_array( array( $this, 'setModule' ), $moduleInfo );
            }
        }
        foreach( $option as $opName => $opVal ) {
            if( substr( $opName, 0, 1 ) === '_' ) {
                $this->_modules[ substr( $opName, 1 ) ] = array(
                    'option' => $opVal,
                );
            }
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    function setModule( $moduleName, $className ) {
        $args = func_get_args();
        list( $loadType, $idName, $config ) = $this->parseInput( $args, 2 );
        $config[ 'className' ] = $className;
        $config[ 'loadType' ] = $loadType;
        $config[ 'idName' ] = $idName;
        $this->_modules[ $moduleName ] = $config;
        return $this;
    }
    // +-------------------------------------------------------------+
    function getModuleInfo( $moduleName ) {
        if( isset( $this->_modules[ $moduleName ] ) ) {
            $moduleInfo = $this->_modules[ $moduleName ];
        }
        else {
            $moduleInfo = array(
                'className' => $moduleName,
                'loadType' => 'get',
                'idName' => '',
                'config' => array(),
                'inject' => array(),
                'option' => array(),
                'undefined' => TRUE,
            );
        }
        return $moduleInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string|array $folder
     * @return Container
     */
    function setFileLocation( $folder ) {
        if( is_array( $folder ) ) {
            $folder = array( $folder );
        }
        $this->_rootDir = $folder + $this->_rootDir;
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * find file_name from $this->loadFolder list and returns the
     * full path.
     * @param string $file_name
     * @return string
     */
    function findFile( $file_name ) {
        $found = FALSE;
        if( empty( $this->_rootDir ) ) return $found;
        foreach( $this->_rootDir as $folder ) {
            $check_file = $folder. '/' . $file_name;
            if( file_exists( $check_file ) ) {
                $found = $check_file;
                break;
            }
        }
        return $found;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $module
     * @return string
     */
    function findModuleFile( $module ) {
        $base_name = substr( $module, strrpos( $module, '\\' ) ) . '.php';
        $found = $this->findFile( $base_name );
        return $found;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $moduleName
     * @throws \RuntimeException
     * @return object|string
     */
    function loadModule( $moduleName ) {
        // get basic information about the modules to load.
        $moduleInfo = $this->getModuleInfo( $moduleName );
        $className = $moduleInfo[ 'className' ];
        // include the class if not already included.
        if( !class_exists( $className ) ) {
            if( $found = $this->findModuleFile( $className ) ) {
                require_once( $found );
            }
            else  {
                throw new \RuntimeException( "Module Class: {$className} for {$moduleName} not found." );
            }
        }
        return $className;
    }
    // +-------------------------------------------------------------+
    function injectAndInit( $module, $moduleInfo ) {
        if( isset( $moduleInfo[ 'inject' ] ) && is_array( $moduleInfo[ 'inject' ] ) ) {
            foreach( $moduleInfo[ 'inject' ] as $injectInfo ) {
                $args = array_merge( array( $module ), $injectInfo );
                call_user_func_array( array( $this, 'injectModule' ), $args );
            }
        }
        if( isset( $moduleInfo[ 'option' ] ) ) {
            $option = ( $moduleInfo[ 'option' ] ) ?: array();
            call_user_func( array( $module, '_init' ), $option );
        }
    }
    // +-------------------------------------------------------------+
    /**
     * @param string$moduleName
     * @param string|null $loadType
     * @param string $idName
     * @param array|null $config
     * @return mixed|object|string
     */
    function getClean( $moduleName, $loadType=NULL, $idName='', $config=null ) {
        $moduleInfo = $this->getModuleInfo( $moduleName );
        $className = $moduleInfo[ 'className' ];
        $loadType = ( $loadType ) ?: $moduleInfo['loadType'];
        if( isset( $config ) && is_array( $config ) ) {
            if( isset( $config[ 'config' ] ) ) {
                $moduleInfo = $config;
            }
            else {
                $moduleInfo[ 'config' ] = $config;
            }
        }
        if( is_object( $className ) ) {
            $module = $className;
        }
        else if( $className instanceof \Closure ) {
            /** @var $className Closure */
            $module = $className( $this );
        }
        else if( $loadType == 'static' ) {
            $module = $className;
        }
        else { // $loadType is 'new' or 'get'.
            if( $loadType == 'new' && empty( $idName ) ) {
                // new without id always return brand-new object.
                $module = new $className( $moduleInfo[ 'config' ] );
            }
            else if( isset( $this->_objects[ $loadType ][ $moduleName ][ $idName ] ) ) {
                $module = $this->_objects[ $loadType ][ $moduleName ][ $idName ];
            }
            else {
                $this->_objects[ $loadType ][ $moduleName ][ $idName ] =
                    $module = new $className( $moduleInfo[ 'config' ] );
            }
        }
        $this->injectAndInit( $module, $moduleInfo );
        return $module;
    }
    // +-------------------------------------------------------------+
    function parseInput( $input=array(), $offset=0 ) {
        if( $offset > 0 ) {
            $input = array_slice( $input, $offset );
        }
        $loadType = 'get';
        $idName = '';
        $config = array();
        if( count( $input ) == 1 ) {
            $arg = $input[0];
            if( is_array( $arg ) ) {
                $config = $arg;
            }
            elseif( in_array( $arg, array( 'get', 'new', 'static' ) ) ) {
                $loadType = $arg;
            }
            else {
                $idName = $arg;
            }
        }
        if( count( $input ) == 2 ) {
            $arg = $input[0];
            if( in_array( $arg, array( 'get', 'new', 'static' ) ) ) {
                $loadType = $arg;
            }
            $arg = $input[1];
            if( is_array( $arg ) ) {
                $config = $arg;
            }
            else {
                $idName = $arg;
            }
        }
        if( count( $input ) >= 3 ) {
            $loadType = $input[0];
            $idName = $input[1];
            $config = $input[2];
        }
        return array( $loadType, $idName, $config );
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $moduleName
     * @internal param array|null $config
     * @return mixed|object|string
     */
    function get( $moduleName ) {
        $args = func_get_args();
        list( $loadType, $idName, $config ) = $this->parseInput( $args, 1 );
        $module = $this->getClean( $moduleName, $loadType, $idName, $config );
        $this->_lastModule = $module;
        return $module;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $module
     * @param string $injectName
     * @param string $moduleName
     * @throws \RuntimeException
     * @internal param null|string $loadType
     * @internal param string $idName
     * @return Container
     */
    function injectModule( $module, $injectName, $moduleName ) {
        $args = func_get_args();
        $args = array_slice( $args, 3 );
        list( $loadType, $idName, $config ) = $this->parseInput( $args );
        $injected = $this->getClean( $moduleName, $loadType, $idName, $config );
        $args = array();
        $method = 'inject' . ucwords( $injectName );
        if( method_exists( $module, $method ) ) {
            $exec = array( $module, $method );
            $args = array( $injected );
        }
        elseif( method_exists( $module, 'inject' ) ) {
            $exec = array( $module, 'inject' );
            $args = array( $injectName, $injected );
        }
        if( isset( $exec ) ) {
            call_user_func_array( $exec, $args );
            return $this;
        }
        throw new \RuntimeException( "Cannot inject $moduleName via $injectName." );
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $injectName
     * @param string $moduleName
     * @internal param null|string $loadType
     * @internal param string $idName
     * @return mixed
     */
    function inject( $injectName, $moduleName ) {
        $args = func_get_args();
        list( $loadType, $idName, $config ) = $this->parseInput( $args, 2 );
        $object = $this->_lastModule;
        return $this->injectModule( $object, $injectName, $moduleName, $loadType, $idName, $config );
    }
    // +-------------------------------------------------------------+
}