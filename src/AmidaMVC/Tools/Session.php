<?php
namespace AmidaMVC\Tools;

class Session
{
    static $session_start = FALSE;
    // +-------------------------------------------------------------+
    static function start() {
        if( !static::$session_start ) {
            session_start();
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
    static function get( $name ) {
        if( isset( $_SESSION ) && array_key_exists( $name,  $_SESSION ) ) {
            return $_SESSION[ $name ];
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}