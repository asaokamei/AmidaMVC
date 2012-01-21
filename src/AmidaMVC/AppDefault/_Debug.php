<?php

class Debug extends AmidaMVC\Component\Debug {
    static function _init() {
        parent::_init();
        /* TODO: this initialization should not be here!! but where? */
        \AmidaMVC\Framework\Event::hook(
            'Controller::dispatch',
            array( 'AmidaMVC\\Component\\Debug', 'listener' )
        );
        \AmidaMVC\Framework\Event::hook(
            'Router::result',
            array( 'AmidaMVC\\Component\\Debug', 'listener' )
        );
        return static::$self;
    }
}
