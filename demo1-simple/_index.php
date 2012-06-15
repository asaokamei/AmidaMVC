<?php
error_reporting( E_ALL );
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\App2::app();
$app    ->get( '/func', function() {
        return "#Closure Output\n" .
               "This is a response from a closure function in markdown text.";
        }, array( 'type' => 'markdown' )
    );
$app->setCtrlRoot( __DIR__ );
$app
    ->setOption( 'site_title', 'AmidaMVC PHP Framework')
    ->start();
