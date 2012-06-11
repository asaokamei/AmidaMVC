<?php
namespace AmidaMVC\Module;

class Filer implements IfModule
{
    protected $mode = '_dev';
    protected $listJs = array();
    protected $listCss = array();
    protected $filerInfo = array(
        'file_mode' => '_filer',
        'file_cmd'  => array(),
        'backup_list' => array(),
        'file_src' => '',
        'curr_folder' => '',
        'file_list ' => array(),
        'error' => FALSE,
        'message' => '',
    );
    protected $devTemplateDefault = 'template._dev.php';
    protected $devTemplate = FALSE;
    protected $commandList = array(
        '_fEdit', '_fPut', '_fPub', '_fDel', '_fPurge', '_fDiff',
        '_bkView', '_bkDiff',
        '_fDir', '_fFile'
    );
    /**
     * @var \AmidaMVC\Tools\Load   static class for loading methods.
     */
    protected $_loadClass = '\AmidaMVC\Tools\Load';
    protected $backup    = '_Backup';
    protected $editors = array(
        'html'       => '\AmidaMVC\Editor\jHtmlArea',
        'markdown'   => '\AmidaMVC\Editor\jsMarkdownExtra',
        'text'       => '\AmidaMVC\Editor\TextArea',
        'css'        => '\AmidaMVC\Editor\TextArea',
        'javascript' => '\AmidaMVC\Editor\TextArea',
    );
    /** @var \AmidaMVC\Framework\Controller */
    protected $_ctrl;
    /** @var \AmidaMVC\Framework\PageObj */
    protected $_pageObj;
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     */
    function __construct( $option=array() ) {
        $this->setup( $option );
    }
    function _init( $option=array() ) {
        $this->setup( $option );
    }
    function injectLoad( $load ) {
        $this->_loadClass = $load;
    }
    function setup( $option=array() ) {
        if( isset( $option[ 'listJs' ] ) ) {
            $this->listJs = $option[ 'listJs' ];
        }
        if( isset( $option[ 'listCss' ] ) ) {
            $this->listCss = $option[ 'listCss' ];
        }
        if( isset( $option[ 'mode' ] ) ) {
            $this->mode = $option[ 'mode' ];
        }
        if( isset( $option[ 'template_file' ] ) ) {
            $this->devTemplate = $option[ 'template_file' ];
        }
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     */
    function _setMenu( &$loadInfo ) {
        // set JavaScript and CSS files for developer's menu...
        if( !empty( $this->listJs ) ) {
            foreach( $this->listJs as $js ) {
                $this->_pageObj->setJs( $js );
            }
        }
        if( !empty( $this->listCss ) ) {
            foreach( $this->listCss as $css ) {
                $this->_pageObj->setCss( $css );
            }
        }
        // set file lists under the folder.
        /** @var $filer_folder string    folder where filer is looking at. */
        /** @var $path_folder string     path folder where filer is looking at */
        $path_folder = $this->_ctrl->getPathInfo();
        $path_folder = ( substr( $path_folder, -1 ) == '/' ) ? substr( $path_folder, 0, -1 ) : dirname( $path_folder );
        if( isset( $loadInfo[ 'file' ] ) ) {
            $filer_folder = ( is_dir( $loadInfo[ 'file' ] ) ) ?
                $loadInfo[ 'file' ] : dirname( $loadInfo[ 'file'] );
        }
        else {
            $filer_folder = $this->_ctrl->getLocation( $path_folder );
        }
        $this->filerInfo[ 'curr_folder' ] = $filer_folder;
        $this->filerInfo[ 'path_folder' ] = $path_folder;
        // gets list of files under the filer_folder.
        $file_list = call_user_func( array( $this->_loadClass, 'search' ), $filer_folder.'/', "*" );
        sort( $file_list );
        $len_folder = strlen( $filer_folder ) + 1;
        foreach( $file_list as &$file ) {
            $file = substr( $file, $len_folder );
        }
        $this->filerInfo[ 'file_list' ] = $file_list;

        // list backup files
        $backup_glob   = $this->_backupFileName( $loadInfo[ 'file' ], '*' );
        $backup_list   = call_user_func( array( $this->_loadClass, 'glob' ), $backup_glob );
        if( !empty( $backup_list ) ) {
            foreach( $backup_list as &$backup ) {
                $this->filerInfo[ 'backup_list' ][] = basename( $backup );
            }
        }
        // set up menu
        $file_to_edit = $this->_getFileToEdit( $loadInfo[ 'file' ] );
        if( !isset( $loadInfo[ 'file' ] ) ) {
        }
        elseif( call_user_func( array( $this->_loadClass, 'exists' ), $file_to_edit ) ) {
            $loadInfo[ 'file_edited' ] = $file_to_edit;
            $this->filerInfo[ 'file_src' ]   = basename( $file_to_edit );
            $this->filerInfo[ 'file_cmd' ][] = '_view';
            $this->filerInfo[ 'file_cmd' ][] = '_fEdit';
            $this->filerInfo[ 'file_cmd' ][] = '_fPub';
            $this->filerInfo[ 'file_cmd' ][] = '_fDel';
            $this->filerInfo[ 'file_cmd' ][] = '_fDiff';
        }
        else {
            $loadInfo[ 'file_edited' ] = FALSE;
            $this->filerInfo[ 'file_src' ]   = basename( $loadInfo[ 'file' ] );
            $this->filerInfo[ 'file_cmd' ][] = '_view';
            $this->filerInfo[ 'file_cmd' ][] = '_fEdit';
            $this->filerInfo[ 'file_cmd' ][] = '_fPurge';
        }
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array|mixed
     */
    function actionDefault( $_ctrl, &$_pageObj, $loadInfo=array() )
    {
        $this->_ctrl = $_ctrl;
        $this->_pageObj = $_pageObj;
        $this->_setMenu( $loadInfo );
        $command = $this->_findFilerCommand( $_ctrl );
        if( $command ) {
            $method = 'action' . $command;
            $loadInfo = $this->$method( $loadInfo );
        }
        else {
            $loadInfo[ 'file' ] = ( $loadInfo[ 'file_edited' ] ) ?: $loadInfo[ 'file' ];
        }
        $this->_template();
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_bkView( $loadInfo=array() )
    {
        $bk_file = $_GET[ 'bf' ];
        $backup_folder = $this->_backupFolder( $loadInfo[ 'file' ] );
        $backup_file = $backup_folder . '/' . $bk_file;
        if( call_user_func( array( $this->_loadClass, 'exists' ), $backup_file ) ) {
            $loadInfo[ 'file' ] = $backup_file;
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_bkDiff( $loadInfo=array() )
    {
        $bk_file = $_GET[ 'bf' ];
        $backup_folder = $this->_backupFolder( $loadInfo[ 'file' ] );
        $backup_file = $backup_folder . '/' . $bk_file;
        if( call_user_func( array( $this->_loadClass, 'exists' ), $backup_file ) ) {
            $lines1   = htmlspecialchars(
                call_user_func( array( $this->_loadClass, 'getContentsByGet' ), $loadInfo[ 'file' ] ) );
            $lines2   = htmlspecialchars(
                call_user_func( array( $this->_loadClass, 'getContentsByGet' ), $backup_file ) );
            $this->_pageObj->setContent( array( 'lines1' => $lines1, 'lines2' => $lines2 ) );
            $this->_pageObj->contentType( 'diff' );
            $this->_ctrl->skipToModel( 'emitter' );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_fDir( $loadInfo=array() )
    {
        if( !isset( $_POST['_folderName'] ) || empty( $_POST['_folderName'] ) ) {
            $this->_error( 'add new folder error:',
                'enter folder name to add.' );
        }
        else {
            $folder = dirname( $loadInfo['file'] ) . '/' . $_POST['_folderName'];
            if( file_exists( $folder ) ) {
                $this->_error( 'add new folder error:',
                    'folder already exists: ' . $_POST['_filderName'] );
            }
            elseif( call_user_func( array( $this->_loadClass, 'mkdir' ), $folder, 0777 ) ) {
                $this->_ctrl->redirect( $this->_ctrl->getPathInfo() );
            }
            else {
                $this->_error( 'add new folder error:',
                    'failed to create folder: ' . $_POST['_filderName'] .
                        'maybe folder\'s permission problem?'
                );
            }
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_fPurge( $loadInfo=array() )
    {
        if( $loadInfo[ 'file_edited' ] ) {
            $this->_error(
                'purge error',
                "cannot purge an edit file: " . $loadInfo[ 'file_edited' ]
            );
        }
        elseif( !call_user_func( array( $this->_loadClass, 'exists' ), $loadInfo[ 'file' ] ) ) {
            $this->_error(
                'purge error',
                "file does not exists: " . $loadInfo[ 'file' ]
            );
        }
        else {
            if( call_user_func( array( $this->_loadClass, 'unlink' ), $loadInfo[ 'file' ] ) ) {
                $this->_ctrl->redirect( $this->_ctrl->getPathInfo() );
            }
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_fDel( $loadInfo=array() )
    {
        if( isset( $loadInfo[ 'file_edited' ] ) ) {
            if( call_user_func( array( $this->_loadClass, 'unlink' ), $loadInfo[ 'file_edited' ] ) ) {
                $this->_ctrl->redirect( $this->_ctrl->getPathInfo() );
            }
            $this->_error(
                'delete error',
                "failed to delete edited file: " . $loadInfo[ 'file_edited' ]
            );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_fDiff( $loadInfo=array() ) {
        if( !isset( $loadInfo[ 'file_edited' ] ) ) {
            $file_to_edit = $this->_getFileToEdit( $loadInfo[ 'file' ] );
            $this->_error(
                'diff error',
                "there are no edit file to diff: {$file_to_edit}. \n"
            );
        }
        else {
            $lines1   = htmlspecialchars(
                call_user_func( array( $this->_loadClass, 'getContentsByGet' ), $loadInfo[ 'file' ] ) );
            $lines2   = htmlspecialchars(
                call_user_func( array( $this->_loadClass, 'getContentsByGet' ), $loadInfo[ 'file_edited' ] ) );
            $this->_pageObj->setContent( array( 'lines1' => $lines1, 'lines2' => $lines2 ) );
            $this->_pageObj->contentType( 'diff' );
            $this->_ctrl->skipToModel( 'emitter' );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_fPub( $loadInfo ) {
        $file_to_publish = $loadInfo[ 'file_edited' ];
        if( call_user_func( array( $this->_loadClass, 'exists' ),  $file_to_publish ) ) {
            $file_to_replaced = $loadInfo[ 'file' ];
            $this->_backup( $file_to_replaced );
            if( rename( $file_to_publish, $file_to_replaced ) ) {
                // success
                $this->_ctrl->redirect( $this->_ctrl->getPathInfo() );
            }
            else {
                $this->_error(
                    'publish error',
                    "could not rename file from {$file_to_publish} to {$file_to_replaced}. \n"
                );
            }
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * create a form to add a new file. check if the given file name
     * is really new (no existing file or directory). This function
     * needs to know where is the working directory because _newFileName
     * contains only the file name (no folder info).
     *
     * @param array $loadInfo
     * @return array
     */
    function action_fFile( $loadInfo ) {
        $new_file = $_POST[ '_newFileName' ];
        $path_info = $this->_ctrl->getPathInfo();
        if( substr( $path_info, -1 ) === '/' ) {
            $path_info = $path_info . $new_file;
        }
        else {
            $path_info = dirname( $path_info ) . '/' . $new_file;
        }
        $file_to_edit = $this->_ctrl->getLocation( $path_info );
        if( call_user_func( array( $this->_loadClass, 'exists' ), $file_to_edit ) || is_dir( $file_to_edit ) ) {
            $this->_error(
                'add new file error',
                "file/directory already exists ({$file_to_edit}).\n" .
                "cannot overwrite existing file or directory."
            );
        }
        else {
            $self = $this->_ctrl->getBaseUrl( $path_info );
            $contents = $this->_makeEditForm( 'Add File '.$new_file, $self, '' );
            $this->_pageObj->setContent( $contents );
            $this->_ctrl->skipToModel( 'emitter' );
            $this->_ctrl->setAction( $this->_ctrl->defaultAct() );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_fPut( $loadInfo ) {
        if( $loadInfo[ 'file' ] ) {
            // always put contents onto edited_file.
            $file_to_edit = $this->_getFileToEdit( $loadInfo[ 'file' ] );
        }
        else {
            // it's a new file to add.
            $file_to_edit = $this->_ctrl->getLocation( $this->_ctrl->getPathInfo() );
        }
        if( isset( $_POST[ '_putContent' ] ) ) {
            $content = $_POST[ '_putContent' ];
            $content = str_replace( "\r\n", "\n", $content );
            $content = str_replace( "\r", "\n", $content );
            $success = call_user_func( array( $this->_loadClass, 'putContents' ), $file_to_edit, $content );
            if( $success !== FALSE ) {
                $this->_ctrl->redirect( $this->_ctrl->getPathInfo() );
            }
            else {
                $this->_error(
                    'file put error',
                    "Could not save contents to file ({$file_to_edit}). <br />" .
                    "maybe file permission problem?"
                );
                $self = $this->_ctrl->getBaseUrl( $this->_ctrl->getPathInfo() );
                $content = $this->_makeEditForm( 'Re-editing ' . basename( $file_to_edit ), $self, $content );
                $loadInfo = $this->action_fEdit( $this->_ctrl, $this->_pageObj, $loadInfo );
                $this->_pageObj->setContent( $content );
                $this->_ctrl->skipToModel( 'emitter' );
            }
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param array $loadInfo
     * @return array
     */
    function action_fEdit( $loadInfo ) {
        $file_name = ( $loadInfo[ 'file_edited' ] ) ?: $loadInfo[ 'file' ];
        $contents  = call_user_func( array( $this->_loadClass, 'getContentsByGet' ), $file_name );
        $self      = $this->_ctrl->getBaseUrl( $this->_ctrl->getPathInfo() );
        $file_type = call_user_func( array( $this->_loadClass, 'getFileType' ), $file_name );
        if( isset( $this->editors[ $file_type ] ) ) {
            $title    = 'Edit: '. basename( $file_name );
            $editor   = $this->editors[ $file_type ];
            /** @var $editor \AmidaMVC\Editor\IfEditor */
            $editor   = new $editor();
            $contents = $editor->edit( $title, $self, $contents );
            $editor->page( $this->_pageObj );
        }
        $this->_pageObj->setContent( $contents );
        $this->_ctrl->skipToModel( 'emitter' );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function _makeEditForm( $title, $self, $contents, $cmd='_fPut' ) {
        $file_type = call_user_func( array( $this->_loadClass, 'getFileType' ), $self );
        if( isset( $this->editors[ $file_type ] ) ) {
            $editor   = $this->editors[ $file_type ];
            /** @var $editor \AmidaMVC\Editor\IfEditor */
            $editor   = new $editor( $cmd );
            $contents = $editor->edit( $title, $self, $contents );
        }
        return $contents;
    }
    // +-------------------------------------------------------------+
    /**
     */
    function _template() {
        $_ctrl = $this->_ctrl;
        $_pageObj = $this->_pageObj;
        $_filerObj  = (object) $this->filerInfo;
        ob_start();
        ob_implicit_flush(0);
        if( $this->devTemplate ) {
            include $this->_ctrl->findFile( $this->devTemplate );
        }
        else {
            include __DIR__ . '/' . $this->devTemplateDefault;
        }
        $contents = ob_get_clean();
        $this->_pageObj->devInfo = $contents;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $file_name
     * @return string
     */
     function _getFileToEdit( $file_name ) {
         if( !$file_name ) { return ''; }
         $folder    = dirname( $file_name );
         if( $folder == '.' ) {
             $folder = '';
         }
         else {
             $folder .= '/';
         }
         $baseName  = basename( $file_name );
         $curr_mode = $this->mode;
         if( substr( $baseName, 0, strlen( $curr_mode ) ) == $curr_mode ) {
             $file_to_edit  = "{$folder}{$baseName}";
         }
         else {
             $file_to_edit  = "{$folder}{$curr_mode}-{$baseName}";
         }
         return $file_to_edit;
     }
    // +-------------------------------------------------------------+
    /**
     * find commands for Filer. commands are in static::$file_list.
     * @return string
     */
    function _findFilerCommand()
    {
        $gotCommandList = $this->_ctrl->getCommands();
        $commandList    = array();
        foreach( $gotCommandList as $command ) {
            $cmd = explode( ':', $command );
            $commandList[]  = $cmd[0];
        }
        foreach( $this->commandList as $cmd ) {
            if( in_array( $cmd, $commandList ) ) {
                // found command.
                return $cmd;
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    function _error( $error, $message ) {
        $this->filerInfo[ 'error' ] = $error;
        $this->filerInfo[ 'message' ] = $message;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $file_name
     * @return string
     */
    function _backupFolder( $file_name ) {
        $backup_folder = pathinfo( $file_name, PATHINFO_DIRNAME ) . '/' . $this->backup;
        return $backup_folder;
    }
    /**
     * @param string $file_name
     * @param string $now
     * @return string
     */
    function _backupFileName( $file_name, $now ) {
        $file_body = pathinfo( $file_name, PATHINFO_FILENAME );
        $file_ext = pathinfo( $file_name, PATHINFO_EXTENSION );
        $backup_dir  = $this->_backupFolder( $file_name );
        $backup_file = "{$backup_dir}/_{$file_body}-{$now}.{$file_ext}";
        return $backup_file;
    }

    /**
     * @param string $file_name
     */
    function _backup( $file_name ) {
        $backup_folder = $this->_backupFolder( $file_name );
        if( !call_user_func( array( $this->_loadClass, 'exists' ), $backup_folder ) ) {
            call_user_func( array( $this->_loadClass, 'mkDir' ), $backup_folder, 0777 );
        }
        if( call_user_func( array( $this->_loadClass, 'isDir' ), $backup_folder ) ) {
            $now       = date( 'YmdHis' );
            $backup_file = $this->_backupFileName( $file_name, $now );
            call_user_func( array( $this->_loadClass, 'rename' ),  $file_name, $backup_file );
        }
    }
    // +-------------------------------------------------------------+
}

