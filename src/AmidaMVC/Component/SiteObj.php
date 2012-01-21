<?php
namespace AmidaMVC\Component;

class SiteObj
{
    /** @var bool
     * skip html template. directly output response content.
     */
    var $as_is = FALSE;
    /** @var \AmidaMVC\Framework\DataTO
     * for html template: title, contents, bread, etc.
     */
    var $htmlObj;
    /**
     * @var \AmidaMVC\Framework\DataTO
     * for http response.
     */
    var $responseObj;
    // +-------------------------------------------------------------+
    function __construct() {
        $this->htmlObj = new \AmidaMVC\Framework\DataTO();
        $htmlDefault = array(
            'title'      => 'title',
            'head_title' => false,
            'contents'   => '',
            'bread'      => '',
        );
        $this->htmlObj->set( $htmlDefault );
        $this->responseObj = new \AmidaMVC\Framework\DataTO();
        $responceDefault = array(
            'content'     => '',
            'status_code' => 200,
            'status_text' => 'OK',
            'http_header' => array(),
        );
        $this->responseObj->set( $responceDefault );
    }
    // +-------------------------------------------------------------+
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
    }
    // +-------------------------------------------------------------+
    function isResponseReady() {
        return $this->as_is;
    }
    // +-------------------------------------------------------------+
    function setResponseAsIs() {
        $this->as_is = TRUE;
        return $this->as_is;
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
    function setHtml( $name, $value ) {
        $this->htmlObj->set( $name, $value );
        if( $name == 'title' ) {
            if( !$this->htmlObj->get( 'head_title' ) ) {
                $this->htmlObj->set( 'head_title', $value );
            }
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    function setTitle( $value ) {
        $this->setHtml( 'title', $value );
    }
    // +-------------------------------------------------------------+
    function setHeadTitle( $value ) {
        $this->setHtml( 'head_title', $value );
    }
    // +-------------------------------------------------------------+
    function setContents( $value ) {
        $this->setHtml( 'contents', $value );
    }
    // +-------------------------------------------------------------+
    function setBread( $value ) {
        $this->setHtml( 'bread', $value );
    }
    // +-------------------------------------------------------------+
    function getHtml( $name=NULL ) {
        return $this->htmlObj->get( $name );
    }
    // +-------------------------------------------------------------+
}