<?php
namespace AmidaMVC\Component;

class Viewer
{
    function actionDefault( $ctrl, $data ) {
        // everything OK.
        echo "Viewer::Default<br />";
        self::showDebugInfo( $ctrl );
        var_dump( $data );
    }
    function actionErr404( $ctrl, $data ) {
        // show some excuses, or blame user for not finding a page.
        echo 'page not found...<br />';
        var_dump( $data );
        self::showDebugInfo( $ctrl );
    }
    function actionException( $ctrl, $data ) {
        // show some nasty things happened and apologize.
        echo 'something terrible has happend...<br />';
        var_dump( $data );
        self::showDebugInfo( $ctrl );
    }
    function showDebugInfo( $ctrl ) {
        $result = $ctrl->debug( 'getResult' );
        $css = "
        <style>
        div.debugInfo table {
        border:1px solid gray; font-size: 11px; border-collapse: collapse;
        }
        div.debugInfo td,th { border: 1px dotted gray; }
        div.debugInfo th { background-color: #F0F0F0; }
        </style>
        <hr>
        ";
        echo $css;
        echo "<div style='font-size: 12px;' class='debugInfo'>{$result}</div>";
    }
}

