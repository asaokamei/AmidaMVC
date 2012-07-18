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
                    array( 'url' => 'tech/',          'title' => 'tech_top' ),
                    array( 'divider' => TRUE ),
                    array( 'url' => 'tech/cena.md',  'title' => 'Cena-DTA' ),
                    array( 'url' => 'tech/amida.md',  'title' => 'AmidaMVC' ),
                    array( 'url' => 'tech/old.md',    'title' => 'tech_old' ),
                )
            ),
            array( 'url' => 'http://wsjp.blogspot.jp/', 'title' => 'blog>>', 'target' => 'blank' ),
        )
    )
);
$app->setModuleOption( 'lang', array(
    'lang_list' => array( 'ja', 'en' ),
    'ctrl_root' => array(
        '_docs.{lang}', 'ja' => FALSE,
    ),
    'match_url' => '^{lang}\\/',
    'match_rewrite' => TRUE,
) );
$app
    ->setOption( 'site_title', 'WorkSpot.JP' )
    ->setOption( 'site_sub_title', 'brings open source to business...' )
    ->start();