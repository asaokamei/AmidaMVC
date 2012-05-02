<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\AppSimple\Application::getInstance(
    array(
        'site_title' => "Demo#3 - Source Code View",
        'ctrl_root' => realpath( '../src' ),
        '_router' => array(
            'indexes' => 'README.md',
        ),
    )
);
$app->separateCommands();
$app->start();
