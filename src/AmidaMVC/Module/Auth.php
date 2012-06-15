<?php
namespace AmidaMVC\Module;

class Auth implements IfModule
{
    /**
     * @var \AmidaMVC\Tools\AuthNot   a static class name for authentication.
     */
    var $authArea = 'AuthNot';
    /**
     * @var \AmidaMVC\Tools\AuthNot   an object.
     */
    var $_auth = NULL;
    var $_defaultOptions = array(
        'authArea' => 'Auth',
        'password_file' => 'password',
        'login_file' => '',
    );
    var $option = array();
    var $_evaluateOn = array();
    /** @var \AmidaMVC\Framework\Controller */
    protected $_ctrl;
    /** @var \AmidaMVC\Framework\PageObj */
    protected $_pageObj;
    // +-------------------------------------------------------------+
    /**
     * initialize class.
     * @param array $option   options to initialize.
     */
    function __construct( $option=array() ) {
        $this->setup( $option );
    }
    function _init( $option=array() ) {
        $this->setup( $option );
    }
    function setup( $option=array() ) {
        if( isset( $option[ 'authArea' ] ) ) {
            $this->authArea = $option[ 'authArea' ];
        }
        if( isset( $option[ 'evaluateOn' ] ) ) {
            $this->_evaluateOn = $option[ 'evaluateOn' ];
        }
        $this->option = array_merge( $this->_defaultOptions, $option );
    }
    // +-------------------------------------------------------------+
    /**
     * default is to use route map first, then scan the file system
     * to determine which file to load.
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return array|mixed $loadInfo for Loader.
     */
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
    {
        $this->_ctrl = $_ctrl;
        $this->_pageObj = $_pageObj;
        if( empty( $this->_evaluateOn ) ) {
            return TRUE;
        }
        // do the authentication
        if( $this->matchPathInfo( $this->_evaluateOn[ 'onPathInfo' ] ) ) {
            // set up auth class as well.
            $this->_auth = $this->_ctrl->getServices()->get( $this->authArea );
            $auth_success = $this->_auth->getAuth();
            if( $auth_success ) {
                $doList = $this->_evaluateOn[ 'onSuccess' ];
            }
            else {
                $doList = $this->_evaluateOn[ 'onFail' ];
            }
            $this->doList( $doList );
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return bool
     */
    function action_loginForm( $_ctrl, &$_pageObj, $option=array() )
    {
        return TRUE;
    }
    // +-------------------------------------------------------------+
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
    /**
     * @param array $doList
     * @return bool
     */
    function doList( $doList ) {
        if( empty( $doList ) ) return TRUE;
        foreach( $doList as $method => $parameter ) {
            if( !is_array( $parameter ) ) {
                $parameter = array( $parameter );
            }
            call_user_func_array( array( $this, $method ), $parameter );
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string                          $url
     * @return bool
     */
    function redirect( $url ) {
        $this->_ctrl->redirect( $url );
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param $login_file
     * @return bool
     */
    function setLoginForm( $login_file ) {
        // read login form template.
        // how can I pass a parameter/message to the loader???
        $this->_ctrl->setAction( '_loginForm' );
        $this->_ctrl->options[ 'loginForm_file' ] = $login_file;
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param $after
     * @param $module
     * @param $name
     * @return bool
     */
    function addModuleAfter( $after, $module, $name ) {
        $this->_ctrl->addModuleAfter( $after, $module, $name );
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param $message
     */
    function logout( $message ) {
        $this->_auth->logout();
    }
    // +-------------------------------------------------------------+
}