<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php');

/**
 * TODO: demo as test site.
 *  before eating dog food, make test/demo site.
 */

$routes = array(
    '/' => array( 'file' => '_App.php', 'action' => 'default' ),
    '/route/:action' => array( 'file' => 'route/_App.php', 'action' => 'index' ),
);
AmidaMVC\Component\Router::set( $routes );

$data = array();
$ctrl = new AmidaMVC\Framework\Controller();
$ctrl
    ->loadDebug()
    ->addModel( 'Router', 'route' )
    ->addModel( 'Loader', 'load' )
    ->addModel( 'Viewer', 'view' )
;

$ctrl->start( $data );


