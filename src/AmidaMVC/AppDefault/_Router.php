<?php

class Router extends \AmidaMVC\Component\Router {
    // +-------------------------------------------------------------+
    static function fireRouterResult( $loadInfo ) {
        AmidaMVC\Framework\Event::fire(
            'Router::result', $loadInfo
        );
    }
}
