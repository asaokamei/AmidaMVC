<?php
require_once( '../src/AmidaMVC/bootstrap.php' );

$app = \AmidaMVC\AppSimple\Application::getInstance(
    array(
        'site_title' => "Demo#1 - Simple Site",
    )
);
$app->get( '/', function() {
    $content = "
#Simple Demo

This simple demo uses the AppSimple components with
default settings.

No external php code, css, javascript, or any other scripts
except the settings written in _index.php file.

[test.md](test.md)
: example of external file using markdown textfile.
";
        return $content;
    }, array( 'type' => 'markdown' )
);
$app->start();
