<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php');

/**
 * TODO: demo as test site.
 *  before eating dog food, make test/demo site.
 */

$routes = array(
    '/' => array( 'file' => '_App.php', 'action' => 'default' ),
    '/:action' => array( 'file' => '_App.php', 'action' => 'default' ),
    '/route/:action' => array( 'file' => 'route/_App.php', 'action' => 'index' ),
);


AmidaMVC\Component\Router::set( $routes );

AmidaMVC\Component\Debug::_init();

$data = array();
$ctrl = new AmidaMVC\Framework\Controller();
$ctrl
    ->loadModel( 'Debug' )
    ->addModel( 'Router', 'route' )
    ->addModel( 'Loader', 'load' )
    ->addModel( 'Viewer', 'view' )
;

$ctrl->start( $data );


