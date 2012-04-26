<?php
namespace AmidaMVC\Tools;

class Load
{
    /**
     * @var array   file extensions to load as is.
     */
    static $ext_asis = array( 'css', 'js', 'pdf', 'png', 'jpg', 'gif' );
    /**
     * @var array   file extensions to load.
     */
    static $ext_view = array(
        'php'      => 'html',
        'html'     => 'html',
        'htm'      => 'html',
        'md'       => 'markdown',
        'markdown' => 'markdown',
        'text'     => 'text',
        'txt'      => 'text',
    );
    /**
     * @var array   file extensions to edit as file.
     */
    static $ext_text = array(
        'css'   => 'css',
        'js'    => 'javascript'
    );
    // +-------------------------------------------------------------+
    static function isWhat( $what, $file ) {
        return static::$what( $file );
    }
    // +-------------------------------------------------------------+
    /**
     * checks if file is viewable in standard way. 
     * @static
     * @param string $file   filename to check
     * @return bool
     */
    static function isView( $file ) {
        $ext  = pathinfo( $file, PATHINFO_EXTENSION );
        return isset( self::$ext_view[ $ext ] );
    }
    // +-------------------------------------------------------------+
    /**
     * checks if file is editable. ex: html, js, css, md, etc.
     * @static
     * @param string $file   filename to check
     * @return bool
     */
    static function isText( $file ) {
        $ext  = pathinfo( $file, PATHINFO_EXTENSION );
        return isset( self::$ext_view[ $ext ] ) || isset( self::$ext_text[ $ext ]  );
    }
    // +-------------------------------------------------------------+
    /**
     * checks  if file is read as is. ex: jpg, pdf, etc.
     * @static
     * @param string $file   filename to check
     * @return bool
     */
    static function isAsIs( $file ) {
        $ext  = pathinfo( $file, PATHINFO_EXTENSION );
        return in_array( $ext, self::$ext_asis );
    }
    // +-------------------------------------------------------------+
    static function getFileType( $file ) {
        $ext  = pathinfo( $file, PATHINFO_EXTENSION );
        if( isset( self::$ext_view[ $ext ] ) ) {
            return self::$ext_view[ $ext ];
        }
        if( isset( self::$ext_text[ $ext ] ) ) {
            return self::$ext_text[ $ext ];
        }
        return $ext;
    }
    // +-------------------------------------------------------------+
    /**
     * get file source using file_get_contents function.
     * @static
     * @param $file_name
     * @return null|string
     */
    static function getContentsByGet( $file_name ) {
        return file_get_contents( $file_name );
    }
    // +-------------------------------------------------------------+
    /**
     * get file contents by executing file and ob_{start|get_clean}.
     * @static
     * @param $file_name
     * @return string
     */
    static function getContentsByBuffer( $file_name ) {
        ob_start();
        ob_implicit_flush(0);
        require( $file_name );
        $content = ob_get_clean();
        return $content;
    }
    // +-------------------------------------------------------------+
}