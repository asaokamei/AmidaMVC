<?php
namespace AmidaMVC\AppSimple;

class Auth implements \AmidaMVC\Framework\IModule
{
    /**
     * @var \AmidaMVC\Tools\AuthNot   a static class name for authentication.
     */
    var $_authClass = '\AmidaMVC\Tools\AuthNot';
    var $_defaultOptions = array(
        'authArea' => 'Auth',
        'password_file' => 'password',
        'login_file' => '',
    );
    var $option = array();
    var $_evaluateOn = array();
    // +-------------------------------------------------------------+
    /**
     * initialize class.
     * @param array $option   options to initialize.
     */
    function _init( $option=array() ) {
        if( isset( $option[ 'authClass' ] ) ) {
            $this->_authClass = $option[ 'authClass' ];
        }
        if( isset( $option[ 'evaluateOn' ] ) ) {
            $this->_evaluateOn = $option[ 'evaluateOn' ];
        }
        $this->option = array_merge( $this->_defaultOptions, $option );
        // set up auth class as well.
        $auth = $this->_authClass;
        $auth->_init( $this->option );
    }
    // +-------------------------------------------------------------+
    /**
     * default is to use route map first, then scan the file system
     * to determine which file to load.
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return array   $loadInfo for Loader.
     */
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
    {
        if( empty( $this->_evaluateOn ) ) {
            return TRUE;
        }
        // do the authentication
        $auth = $this->_authClass;
        $auth_success = $auth->getAuth();
        if( $this->matchPathInfo( $_ctrl, $this->_evaluateOn[ 'onPathInfo' ] ) ) {
            if( $auth_success ) {
                $doList = $this->_evaluateOn[ 'onSuccess' ];
            }
            else {
                $doList = $this->_evaluateOn[ 'onFail' ];
            }
            $this->doList( $_ctrl, $_pageObj, $doList );
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return bool
     */
    function action_loginForm( $_ctrl, &$_pageObj, $option=array() )
    {
        // generally, do nothing for loginForm action in auth.
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param $path
     * @return bool
     */
    function matchPathInfo( $_ctrl, $path ) {
        $pathInfo = $_ctrl->getPathInfo();
        if( $path == substr( $pathInfo, 0, strlen( $path ) ) ) {
            return TRUE;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $doList
     * @return bool
     */
    function doList( $_ctrl, $_pageObj, $doList ) {
        if( empty( $doList ) ) return TRUE;
        foreach( $doList as $method => $parameter ) {
            $args = array( $_ctrl, $_pageObj );
            if( is_array( $parameter ) ) {
                $args = array_merge( $args, $parameter );
            }
            else {
                $args[] = $parameter;
            }
            call_user_func_array( array( $this, $method ), $args );
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj     $_pageObj
     * @param string                          $url
     * @return bool
     */
    function redirect( $_ctrl, $_pageObj, $url ) {
        $_ctrl->redirect( $url );
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param string $message
     * @return bool
     */
    function setLoginForm( $_ctrl, $_pageObj, $message ) {
        // read login form template.
        // how can I pass a parameter/message to the loader???
        $_ctrl->setAction( '_loginForm' );
        $_ctrl->options[ 'loginForm_file' ] = $this->option[ 'login_file' ];
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param $after
     * @param $module
     * @param $name
     * @return bool
     */
    function addModuleAfter( $_ctrl, $_pageObj, $after, $module, $name ) {
        $_ctrl->addModuleAfter( $after, $module, $name );
        return TRUE;
    }
    // +-------------------------------------------------------------+
}