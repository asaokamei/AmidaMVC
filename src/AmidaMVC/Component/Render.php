<?php
namespace AmidaMVC\Component;

/**
 * Renders html web page. 
 * TODO: really need to rewrite this component. 
 */

class Render
{
    static $template = array( '\AmidaMVC\Tools\Template', 'inject' );
    static $convert  = array( '\AmidaMVC\Tools\Template', 'convertContents' );
    // +-------------------------------------------------------------+
    /**
     * renders data (to html) and output data.
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Component\SiteObj $_siteObj
     * @return
     */
    static function actionDefault( 
        \AmidaMVC\Framework\Controller $_ctrl, 
        \AmidaMVC\Component\SiteObj $_siteObj ) 
    {
        if( !$_siteObj->isResponseReady() ) {
            $content = (array) $_siteObj->getContent();
            $content = call_user_func( static::$convert, $content );
            $_siteObj->set( 'contentObj', $content );
            static::template( $_ctrl, $_siteObj );
        }
        static::emitResponse( $_ctrl, $_siteObj );
        return;
    }
    // +-------------------------------------------------------------+
    /**
     * action for page not found; this action is invoked only from 
     * _App.php or some other models... reload pageNofFound file if 
     * set in siteObj. if not, generate simple err404 contents. 
     * @static
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @return array
     */
    static function action_PageNotFound( 
        \AmidaMVC\Framework\Controller $ctrl, 
        \AmidaMVC\Component\SiteObj $_siteObj ) 
    {
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
    /**
     * _edit mode: text to edit is in the content. template should
     * handle the text-editing. 
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @return mixed
     */
    static function action_edit(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj $_siteObj )
    {
        if( !$_siteObj->isResponseReady() ) {
            static::template( $_ctrl, $_siteObj );
        }
        static::emitResponse( $_ctrl, $_siteObj );
        return;
    }
    // +-------------------------------------------------------------+
    /**
     * _src mode: show file source as php code. 
     * TODO: same as actionDefault? rewrite or dump this method. 
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @return mixed
     */
    static function action_src(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj $_siteObj )
    {
        if( !$_siteObj->isResponseReady() ) {
            $content = (array) $_siteObj->getContent();
            $content = call_user_func( static::$convert, $content );
            $_siteObj->set( 'contentObj', $content );
            static::template( $_ctrl, $_siteObj );
        }
        static::emitResponse( $_ctrl, $_siteObj );
        return;
    }
    // +-------------------------------------------------------------+
    /**
     * _raw mode: content is already in responseObj, so just emit
     * the data as text/plain. 
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @return mixed
     */
    static function action_raw(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj $_siteObj )
    {
        $_siteObj->setMimeType( 'text/plain' );
        static::emitResponse( $_ctrl, $_siteObj );
        return;
    }
    // +-------------------------------------------------------------+
    static function actionException( $ctrl, $_siteObj ) {
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
    static function template( 
        \AmidaMVC\Framework\Controller $_ctrl, 
        \AmidaMVC\Component\SiteObj $_siteObj ) 
    {
        if( $_siteObj->isResponseReady() ) {
            static::emitResponse( $_ctrl, $_siteObj );
            return;
        }
        $_siteObj->setContent( 'debug', Debug::result() );
        $siteObj = $_siteObj->get( 'siteObj' );
        $template = $siteObj->template_file;
        $args = (array) $_siteObj->getContent();
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
    /**
     * emitting contents to request.
     * TODO: this method should be in new Emitter component. 
     * @static
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @return mixed
     */
    static function emitResponse(
        \AmidaMVC\Framework\Controller $_ctrl, 
        \AmidaMVC\Component\SiteObj $_siteObj ) 
    {
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
    /**
     * find mime type from file extension. 
     * TODO: this method should be in Tools folder. 
     * @static
     * @param $file_ext
     * @return string
     */
    static function findMimeType( $file_ext ) {
        switch( strtolower( $file_ext ) ) {
            case 'text':
                $mime = 'text/plain';
                break;
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

