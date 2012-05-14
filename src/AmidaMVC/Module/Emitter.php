<?php
namespace AmidaMVC\Module;

class Emitter extends AModule implements IfModule
{
    /**
     * @var \AmidaMVC\Tools\Emit
     */
    var $_emitClass = '\AmidaMVC\Tools\Emit';
    /**
     * @var array    list of supported commands.
     */
    var $commands = array( '_view', '_src', '_raw' );
    // +-------------------------------------------------------------+
    /**
     * initialize class.
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
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return bool
     */
    function actionDefault( $_ctrl, &$_pageObj, $option=array() )
    {
        if( $command = $this->findCommand( $_ctrl->getCommands() ) ) {
            $method = $_ctrl->makeActionMethod( $command );
            return $this->$method( $_ctrl, $_pageObj, $option );
        }
        // default is view method.
        return $this->action_view( $_ctrl, $_pageObj, $option );
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $option
     * @return bool
     */
    function action_view( $_ctrl, &$_pageObj, $option=array() ) {
        $this->convert( $_pageObj );
        $this->template( $_ctrl, $_pageObj );
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
        $this->convert( $_pageObj );
        $this->template( $_ctrl, $_pageObj );
        $_pageObj->emit();
        return TRUE;
    }
    // +-------------------------------------------------------------+
    /**
     * action for page not found; this action is invoked only from
     * _App.php or some other models... reload pageNofFound file if
     * set in siteObj. if not, generate simple err404 contents.
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @return array
     */
    function action_PageNotFound( $_ctrl, $_pageObj )
    {
        // show some excuses, or blame user for not finding a page.
        if( $_ctrl->getOption( 'pageNotFound_file' ) ) {
            // pageNotFound file is set. should load this page.
            $_ctrl->prependModule( array(
                array( '\AmidaMVC\AppSimple\Loader',  'loader' ),
                array( '\AmidaMVC\AppSimple\Emitter', 'emitter' ),
            ) );
            return array();
        }
        $_pageObj->title( 'Page Not Found' );
        $contents  = "#Error 404\n\nrequested page not found...\n\n";
        $contents .= "[back to top](" . $_ctrl->getBaseUrl() . ")";
        $_pageObj->setContent( $contents );
        $_pageObj->contentType( 'markdown' );
        $_ctrl->setMyAction( $_ctrl->defaultAct() );
        return array();
    }
    // +-------------------------------------------------------------+
    /**
     * convert contents to HTML for md/text.
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
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    function template( $_ctrl, $_pageObj )
    {
        if( $_pageObj->contentType() == 'html' ) {
            $emit     = $this->_emitClass;
            // if template_file is set, use it as relative to ctrl_root.
            if( $_ctrl->getOption( 'template_file' ) ) {
                $template = $_ctrl->findFile( $_ctrl->getOption( 'template_file' ) );
            }
            else {
                // or use the template in the AppSimple folder as default.
                $template = __DIR__ . '/template.php';
            }
            $content_data = array( '_ctrl' => $_ctrl, '_pageObj' => $_pageObj );
            $content = $emit::inject( $template, $content_data );
            $_pageObj->setContent( $content );
        }
    }
    // +-------------------------------------------------------------+
}