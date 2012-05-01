<?php
namespace AmidaMVC\Tools;

class Emit
{
    // +-------------------------------------------------------------+
    /**
     * inject data into template, and returns the result.
     * @static
     * @param string $template
     * @param array $content_data
     * @return string
     */
    static function inject( $template, $content_data ) {
        ob_start();
        ob_implicit_flush(0);
        self::injectTemplate( $template, $content_data );
        $content = ob_get_clean();
        return $content;
    }
    // +-------------------------------------------------------------+
    /**
     * inject data into template.
     * @static
     * @param $template
     * @param $__content_data
     */
    static function injectTemplate( $template, $__content_data ) {
        if( file_exists( $template ) && !is_dir( $template ) ) {
            if( is_array( $__content_data ) ) { extract( $__content_data ); }
            include( $template );
        }
    }
    // +-------------------------------------------------------------+
    /**
     * convert contents to html according to type.
     * @static
     * @param string $content
     * @param string $type
     * @return string
     */
    static function convertContentToHtml( &$content, &$type ) {
        switch( $type ) {
            case 'md':
            case 'markdown':
                static::convertMarkdown( $content );
                $type    = 'html';
                break;
            case 'text':
                $content = '<pre>' . $content . '</pre>';
                $type    = 'html';
                break;
            case 'php':
                $content = highlight_string( $content, TRUE );
                $type    = 'html';
            default:
                break;
        }
    }
    // +-------------------------------------------------------------+
    /**
     * load markdown file into view object.
     * @param string $content
     * @return string
     */
    static function convertMarkdown( &$content ) {
        include_once( __DIR__ .  '/../../../vendor/PHPMarkdownExtra/markdown.php' );
        $content = Markdown( $content );
    }
    // +-------------------------------------------------------------+
    /**
     * find mime type from file type.
     * @static
     * @param $file_type
     * @return string
     */
    static function findMimeType( $file_type ) {
        switch( strtolower( $file_type ) ) {
            case 'html':
            case 'htm':
                $mime = 'text/html';
                break;
            case 'text':
            case 'md':
            case 'markdown':
            case 'txt':
                $mime = 'text/plain';
                break;
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