<?php

class Debug extends AmidaMVC\Component\Debug {
    static function actionDefault( $ctrl, &$data ) {
        if( in_array( '_dev', $ctrl->command ) ) {
            self::_init();
        }
    }
    // +-------------------------------------------------------------+
    static function _init() {
        parent::_init();
        self::setupListener();
    }
    // +-------------------------------------------------------------+
    static function setupListener() {
        /* TODO: this initialization should not be here!! but where? */
        \AmidaMVC\Framework\Event::hook(
            'Controller::dispatch',
            array( 'Debug', 'listener' )
        );
        \AmidaMVC\Framework\Event::hook(
            'Controller::start',
            array( 'Debug', 'listener' )
        );
        \AmidaMVC\Framework\Event::hook(
            'Router::result',
            array( 'Debug', 'listener' )
        );
    }
    // +-------------------------------------------------------------+
    static function listener() {
        $args  = func_get_args();
        $event = $args[0];
        $args  = array_slice( $args, 1 );
        if( count( $args ) === 1 ) {
            $args = $args[0];
        }
        if( $event == 'Controller::dispatch' ) {
            self::bug( 'head', "{$event}: {$args}" );
        }
        else {
            self::bug( 'table', $args, '[event]'.$event );
        }
    }
    // +-------------------------------------------------------------+
}
