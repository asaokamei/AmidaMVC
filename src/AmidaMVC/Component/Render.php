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
    function actionAsIs( $ctrl, $data ) {
        // output as is: css, js, img, etc.
        echo $data;
    }
    // +-------------------------------------------------------------+
    function actionPageNotFound( $ctrl, $data ) {
        // show some excuses, or blame user for not finding a page.
        $contents  = 'Error 404<br /><strong>page not found...</strong><br />';
        $contents .= '<hr>' . $data->getHtml( 'contents' );
        $data->setHtml( 'title', 'Page Not Found' );
        $data->setHtml( 'contents', $contents );
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
    function template( $_ctrl, $_siteObj ) {
        extract( $_siteObj->getHtml() );
        Debug::bug( 'table', $_siteObj->getHtml() );
        $debug    = self::showDebugInfo( $_ctrl );
        include( $_ctrl->ctrl_root . '/_Template/template.php' );
    }
    // +-------------------------------------------------------------+
    function showDebugInfo( $ctrl ) {
        $debugInfo = Debug::result();
        if( !$debugInfo ) return '';
        return $debugInfo;
    }
    // +-------------------------------------------------------------+
}

