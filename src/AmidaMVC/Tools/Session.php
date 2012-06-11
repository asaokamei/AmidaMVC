<?php
namespace AmidaMVC\Tools;
/**
 * Session wrapper.  
 */

class Session
{
    /** @var bool  flag to check if session started. */
    protected $session_start = FALSE;
    /** @var null   temporary saves generated token. */
    protected $session_token = NULL;
    /**  name of tokens stored in Session.     */
    const  TOKEN_ID   = 'session..token.ids';
    /**  name of token send via post */
    const  TOKEN_NAME = 'sessionTokenValue_';
    /** @var array|bool   where session data is */
    protected $_session = NULL;
    // +-------------------------------------------------------------+
    function __construct( $config=NULL ) {
        $this->start();
        if( isset( $config ) && is_array( $config ) ) {
            $this->_session = $config;
        }
        else {
            $this->_session = &$_SESSION;
        }
    }
    // +-------------------------------------------------------------+
    function start() {
        if( !$this->session_start ) {
            session_start();
            $this->session_start = TRUE;
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    function set( $name, $value ) {
        if( !isset( $this->_session ) ) return FALSE;
        $this->_session[ $name ] = $value;
        return $value;
    }
    // +-------------------------------------------------------------+
    function del( $name ) {
        if( isset( $this->_session ) && array_key_exists( $name,  $this->_session ) ) {
            unset( $this->_session[ $name ] );
        }
        return TRUE;
    }
    // +-------------------------------------------------------------+
    function get( $name ) {
        if( isset( $this->_session ) && array_key_exists( $name,  $this->_session ) ) {
            return $this->_session[ $name ];
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    //  managing token for CSRF. 
    // +-------------------------------------------------------------+
    function pushToken() {
        $token = md5( 'session.dumb' . time() . mt_rand(1,100*100) . __DIR__ );
        $this->_pushToken( $token );
        $this->session_token = $token;
        return $token;
    }
    // +-------------------------------------------------------------+
    function _pushToken( $token ) {
        static::start();
        if( !isset( $this->_session[ static::TOKEN_ID ] ) ) {
            $this->_session[ static::TOKEN_ID ] = array();
        }
        $max_token = 20;
        $this->_session[ static::TOKEN_ID ][] = $token;
        if( count( $this->_session[ static::TOKEN_ID ] ) > $max_token ) {
            $num_remove = count( $this->_session[ static::TOKEN_ID ] ) - $max_token;
            $this->_session[ static::TOKEN_ID ] =
                array_slice( $this->_session[ static::TOKEN_ID ], $num_remove );
        }
    }
    // +-------------------------------------------------------------+
    function popToken() {
        $name  = static::TOKEN_NAME;
        $value = $this->session_token;
        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\">";
    }
    // +-------------------------------------------------------------+
    function verifyToken() {
        static::start();
        $token = $_POST[ static::TOKEN_NAME ];
        if( $token && !empty( $this->_session[ static::TOKEN_ID ] ) ) {
            if( in_array( $token, $this->_session[ static::TOKEN_ID ] ) ) {
                foreach( $this->_session[ static::TOKEN_ID ] as $k=>$v ) {
                    if( $v === $token ) {
                        unset( $this->_session[ static::TOKEN_ID ][$k] );
                    }
                }
                $this->_session[ static::TOKEN_ID ] = array_values( $this->_session[ static::TOKEN_ID ] );
                return TRUE;
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}