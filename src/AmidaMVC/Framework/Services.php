<?php
namespace AmidaMVC\Framework;

/**
 * Module Locator.
 * Service Locator or DI Container for AmidaMVC's modules.
 */

class Services
{
    /**
     * @var array   options for creating module.
     *    array( modName  => array(
     *            din => [ class, type, idName ],
     *            config => [],
     *        ),
     *        modName2 =>...
     *    )
     */
    const COL_CLASS   = 0;
    const COL_TYPE    = 1;
    const COL_ID_NAME = 2;
    private $_modules = array();
    private $_objects = array();
    private $_lastObj = NULL;
    private $_lastMod = NULL;
    // +-------------------------------------------------------------+
    private function __construct() {}
    static function getInstance() {}
    static function start() {}
    static function resume() {}
    static function clean() {}
    // +-------------------------------------------------------------+
    function setModule( $moduleName, $class, $type='GET', $idName=NULL, $cfg=NULL ) {
        $din = array( $class, $type, $idName );
        if( isset( $this->_modules[ $moduleName ] ) ) {
            $this->_modules[ $moduleName ][ 'din' ] = $din;
            if( isset( $cfg ) ) {
                $this->_modules[ $moduleName ][ 'config' ] = $cfg;
            }
        }
        else {
            if( !is_array( $cfg ) ) $cfg = array();
            $this->_modules = array(
                'din' => $din,
                'config' => $cfg,
                'inject' => array(),
            );
        }
        return $this;
    }
    function setConfig( $config, $value=NULL ) {}
    function setInject( $injectName, $mName, $type='GET', $id=NULL ) {}

    /**
     * @param string $moduleName
     * @param array  $din
     * @param array  $config
     * @param array  $inject
     * @return Services
     */
    function getModule( $moduleName, &$din, &$config, &$inject ) {
        if( isset( $this->_modules[ $moduleName ] ) ) {
            $din    = $this->_modules[ $moduleName ][ 'din' ];
            $config = $this->_modules[ $moduleName ][ 'config' ];
            $inject = $this->_modules[ $moduleName ][ 'inject' ];
        }
        else {
            $din    = array( $moduleName, 'GET', NULL );
            $config = array( '_undefined' => TRUE );
            $inject = array();
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * @param $moduleName
     * @param null|string $type
     * @param null|string $idName
     * @param array $cfg
     * @return Services|object
     */
    function get( $moduleName, $type=NULL, $idName=NULL, $cfg=array() ) {
        $module = $this->getClean( $moduleName, $type, $idName, $cfg );
        $this->_lastObj = $module;
        return $module;
    }
    /**
     * @param string $moduleName
     * @param null|string $type
     * @param null|string $idName
     * @param array $cfg
     * @return object|static
     */
    function getClean( $moduleName, $type=NULL, $idName=NULL, $cfg=array() ) {
        $this->getModule( $moduleName, $din, $config, $inject );
        $className = $din[ self::COL_CLASS ];
        if( !isset( $type   ) ) { $type = $din[ self::COL_TYPE    ]; }
        if( !isset( $idName ) ) { $idName= $din[ self::COL_ID_NAME ]; }
        if( !empty( $cfg ) ) { $config = array_merge( $config, $cfg ); }
        if( is_object( $className ) ) {
            $module = $className;
        }
        elseif( $className instanceof \Closure ) {
            /** @var $className Closure */
            $module = $className( $this );
        }
        else if( $type == 'static' ) {
            $module = $className;
        }
        else {
            if( isset( $this->_objects[ $type ][ $moduleName ][ $idName ] ) ) {
                $module = $this->_objects[ $type ][ $moduleName ][ $idName ];
            }
            else {
                $this->_objects[ $type ][ $moduleName ][ $idName ] =
                $module = new $className( $config );
            }
        }
        $this->injectAndInit( $module, $inject );
        return $module;
    }
    function injectAndInit( $module, $inject ) {
        if( !empty( $inject ) ) {
            foreach( $inject as $name => $din ) {
                $injMod = call_user_func_array( array( $this, 'getClean' ), $din );
                $method = 'inject' . ucwords( $name );
                if( method_exists( $module, $method ) ) {
                    call_user_func( array( $module, $method ), $injMod );
                }
                elseif( method_exists( $module, 'inject' ) ) {
                    call_user_func( array( $module, 'inject' ), $name, $injMod );
                }
            }
        }
        if( method_exists( $module, '_init' ) ) {
            call_user_func( array( $module, '_init' ) );
        }
    }
    function inject( $name, $moduleName, $type=NULL, $idName=NULL, $cfg=array() ) {
        $injMod = $this->getClean( $moduleName, $type, $idName, $cfg );
        $module = $this->_lastObj;
        $this->injectAndInit( $module, $injMod );
        return $this;
    }
    // +-------------------------------------------------------------+
}