<?php
require_once( '../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\Application::simple(
    array(
        'site_title' => "Demo#1 - Simple Site",
    )
);
$app->get( '/func', function() {
    return "#Closure Output\n" .
           "This is a response from a closure function in markdown text.";
    }, array( 'type' => 'markdown' )
);
$app->start();
