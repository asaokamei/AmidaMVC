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
    function inject( $template, $_data ) {
        extract( $_data );
        include( $template );
    }
    // +-------------------------------------------------------------+
    function convertContents( $data ) {
        $content_type = $data[ 'content_type' ];
        if( !$content_type ) {
            $content_type = pathinfo( $data[ 'file_name' ], PATHINFO_EXTENSION );
        }
        if( in_array( $content_type, static::$ext_php ) ) {
            self::loadPhpAsCode( $data );
        }
        else if( in_array( $content_type, static::$ext_html ) ) {
            self::loadHtml( $data );
        }
        else if( in_array( $content_type, static::$ext_text ) ) {
            self::loadText( $data );
        }
        else if( in_array( $content_type, static::$ext_md ) ) {
            self::loadMarkdown( $data );
        }
        return $data;
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
        $content = '<pre>'. $data[ 'contents' ] . '</pre>';
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
