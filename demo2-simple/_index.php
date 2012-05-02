<?php
require_once( __DIR__ . '/../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\AppSimple\Application::getInstance(
    array(
        'site_title' => "Demo#2 - Simple Site with Template",
        'template_file' => 'template.php',
        'ctrl_root' => realpath( '../demo1-simple' ),
    )
);
$app->get( '/func', function() {
    $content = "
#Closure Output

AmidaMVC can handle closure function to generate a response;
this text is an output from closure in MarkDown description.

Just in case you may notice, this page uses template with demo.css
in demo1-simple folder.
";
        return $content;
    }, array( 'type' => 'markdown' )
);
$app->separateCommands();
$app->start();
