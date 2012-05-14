<?php
require_once( __DIR__ . '/src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\Application::simple(
    array(
        'site_title' => "AmidaMVC PHP Framework",
        'template_file' => '/demo1-simple/template.php'
    )
);
$app->start();