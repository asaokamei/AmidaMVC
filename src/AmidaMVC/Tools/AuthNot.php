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
    static $_self    = array();
    static $_this    = NULL;
    // +-------------------------------------------------------------+
    function __construct() {
        static::$_this = $this;
    }
    // +-------------------------------------------------------------+
    static function getThisInstance() {
        if( !isset( static::$_this ) ) {
            new static();
        }
        return static::$_this;
    }
    // +-------------------------------------------------------------+
    static function getInstance( $authArea=NULL ) {
        if( !isset( static::$_self[ $authArea ] ) ) {
            static::$_self[ $authArea ] = new self();
        }
        return static::$_self[ $authArea ];
    }
    // +-------------------------------------------------------------+
    static function isThisLoggedIn() {
        if( isset( static::$_this ) ) {
            $auth = static::$_this;
            return $auth->isLoggedIn;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    static function isLoggedIn( $authArea ) {
        if( isset( static::$_self[ $authArea ] ) ) {
            $auth = static::$_self[ $authArea ];
            return $auth->isLoggedIn;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    function _init( $option=array() ) {
        $this->option = $option;
    }
    // +-------------------------------------------------------------+
    function getAuth() {
        if( $auth_info = static::_verifySession() ) {
            $auth_info[ 'auth_method' ] = 'session';
            $this->auth_info = $auth_info;
        }
        elseif( $auth_info = static::_verifyPost() ) {
            $auth_info[ 'auth_method' ] = 'post';
            $this->auth_info = $auth_info;
            static::_saveSession( $auth_info );
        }
        return $auth_info;
    }
    // +-------------------------------------------------------------+
    function logout(){
        Session::start();
        Session::del( $this->auth_id );
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
        Session::set( $this->auth_id, $auth_info );
    }
    // +-------------------------------------------------------------+
    function _verifySession() {
        Session::start();
        $auth_info = Session::get( $this->auth_id );
        if( $auth_info && 
            $auth_info[ $this->act_name ] == $this->act_value ) {
            return $auth_info;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}
