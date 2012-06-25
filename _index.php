<?php
require_once( __DIR__ . '/src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\App2::app();
$app->setCtrlRoot( __DIR__ );
$app->setModuleOption( 'router', array( 'indexes' => 'README.md' ) );
$app->setModuleOption( 'menus',
    array( 'menu' => array(
            array( 'url' => '',       'title' => 'Home' ),
            array( 'url' => 'docs/',  'title' => 'documents' ),
            array( 'url' => 'src/',   'title' => 'source code',
                'pages' => array(
                    array( 'url' => 'src/',          'title' => 'src top' ),
                    array( 'url' => 'src/AmidaMVC/', 'title' => 'main code' ),
                    array( 'url' => 'src/www/',      'title' => 'shadow www' ),
                    array( 'url' => 'vendor/',       'title' => 'vendors code' ),
                )
            ),
            array( 'url' => 'tests/', 'title' => 'tests' ),
            array( 'url' => 'demo/',  'title' => 'demo' ),
        )
    )
);
$app
    ->setOption( 'site_title', 'AmidaMVC PHP Framework' )
    ->start();