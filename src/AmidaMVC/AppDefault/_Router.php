<?php

class Router extends \AmidaMVC\Component\Router {
    // +-------------------------------------------------------------+
    static function fireRouterResult( $loadInfo ) {
        Event::fire(
            'Router::result', $loadInfo
        );
    }
}
