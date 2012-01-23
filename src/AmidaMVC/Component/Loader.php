<?php
namespace AmidaMVC\Component;

/**
 * Loader class to load file.
 */
class Loader
{
    // +-------------------------------------------------------------+
    static function _init() {
    }
    // +-------------------------------------------------------------+
    /**
     * loads file.
     * specify absolute path of a file to load in $loadInfo[ 'file' ].
     * @static
     * @param $ctrl
     * @param $data
     * @param $loadInfo    info about file to load from Router.
     */
    static function actionDefault( $ctrl, &$data, $loadInfo ) {
        $file_name = $loadInfo['file'];
        $base_name  = basename( $file_name );
        $file_ext  = pathinfo( $file_name, PATHINFO_EXTENSION );
        $action    = ( $loadInfo['action'] ) ? $loadInfo['action'] : $ctrl->currAct();
        \AmidaMVC\Component\Debug::bug( 'head', "loading file: ".$file_name );
        \AmidaMVC\Framework\Event::fire( 'Loader::load', $loadInfo );
        $ctrl->currAct( $action );
        if( $file_ext == 'php' && substr( $base_name, 0, 4 ) == '_App' ) {
            include $loadInfo[ 'file' ];
        }
        else if( $file_ext == 'php' ) {
            self::loadPhpAsCode( $data, $loadInfo );
        }
        else if( in_array( $file_ext, array( 'html', 'html' ) ) ) {
            self::loadHtml( $data, $loadInfo );
        }
        else if( in_array( $file_ext, array( 'text', 'md', 'markdown', 'mark' ) ) ) {
            self::loadMarkdown( $data, $loadInfo );
        }
        else if( in_array( $file_ext, array( 'css', 'js', 'pdf', 'png', 'jpg', 'gif' ) ) ) {
            self::loadAsIs( $data, $loadInfo, $file_ext );
        }
    }
    // +-------------------------------------------------------------+
    function actionPageNotFound( $ctrl, &$data ) {
        // do something about error 404, a file not found.
        // maybe load sorry file.
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
        }
        return $mime;
    }
    // +-------------------------------------------------------------+
    function loadAsIs( &$data, $loadInfo, $_file_ext ) {
        $data->setHttpContent( file_get_contents( $loadInfo[ 'file' ] ) );
        $mime  = self::findMimeType( $_file_ext );
        $data->setMimeType( $mime );
    }
    // +-------------------------------------------------------------+
    function loadPhpAsCode( &$data, $loadInfo ) {
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $content = highlight_string( $content, TRUE );
        $title   = pathinfo( $loadInfo[ 'file' ], PATHINFO_BASENAME );
        $data->setTitle( $title );
        $data->setContents( $content );
    }
    // +-------------------------------------------------------------+
    /** load html file into view object.
     * @param $data
     * @param $loadInfo
     */
    function loadHtml( &$data, $loadInfo ) {
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $title   = self::extractTitle( $content );
        if( $title  ) {
            $data->setTitle( $title );
        }
        $data->setContents( $content );
    }
    // +-------------------------------------------------------------+
    /** load markdown file into view object.
     * @param $data
     * @param $loadInfo
     * @return void
     */
    function loadMarkdown( &$data, $loadInfo ) {
        include_once( __DIR__ .  '/../../../vendor/PHPMarkdown/markdown.php' );
        $content = file_get_contents( $loadInfo[ 'file' ] );
        $title   = self::extractTitle( $content );
        if( $title  ) {
            $data->setTitle( $title );
        }
        $content = Markdown( $content );
        $data->setContents( $content );
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
    static function getAction( $string ) {
        if( is_array( $string ) ) $string = $string[0];
        $action = preg_replace( '/[^._a-zA-Z0-9]/m', '', $string );
        return $action;
    }
    // +-------------------------------------------------------------+
}


