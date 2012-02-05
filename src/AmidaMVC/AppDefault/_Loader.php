<?php

class Loader extends \AmidaMVC\Component\Loader {
    // +-------------------------------------------------------------+
    function fireLoad( $loadInfo ) {
        \AmidaMVC\Framework\Event::fire( 'Loader::load', $loadInfo );
    }
    // +-------------------------------------------------------------+
}
