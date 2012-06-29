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
                    array( 'url' => 'src/',          'title' => 'src/ dir top' ),
                    array( 'url' => 'src/AmidaMVC/', 'title' => 'main code' ),
                    array( 'url' => 'src/www/',      'title' => 'shadow www' ),
                    array( 'divider' => TRUE ),
                    array( 'url' => 'vendor/',       'title' => 'vendors code' ),
                )
            ),
            array( 'url' => 'tests/', 'title' => 'tests' ),
            array( 'url' => 'demo/',  'title' => 'demo' ),
        )
    )
);
$app->setModuleOption( 'lang', array(
    'lang_list' => array( 'en', 'ja' ),
    'ctrl_root' => array(
        '_docs.{lang}', 'en' => FALSE,
    ),
    'match_url' => '^{lang}\\/',
) );
$app
    ->setOption( 'site_title', 'AmidaMVC PHP Framework' )
    ->setOption( 'site_sub_title', 'A PHP Framework turned into CMS. dah...' )
    ->start();