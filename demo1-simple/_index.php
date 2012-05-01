<?php
require_once( '../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\AppSimple\Application::getInstance(
    array(
        'site_title' => "Demo#1 - Simple Site",
    )
);
$app->get( '/func', function() {
    $content = "
#Closure Output

AmidaMVC can handle closure function to generate a response;
this text is an output from closure in MarkDown description.
";
        return $content;
    }, array( 'type' => 'markdown' )
);
$app->get( '/', '/index.md' );
$app->separateCommands();
$app->start();
