<?php
namespace AmidaMVC\Tools;

class i18n
{
    // +-------------------------------------------------------------+
    /** @var \AmidaMVC\Tools\Load */
    protected $load;
    protected $directory   = '_Config';
    protected $language    = 'en';
    protected $extension   = 'ini';
    protected $textSection = '';
    protected $textData    = array();
    // +-------------------------------------------------------------+
    function __construct( $config=array() ) {}
    function _init( $config=array() ) {}
    function _setup( $config ) {
        if( empty( $config ) ) {
            return ;
        }
        foreach( $config as $key => $file ) {
            if( substr( $key, 0, 5 ) == 'file_' ) {
                $this->_loadTextFile( $file );
            }
        }
    }
    function injectLoad( $load ) {
        $this->load = $load;
    }
    function _loadTextFile( $filename ) {
        $loadFile  = ( $this->directory ) ? $this->directory.'/' : '';
        $loadFile .= "{$filename}.{$this->language}.{$this->extension}";
        if( !$found = $this->load->findFile( $loadFile ) ) {
            // assume ini file
            $textData = $this->load->parse_ini( $found );
            $this->textData = array_merge( $this->textData, $textData );
        }
    }
    // +-------------------------------------------------------------+
    function directory( $dir='_Config' ) {
        $this->directory = $dir;
    }
    function extension( $ext='ini' ) {
        $this->extension = $ext;
    }
    function language( $lang ) {
        $this->language = substr( $lang, 0, 2 );
    }
    // +-------------------------------------------------------------+
    function textSection( $section ) {
        $this->textSection = $section;
    }
    function text( $text ) {
        if( !isset( $this->textData[ $this->textSection ][ $text ] ) ) {
            return FALSE;
        }
        $words = $this->textData[ $this->textSection ][ $text ];
        $args  = array_shift( func_get_args() );
        if( !empty( $args ) ) $words = $this->_replace( $words, $args );
        return $words;
    }
    function _replace( $words, $args ) {
        foreach( $args as $key => $val ) {
            if( is_array( $val ) ) {
                $words = $this->_replace( $words, $val );
            }
            else {
                $words = str_replace( ":{$key}", $val, $words );
            }
        }
        return $words;
    }
    // +-------------------------------------------------------------+
}
