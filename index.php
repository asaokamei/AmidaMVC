<?php
require_once( __DIR__ . '/src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\App2::app();
$app
    ->setOption( 'site_title', 'AmidaMVC PHP Framework' )
    ->start();