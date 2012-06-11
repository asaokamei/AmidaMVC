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
    var $_session   = NULL;
    // +-------------------------------------------------------------+
    function __construct( $option=array() ) {
        $this->setup( $option );
        $this->_session = new Session();
    }
    function setup( $option=array() ) {
        if( isset( $option[ 'authArea' ] ) ) {
            $this->auth_id = $option[ 'authArea' ];
        }
    }
    function _init( $option=array() ) {
        $this->setup( $option );
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
        if( !isset( $_POST ) || 
            !isset( $_POST[ $this->act_name ] ) ||
            !isset( $_POST[ $this->login_name ] ) ||
            !isset( $_POST[ $this->login_pass ] ) ) {
            return FALSE;
        }
        if( $_POST[ $this->act_name ] !== $this->act_value ) return FALSE;
        if( $_POST[ $this->login_name ] !== $_POST[ $this->login_pass ] ) return FALSE;
        $auth_info = array(
            $this->login_name => $_POST[ $this->login_name ],
            $this->login_pass => $_POST[ $this->login_pass ],
            $this->act_name   => $_POST[ $this->act_name ],
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
