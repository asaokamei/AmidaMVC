<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php');

/**
 * AmidaMVC's Demo Site...
 */

$routes = array(
    '/' => array( 'file' => '_App.php', 'action' => 'default' ),
    '/:action' => array( 'file' => '_App.php', 'action' => 'default' ),
    '/route/:action' => array( 'file' => 'route/_App.php', 'action' => 'index' ),
);


AmidaMVC\Framework\Route::set( $routes );

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


