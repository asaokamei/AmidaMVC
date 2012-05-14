<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\Application::simple(
    array(
        'site_title' => "Demo#3 - Source Code View",
        'ctrl_root' => realpath( '../src' ),
        '_init' => array(
            'router' => array(
                'indexes' => 'README.md',
            ),
        ),
    )
);
$app->setModuleOption( 'router', array( 'indexes' => 'README.md', ) );
$app->start();
