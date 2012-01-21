<?php
namespace AmidaMVC\Component;

class SiteObj
{
    /** @var \AmidaMVC\Component\AmidaMVC\Framework\Dto
     * for html template: title, contents, bread, etc.
     */
    var $htmlObj;
    // +-------------------------------------------------------------+
    function __construct() {
        $this->htmlObj = new \AmidaMVC\Framework\DataTO();
        $htmlDefault = array(
            'title' => 'title',
            'head_title' => false,
            'contents' => '',
            'bread' => '',
        );
        $this->htmlObj->set( $htmlDefault );
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