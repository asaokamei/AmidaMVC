<?php
namespace AmidaMVC\Component;

class Render
{
    static $template = array( '\AmidaMVC\Tools\Template', 'inject' );
    // +-------------------------------------------------------------+
    /**
     * renders data (to html) and output data.
     * @param $ctrl
     * @param $data
     */
    function actionDefault( $ctrl, $data ) {
        self::template( $ctrl, $data );
    }
    // +-------------------------------------------------------------+
    function actionPageNotFound( \AmidaMVC\Framework\Controller $ctrl, \AmidaMVC\Component\SiteObj $data ) {
        // show some excuses, or blame user for not finding a page.
        $contents  = 'Error 404<br /><strong>page not found...</strong><br />';
        $data->setContent( 'title', 'Page Not Found' );
        $data->setContent( 'contents', $contents );
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
    /**
     * if response data is already generated (i.e. jpg loaded as is),
     * emit data without template. or use template to convert
     * html content to response content.
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @return mixed
     */
    function template( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj $_siteObj ) {
        if( $_siteObj->isResponseReady() ) {
            $mime_type = $_siteObj->getResponse( 'mime_type' );
            header("Content-type:" . $mime_type );
            echo $_siteObj->getResponse( 'content' );
            return;
        }
        $template = $_ctrl->ctrl_root . '/_Config/template.php';
        Debug::bug( 'table', $_siteObj, 'template data' );
        call_user_func( static::$template, $template, $_siteObj );
    }
    // +-------------------------------------------------------------+
    function findMimeType( $file_ext ) {
        switch( strtolower( $file_ext ) ) {
            case 'css':
                $mime = 'text/css';
                break;
            case 'js':
            case 'javascript':
                $mime = 'text/javascript';
                break;
            case 'jpg':
            case 'jpeg':
                $mime = 'image/jpeg';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'pdf':
                $mime = 'application/pdf';
                break;
            default:
                $mime = 'application/octet-stream';
                break;
        }
        return $mime;
    }
    // +-------------------------------------------------------------+
}

