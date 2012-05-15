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
        '_fFolder', '_fFile'
    );
    /**
     * @var \AmidaMVC\Tools\Load   static class for loading methods.
     */
    protected $_loadClass = '\AmidaMVC\Tools\Load';
    protected $backup    = '_Backup';
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
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     */
    function _setMenu( $_ctrl, &$_pageObj, &$loadInfo ) {
        // set JavaScript and CSS files for developer's menu...
        if( !empty( $this->listJs ) ) {
            foreach( $this->listJs as $js ) {
                $_pageObj->setJs( $js );
            }
        }
        if( !empty( $this->listCss ) ) {
            foreach( $this->listCss as $css ) {
                $_pageObj->setCss( $css );
            }
        }
        if( !isset( $loadInfo[ 'file' ] ) ) {
            return;
        }
        // set file lists under the folder.
        if( is_dir( $loadInfo[ 'file'] ) ) {
            $filer_folder = $loadInfo[ 'file' ];
        }
        else {
            $filer_folder = dirname( $loadInfo[ 'file'] );
        }
        $this->filerInfo[ 'curr_folder' ] = $filer_folder;
        $file_list = glob( "{$filer_folder}/*", GLOB_MARK );
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
        if( call_user_func( array( $this->_loadClass, 'exists' ), $file_to_edit ) ) {
            $loadInfo[ 'file_edited' ] = $file_to_edit;
            $this->filerInfo[ 'file_src' ]   = basename( $file_to_edit );
            $this->filerInfo[ 'file_cmd' ][] = '_fEdit';
            $this->filerInfo[ 'file_cmd' ][] = '_fPub';
            $this->filerInfo[ 'file_cmd' ][] = '_fDel';
            $this->filerInfo[ 'file_cmd' ][] = '_fDiff';
        }
        else {
            $loadInfo[ 'file_edited' ] = FALSE;
            $this->filerInfo[ 'file_src' ]   = basename( $loadInfo[ 'file' ] );
            $this->filerInfo[ 'file_cmd' ][] = '_fEdit';
            $this->filerInfo[ 'file_cmd' ][] = '_fPurge';
        }
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function actionDefault( $_ctrl, &$_pageObj, $loadInfo=array() )
    {
        $this->_setMenu( $_ctrl, $_pageObj, $loadInfo );
        $command = $this->_findFilerCommand( $_ctrl );
        if( $command ) {
            $method = 'action' . $command;
            $loadInfo = $this->$method( $_ctrl, $_pageObj, $loadInfo );
        }
        else {
            $loadInfo[ 'file' ] = ( $loadInfo[ 'file_edited' ] ) ?: $loadInfo[ 'file' ];
        }
        $this->_template( $_ctrl, $_pageObj );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function action_fPub( $_ctrl, $_pageObj, $loadInfo ) {
        $file_to_publish = $loadInfo[ 'file_edited' ];
        if( call_user_func( array( $this->_loadClass, 'exists' ),  $file_to_publish ) ) {
            $file_to_replaced = $loadInfo[ 'file' ];
            $this->_backup( $file_to_replaced );
            if( rename( $file_to_publish, $file_to_replaced ) ) {
                // success
                $_ctrl->redirect( $_ctrl->getPathInfo() );
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
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function action_fFile( $_ctrl, $_pageObj, $loadInfo ) {
        $new_file = $_POST[ '_newFileName' ];
        if( isset( $loadInfo[ 'file' ] ) ) {
            $file_to_edit = dirname( $loadInfo[ 'file' ] );
        }
        else {
            // in case pageNotFound, find location from pathInfo.
            $file_to_edit = $_ctrl->getLocation( dirname( $_ctrl->getPathInfo() ) );
        }
        $file_to_edit = $file_to_edit . '/' . $new_file;
        if( call_user_func( array( $this->_loadClass, 'exists' ), $file_to_edit ) || is_dir( $file_to_edit ) ) {
            $this->_error(
                'add new file error',
                "file/directory already exists ({$file_to_edit}).\n" .
                "cannot overwrite existing file or directory."
            );
        }
        else {
            $self = $_ctrl->getBaseUrl( $_ctrl->getPathInfo() );
            if( $loadInfo[ 'foundBy' ] === 'index' ) {
                $self = $self . '/' . $new_file;
            }
            else {
                $self = dirname( $self ) . '/' . $new_file;
            }
            $contents = $this->_makeEditForm( 'Add File '.$new_file, $self, '' );
            $_pageObj->setContent( $contents );
            $_ctrl->skipToModel( 'emitter' );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function action_fPut( $_ctrl, $_pageObj, $loadInfo ) {
        if( $loadInfo[ 'file' ] ) {
            // always put contents onto edited_file.
            $file_to_edit = $this->_getFileToEdit( $loadInfo[ 'file' ] );
        }
        else {
            // it's a new file to add.
            $file_to_edit = basename( $_ctrl->getPathInfo() );
            $file_to_edit = $_ctrl->getLocation( $file_to_edit );
        }
        if( isset( $_POST[ '_putContent' ] ) ) {
            $content = $_POST[ '_putContent' ];
            $content = str_replace( "\r\n", "\n", $content );
            $content = str_replace( "\r", "\n", $content );
            $success = call_user_func( array( $this->_loadClass, 'putContents' ), $file_to_edit, $content );
            if( $success !== FALSE ) {
                $_ctrl->redirect( $_ctrl->getPathInfo() );
            }
            else {
                $this->_error(
                    'file put error',
                    "Could not save contents to file ({$file_to_edit}). <br />" .
                    "maybe file permission problem?"
                );
                $self = $_ctrl->getBaseUrl( $_ctrl->getPathInfo() );
                $content = $this->_makeEditForm( 'Re-editing ' . basename( $file_to_edit ), $self, $content );
                $loadInfo = $this->action_fEdit( $_ctrl, $_pageObj, $loadInfo );
                $_pageObj->setContent( $content );
                $_ctrl->skipToModel( 'emitter' );
            }
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function action_fEdit( $_ctrl, $_pageObj, $loadInfo ) {
        $load = $this->_loadClass;
        $file_name = ( $loadInfo[ 'file_edited' ] ) ?: $loadInfo[ 'file' ];
        $contents = $load::getContentsByGet( $file_name );
        $self = $_ctrl->getBaseUrl( $_ctrl->getPathInfo() );
        $contents = $this->_makeEditForm( 'Editing '. basename( $file_name ), $self, $contents );
        $_pageObj->setContent( $contents );
        $_ctrl->skipToModel( 'emitter' );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function _makeEditForm( $title, $self, $contents, $cmd='_fPut' ) {
        $contents = htmlspecialchars( $contents );
        $contents =<<<END_OF_HTML
<h1>{$title}</h1>

    <form method="post" name="_editFile" action="{$self}/{$cmd}">
        <textarea name="_putContent" style="width:95%; height:350px; font-family: courier;">{$contents}</textarea>
        <input type="submit" class="btn-primary" name="submit" value="Save File"/>
        <input type="button" class="btn" name="cancel" value="cancel" onclick="location.href='{$self}'"/>
    </form>
END_OF_HTML;
        return $contents;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    function _template( $_ctrl, $_pageObj ) {
        $_filerObj  = (object) $this->filerInfo;
        ob_start();
        ob_implicit_flush(0);
        if( $this->devTemplate ) {
            include $_ctrl->findFile( $this->devTemplate );
        }
        else {
            include __DIR__ . '/' . $this->devTemplateDefault;
        }
        $contents = ob_get_clean();
        $_pageObj->devInfo = $contents;
    }
    // +-------------------------------------------------------------+
    /**
     * @param string $file_name
     * @return string
     */
     function _getFileToEdit( $file_name ) {
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
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @return string
     */
    function _findFilerCommand( $_ctrl )
    {
        $gotCommandList = $_ctrl->getCommands();
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
        $file_dir = pathinfo( $file_name, PATHINFO_DIRNAME );
        $backup_file = "{$file_dir}/_{$file_body}-{$now}.{$file_ext}";
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

