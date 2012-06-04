<?php
namespace AmidaMVC\Tools;

class Load
{
    /**
     * @var array   file extensions to load as is.
     */
    var $ext_asIs = array( 'css', 'js', 'pdf', 'png', 'jpg', 'gif' );
    /**
     * @var array   file extensions to load.
     */
    var $ext_view = array(
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
    var $ext_text = array(
        'css'   => 'css',
        'js'    => 'javascript'
    );
    /**
     * @var array   folders to look files for.
     */
    var $_loadFolder = array();
    // +-------------------------------------------------------------+
    function __construct( $option=array() ) {
        if( !empty( $option ) && is_array( $option ) ) {
            foreach( $option as $root ) {
                $this->setFileLocation( $root );
            }
        }
    }
    // +-------------------------------------------------------------+
    function isWhat( $what, $file ) {
        return $this->$what( $file );
    }
    // +-------------------------------------------------------------+
    /**
     * checks if file is viewable in standard way. 
     * @static
     * @param string $file   filename to check
     * @return bool
     */
    function isView( $file ) {
        $ext  = pathinfo( $file, PATHINFO_EXTENSION );
        return isset( $this->ext_view[ $ext ] );
    }
    // +-------------------------------------------------------------+
    /**
     * checks if file is editable. ex: html, js, css, md, etc.
     * @static
     * @param string $file   filename to check
     * @return bool
     */
    function isText( $file ) {
        $ext  = pathinfo( $file, PATHINFO_EXTENSION );
        return isset( $this->ext_view[ $ext ] ) || isset( $this->ext_text[ $ext ]  );
    }
    // +-------------------------------------------------------------+
    /**
     * checks  if file is read as is. ex: jpg, pdf, etc.
     * @static
     * @param string $file   filename to check
     * @return bool
     */
    function isAsIs( $file ) {
        $ext  = pathinfo( $file, PATHINFO_EXTENSION );
        return in_array( $ext, $this->ext_asIs );
    }
    // +-------------------------------------------------------------+
    function getFileType( $file ) {
        $ext  = pathinfo( $file, PATHINFO_EXTENSION );
        if( isset( $this->ext_view[ $ext ] ) ) {
            return $this->ext_view[ $ext ];
        }
        if( isset( $this->ext_text[ $ext ] ) ) {
            return $this->ext_text[ $ext ];
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
    function getContentsByGet( $file_name ) {
        return file_get_contents( $file_name );
    }
    // +-------------------------------------------------------------+
    /**
     * get file contents by executing file and ob_{start|get_clean}.
     * @static
     * @param string $file_name      file name to include
     * @internal param array $option extracted for included file.
     * @return string
     */
    function getContentsByBuffer( $file_name ) {
        if( func_num_args() > 1 && is_array( func_get_arg(1) ) ) {
            extract( func_get_arg(1) );
        }
        ob_start();
        ob_implicit_flush(0);
        require( $file_name );
        $content = ob_get_clean();
        return $content;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $folder
     * @return Load
     */
    function setFileLocation( $folder ) {
        if( !is_array( $folder ) ) {
            $folder = array( $folder );
        }
        $this->_loadFolder = array_merge( $folder, $this->_loadFolder );
        return $this->_loadFolder;
    }
    /**
     * find file_name from $this->loadFolder list and returns the
     * full path.
     * @param string $file_name
     * @return string
     */
    function findFile( $file_name ) {
        $found = FALSE;
        if( empty( $this->_loadFolder ) ) return $found;
        foreach( $this->_loadFolder as $folder ) {
            $check_file = $folder. '/' . $file_name;
            if( file_exists( $check_file ) ) {
                $found = $check_file;
                break;
            }
        }
        return $found;
    }
    /**
     * @param $className
     * @return bool
     */
    function loadClassFile( $className ) {
        if( !class_exists( $className ) ) {
            $base_name = substr( $className, strrpos( $className, '\\' ) ) . '.php';
            if( $found = static::findFile( $base_name ) ) {
                require_once( $found );
                return TRUE;
            }
            return FALSE;
        }
        return TRUE;
    }
    /**
     * @param string $filename
     * @param string $content
     * @return int
     */
    function putContents( $filename, $content ) {
        return @file_put_contents( $filename, $content );
    }
    /**
     * @param string $filename
     * @return bool
     */
    function exists( $filename ) {
        return file_exists( $filename );
    }
    /**
     * @param string $dir
     * @param string $permission
     * @return bool
     */
    function mkDir( $dir, $permission ) {
        return mkdir( $dir, $permission );
    }
    /**
     * @param string $file1
     * @param string $file2
     * @return bool
     */
    function rename( $file1, $file2 ) {
        return rename( $file1, $file2 );
    }
    /**
     * @param string $dir
     * @return bool
     */
    function isDir( $dir ) {
        return is_dir( $dir );
    }
    function search( $path, $pattern, $flag=NULL ) {
        if( !isset( $flag ) ) $flag = GLOB_MARK|GLOB_BRACE;
        if( is_array( $pattern ) ) {
            $pattern = '{' .implode( ',', $pattern ) . '}';
        }
        $found = array();
        if( empty( $this->_loadFolder ) ) return $found;
        foreach( $this->_loadFolder as $folder ) {
            $search = $folder . '/' . $path . $pattern;
            $found = glob( $search, $flag );
            if( !empty( $found ) ) break;
        }
        return $found;
    }
    /**
     * @param string $pattern
     * @param null|int $flag      default is GLOB_MARK|GLOB_BRACE
     * @return array
     */
    function glob( $pattern, $flag=NULL ) {
        if( !isset( $flag ) ) $flag = GLOB_MARK|GLOB_BRACE;
        return glob( $pattern, $flag );
    }
    /**
     * @param string $filename
     * @return bool
     */
    function unlink( $filename ) {
        return unlink( $filename );
    }
    // +-------------------------------------------------------------+
}