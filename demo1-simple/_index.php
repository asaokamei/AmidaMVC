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
: example of external file using markdown text file.

[none for error 404](none.html)
: this link points to non-existing file and causes
error 404 error.
";
        return $content;
    }, array( 'type' => 'markdown' )
);
$app->start();
