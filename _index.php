<?php
require_once( __DIR__ . '/src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\App::cms();
$app->setCtrlRoot( __DIR__ );
$app->setModuleOption( 'router', array( 'indexes' => 'README.md' ) );
$app->setModuleOption( 'menus',
    array( 'menu' => array(
            array( 'url' => '',       'title' => 'home', 'icon' => 'home' ),
            array( 'url' => 'docs/',  'title' => 'docs', 'icon' => 'book' ),
            array( 'url' => 'src/',   'title' => 'src',  'icon' => 'folder-open',
                'pages' => array(
                    array( 'url' => 'src/AmidaMVC/', 'title' => 'src_code' ),
                    array( 'url' => 'src/www/',      'title' => 'src_www' ),
                    array( 'url' => 'tests/',        'title' => 'tests' ),
                    array( 'divider' => TRUE ),
                    array( 'url' => 'vendor/',       'title' => 'src_vendor' ),
                )
            ),
            array( 'url' => 'demo/',  'title' => 'demo', 'icon' => 'pencil' ),
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
    ->setOption( 'template_file', '_Config/template.boot.php' )
    ->setOption( 'site_title', 'AmidaMVC PHP Framework' )
    ->setOption( 'site_sub_title', 'A PHP Framework turned into CMS. dah...' )
    ->start();