<?php
namespace AmidaMVC\Framework;

/**
 * Service Locator for AmidaMVC's modules.
 * kind of DI Container?
 */

class Services
{
    const COL_CLASS   = 0;
    const COL_TYPE    = 1;
    const COL_ID_NAME = 2;
    /**
     * @var array   options for creating module.
     *    array( service  => array(
     *            din => [ class, type, idName ],
     *            config => [],
     *            inject => []
     *        ),
     *        modName2 =>...
     *    )
     */
    private $_services    = array();
    private $_objects     = array();
    private $_lastObject  = NULL;
    private $_lastService = NULL;
    static private $_self = NULL;
    // +-------------------------------------------------------------+
    /**  */
    private function __construct() {
        $idName = NULL;
        $this->_objects[ 'GET' ][ '_self' ][ $idName ] = $this;
    }
    /**
     * @static
     * @return Services
     */
    static function getInstance() {
        if( !static::$_self ) {
            static::$_self = new static();
        }
        return static::$_self;
    }
    /**
     * @static
     * @return Services
     */
    static function start() {
        $self = static::getInstance();
        $self->_objects[ 'NEW' ] = array();
        return $self;
    }
    /**
     * @static
     * @return Services
     */
    static function resume() {
        return static::getInstance();
    }
    /**
     * @static
     */
    static function clean() {
        static::$_self = NULL;
    }
    // +-------------------------------------------------------------+
    /**
     * @param $service
     * @param $class
     * @param string $type
     * @param null|string $idName
     * @param null $cfg
     * @return Services
     */
    function setService( $service, $class, $type='GET', $idName=NULL, $cfg=NULL )
    {
        $din = array( $class, $type, $idName );
        if( isset( $this->_services[ $service ] ) ) {
            $this->_services[ $service ][ 'din' ] = $din;
            if( isset( $cfg ) ) {
                $this->_services[ $service ][ 'config' ] = $cfg;
            }
        }
        else {
            if( !is_array( $cfg ) ) $cfg = array();
            $this->_services[ $service ] = array(
                'din' => $din,
                'config' => $cfg,
                'inject' => array(),
            );
        }
        $this->_lastService = $service;
        return $this;
    }
    /**
     * @param $config
     * @param null $value
     * @return Services
     */
    function setConfig( $config, $value=NULL )
    {
        if( isset( $this->_lastService ) && isset( $this->_services[ $this->_lastService ] ) ) {
            if( is_array( $config ) ) {
                $this->_services[ $this->_lastService ][ 'config' ] = array_merge(
                    $this->_services[ $this->_lastService ][ 'config' ], $config
                );
            }
            elseif( isset( $value ) ) {
                $this->_services[ $this->_lastService ][ 'config' ][ $config ] = $value;
            }
            else {
                $this->_services[ $this->_lastService ][ 'config' ][] = $config;
            }
        }
        return $this;
    }
    /**
     * @param string $name
     * @param string $service
     * @param string $type
     * @param null|string   $id
     * @return Services
     */
    function setInject( $name, $service, $type='GET', $id=NULL )
    {
        if( isset( $this->_lastService ) && isset( $this->_services[ $this->_lastService ] ) ) {
            $this->_services[ $this->_lastService ][ 'inject' ][ $name ] = array(
                $service, $type, $id
            );
        }
        return $this;
    }
    /**
     * @param string $service
     * @param array  $din
     * @param array  $config
     * @param array  $inject
     * @return Services
     */
    function getService( $service, &$din, &$config, &$inject )
    {
        if( isset( $this->_services[ $service ] ) ) {
            $din    = $this->_services[ $service ][ 'din' ];
            $config = $this->_services[ $service ][ 'config' ];
            $inject = $this->_services[ $service ][ 'inject' ];
        }
        else {
            $din    = array( $service, 'GET', NULL );
            $config = array();
            $inject = array();
        }
        return $this;
    }
    /**
     * @param null|string $type
     * @param null|string $idName
     * @param array $cfg
     */
    function _prepDin( &$type, &$idName, &$cfg )
    {
        if( is_array( $type ) ) {
            $cfg = $type;
            $idName = NULL;
            $type = 'GET';
        }
        elseif( in_array( strtoupper( $type ), array( 'NEW', 'GET', 'STATIC' ) ) ) {
            $type = strtoupper( $type );
        }
        else {
            $idName = $type;
            $type = 'GET';
        }
        if( is_array( $idName ) ) {
            $cfg = $idName;
            $idName = NULL;
        }
    }
    // +-------------------------------------------------------------+
    /**
     * @param $service
     * @param null|string $type
     * @param null|string $idName
     * @param array $cfg
     * @return Services|object
     */
    function get( $service, $type=NULL, $idName=NULL, $cfg=array() )
    {
        $this->_prepDin( $type, $idName, $cfg );
        $object = $this->getClean( $service, $type, $idName, $cfg );
        $this->_lastObject = $object;
        return $object;
    }
    /**
     * @param string $service
     * @param null|string $type
     * @param null|string $idName
     * @param array $cfg
     * @return object|static
     */
    function getClean( $service, $type=NULL, $idName=NULL, $cfg=array() )
    {
        $this->getService( $service, $din, $config, $inject );
        $className = $din[ self::COL_CLASS ];
        if( !isset( $type   ) ) { $type   = $din[ self::COL_TYPE    ]; }
        if( !isset( $idName ) ) { $idName = $din[ self::COL_ID_NAME ]; }
        if( !empty( $cfg ) ) { $config = array_merge( $config, $cfg ); }
        if( is_object( $className ) ) {
            $object = $className;
        }
        elseif( $className instanceof \Closure ) {
            /** @var $className Closure */
            $object = $className( $this );
        }
        else if( $type == 'STATIC' ) {
            $object = $className;
        }
        else {
            if( isset( $this->_objects[ $type ][ $service ][ $idName ] ) ) {
                $object = $this->_objects[ $type ][ $service ][ $idName ];
            }
            else {
                $this->_objects[ $type ][ $service ][ $idName ] =
                $object = new $className( $config );
            }
        }
        $this->_injectAndInit( $object, $inject );
        return $object;
    }
    /**
     * @param object $object
     * @param array $inject
     */
    function _injectAndInit( $object, $inject )
    {
        if( !empty( $inject ) ) {
            foreach( $inject as $name => $din ) {
                $injected = call_user_func_array( array( $this, 'getClean' ), $din );
                $this->_inject( $object, $name, $injected );
            }
        }
        if( method_exists( $object, '_init' ) ) {
            call_user_func( array( $object, '_init' ) );
        }
    }
    /**
     * @param object $object
     * @param string $name
     * @param object $injected
     * @return Services
     */
    function _inject( $object, $name, $injected )
    {
        $method = 'inject' . ucwords( $name );
        if( method_exists( $object, $method ) ) {
            call_user_func( array( $object, $method ), $injected );
        }
        elseif( method_exists( $object, 'inject' ) ) {
            call_user_func( array( $object, 'inject' ), $name, $injected );
        }
        return $this;
    }
    /**
     * @param $name
     * @param $service
     * @param null|string $type
     * @param null|string $idName
     * @param array $cfg
     * @return Services
     */
    function inject( $name, $service, $type=NULL, $idName=NULL, $cfg=array() )
    {
        $injected = $this->getClean( $service, $type, $idName, $cfg );
        $object = $this->_lastObject;
        $this->_inject( $object, $name, $injected );
        return $this;
    }
    // +-------------------------------------------------------------+
}