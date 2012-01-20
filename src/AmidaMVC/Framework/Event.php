<?php
namespace AmidaMVC\Framework;

/**
 * hooks to events.
 * @see
 * http://stackoverflow.com/questions/6846118/event-driven-architecture-and-hooks-in-php
 * @author Asao Kamei
 */
class Event
{
    static $hooks = array();
    // +--------------------------------------------------------------- +
    static public function hook( $event, $callback ) {
        if( !is_callable( $callback ) ) {
            throw new RuntimeException( "not callable hook to event '{$event}'" );
        }
        if( !isset( self::$hooks[ $event ] ) ) {
            static::$hooks[ $event ] = array();
        }
        static::$hooks[ $event ][] = $callback;
    }
    // +--------------------------------------------------------------- +
    static public function fire( $event ) {
        $args = func_get_args();
        if( isset( static::$hooks[$event] ) ) {
            foreach( static::$hooks[$event] as $callback ) {
                call_user_func_array( $callback, $args );
            }
        }
    }
    // +--------------------------------------------------------------- +
}