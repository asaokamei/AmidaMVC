<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\AppCms\Application::getInstance(
    array(
        'site_title' => "Demo#4 - like a CMS",
    )
);
$app->start();
