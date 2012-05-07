<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

error_reporting( E_ALL );

$app = \AmidaMVC\AppCms\Application::getInstance(
    array(
        'site_title' => "Demo#4 - like a CMS",
    )
);
$app->setFileLocation( '_Config' );
$app->start();
