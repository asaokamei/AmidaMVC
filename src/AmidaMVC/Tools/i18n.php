<?php
namespace AmidaMVC\Tools;

class i18n
{
    // +-------------------------------------------------------------+
    const FILE_HEADER_TEXT = 'i18n.text.';
    protected $config      = array();
    /** @var \AmidaMVC\Tools\Load */
    protected $load        = NULL;
    protected $directory   = '_Config/';
    protected $language    = '';
    protected $textSection = '';
    protected $textData    = array();
    // +-------------------------------------------------------------+
    function __construct( $config=array() ) {
        $this->config = $config;
    }
    function _init( $config=array() ) {
        $this->config = array_merge( $this->config, $config );
        $this->_setup();
    }
    function _setup() {
        if( empty( $this->config ) ) {
            return ;
        }
        foreach( $this->config as $key => $val ) {
            if( substr( $key, 0, 5 ) == 'file_' ) {
                $this->_loadTextFile( $val );
            }
            elseif( $key == 'language' ) {
                $this->language( $val );
            }
            elseif( $key == 'directory' ) {
                $this->directory( $val );
            }
        }
    }
    function injectLoad( $load ) {
        $this->load = $load;
    }
    function _loadTextFile( $filename ) {
        $found = FALSE;
        $loadFile_default  = $this->directory . self::FILE_HEADER_TEXT . $filename;
        if( $this->language ) {
            $loadFile_lang     = $loadFile_default . '.'.$this->language;
            $found = $this->load->findFile( $loadFile_lang.'.ini' );
        }
        if( !$found ) {
            $found = $this->load->findFile( $loadFile_default.'.ini' );
        }
        if( $found ) {
            // assume ini file
            $textData = $this->load->parse_ini( $found );
            $this->textData = array_merge( $this->textData, $textData );
        }
    }
    // +-------------------------------------------------------------+
    function directory( $dir='_Config/' ) {
        $this->directory = (substr( $dir, -1, 1 )=='/')?$dir:$dir.'/';
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
        $args  = func_get_args();
        array_shift( $args );
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
