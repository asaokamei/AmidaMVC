<?php
namespace AmidaMVC\Component;

class SiteObj extends \AmidaMVC\Tools\DataTO
{
    /** @var bool
     * skip html template. directly output response content.
     */
    var $as_is = FALSE;
    /** @var \AmidaMVC\Tools\DataTO
     * contents data for template: title, contents, bread, etc.
     */
    var $contentObj;
    /**
     * @var \AmidaMVC\Tools\DataTO
     * for http response.
     */
    var $responseObj;
    // +-------------------------------------------------------------+
    function __construct() {
        parent::__construct();

        $htmlDefault = array(
            'content_type'   => 'html',
            'file_name'      => '',
            'title'      => '',
            'head_title' => '',
            'contents'   => '',
            'bread'      => '',
            'debug'      => false,
        );
        $this->set( 'contentObj', $htmlDefault );
        $responseDefault = array(
            'content'     => '',
            'status_code' => 200,
            'status_text' => 'OK',
            'http_header' => array(),
            'mime_type'   => 'text/html'
        );
        $this->set( 'responseObj', $responseDefault );
    }
    // +-------------------------------------------------------------+
    /**
     * set data for response object.
     * @param $name
     * @param $value
     * @return SiteObj
     */
    function setResponse( $name, $value ) {
        $this->responseObj->set( $name, $value );
        return $this;
    }
    // +-------------------------------------------------------------+
    function getResponse( $name ) {
        return $this->responseObj->get( $name );
    }
    // +-------------------------------------------------------------+
    function setHttpContent( $value ) {
        $this->setResponse( 'content', $value );
        $this->as_is = TRUE;
        return $this;
    }
    // +-------------------------------------------------------------+
    function setMimeType( $mime ) {
        $this->setResponse( 'mime_type', $mime );
        return $this;
    }
    // +-------------------------------------------------------------+
    function isResponseReady() {
        return $this->as_is;
    }
    // +-------------------------------------------------------------+
    function setResponseAsIs() {
        $this->as_is = TRUE;
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * data for html template. must be html formatted already.
     *   title:  page title
     *   head_title: page title in html header.
     *   contents: main contents.
     *   bread: bread of the page.
     * @param $name
     * @param $value
     * @return SiteObj
     */
    function setContent( $name, $value ) {
        $this->contentObj->set( $name, $value );
        if( $name == 'title' ) {
            if( !$this->contentObj->get( 'head_title' ) ) {
                $this->contentObj->set( 'head_title', $value );
            }
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    function setContentType( $type ) {
        $this->setContent( 'content_type', $type );
    }
    // +-------------------------------------------------------------+
    function setFileName( $name ) {
        $this->setContent( 'file_name', $name );
    }
    // +-------------------------------------------------------------+
    function setTitle( $value ) {
        $this->setContent( 'title', $value );
    }
    // +-------------------------------------------------------------+
    function setHeadTitle( $value ) {
        $this->setContent( 'head_title', $value );
    }
    // +-------------------------------------------------------------+
    function setContents( $value ) {
        $this->setContent( 'contents', $value );
    }
    // +-------------------------------------------------------------+
    function setBread( $value ) {
        $this->setContent( 'bread', $value );
    }
    // +-------------------------------------------------------------+
    function getContent( $name=NULL ) {
        return $this->contentObj->get( $name );
    }
    // +-------------------------------------------------------------+
}