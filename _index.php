<?php
require_once(__DIR__ . '/src/AmidaMVC/bootstrap.php');

/**
 * AmidaMVC's Demo Site...
 */

$routes = array(
    '/demo.css' => array( 'file' => 'demo.css', 'action' => 'default' ),
    '/example.html' => array( 'file' => 'demo/index.html', 'action' => 'default'),
    '/' => array( 'file' => 'index.md', 'action' => 'default' ),
    '/demo/:action' => array( 'file' => 'demo/_App.php', 'action' => 'index' ),
);


AmidaMVC\Tools\Route::set( $routes );

AmidaMVC\Component\Debug::_init();

$data = new \AmidaMVC\Component\SiteObj();
$ctrl = new \AmidaMVC\Framework\Controller();
$ctrl
    ->addComponent( 'Config', 'config' )
    ->addComponent( 'Debug',  'debug' )
    ->addComponent( 'Auth',   'auth' )
    ->addComponent( 'Router', 'router' )
    ->addComponent( 'Loader', 'loader' )
    ->addComponent( 'Render', 'render' )
;

$ctrl->start( $data );


