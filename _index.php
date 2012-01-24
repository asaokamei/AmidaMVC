<?php
require_once(__DIR__ . '/src/AmidaMVC/bootstrap.php');

/**
 * AmidaMVC's Demo Site...
 */

$routes = array(
    '/demo.css' => array( 'file' => 'demo.css', 'action' => 'default' ),
    '/' => array( 'file' => 'README.md', 'action' => 'default' ),
    '/route/:action' => array( 'file' => 'route/_App.php', 'action' => 'index' ),
);


AmidaMVC\Tools\Route::set( $routes );

//AmidaMVC\Component\Debug::_init();

$data = new \AmidaMVC\Component\SiteObj();
$ctrl = new \AmidaMVC\Framework\Controller();
$ctrl
    ->addModel( 'Config', 'config' )
    ->addModel( 'Debug',  'debug' )
    ->addModel( 'Router', 'router' )
    ->addModel( 'Loader', 'loader' )
    ->addModel( 'Render', 'render' )
;

$ctrl->start( $data );


