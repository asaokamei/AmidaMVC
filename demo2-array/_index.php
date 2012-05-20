<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

$di = \AmidaMVC\Framework\Container::start();
$di->setModule( '\AmidaMVC\Tools\Load', '\AmidaMVC\Tools\LoadArray', 'static' );

/** @var $load \AmidaMVC\Tools\LoadArray */
$load = $di->get( '\AmidaMVC\Tools\Load' );
$load::setFiles( array(
    '/path/to/' => NULL,
    '/path/to/index.md' => "
#Top of Array
hello from Array!

*   [output from a closure](func)",
    '' => '',
) );

$app = \AmidaMVC\Application\Application::simple(
    array(
        'site_title' => "Demo#2 - Simple Demo Using Array Data",
        'template_file' => NULL,
        'ctrl_root' => '/path/to/',
    )
);
$app->get( '/func',
    function() {
        $content = "#Closure Output\nfrom closure."; return $content;
    },
    array( 'type' => 'markdown' )
);
$app->start();
