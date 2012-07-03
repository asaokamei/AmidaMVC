<?php
namespace AmidaMVC\Tools;
/**
 * Authentication class which does not really authorize. 
 * only requires login_id and password to match. 
 */

class AuthNot
{
    var $auth_id    = 'AuthNot_id';
    var $login_name = 'auth_name';
    var $login_pass = 'auth_pass';
    var $act_name   = 'auth_act';
    var $act_value  = 'authNot';
    var $auth_info  = array();
    var $option     = array();
    var $isLoggedIn = FALSE;
    /** @var \AmidaMVC\Tools\Session  */
    var $_session   = NULL;
    /** @var \AmidaMVC\Tools\Request */
    var $_request   = NULL;
    // +-------------------------------------------------------------+
    function __construct( $option=array() ) {
        $this->setup( $option );
    }
    function setup( $option=array() ) {
        if( isset( $option[ 'authArea' ] ) ) {
            $this->auth_id = $option[ 'authArea' ];
        }
    }
    function _init( $option=array() ) {
        $this->setup( $option );
    }
    function injectSession( $session ) {
        $this->_session = $session;
    }
    function injectRequest( $request ) {
        $this->_request = $request;
    }
    // +-------------------------------------------------------------+
    function isLoggedIn() {
        return $this->isLoggedIn;
    }
    // +-------------------------------------------------------------+
    function getAuth() {
        if( $auth_info = $this->_verifySession() ) {
            $auth_info[ 'auth_method' ] = 'session';
            $this->auth_info = $auth_info;
        }
        elseif( $auth_info = static::_verifyPost() ) {
            $auth_info[ 'auth_method' ] = 'post';
            $this->auth_info = $auth_info;
            $this->_saveSession( $auth_info );
        }
        return $auth_info;
    }
    // +-------------------------------------------------------------+
    function logout(){
        $this->_session->start();
        $this->_session->del( $this->auth_id );
    }
    // +-------------------------------------------------------------+
    function _verifyPost() {
        $act_name   = $this->_request->getPost( $this->act_name,   'code' );
        $login_name = $this->_request->getPost( $this->login_name, 'code' );
        $login_pass = $this->_request->getPost( $this->login_pass, 'code' );
        if( !$act_name || !$login_name || !$login_pass  ) {
            return FALSE;
        }
        if( $act_name !== $this->act_value ) return FALSE;
        if( $login_name !== $login_pass ) return FALSE;
        $auth_info = array(
            $this->login_name => $login_name,
            $this->login_pass => $login_pass,
            $this->act_name   => $act_name,
        );
        $this->isLoggedIn = TRUE;
        return $auth_info;
    }
    // +-------------------------------------------------------------+
    function _saveSession( $auth_info ) {
        $this->_session->set( $this->auth_id, $auth_info );
    }
    // +-------------------------------------------------------------+
    function _verifySession() {
        $this->_session->start();
        $auth_info = $this->_session->get( $this->auth_id );
        if( $auth_info && 
            $auth_info[ $this->act_name ] == $this->act_value ) {
            $this->isLoggedIn = TRUE;
            return $auth_info;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}
