<?php
namespace AmidaMVC\Tools;

/**
 * really dumb and simple template by PHP
 */

class Template
{
    static $ext_php  = array( 'php' );
    static $ext_html = array( 'html', 'html' );
    static $ext_md   = array( 'md', 'markdown' );
    static $ext_text = array( 'text', 'txt' );
    // +-------------------------------------------------------------+
    function inject( $template, $_siteObj ) {
        $_data = $_siteObj->getContent();
        $_site = $_siteObj->get( 'siteObj' );
        self::convertContents( $_data );
        extract( $_data );
        include( $template );
    }
    // +-------------------------------------------------------------+
    function convertContents( &$data ) {
        $file_ext = pathinfo( $data[ 'file_name' ], PATHINFO_EXTENSION );
        if( in_array( $file_ext, static::$ext_php ) ) {
            self::loadPhpAsCode( $data );
        }
        else if( in_array( $file_ext, static::$ext_html ) ) {
            self::loadHtml( $data );
        }
        else if( in_array( $file_ext, static::$ext_text ) ) {
            self::loadText( $data );
        }
        else if( in_array( $file_ext, static::$ext_md ) ) {
            self::loadMarkdown( $data );
        }
    }
    // +-------------------------------------------------------------+
    function loadPhpAsCode( &$data ) {
        $content = $data[ 'contents' ];
        $content = highlight_string( $content, TRUE );
        $data[ 'contents' ] = $content;
        if( !$data[ 'title' ] ) {
            $title   = pathinfo( $data[ 'file_name' ], PATHINFO_BASENAME );
            $data[ 'title' ] = $title;
        }
    }
    // +-------------------------------------------------------------+
    function loadText( &$data ) {
        $content = $data[ 'contents' ];
        $content = nl2br( $content );
        $data[ 'contents' ] = $content;
        if( !$data[ 'title' ] ) {
            $title   = pathinfo( $data[ 'file_name' ], PATHINFO_BASENAME );
            $data[ 'title' ] = $title;
        }
    }
    // +-------------------------------------------------------------+
    /** load html file into view object.
     * @param $data
     */
    function loadHtml( &$data ) {
        $content = $data[ 'contents' ];
        $data[ 'contents' ] = $content;
        if( !$data[ 'title' ] ) {
            $title   = self::extractTitle( $content );
            $data[ 'title' ] = $title;
        }
    }
    // +-------------------------------------------------------------+
    /** load markdown file into view object.
     * @param $data
     * @return void
     */
    function loadMarkdown( &$data ) {
        include_once( __DIR__ .  '/../../../vendor/PHPMarkdownExtra/markdown.php' );
        $content = $data[ 'contents' ];
        $content = Markdown( $content );
        $data[ 'contents' ] = $content;
        if( !$data[ 'title' ] ) {
            $title   = self::extractTitle( $content );
            $data[ 'title' ] = $title;
        }
    }
    // +-------------------------------------------------------------+
    /** extracts title tag from text/html, and remove it.
     * @param $content
     * @return bool
     */
    function extractTitle( &$content ) {
        $pattern = '/\<title\>([^<]*)\<\/title\>/i';
        if( preg_match( $pattern, $content, $matched ) ) {
            $title = $matched[1];
            $content = preg_replace( $pattern, '', $content );
            return $title;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}
