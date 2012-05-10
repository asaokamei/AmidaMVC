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
                $className = $moduleInfo[0];
                $moduleInfo[0] = $moduleInfo[1];
                $moduleInfo[1] = $className;
                call_user_func_array( $this->setModule, $moduleInfo );
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
    function setModule( $moduleName, $className, $loadType='get', $config=array(), $option=array() ) {
        $this->_modules[ $moduleName ] = array(
            $className, $loadType, $config, $option
        );
        return $this;
    }
    // +-------------------------------------------------------------+
    function setModuleConfig( $moduleName, $options ) {
        $this->_options[ $moduleName ][ 'config' ] = $options;
        return $this;
    }
    // +-------------------------------------------------------------+
    function setModuleInjections( $moduleName, $options ) {
        $this->_options[ $moduleName ][ 'inject' ] = $options;
        return $this;
    }
    // +-------------------------------------------------------------+
    function setModuleInit( $moduleName, $options ) {
        $this->_options[ $moduleName ][ 'init' ] = $options;
        return $this;
    }
    // +-------------------------------------------------------------+
    function getModuleOptions( $moduleName ) {
        if( isset( $this->_options[ $moduleName ] ) ) {
            return $this->_options[ $moduleName ];
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    function getModuleInfo( $moduleName ) {
        if( isset( $this->_modules[ $moduleName ] ) ) {
            return $this->_modules[ $moduleName ];
        }
        return FALSE;
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
    /**
     * @param string $moduleName
     * @return string
     */
    function getLoadType( $moduleName ) {
        $loadType = 'get';
        if( isset( $this->_modules[ $moduleName ] ) &&
            is_array( $this->_modules[ $moduleName ] ) &&
            isset( $this->_modules[ $moduleName ][1]) ) {
            $loadType = $this->_modules[ $moduleName ][1];
        }
        return $loadType;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $moduleName
     * @param string $className
     * @param $config
     * @return object
     */
    function forgeNewModule( $moduleName, $className, $config ) {
        if( isset( $config ) && is_array( $config ) ) {
            // use the given config
        }
        elseif( isset( $moduleOption[ 'config' ] ) ) {
            $config = $moduleOption[ 'config' ];
        }
        else {
            $config = array();
        }
        $module = new $className( $config );
        if( isset( $this->_options[ $moduleName ] )
            && $className instanceof \AmidaMVC\Framework\IModule ) {
            $option = $this->_options[ $moduleName ];
            call_user_func( array( $className, '_init' ), $option );
        }
        return $module;
    }
    // +-------------------------------------------------------------+
    function forgeInjectAndInit( $module, $moduleInfo ) {
        if( isset( $moduleInfo[ 'option' ] ) ) {
            call_user_func( array( $module, '_init' ), $moduleInfo[ 'option' ] );
        }
        if( isset( $moduleInfo[ 'inject' ] ) && is_array( $moduleInfo[ 'inject' ] ) ) {
            foreach( $moduleInfo[ 'inject' ] as $injectInfo ) {

            }
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
        $moduleOption = $this->getModuleOptions( $moduleName );
        $className = $this->loadModule( $moduleName );
        // instantiate an object based on loadType
        $loadType = ( $loadType ) ?: $moduleOption[2];
        if( $loadType == 'static' ) {
            $module = $className;
        }
        elseif( $loadType == 'get' ) {
            if( !isset( $this->_objects[ $moduleName ][ $idName ] ) ) {
                $this->_objects[ $moduleName ][ $idName ] = $this->forgeNewModule( $moduleName, $className, $config );
            }
            $module = $this->_objects[ $moduleName ][ $idName ];
        }
        else {
            $module = $this->forgeNewModule( $moduleName, $className, $config );;
        }
        return $module;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $moduleName
     * @param array|null $config
     * @return mixed|object|string
     */
    function get( $moduleName, $config=NULL ) {
        $module = $this->getClean( $moduleName, 'get', '', $config );
        $this->_lastModule = $module;
        return $module;
    }
    // +-------------------------------------------------------------+
    function injectModule( $module, $injectName, $moduleName, $loadType=NULL, $idName='' ) {
        $injected = $this->getClean( $moduleName, $loadType, $idName );
        $args = array();
        if( substr( $injectName, 6 ) == 'inject' && method_exists( $module, $injectName ) ) {
            $exec = array( $module, $injectName );
            $args = array( $injected );
        }
        elseif( method_exists( $module, 'inject' ) ) {
            $exec = array( $module, 'inject' );
            $args = array( $injectName, $injected );
        }
        elseif( method_exists( $module, 'inject'.$injectName ) ) {
            $exec = array( $module, 'inject'.$injectName );
            $args = array( $injected );
        }
        if( isset( $exec ) ) {
            return call_user_func_array( $exec, $args );
        }
        throw new \RuntimeException( "Cannot inject $moduleName via $injectName." );
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