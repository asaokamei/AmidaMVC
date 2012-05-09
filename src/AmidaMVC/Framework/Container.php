<?php
namespace AmidaMVC\Framework;

class Container
{
    /**
     * @var array     array(  name => class, ...  )
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
    // +-------------------------------------------------------------+
    function __construct() {}
    // +-------------------------------------------------------------+
    function _init( $option=array() ) {}
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
    function findModule( $module ) {
        $base_name = substr( $module, strrpos( $module, '\\' ) ) . '.php';
        $found = $this->findFile( $base_name );
        return $found;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $name
     * @throws \RuntimeException
     * @return object|string
     */
    function loadModule( $name )
    {
        // get basic information about the modules to load.
        if( isset( $this->_modules[ $name ] ) ) {
            $moduleInfo = $this->_modules[ $name ];
            $moduleClass = $moduleInfo[0];
        }
        else { // it is not preset. use $name as class name.
            $moduleClass = $name;
        }
        // include the class if not already included.
        if( !class_exists( $moduleClass ) ) {
            if( $found = $this->findModule( $moduleClass ) ) {
                require_once( $found );
            }
            else  {
                throw new \RuntimeException( "Module Class: {$moduleClass} for {$name} not found." );
            }
        }
        return $moduleClass;
    }
    // +-------------------------------------------------------------+
    function forgeNewModule( $moduleName, $moduleClass ) {
        $module = new $moduleClass();
        if( isset( $this->_options[ $moduleName ] )
            && $moduleClass instanceof \AmidaMVC\Framework\IModule ) {
            $option = $this->_options[ $moduleName ];
            call_user_func( array( $moduleClass, '_init' ), $option );
        }
        return $module;
    }
    // +-------------------------------------------------------------+
    function get( $moduleName, $loadType='get', $idName='' ) {
        $moduleClass = $this->loadModule( $moduleName );
        // instantiate an object based on loadType
        if( $loadType == 'static' ) {
            $module = $moduleClass;
        }
        elseif( $loadType == 'get' ) {
            if( !isset( $this->_objects[ $moduleName ][ $idName ] ) ) {
                $this->_objects[ $moduleName ][ $idName ] = $this->forgeNewModule( $moduleName, $moduleClass );
            }
            $module = $this->_objects[ $moduleName ][ $idName ];
        }
        else {
            $module = $this->forgeNewModule( $moduleName, $moduleClass );;
        }
        $this->_lastModule = $module;
        return $module;
    }
    // +-------------------------------------------------------------+
    function magic( $moduleName, $method ) {
        $argc = func_num_args();
        $return = FALSE;
        if( $argc > 1 ) {
            $args = func_get_args();
            $args = array_slice( $args, 2 );
            $module = $this->get( $moduleName );
            $return = call_user_func_array( array( $module, $method ), $args );
        }
        return $return;
    }
    // +-------------------------------------------------------------+
}