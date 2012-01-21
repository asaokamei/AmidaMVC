<?php
namespace AmidaMVC\Component;

class Render
{
    // +-------------------------------------------------------------+
    function actionDefault( $ctrl, $data ) {
        // everything OK.
        self::template( $ctrl, $data );
    }
    // +-------------------------------------------------------------+
    function actionErr404( $ctrl, $data ) {
        // show some excuses, or blame user for not finding a page.
        echo 'page not found...<br />';
        var_dump( $data );
        self::template( $ctrl, $data );
    }
    // +-------------------------------------------------------------+
    function actionException( $ctrl, $data ) {
        // show some nasty things happened and apologize.
        echo 'something terrible has happend...<br />';
        var_dump( $data );
        self::template( $ctrl, $data );
    }
    // +-------------------------------------------------------------+
    function template( $ctrl, $data ) {
        $template = $ctrl->ctrl_root . '/_Template/template.php';
        $title    = 'AmidaMVC H1';
        $contents = $data;
        $debug    = self::showDebugInfo( $ctrl );
        include( $template );
    }
    // +-------------------------------------------------------------+
    function showDebugInfo( $ctrl ) {
        $debugInfo = Debug::result();
        if( !$debugInfo ) return '';
        $result = "
        <style>
        div.debugInfo {
            font-size: 12px;
        }
        div.debugInfo table {
        border:1px solid gray; font-size: 11px; border-collapse: collapse;
        }
        div.debugInfo td,th { border: 1px dotted gray; vertical-align: top; }
        div.debugInfo th { background-color: #F0F0F0; }
        </style>
        <hr>
        <div class='debugInfo'>{$debugInfo}</div>";
        return $result;
    }
    // +-------------------------------------------------------------+
}

