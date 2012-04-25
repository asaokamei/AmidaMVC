<?php
namespace AmidaMVC\Component;

class ResponseObj
{
    /**
     * @var integer   status code for http response.
     */
    var $status = 200;
    /**
     * @var string    content/body of response.
     */
    var $content = '';
    /**
     * @var string    type of content. default is html.
     */
    var $contentType = 'html';
    /**
     * @var array     list of http headers in type => value.
     */
    var $headers = array();
    /**
     * @var array     list of JavaScript and CSS files to include.
     */
    var $includes = array();
    /**
     * @var string    title of the content/page.
     */
    var $title;
    // +-------------------------------------------------------------+
    function __construct() {

    }
    // +-------------------------------------------------------------+
    /**
     * set http headers.
     * @param string $type
     * @param string $value
     * @param bool $append    set to TRUE to append values to header.
     * @return ResponseObj
     */
    function header( $type, $value, $append=FALSE ) {
        if( $append && isset( $this->headers[ $type ] ) ) {
            if( is_array( $this->headers[ $type ] ) ) {
                $this->headers[ $type ][] = $value;
            }
            else {
                $temp = $this->headers[ $type ];
                $this->headers[ $type ] = array();
                $this->headers[ $type ][] = $temp;
                $this->headers[ $type ][] = $value;
            }
        }
        else {
            $this->headers[ $type ] = $value;
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
            $this->content = $content;
        }
        return $this->content;
    }
    // +-------------------------------------------------------------+
    /**
     * getter for content.
     * @return string
     */
    function getContent() {
        return $this->content;
    }
    // +-------------------------------------------------------------+
    /**
     * setter for content.
     * @param string $content
     * @return string
     */
    function setContent( $content ) {
        $this->content = $content;
        return $this->content;
    }
    // +-------------------------------------------------------------+
    /**
     * appends string to content.
     * @param string $content
     * @return string
     */
    function appendContent( $content ) {
        $this->content .= $content;
        return $this->content;
    }
    // +-------------------------------------------------------------+
    /**
     * prepends string to content.
     * @param string $content
     * @return string
     */
    function prependContent( $content ) {
        $this->content = $content . $this->content;
        return $this->content;
    }
    // +-------------------------------------------------------------+
    /**
     * setter/getter for contentType.
     * @param null|string $type
     * @return string
     */
    function contentType( $type=NULL ) {
        if( isset( $type ) ) {
            $this->contentType = $type;
        }
        return $this->contentType;
    }
    // +-------------------------------------------------------------+
    /**
     * set files to include (JavaScript or CSS) for template.
     * @param $file
     * @return ResponseObj
     */
    function includeFile( $file ) {
        $this->includes[] = $file;
        return $this;
    }
    // +-------------------------------------------------------------+
    function title( $title=NULL ) {
        if( isset( $title ) ) {
            $this->title = $title;
        }
        return $this->title;
    }
    // +-------------------------------------------------------------+
}