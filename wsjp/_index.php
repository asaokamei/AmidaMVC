<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\Application\App2::app();
$app->setCtrlRoot( __DIR__ );
$app->setModuleOption( 'router', array( 'indexes' => 'README.md' ) );
$app->setModuleOption( 'menus',
    array( 'menu' => array(
            array( 'url' => '',          'title' => 'home' ),
            array( 'url' => 'serv.md',   'title' => 'service' ),
            array( 'url' => 'prof.md',   'title' => 'profile' ),
            array( 'url' => 'expr.md',   'title' => 'experience' ),
            array( 'url' => 'tech/',     'title' => 'technology',
                'pages' => array(
                    array( 'url' => 'tech/',          'title' => 'technology top' ),
                    array( 'divider' => TRUE ),
                    array( 'url' => 'vendor/',       'title' => 'src_vendor' ),
                    array( 'divider' => TRUE ),
                    array( 'title' => 'old stuff' ),
                    array( 'url' => 'tech/old_fw', 'title' => 'framework' ),
                    array( 'url' => 'tech/old', 'title' => 'old stuff' ),
                )
            ),
            array( 'url' => '#blog', 'title' => 'blog...' ),
        )
    )
);
$app->setModuleOption( 'lang', array(
    'lang_list' => array( 'en', 'ja' ),
    'ctrl_root' => array(
        '_docs.{lang}',
    ),
    'match_url' => '^{lang}\\/',
) );
$app
    ->setOption( 'site_title', 'WorkSpot.JP' )
    ->setOption( 'site_sub_title', 'brings open source to business...' )
    ->start();