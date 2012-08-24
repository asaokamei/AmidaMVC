<?php
namespace AmidaMVC\Module;

class Config implements IfModule
{
    /** @var \AmidaMVC\Framework\Controller */
    var $_ctrl;
    /** @var \AmidaMVC\Framework\PageObj */
    var $_pageObj;
    /** @var array */
    var $config = array();
    /** @var mixed */
    var $return;

    // +-------------------------------------------------------------+
    /**
     * @param array $option
     */
    function __construct( $option=array() ) {
        $this->setup( $option );
    }

    /**
     * @param array $option
     * @return void
     */
    public function _init( $option = array() ) {
        $this->setup( $option );
    }
    /**
     * @param array $option
     * @return void
     */
    public function setup( $option ) {
        $this->config = array_merge( $this->config, $option );
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $extra
     * @return mixed
     */
    public function actionDefault( $_ctrl, &$_pageObj, $extra = array() )
    {
        $this->_ctrl = $_ctrl;
        $this->_pageObj = $_pageObj;
        if( !is_array( $this->config ) ) return TRUE;
        if( isset( $this->config[ 'onPathInfo' ] ) ) {
            $this->match( $this->config[ 'onPathInfo' ], $this->config );
        }
        else {
            foreach( $this->config as $config ) {
                if( isset( $config[ 'onPathInfo' ] ) ) {
                    $this->match( $config[ 'onPathInfo' ], $config );
                }
            }
        }
        return TRUE;
    }

    /**
     * @param string $path
     * @param array $config
     * @return void
     */
    public function match( $path, $config )
    {
        if( $this->matchPathInfo( $path ) && isset( $config[ 'evaluate' ] ) ) {
            $this->return = $this->evaluate( $config[ 'evaluate' ] );
            if( isset( $config[ 'onAny' ] ) ) {
                $this->evaluate( $config[ 'onAny' ] );
            }
            if( isset( $config[ 'onFail' ] ) && !$this->return ) {
                $this->evaluate( $config[ 'onFail' ] );
            }
            if( isset( $config[ 'onSuccess' ] ) && $this->return ) {
                $this->evaluate( $config[ 'onSuccess' ] );
            }
        }
    }

    /**
     * @param mixed $evaluate
     * @return mixed
     */
    public function evaluate( $evaluate )
    {
        $return = NULL;
        if( is_callable( $evaluate ) ) {
            $return = $evaluate( $this );
        }
        elseif( is_array( $evaluate ) && isset( $evaluate[0] ) && !is_array( $evaluate[0] ) ) {
            $object = $this->_ctrl->getServices()->get( $evaluate[0] );
            $method = $evaluate[1];
            $return = $object->$method( $this );
        }
        elseif( is_array( $evaluate ) ) {
            foreach( $evaluate as $ev ) {
                $return = $this->evaluate( $ev );
            }
        }
        else {
            $return = $evaluate;
        }
        return $return;
    }
    /**
     * @param array|string $path
     * @return bool
     */
    function matchPathInfo( $path ) {
        $pathInfo = '/' . $this->_ctrl->getPathInfo();
        if( !is_array( $path ) ) {
            $path = array( $path );
        }
        foreach( $path as $p ) {
            if( $p == substr( $pathInfo, 0, strlen( $p ) ) ) {
                return TRUE;
            }
        }
        return FALSE;

    }
    // +-------------------------------------------------------------+
}
