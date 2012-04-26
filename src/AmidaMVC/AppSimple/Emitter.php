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
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @return bool
     */
    static function actionDefault( $_ctrl, $_pageObj )
    {
        self::convert( $_pageObj );
        self::template( $_ctrl, $_pageObj );
        $_pageObj->emit();
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * action for page not found; this action is invoked only from
     * _App.php or some other models... reload pageNofFound file if
     * set in siteObj. if not, generate simple err404 contents.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @return array
     */
    static function action_PageNotFound( $_ctrl, $_pageObj )
    {
        // show some excuses, or blame user for not finding a page.
        $_pageObj->status( '404' );
        $_pageObj->title( 'Page Not Found' );
        $contents  = "#Error 404\n\nrequested page not found...";
        $_pageObj->setContent( $contents );
        $_pageObj->contentType( 'markdown' );
        $_ctrl->setMyAction( $_ctrl->defaultAct() );
        return array();
    }
    // +-------------------------------------------------------------+
    /**
     * convert contents to HTML for md/text.
     * @static
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    static function convert( $_pageObj )
    {
        $content = $_pageObj->getContent();
        $type    = $_pageObj->contentType();
        $emit    = static::$_emit;
        $emit::convertContentToHtml( $content, $type );
        $_pageObj->setContent( $content );
        $_pageObj->contentType( $type );
    }
    // +-------------------------------------------------------------+
    /**
     * inject into template if contentType is html.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    static function template( $_ctrl, $_pageObj )
    {
        if( $_pageObj->contentType() == 'html' ) {
            $emit     = static::$_emit;
            $template = $_ctrl->options[ 'template_file' ];
            $content_data = array( '_ctrl' => $_ctrl, '_pageObj' => $_pageObj );
            $content = $emit::inject( $template, $content_data );
            $_pageObj->setContent( $content );
        }
    }
    // +-------------------------------------------------------------+
}