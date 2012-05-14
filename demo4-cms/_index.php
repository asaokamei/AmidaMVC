<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

error_reporting( E_ALL );
/** @var $app \AmidaMVC\Framework\Controller */
$app = \AmidaMVC\Application\Application::cms(
    array(
        'site_title' => "Demo#4 - like a CMS",
        'template_file' => 'template.php'
    )
);
$app->setFileLocation( '_Config' );
$app->start();
