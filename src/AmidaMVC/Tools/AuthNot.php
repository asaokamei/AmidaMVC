<?php
namespace AmidaMVC\Tools;
/**
 * Authentication class which does not really authorize. 
 * only requires login_id and password to match. 
 */

class AuthNot
{
    static $auth_id    = 'AuthNot_id';
    static $login_name = 'auth_name';
    static $login_pass = 'auth_pass';
    static $act_name   = 'auth_act';
    static $act_value  = 'authNot';
    static $auth_info  = array();
    // +-------------------------------------------------------------+
    static function getAuth() {
        if( $auth_info = static::_verifySession() ) {
            $auth_info[ 'auth_method' ] = 'session';
            static::$auth_info = $auth_info;
        }
        elseif( $auth_info = static::_verifyPost() ) {
            $auth_info[ 'auth_method' ] = 'post';
            static::$auth_info = $auth_info;
            static::_saveSession( $auth_info );
        }
        return $auth_info;
    }
    // +-------------------------------------------------------------+
    static function logout(){
        Session::start();
        Session::del( static::$auth_id );
    }
    // +-------------------------------------------------------------+
    static function _verifyPost() {
        if( !isset( $_POST ) || 
            !isset( $_POST[ static::$act_name ] ) || 
            !isset( $_POST[ static::$login_name ] ) ||
            !isset( $_POST[ static::$login_pass ] ) ) {
            return FALSE;
        }
        if( $_POST[ static::$act_name ] !== static::$act_value ) return FALSE;
        if( $_POST[ static::$login_name ] !== $_POST[ static::$login_pass ] ) return FALSE;
        $auth_info = array(
            static::$login_name => $_POST[ static::$login_name ],
            static::$login_pass => $_POST[ static::$login_pass ],
            static::$act_name   => $_POST[ static::$act_name ],
        );
        return $auth_info;
    }
    // +-------------------------------------------------------------+
    static function _saveSession( $auth_info ) {
        Session::set( static::$auth_id, $auth_info );
    }
    // +-------------------------------------------------------------+
    static function _verifySession() {
        Session::start();
        $auth_info = Session::get( static::$auth_id );
        if( $auth_info && 
            $auth_info[ static::$act_name ] == static::$act_value ) {
            return $auth_info;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}
