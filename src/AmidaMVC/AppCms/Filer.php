<?php
namespace AmidaMVC\AppCms;

class Filer implements \AmidaMVC\Framework\IModule
{
    var $mode = '_dev';
    var $listJs = array();
    var $listCss = array();
    var $filerInfo = array();
    var $devTemplateDefault = 'template._dev.php';
    var $devTemplate = FALSE;
    var $commandList = array(
        '_fEdit', '_fPut', '_fPub', '_fDel', '_fPurge', '_fDiff',
        '_bkView', '_bkDiff',
        '_fFolder', '_fFile'
    );
    /**
     * @var \AmidaMVC\Tools\Load   static class for loading methods.
     */
    var $_loadClass = '\AmidaMVC\Tools\Load';
    // +-------------------------------------------------------------+
    /**
     * @param array $option
     */
    function _init( $option=array() ) {
        $filerObj = array(
            'file_mode' => '_filer',
            'file_cmd'  => array(),
            'backup_list' => array(),
            'file_src' => '',
            'curr_folder' => '',
            'file_list ' => array(),
            'error' => FALSE,
            'message' => '',
        );
        $this->filerInfo = $filerObj;

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
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     */
    function _setup( $_ctrl, &$_pageObj, &$loadInfo ) {
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

        // set up menu
        $file_to_edit = $this->_getFileToEdit( $loadInfo[ 'file' ] );
        if( file_exists( $file_to_edit ) ) {
            $loadInfo[ 'file' ] = $file_to_edit;
            $this->filerInfo[ 'file_src' ]   = basename( $file_to_edit );
            $this->filerInfo[ 'file_cmd' ][] = '_fEdit';
            $this->filerInfo[ 'file_cmd' ][] = '_fPub';
            $this->filerInfo[ 'file_cmd' ][] = '_fDel';
            $this->filerInfo[ 'file_cmd' ][] = '_fDiff';
        }
        else {
            $this->filerInfo[ 'file_src' ]   = basename( $loadInfo[ 'file' ] );
            $this->filerInfo[ 'file_cmd' ][] = '_fEdit';
            $this->filerInfo[ 'file_cmd' ][] = '_fPurge';
        }
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function actionDefault( $_ctrl, &$_pageObj, $loadInfo=array() )
    {
        $this->_setup( $_ctrl, $_pageObj, $loadInfo );
        $command = $this->_findFilerCommand( $_ctrl );
        if( $command ) {
            $method = 'action' . $command;
            $loadInfo = $this->$method( $_ctrl, $_pageObj, $loadInfo );
        }
        $this->_template( $_ctrl, $_pageObj );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function action_fFile( $_ctrl, $_pageObj, $loadInfo ) {
        $new_file = $_POST[ '_newFileName' ];
        $file_to_edit = $this->_getFileToEdit( $new_file );
        $file_to_edit = $_ctrl->getLocation( $file_to_edit );
        if( file_exists( $file_to_edit ) ) {
            $this->_error(
                'new file error',
                "file already exists ({$file_to_edit}). <br />" .
                "cannot overwrite existing file."
            );
        }
        else
        if( is_dir( $file_to_edit ) ) {
            $this->_error(
                'new file error',
                "directory already exists ({$file_to_edit}). <br />" .
                "cannot write to a directory."
            );
        }
        else {
            $self = $_ctrl->getPath( $_ctrl->getPathInfo() );
            if( $loadInfo[ 'foundBy' ] === 'index' ) {
                $self = $self . '/' . $new_file;
            }
            else {
                $self = dirname( $self ) . '/' . $new_file;
            }
            $contents = $this->_makeEditForm( $self, '' );
            $_pageObj->setContent( $contents );
            $_ctrl->skipToModel( 'emitter' );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function action_fPut( $_ctrl, $_pageObj, $loadInfo ) {
        if( $loadInfo ) {
            $file_to_edit = $this->_getFileToEdit( $loadInfo['file'] );
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
            $success = @file_put_contents( $file_to_edit, $content );
            if( $success !== FALSE ) {
                $loadInfo[ 'file' ] = $file_to_edit;
                // when adding a new file, the action is set to pageNotFound.
                // so set to normal just in case.
                $_ctrl->setAction( $_ctrl->defaultAct() );
            }
            else {
                $this->_error(
                    'file put error',
                    "Could not save contents to file ({$file_to_edit}). <br />" .
                    "maybe file permission problem?"
                );
                $loadInfo = $this->action_fEdit( $_ctrl, $_pageObj, $loadInfo );
            }
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     * @param array $loadInfo
     * @return array
     */
    function action_fEdit( $_ctrl, $_pageObj, $loadInfo ) {
        $load = $this->_loadClass;
        $contents = '';
        if( file_exists( $this->_getFileToEdit( $loadInfo['file'] ) ) ) {
            $file_name = $this->_getFileToEdit( $loadInfo['file'] );
            $contents = $load::getContentsByGet( $file_name );
        }
        else if( file_exists( $loadInfo['file'] ) ) {
            $file_name = $loadInfo['file'];
            $contents = $load::getContentsByGet( $file_name );
        }
        $self = $_ctrl->getPath( $_ctrl->getPathInfo() );
        $contents = $this->_makeEditForm( $self, $contents );
        $_pageObj->setContent( $contents );
        $_ctrl->skipToModel( 'emitter' );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function _makeEditForm( $self, $contents, $cmd='_fPut' ) {
        $contents = htmlspecialchars( $contents );
        $contents =<<<END_OF_HTML

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
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    function _template( $_ctrl, $_pageObj ) {
        $_filerInfo = $this->filerInfo;
        $_filerObj  = (object) $_filerInfo;
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
             $file_to_edit  = $baseName;
         }
         else {
             $file_to_edit  = "{$folder}{$curr_mode}-{$baseName}";
         }
         return $file_to_edit;
     }
    // +-------------------------------------------------------------+
    /**
     * find commands for Filer. commands are in static::$file_list.
     * @param \AmidaMVC\AppSimple\Application $_ctrl
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
}

