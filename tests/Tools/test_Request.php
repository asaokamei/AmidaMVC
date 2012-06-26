<?php
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

class test_ToolsRequest extends PHPUnit_Framework_TestCase
{
    function test_AcceptLang_ja_and_en_2() {
        $server = array(
            'HTTP_ACCEPT_LANGUAGE' => 'ja;q=0.8,en;q=0.4,fr'
        );
        $req  = new \AmidaMVC\Tools\Request( $server );
        $list = $req->getLanguageList();
        $listOK = array( 'fr', 'ja', 'en' );
        $this->assertEquals( $listOK, $list );
    }
    function test_AcceptLang_ja_and_en_3() {
        $server = array(
            'HTTP_ACCEPT_LANGUAGE' => 'abc,ja;q=0.8,en'
        );
        $req  = new \AmidaMVC\Tools\Request( $server );
        $list = $req->getLanguageList();
        $listOK = array( 'abc', 'en', 'ja' );
        $this->assertEquals( $listOK, $list );
    }
    function test_AcceptLang_ja_and_en_1() {
        $server = array(
            'HTTP_ACCEPT_LANGUAGE' => 'ja;q=0.8,en'
        );
        $req  = new \AmidaMVC\Tools\Request( $server );
        $list = $req->getLanguageList();
        $listOK = array( 'en', 'ja' );
        $this->assertEquals( $listOK, $list );
    }
}