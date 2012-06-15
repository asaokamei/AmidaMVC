<?php
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

class load
{
    var $loadFile;
    var $textData = array(
        'section1' => array(
            'word' => 'WORD',
            'print' => 'print :0 and :1',
        ),
        'section2' => array(
            'word' => 'WORD2',
        ),
    );
    function findFile( $loadFile ) {
        $this->loadFile = $loadFile;
        return TRUE;
    }
    function parse_ini( $found ) {
        return $this->textData;
    }
}

class test_ToolsI18n extends PHPUnit_Framework_TestCase
{
    /** @var \AmidaMVC\Tools\i18n */
    var $i18n;
    /** @var load */
    var $load;
    var $config;
    function setUp()
    {
        $this->load = new load();
        $this->config = array(
            'file_something' => 'something.ini',
        );
        $this->i18n = new \AmidaMVC\Tools\i18n( $this->config );
        $this->i18n->injectLoad( $this->load );
        $this->i18n->_init();
    }
    function test_replaceText()
    {
        $section = 'section1';
        $input   = 'print';
        $textData= $this->load->parse_ini( TRUE );
        $expect  = $textData[ $section ][ $input ];
        $test1   = 'test1';
        $test2   = 'test2';
        $expect  = str_replace( ':0', $test1, $expect );
        $expect  = str_replace( ':1', $test2, $expect );

        $this->i18n->textSection( $section );
        $result  = $this->i18n->text( $input, $test1, $test2 );
        $this->assertEquals( $expect, $result );
    }
    function test_simpleText()
    {
        $section = 'section1';
        $input   = 'word';
        $textData= $this->load->parse_ini( TRUE );
        $expect  = $textData[ $section ][ $input ];

        $this->i18n->textSection( $section );
        $result  = $this->i18n->text( $input );
        $this->assertEquals( $expect, $result );
    }
    function test_sectionText()
    {
        $section = 'section2';
        $input   = 'word';
        $textData= $this->load->parse_ini( TRUE );
        $expect  = $textData[ $section ][ $input ];

        $this->i18n->textSection( $section );
        $result  = $this->i18n->text( $input );
        $this->assertEquals( $expect, $result );
    }
}