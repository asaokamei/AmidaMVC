<?php
namespace AmidaMVC\Tools;
/**
 * Session wrapper.  
 */

class Session
{
    /**
     * @var bool  flag to check if session started.
     */
    static $session_start = FALSE;
    /**
     * @var null   temporary saves generated token. 
     */
    static $session_token = NULL;
    /**  name of tokens stored in Session.     */
    const  TOKEN_ID   = 'session.token.ids';
    /**  name of token send via post */
    const  TOKEN_NAME = 'sessionTokenValue_';
    // +-------------------------------------------------------------+
    static function start() {
        if( !static::$session_start ) {
            session_start();
            static::$session_start = TRUE;
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    static function set( $name, $value ) {
        if( !isset( $_SESSION ) ) return FALSE;
        $_SESSION[ $name ] = $value;
        return $value;
    }
    // +-------------------------------------------------------------+
    static function del( $name ) {
        if( isset( $_SESSION ) && array_key_exists( $name,  $_SESSION ) ) {
            unset( $_SESSION[ $name ] );
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    static function get( $name ) {
        if( isset( $_SESSION ) && array_key_exists( $name,  $_SESSION ) ) {
            return $_SESSION[ $name ];
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    //  managing token for CSRF. 
    // +-------------------------------------------------------------+
    static function pushToken() {
        $token = md5( 'sess.dumb' . time() . $_SERVER["SCRIPT_FILENAME"] );
        static::_pushToken( $token );
        static::$session_token = $token;
        return $token;
    }
    // +-------------------------------------------------------------+
    static function _pushToken( $token ) {
        static::start();
        if( !isset( $_SESSION[ static::TOKEN_ID ] ) ) {
            $_SESSION[ static::TOKEN_ID ] = array();
        }
        $max_token = 20;
        $_SESSION[ static::TOKEN_ID ][] = $token;
        if( count( $_SESSION[ static::TOKEN_ID ] ) > $max_token ) {
            $num_remove = count( $_SESSION[ static::TOKEN_ID ] ) - $max_token;
            $_SESSION[ static::TOKEN_ID ] =
                array_slice( $_SESSION[ static::TOKEN_ID ], $num_remove );
        }
    }
    // +-------------------------------------------------------------+
    static function popToken() {
        $name  = static::TOKEN_NAME;
        $value = static::$session_token;
        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\">";
    }
    // +-------------------------------------------------------------+
    static function verifyToken() {
        static::start();
        $token = $_POST[ static::TOKEN_NAME ];
        if( $token && !empty( $_SESSION[ static::TOKEN_ID ] ) ) {
            if( in_array( $token, $_SESSION[ static::TOKEN_ID ] ) ) {
                foreach( $_SESSION[ static::TOKEN_ID ] as $k=>$v ) {
                    if( $v === $token ) {
                        unset( $_SESSION[ static::TOKEN_ID ][$k] );
                    }
                }
                $_SESSION[ static::TOKEN_ID ] = array_values( $_SESSION[ static::TOKEN_ID ] );
                return TRUE;
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}