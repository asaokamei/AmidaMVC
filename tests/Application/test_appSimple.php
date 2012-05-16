<?php
namespace AmidaMVC\Tests\Application\AppSimple;
error_reporting( E_ALL );
require( __DIR__ . '/../../src/AmidaMVC/bootstrap.php' );

class testLoad extends \AmidaMVC\Tools\Load
{
    // still static!!!
    static $fileSys = array();
    static function setFiles( $files ) {
        static::$fileSys = $files;
    }
    static function getContentsByGet( $file_name ) {
        return ( isset( static::$fileSys[ $file_name ] ) ) ? static::$fileSys[ $file_name ]: FALSE;
    }
    static function getContentsByBuffer( $file_name ) {
        return static::getContentsByGet( $file_name );
    }
    function findFile( $file_name ) {
        $found = FALSE;
        if( empty( static::$_loadFolder ) ) return $found;
        foreach( static::$_loadFolder as $folder ) {
            $check_file = $folder. '/' . $file_name;
            if( isset( static::$fileSys[ $check_file ] ) ) {
                $found = $check_file;
                break;
            }
        }
        return $found;
    }
    function putContents( $filename, $content ) {
        static::$fileSys[ $filename ] = $content;
        return TRUE;
    }
    function exists( $filename ) {
        return isset( static::$fileSys[ $filename ] );
    }
    function mkDir( $dir, $permission ) {
        static::$fileSys[ $dir ] = NULL;
        return TRUE;
    }
    function rename( $file1, $file2 ) {
        static::$fileSys[ $file2 ] = static::$fileSys[ $file1 ];
        unset( static::$fileSys[ $file1 ] );
        return TRUE;
    }
    function isDir( $dir ) {
        if( array_key_exists( $dir, static::$fileSys ) && !isset( static::$fileSys[ $dir ] ) ) {
            return TRUE;
        }
        return FALSE;
    }
    function glob( $pattern, $flag=NULL ) {
        $found = array();
        if( strpos( $pattern, '{' ) !== FALSE ) {
            $pattern = substr( $pattern, 0, strpos( $pattern, '{' ) );
            $pattern .= substr( $pattern, strpos( $pattern, '{' ) + 1, strpos( $pattern, ',' ) );
        }
        foreach( static::$fileSys as $file => $content ) {
            if( substr( $file, 0, strlen( $pattern ) ) == $pattern ) {
                $found[] = $file;
            }
        }
        return $found;
    }
    function unlink( $filename ) {
        if( array_key_exists( $filename, static::$fileSys ) ) {
            unset( static::$fileSys[ $filename ] );
            return TRUE;
        }
        return FALSE;
    }
}

class test_ITest_AppSimple extends \PHPUnit_Framework_TestCase
{
    /** @var \AmidaMVC\Framework\Controller */
    var $app;
    /** @var \AmidaMVC\Framework\Container */
    var $di;
    /** @var array */
    var $req;
    function setUp() {
        $this->di = \AmidaMVC\Framework\Container::getInstance();
        $this->req = array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/url/to/',
            'SCRIPT_NAME' => '/path/to/',
        );
    }
    function test_1st() {
        $this->di->setModule( '\AmidaMVC\Tools\Request', '\AmidaMVC\Tools\Request', 'get', $this->req  );
        $this->app = \AmidaMVC\Application\Application::simple( array(
            '' => '',
        ) );
        $this->app->start();
        $this->assertTrue( FALSE );
    }
}
