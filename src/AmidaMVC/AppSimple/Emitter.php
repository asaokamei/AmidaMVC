<?php
namespace AmidaMVC\AppSimple;

class Emitter
{
    /**
     * @var \AmidaMVC\Tools\Emit
     */
    static $_emit = '\AmidaMVC\Tools\Emit';
    // +-------------------------------------------------------------+
    /**
     * initialize class.
     * @static
     * @param array $option     option to initialize.
     */
    static function _init( $option=array() ) {
        if( isset( $option[ 'emitClass' ] ) ) {
            static::$_emit = $option[ 'emitClass' ];
        }
    }
    // +-------------------------------------------------------------+
    /**
     * renders data (to html) and output data.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_siteObj
     * @return bool
     */
    static function actionDefault( $_ctrl, $_siteObj )
    {
        self::convert( $_siteObj );
        self::template( $_ctrl, $_siteObj );
        $_siteObj->emit();
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * action for page not found; this action is invoked only from
     * _App.php or some other models... reload pageNofFound file if
     * set in siteObj. if not, generate simple err404 contents.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_siteObj
     * @return array
     */
    static function action_PageNotFound( $_ctrl, $_siteObj )
    {
        // show some excuses, or blame user for not finding a page.
        $_siteObj->status( '404' );
        $_siteObj->title( 'Page Not Found' );
        $contents  = "#Error 404\n\nrequested page not found...";
        $_siteObj->setContent( $contents );
        $_siteObj->contentType( 'markdown' );
        $_ctrl->setMyAction( $_ctrl->defaultAct() );
        return array();
    }
    // +-------------------------------------------------------------+
    /**
     * convert contents to HTML for md/text.
     * @static
     * @param \AmidaMVC\Framework\PageObj $_siteObj
     */
    static function convert( $_siteObj )
    {
        $content = $_siteObj->getContent();
        $type    = $_siteObj->contentType();
        $emit    = static::$_emit;
        $emit::convertContentToHtml( $content, $type );
        $_siteObj->setContent( $content );
        $_siteObj->contentType( $type );
    }
    // +-------------------------------------------------------------+
    /**
     * inject into template if contentType is html.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_siteObj
     */
    static function template( $_ctrl, $_siteObj )
    {
        if( $_siteObj->contentType() == 'html' ) {
            $emit     = static::$_emit;
            $template = $_ctrl->options[ 'template_file' ];
            $content_data = array( '_ctrl' => $_ctrl, '_pageObj' => $_siteObj );
            $content = $emit::inject( $template, $content_data );
            $_siteObj->setContent( $content );
        }
    }
    // +-------------------------------------------------------------+
}