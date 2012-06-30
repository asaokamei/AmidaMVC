<?php
require_once( __DIR__ . '/src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\App2::app();
$app->setCtrlRoot( __DIR__ );
$app->setModuleOption( 'router', array( 'indexes' => 'README.md' ) );
$app->setModuleOption( 'menus',
    array( 'menu' => array(
            array( 'url' => '',       'title' => 'home' ),
            array( 'url' => 'docs/',  'title' => 'docs' ),
            array( 'url' => 'src/',   'title' => 'src',
                'pages' => array(
                    array( 'url' => 'src/',          'title' => 'src_top' ),
                    array( 'url' => 'src/AmidaMVC/', 'title' => 'src_code' ),
                    array( 'url' => 'src/www/',      'title' => 'src_www' ),
                    array( 'divider' => TRUE ),
                    array( 'url' => 'vendor/',       'title' => 'src_vendor' ),
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
$app->get( '/demo/func', function() { return "#closure\n output from closure."; }, array( 'type' => 'markdown' ) );
$app
    ->setOption( 'site_title', 'AmidaMVC PHP Framework' )
    ->setOption( 'site_sub_title', 'A PHP Framework turned into CMS. dah...' )
    ->start();