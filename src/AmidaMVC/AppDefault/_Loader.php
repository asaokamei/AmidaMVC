<?php

class Loader extends \AmidaMVC\Component\Loader {
    // +-------------------------------------------------------------+
    static function fireLoad( $loadInfo ) {
        \AmidaMVC\Framework\Event::fire( 'Loader::load', $loadInfo );
    }
    // +-------------------------------------------------------------+
}
