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
    protected $fileToLoad  = array();
    protected $listOfLang  = array(
        'en' => 'English',
        'es' => 'español',
        'fr' => 'French',
        'ja' => '日本語',
        'ko' => '贛語',
        'zh' => '中文',
    );
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
                $this->fileToLoad[ $val ] = TRUE;
            }
            elseif( $key == 'language' ) {
                $this->language( $val );
            }
            elseif( $key == 'directory' ) {
                $this->directory( $val );
            }
            elseif( $key == 'loadFiles' ) {
                $this->loadFiles();
            }
        }
    }
    function injectLoad( $load ) {
        $this->load = $load;
    }
    function loadFiles() {
        if( !empty( $this->fileToLoad ) ) {
            foreach( $this->fileToLoad as $file => $val ) {
                $this->_loadTextFile( $file );
            }
        }
    }
    function _loadTextFile( $filename ) {
        $loadFile = $this->directory . self::FILE_HEADER_TEXT . $filename . '.'.$this->language;
        $found = $this->load->findFile( $loadFile.'.ini' );
        if( $found ) {
            $textData = $this->load->parse_ini( $found );
            $this->_mergeText( $textData );
        }
    }
    function _mergeText( $textData ) {
        foreach( $textData as $section => $data ) {
            if( isset( $this->textData[ $section ] ) ) {
                $this->textData[ $section ] = array_merge( $this->textData[ $section ], $data );
            }
            else {
                $this->textData[ $section ] = $data;
            }
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
        $this->config[ 'language' ] = $lang;
        $this->language = substr( $lang, 0, 2 );
    }
    function langCode( $code ) {
        return (isset( $this->listOfLang[$code])) ? $this->listOfLang[$code]:FALSE;
    }
    // +-------------------------------------------------------------+
    function textSection( $section ) {
        $this->textSection = $section;
    }
    function html() {
        $args  = func_get_args();
        $text = call_user_func_array( array( $this, 'text' ), $args );
        return nl2br( $text );
    }
    function text( $text ) {
        if( !isset( $this->textData[ $this->textSection ][ $text ] ) ) {
            return $text;
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
