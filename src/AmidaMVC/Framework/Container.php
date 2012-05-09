<?php
namespace AmidaMVC\Framework;

class Container
{
    /**
     * @var array     array(  moduleName => [ className, loadType ], ...  )
     */
    protected $_modules = array();
    /**
     * @var array    list of folders to look for.
     */
    protected $_rootDir = array();
    /**
     * @var array    options to configure modules.
     */
    protected $_options = array();
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
    static $self;
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
    /**
     * @param array $option
     * @return Container
     */
    function _init( $option=array() ) {
        if( !is_array( $option ) ) return $this;
        if( isset( $option[ 'modules' ] ) && is_array( $option[ 'modules' ] ) ) {
            foreach( $option[ 'modules' ] as $moduleInfo ) {
                $loadType = ( $moduleInfo[2] ) ?: 'get';
                $this->_modules[ $moduleInfo[1] ] = array(
                    $moduleInfo[0], $loadType,
                );
            }
        }
        foreach( $option as $opName => $opVal ) {
            if( substr( $opName, 0, 1 ) === '_' ) {
                $this->_options[ substr( $opName, 1 ) ] = $opVal;
            }
        }
        return $this;
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
    function findModule( $module ) {
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
    function loadModule( $moduleName )
    {
        // get basic information about the modules to load.
        if( isset( $this->_modules[ $moduleName ] ) ) {
            $className = $this->_modules[ $moduleName ][0];
        }
        else { // it is not preset. use $name as class name.
            $className = $moduleName;
        }
        // include the class if not already included.
        if( !class_exists( $className ) ) {
            if( $found = $this->findModule( $className ) ) {
                require_once( $found );
            }
            else  {
                throw new \RuntimeException( "Module Class: {$className} for {$moduleName} not found." );
            }
        }
        return $className;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $moduleName
     * @return string
     */
    function loadType( $moduleName ) {
        $loadType = ( $this->_modules[ $moduleName ][1] ) ?: 'get';
        return $loadType;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $moduleName
     * @param string $className
     * @return object
     */
    function forgeNewModule( $moduleName, $className ) {
        $module = new $className();
        if( isset( $this->_options[ $moduleName ] )
            && $className instanceof \AmidaMVC\Framework\IModule ) {
            $option = $this->_options[ $moduleName ];
            call_user_func( array( $className, '_init' ), $option );
        }
        return $module;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string$moduleName
     * @param string|null $loadType
     * @param string $idName
     * @return mixed|object|string
     */
    function getClean( $moduleName, $loadType=NULL, $idName='' ) {
        $className = $this->loadModule( $moduleName );
        // instantiate an object based on loadType
        $loadType = ( $loadType ) ?: $this->loadType( $moduleName );
        if( $loadType == 'static' ) {
            $module = $className;
        }
        elseif( $loadType == 'get' ) {
            if( !isset( $this->_objects[ $moduleName ][ $idName ] ) ) {
                $this->_objects[ $moduleName ][ $idName ] = $this->forgeNewModule( $moduleName, $className );
            }
            $module = $this->_objects[ $moduleName ][ $idName ];
        }
        else {
            $module = $this->forgeNewModule( $moduleName, $className );;
        }
        return $module;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $moduleName
     * @param string|null $loadType
     * @param string $idName
     * @return mixed|object|string
     */
    function get( $moduleName, $loadType=NULL, $idName='' ) {
        $module = $this->getClean( $moduleName, $loadType, $idName);
        $this->_lastModule = $module;
        return $module;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $injectName
     * @param string $moduleName
     * @param string|null $loadType
     * @param string $idName
     * @throws \RuntimeException
     * @return mixed
     */
    function inject( $injectName, $moduleName, $loadType=NULL, $idName='' ) {
        $object = $this->_lastModule;
        $injected = $this->getClean( $moduleName, $loadType, $idName );
        $args = array();
        if( method_exists( $object, $injectName ) ) {
            $exec = array( $object, $injectName );
            $args = array( $object );
        }
        elseif( method_exists( $object, 'inject' ) ) {
            $exec = array( $object, 'inject' );
            $args = array( $injectName, $object );
        }
        elseif( method_exists( $object, 'inject'.$injectName ) ) {
            $exec = array( $object, 'inject'.$injectName );
            $args = array( $object );
        }
        if( isset( $exec ) ) {
            return call_user_func_array( $exec, $args );
        }
        throw new \RuntimeException( "Cannot inject $moduleName via $injectName." );
    }
    // +-------------------------------------------------------------+
}