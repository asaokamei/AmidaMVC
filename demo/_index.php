<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php');

/**
 * AmidaMVC's Demo Site...
 */

$routes = array(
    '/text' => array( 'file' => 'text.php', 'action' => 'default' ),
    '/demo.css' => array( 'file' => 'demo.css', 'action' => 'default' ),
    '/index.md' => array( 'file' => 'index.md', 'action' => 'default' ),
    '/' => array( 'file' => 'index.html', 'action' => 'default' ),
    '/:action' => array( 'file' => 'index.html', 'action' => 'default' ),
    '/route/:action' => array( 'file' => 'route/_App.php', 'action' => 'index' ),
);


AmidaMVC\Framework\Route::set( $routes );

//AmidaMVC\Component\Debug::_init();

$data = new \AmidaMVC\Component\SiteObj();
$ctrl = new \AmidaMVC\Framework\Controller();
$ctrl
    ->addModel( 'Debug',  'debug' )
    ->addModel( 'Router', 'router' )
    ->addModel( 'Loader', 'loader' )
    ->addModel( 'Render', 'render' )
;

$ctrl->start( $data );


