<?php
namespace AmidaMVC\Tools;
/**
 * Authentication class which does not really authorize. 
 * only requires login_id and password to match. 
 */

class AuthBasic
{
    var $auth_id    = 'AuthBasic_id';
    var $login_name = 'auth_name';
    var $login_pass = 'auth_pass';
    var $act_name   = 'auth_act';
    var $act_value  = 'authBasic';
    var $auth_info  = array();
    var $option     = array();
    var $isLoggedIn = FALSE;
    /** @var string     name of password file */
    var $password_file = NULL;
    /** @var \AmidaMVC\Tools\Session  */
    var $_session   = NULL;
    /** @var \AmidaMVC\Tools\Request */
    var $_request   = NULL;
    /** @var \AmidaMVC\Tools\Load */
    var $_load      = NULL;
    // +-------------------------------------------------------------+
    function __construct( $option=array() ) {
        $this->setup( $option );
    }
    function setup( $option=array() ) {
        if( isset( $option[ 'authArea' ] ) ) {
            $this->auth_id = $option[ 'authArea' ];
        }
        if( isset( $option[ 'password_file' ] ) ) {
            $this->password_file = $option[ 'password_file' ];
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
    function injectLoad( $load ) {
        $this->_load = $load;
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
    /**
     * @return array|bool
     */
    function _loadPassword() {
        if( !$pwFile = $this->_load->findFile( $this->password_file ) ) {
            return FALSE;
        }
        $data = $this->_load->getContentsByGet( $pwFile );
        $data = explode( "\n", $data );
        if( empty( $data ) ) return FALSE;
        $pwData = array();
        foreach( $data as $line ) {
            $split = preg_split( '/[\s]+/', $line );
            list( $user, $pass )  = explode( ':', $split[0] );
            $auth_info = array(
                $this->login_name => $user,
                $this->login_pass => $pass,
            );
            array_shift( $split );
            foreach( $split as $info ) {
                list( $name, $val ) = explode( ':', $info );
                $auth_info[ $name ] = $val;
            }
            $pwData[ $user ] = $auth_info;
        }
        return $pwData;
    }

    /**
     * @param $auth_info
     * @param $password
     * @return bool
     */
    function _matchPass( $auth_info, $password ) {
        $match = FALSE;
        $passToken = $auth_info[ $this->login_pass ];
        $userName  = $auth_info[ $this->login_name ];
        if( $passToken === $password ) {
            $match = TRUE;
        }
        elseif( $passToken === sha1( $passToken . $userName . $password ) ) {
            $match = TRUE;
        }
        return $match;
    }

    /**
     * @return array|bool
     */
    function _verifyPost() {
        $act_name   = $this->_request->getPost( $this->act_name,   'code' );
        $login_name = $this->_request->getPost( $this->login_name, 'code' );
        $login_pass = $this->_request->getPost( $this->login_pass, 'code' );
        if( !$act_name || !$login_name || !$login_pass  ) {
            return FALSE;
        }
        $pwData = $this->_loadPassword();
        $auth_info = FALSE;
        if( $pwData && isset( $pwData[ $login_name ] ) ) {
            if( $this->_matchPass( $pwData[ $login_name ], $login_pass ) ) {
                $auth_info = $pwData[ $login_name ];
                $auth_info[ $this->act_name ] = $this->act_value;
            }
        }
        else {
        }
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
