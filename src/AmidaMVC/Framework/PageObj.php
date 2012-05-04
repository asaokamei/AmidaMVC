<?php
namespace AmidaMVC\Framework;

class PageObj
{
    /**
     * @var integer   status code for http response.
     */
    var $_status = 200;
    /**
     * @var string    content/body of response.
     */
    var $_content = '';
    /**
     * @var string    type of content. default is html.
     */
    var $_contentType = 'html';
    /**
     * @var array     list of http headers in type => value.
     */
    var $_headers = array();
    /**
     * @var array     list of JavaScript and CSS files to include.
     */
    var $_includes = array();
    /**
     * @var string    title of the content/page.
     */
    var $_title;
    /**
     * @var array    list of css files to include.
     */
    var $_css = array();
    /**
     * @var array    list of javascript libraries to include.
     */
    var $_js = array();
    /**
     * @var \AmidaMVC\Tools\Response    class to use for emitting various responses.
     */
    var $_emitter = '\AmidaMVC\Tools\Response';
    /**
     * @var array   information about loaded file.
     */
    var $loadInfo = array();
    // +-------------------------------------------------------------+
    function __construct() {

    }
    // +-------------------------------------------------------------+
    /**
     * set http headers.
     * @param string $type
     * @param string $value
     * @param bool $append    set to TRUE to append values to header.
     * @return PageObj
     */
    function header( $type, $value, $append=FALSE ) {
        if( $append && isset( $this->_headers[ $type ] ) ) {
            if( is_array( $this->_headers[ $type ] ) ) {
                $this->_headers[ $type ][] = $value;
            }
            else {
                $temp = $this->_headers[ $type ];
                $this->_headers[ $type ] = array();
                $this->_headers[ $type ][] = $temp;
                $this->_headers[ $type ][] = $value;
            }
        }
        else {
            $this->_headers[ $type ] = $value;
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * setter/getter for content.
     * @param null|string $content
     * @return string
     */
    function content( $content=NULL ) {
        if( isset( $content ) ) {
            $this->_content = $content;
        }
        return $this->_content;
    }
    // +-------------------------------------------------------------+
    /**
     * getter for content.
     * @return string
     */
    function getContent() {
        return $this->_content;
    }
    // +-------------------------------------------------------------+
    /**
     * setter for content.
     * @param string $content
     * @return string
     */
    function setContent( $content ) {
        $this->_content = $content;
        return $this->_content;
    }
    // +-------------------------------------------------------------+
    /**
     * appends string to content.
     * @param string $content
     * @return string
     */
    function appendContent( $content ) {
        $this->_content .= $content;
        return $this->_content;
    }
    // +-------------------------------------------------------------+
    /**
     * prepends string to content.
     * @param string $content
     * @return string
     */
    function prependContent( $content ) {
        $this->_content = $content . $this->_content;
        return $this->_content;
    }
    // +-------------------------------------------------------------+
    /**
     * setter/getter for contentType.
     * @param null|string $type
     * @return string
     */
    function contentType( $type=NULL ) {
        if( isset( $type ) ) {
            $this->_contentType = $type;
        }
        return $this->_contentType;
    }
    // +-------------------------------------------------------------+
    /**
     * set files to include (JavaScript or CSS) for template.
     * @param $file
     * @return PageObj
     */
    function includeFile( $file ) {
        $this->_includes[] = $file;
        return $this;
    }
    // +-------------------------------------------------------------+
    function title( $title=NULL ) {
        if( isset( $title ) ) {
            $this->_title = $title;
        }
        return $this->_title;
    }
    // +-------------------------------------------------------------+
    function status( $status=NULL ) {
        if( isset( $status ) ) {
            $this->_status = $status;
        }
        return $this->_status;
    }
    // +-------------------------------------------------------------+
    function emitHeaders() {
        $emit = $this->_emitter;
        $emit::emitStatus( $this->_status );
        if( !empty( $this->_headers ) )
        foreach( $this->_headers as $name => $val ) {
            header( "{$name}: {$val}" );
        }
        $content_type = $emit::findMimeType( $this->contentType() );
        header( "Content-type: " . $content_type );
    }
    // +-------------------------------------------------------------+
    /**
     * emits http response.
     */
    function emit() {
        $this->emitHeaders();
        echo $this->_content;
    }
    // +-------------------------------------------------------------+
    function setCss( $css ) {
        $file = basename( $css );
        $this->_css[ $file ] = $css;
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @return string
     */
    function getCssLinks( $_ctrl ) {
        $html = '';
        if( empty( $this->_css ) ) { return $html; }
        foreach( $this->_css as $css ) {
            $link = $_ctrl->getPath( $css );
            /** @var $link string */
            $html .= "<link rel=\"stylesheet\" href=\"{$link}\" />\n";
        }
        return $html;
    }
    // +-------------------------------------------------------------+
    function setJs( $js ) {
        $file = basename( $js );
        $this->_js[ $file ] = $js;
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @return string
     */
    function getJsLinks( $_ctrl ) {
        $html = '';
        if( empty( $this->_js ) ) { return $html; }
        foreach( $this->_js as $js ) {
            $link = $_ctrl->getPath( $js );
            /** @var $link string */
            $html .= "<script src=\"{$link}\" type=\"javascript\"></script>\n";
        }
        return $html;
    }
    // +-------------------------------------------------------------+
}