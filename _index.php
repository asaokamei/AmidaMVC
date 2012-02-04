<?php
require_once(__DIR__ . '/src/AmidaMVC/bootstrap.php');

/**
 * AmidaMVC's Demo Site...
 */

$routes = array(
    '/demo.css' => array( 'file' => 'demo.css', 'action' => 'default' ),
    '/example.html' => array( 'file' => 'demo/index.html', 'action' => 'default'),
    '/' => array( 'file' => 'index.md', 'action' => 'default' ),
    '/indexView' => array( 'file' => '_index.php', 'action' => 'default' ),
    '/todo/toggle/:id' => array( 'file' => 'demo/todo/_App.php', 'action' => 'toggle' ),
    '/todo/detail/:id' => array( 'file' => 'demo/todo/_App.php', 'action' => 'detail' ),
    '/todo/put/:id' => array( 'file' => 'demo/todo/_App.php', 'action' => 'put' ),
    '/todo/:action' => array( 'file' => 'demo/todo/_App.php', 'action' => 'list' ),
);


AmidaMVC\Tools\Route::set( $routes );

AmidaMVC\Component\Debug::_init();

$ctrl = new \AmidaMVC\Framework\Controller();
$ctrl    ->actionFatal();
$ctrl
    ->addComponent( 'Config', 'config' )
    ->addComponent( 'Debug',  'debug' )
    ->addComponent( 'Auth',   'auth' )
    ->addComponent( 'Router', 'router' )
    ->addComponent( 'Loader', 'loader' )
    ->addComponent( 'Render', 'render' )
;
$data = \AmidaMVC\Component\SiteObj::getInstance();
$ctrl->start( $data );


