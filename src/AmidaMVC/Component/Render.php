<?php
namespace AmidaMVC\Component;

class Render
{
    static $template = array( '\AmidaMVC\Tools\Template', 'inject' );
    static $convert  = array( '\AmidaMVC\Tools\Template', 'convertContents' );
    // +-------------------------------------------------------------+
    /**
     * renders data (to html) and output data.
     * @param $ctrl
     * @param $data
     */
    function actionDefault( \AmidaMVC\Framework\Controller $_ctrl, \AmidaMVC\Component\SiteObj $_siteObj ) {
        if( !$_siteObj->isResponseReady() ) {
            static::template( $_ctrl, $_siteObj );
        }
        static::emitResponse( $_ctrl, $_siteObj );
        return;
    }
    // +-------------------------------------------------------------+
    function action_PageNotFound( \AmidaMVC\Framework\Controller $ctrl, \AmidaMVC\Component\SiteObj $_siteObj ) {
        // show some excuses, or blame user for not finding a page.
        $siteObj = $_siteObj->get( 'siteObj' );
        if( isset( $siteObj->pageNotFound ) ) {
            $loadInfo = array(
                'file' => $siteObj->pageNotFound,
            );
            $ctrl->setAction( $ctrl->defaultAct() );
            $ctrl->prependComponent( array(
                array( 'Loader', 'loader' ),
                array( 'Render', 'render' ),
            ) );
            return $loadInfo;
        }
        $contents  = 'Error 404<br /><strong>page not found...</strong><br />';
        $_siteObj->setContent( 'title', 'Page Not Found' );
        $_siteObj->setContent( 'contents', $contents );
        static::template( $ctrl, $_siteObj );
    }
    // +-------------------------------------------------------------+
    function actionException( $ctrl, $_siteObj ) {
        // show some nasty things happened and apologize.
        echo 'something terrible has happend...<br />';
        var_dump( $_siteObj );
        static::template( $ctrl, $_siteObj );
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
            static::emitResponse( $_ctrl, $_siteObj );
            return;
        }
        $_siteObj->setContent( 'debug', Debug::result() );
        $siteObj = $_siteObj->get( 'siteObj' );
        $template = $siteObj->template_file;
        $args = (array) $_siteObj->getContent();

        $args = call_user_func( static::$convert, $args );
        $args[ '_ctrl' ] = $_ctrl;
        $args[ '_siteObj' ] = $_siteObj;

        ob_start();
        ob_implicit_flush(0);
        call_user_func( static::$template, $template, $args );
        $content = ob_get_clean();
        $_siteObj->setResponse( 'content', $content );
        return;
    }
    // +-------------------------------------------------------------+
    function emitResponse( $_ctrl, \AmidaMVC\Component\SiteObj $_siteObj ) {
        $mime_type = $_siteObj->get( 'responseObj', 'mime_type' );
        if( !$mime_type ) {
            $file_name = $_siteObj->getContent( 'file_name' );
            $file_ext  = pathinfo( $file_name, PATHINFO_EXTENSION );
            $mime_type = self::findMimeType( $file_ext );
        }
        header("Content-type:" . $mime_type );
        echo $_siteObj->getResponse( 'content' );
        return;
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

