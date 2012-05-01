<?php
namespace AmidaMVC\AppSimple;

class Emitter extends \AmidaMVC\Framework\AModule implements \AmidaMVC\Framework\IModule
{
    /**
     * @var \AmidaMVC\Tools\Emit
     */
    var $_emitClass = '\AmidaMVC\Tools\Emit';
    var $commands = array( '_src', '_raw' );
    // +-------------------------------------------------------------+
    /**
     * initialize class.
     * @static
     * @param array $option     option to initialize.
     */
    function _init( $option=array() ) {
        if( isset( $option[ 'emitClass' ] ) ) {
            $this->_emitClass = $option[ 'emitClass' ];
        }
    }
    // +-------------------------------------------------------------+
    /**
     * renders data (to html) and output data.
     * @static
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return bool
     */
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
    {
        if( $command = $this->findCommand( $_ctrl->cmds ) ) {
            $method = $_ctrl->makeActionMethod( $command );
            return $this->$method( $_ctrl, $_pageObj, $option );
        }
        self::convert( $_pageObj );
        self::template( $_ctrl, $_pageObj );
        $_pageObj->emit();
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return bool
     */
    function action_raw( $_ctrl, &$_pageObj, $option=array() ) {
        $_pageObj->contentType( 'text' );
        $_pageObj->emit();
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return bool
     */
    function action_src( $_ctrl, &$_pageObj, $option=array() ) {
        $_pageObj->contentType( 'php' );
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
    function action_PageNotFound( $_ctrl, $_pageObj )
    {
        // show some excuses, or blame user for not finding a page.
        if( $_ctrl->getOption( 'pageNotFound_file' ) ) {
            // pageNotFound file is set. should load this page.
            $_ctrl->prependComponent( array(
                array( '\AmidaMVC\AppSimple\Loader',  'loader' ),
                array( '\AmidaMVC\AppSimple\Emitter', 'emitter' ),
            ) );
            return array();
        }
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
    function convert( $_pageObj )
    {
        $content = $_pageObj->getContent();
        $type    = $_pageObj->contentType();
        $emit    = $this->_emitClass;
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
    function template( $_ctrl, $_pageObj )
    {
        if( $_pageObj->contentType() == 'html' ) {
            $emit     = $this->_emitClass;
            $template = $_ctrl->options[ 'template_file' ];
            $content_data = array( '_ctrl' => $_ctrl, '_pageObj' => $_pageObj );
            $content = $emit::inject( $template, $content_data );
            $_pageObj->setContent( $content );
        }
    }
    // +-------------------------------------------------------------+
}