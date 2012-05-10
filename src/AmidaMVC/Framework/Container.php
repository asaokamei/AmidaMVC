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
    function setModule( $moduleName, $className, $loadType='get', $idName='' ) {
        $this->_modules[ $moduleName ] = array(
            'className' => $className,
            'loadType' => $loadType,
            'idName' => $idName,
        );
        return $this;
    }
    // +-------------------------------------------------------------+
    function setModuleConfig( $moduleName, $options ) {
        $this->_modules[ $moduleName ][ 'config' ] = $options;
        return $this;
    }
    // +-------------------------------------------------------------+
    function setModuleInjections( $moduleName, $options ) {
        $this->_modules[ $moduleName ][ 'inject' ] = $options;
        return $this;
    }
    // +-------------------------------------------------------------+
    function setModuleOption( $moduleName, $options ) {
        $this->_modules[ $moduleName ][ 'Option' ] = $options;
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
        $option = ( isset( $moduleInfo[ 'option' ] ) ) ? $moduleInfo[ 'option' ]: array();
        call_user_func( array( $module, '_init' ), $option );
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
        if( $loadType == 'static' ) {
            $module = $className;
        }
        else { // $loadType is 'new' or 'get'.
            if( !isset( $this->_objects[ $loadType ][ $moduleName ][ $idName ] ) ) {
                $this->_objects[ $loadType ][ $moduleName ][ $idName ] =
                    new $className( $moduleInfo[ 'config' ] );
            }
            $module = $this->_objects[ $loadType ][ $moduleName ][ $idName ];
        }
        $this->injectAndInit( $module, $moduleInfo );
        return $module;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $moduleName
     * @internal param array|null $config
     * @return mixed|object|string
     */
    function get( $moduleName ) {
        $loadType = 'get';
        $idName = '';
        $config = array();
        if( func_num_args() == 2 ) {
            $arg = func_get_arg(1);
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
        if( func_num_args() == 3 ) {
            $arg = func_get_arg(1);
            if( in_array( $arg, array( 'get', 'new', 'static' ) ) ) {
                $loadType = $arg;
            }
            $arg = func_get_arg(2);
            if( is_array( $arg ) ) {
                $config = $arg;
            }
            else {
                $idName = $arg;
            }
        }
        if( func_num_args() >= 4 ) {
            $loadType = func_get_arg(1);
            $idName = func_get_arg(2);
            $config = func_get_arg(3);
        }
        $module = $this->getClean( $moduleName, $loadType, $idName, $config );
        $this->_lastModule = $module;
        return $module;
    }
    // +-------------------------------------------------------------+
    function injectModule( $module, $injectName, $moduleName, $loadType=NULL, $idName='' ) {
        $injected = $this->getClean( $moduleName, $loadType, $idName );
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
        return $this->injectModule( $object, $injectName, $moduleName, $loadType, $idName );
    }
    // +-------------------------------------------------------------+
}