<?php
namespace AmidaMVC\Component;

class Render
{
    // +-------------------------------------------------------------+
    function actionDefault( $ctrl, $data ) {
        // everything OK.
        self::template( $ctrl, $data );
    }
    // +-------------------------------------------------------------+
    function actionAsIs( $ctrl, $data ) {
        // output as is: css, js, img, etc.
        echo $data;
    }
    // +-------------------------------------------------------------+
    function actionPageNotFound( \AmidaMVC\Framework\Controller $ctrl, \AmidaMVC\Component\SiteObj $data ) {
        // show some excuses, or blame user for not finding a page.
        $contents  = 'Error 404<br /><strong>page not found...</strong><br />';
        $data->setContent( 'title', 'Page Not Found' );
        $data->setContent( 'contents', $contents );
        self::template( $ctrl, $data );
    }
    // +-------------------------------------------------------------+
    function actionException( $ctrl, $data ) {
        // show some nasty things happened and apologize.
        echo 'something terrible has happend...<br />';
        var_dump( $data );
        self::template( $ctrl, $data );
    }
    // +-------------------------------------------------------------+
    function template( $_ctrl, $_siteObj ) {
        if( $_siteObj->isResponseReady() ) {
            $mime_type = $_siteObj->getResponse( 'mime_type' );
            header("Content-type:" . $mime_type );
            echo $_siteObj->getResponse( 'content' );
            return;
        }
        $template = $_ctrl->ctrl_root . '/_Config/template.php';
        Debug::bug( 'table', $_siteObj, 'template data' );
        PhpHtmlTemplate::inject( $template, $_siteObj );
    }
    // +-------------------------------------------------------------+
    function showDebugInfo( $ctrl ) {
        $debugInfo = Debug::result();
        if( !$debugInfo ) return '';
        return $debugInfo;
    }
    // +-------------------------------------------------------------+
    function findMimeType( $_file_ext ) {
        switch( strtolower( $_file_ext ) ) {
            case 'css':
                $mime = 'text/css';
                break;
            case 'js':
            case 'javascript':
                $mime = 'text/javascript';
                break;
            case 'jpg':
            case 'jpeg':
                $mime = 'image/jpeg';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'pdf':
                $mime = 'application/pdf';
                break;
            default:
                $mime = 'application/octet-stream';
                break;
        }
        return $mime;
    }
    // +-------------------------------------------------------------+
}

class PhpHtmlTemplate
{
    static $ext_php  = array( 'php' );
    static $ext_html = array( 'html', 'html' );
    static $ext_md   = array( 'md', 'markdown' );
    static $ext_text = array( 'text', 'txt' );
    // +-------------------------------------------------------------+
    function inject( $template, $_siteObj ) {
        $_data = $_siteObj->getHtml();
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
        include_once( __DIR__ .  '/../../../vendor/PHPMarkdown/markdown.php' );
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