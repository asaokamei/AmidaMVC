<?php
namespace AmidaMVC\Component;

class Filer
{
    static $file_list = array( 
        '_edit', '_put', '_pub', '_del', '_purge', 
        '_bkView', '_bkDiff', 
        '_fileFolder' 
    );
    static $backup    = '_Backup';
    // +-------------------------------------------------------------+
    /**
     * default action to setup Filer. 
     * dispatch myAction based on command in $file_list. 
     * @param \AmidaMVC\Framework\Controller $_ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     * @return array
     */
    function actionDefault(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        array $loadInfo )
    {
        // create filerObj.
        $filerObj = array(
            'file_mode' => '_filer',
            'file_cmd'  => array(),
            'backup_list' => array(),
            'src_type' => NULL,
        );
        $_siteObj->set( 'filerObj', $filerObj );
        // see if Filer command is in.  
        $command = static::findFilerCommand( $_siteObj );
        if( $command ) {
            // set file_mode and dispatch $cmd as myAction. 
            $_ctrl->setMyAction( $command );
            $_siteObj->filerObj->file_mode = $command;
            return $loadInfo;
        }
        // standard Filer mode. setup mode, and add available command. 
        $file_target  = $loadInfo[ 'file' ];
        $file_to_edit = static::getFileToEdit( $_siteObj, $loadInfo );
        if( file_exists( $file_to_edit ) ) {
            $loadInfo[ 'file' ] = $file_to_edit;
            $_siteObj->filerObj->src_type   = '_dev';
            $_siteObj->filerObj->file_cmd[] = '_edit';
            $_siteObj->filerObj->file_cmd[] = '_pub';
            $_siteObj->filerObj->file_cmd[] = '_del';
        }
        else {
            $_siteObj->filerObj->file_cmd[] = '_edit';
            $_siteObj->filerObj->file_cmd[] = '_purge';
        }
        $_siteObj->filerObj->curr_folder = dirname( $loadInfo['file'] );
        if( $_siteObj->filerObj->curr_folder == '.' ) {
            $_siteObj->filerObj->curr_folder = '';
        }
        $_siteObj->filerObj->file_cmd[] = '_fileFolder';
        // get backup file list
        $backup_list = static::backupList( $file_target );
        $_siteObj->filerObj->backup_list = $backup_list;
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function findFilerCommand( 
        \AmidaMVC\Component\SiteObj &$_siteObj, 
        &$command=NULL ) 
    {
        foreach( $_siteObj->siteObj->command as $command ) {
            $cmds = explode( ':', $command );
            $cmd  = $cmds[0];
            if( in_array( $cmd, static::$file_list ) ) {
                // found command. 
                return $cmd;
            }
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    function action_pageNotFound(
        \AmidaMVC\Framework\Controller $_ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        $loadInfo )
    {
        // create filerObj.
        $filerObj = array(
            'file_mode' => '_filer',
            'file_cmd'  => array(),
            'backup_list' => array(),
            'src_type' => NULL,
        );
        $_siteObj->set( 'filerObj', $filerObj );
        $_siteObj->filerObj->file_cmd[] = '_fileFolder';
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * returns a file for edit; _dev-file_name.ext. 
     * @param $_siteObj
     * @param $loadInfo
     * @return string
     */
    function getFileToEdit( $_siteObj, $loadInfo ) {
        $file_name = $loadInfo[ 'file' ];
        $folder    = dirname( $file_name );
        $basename  = basename( $file_name );
        $curr_mode = $_siteObj->siteObj->mode;
        $file_to_edit  = "{$folder}/{$curr_mode}-{$basename}";
        return $file_to_edit;
    }
    // +-------------------------------------------------------------+
    /**
     * Edit file. if _dev-file is available, edit the _dev-file.
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     * @return array
     */
    function action_edit(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_to_edit = static::getFileToEdit( $_siteObj, $loadInfo );
        $_siteObj->filerObj->file_mode = '_edit';
        if( file_exists( $file_to_edit ) ) {
            $loadInfo[ 'file' ] = $file_to_edit;
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * put contents in _dev-file.   
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     * @return array
     */
    function action_put(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_to_edit = static::getFileToEdit( $_siteObj, $loadInfo );
        // TODO: verify input! Security alert!
        if( isset( $_POST[ '_putContent' ] ) ) {
            $content = $_POST[ '_putContent' ];
            $success = file_put_contents( $file_to_edit, $content );
            if( $success !== FALSE ) {
                $loadInfo[ 'file' ] = $file_to_edit;
                $reload = $ctrl->getPathInfo();
                $ctrl->redirect( $reload );
            }
            $ctrl->setMyAction( '_reedit' );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * re-call this action when edit fails to put content. 
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     * @return array
     */
    function action_reedit(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_to_edit = static::getFileToEdit( $_siteObj, $loadInfo );
        if( file_exists( $file_to_edit ) ) {
            $loadInfo[ 'file' ] = $file_to_edit;
        }
        $_siteObj->filerObj->error = 'failed_to_put_content.';
        $_siteObj->filerObj->err_msg = 'maybe folder\'s permission problem?';
        $loadInfo[ 'content' ] = $_POST[ '_putContent' ];
        $_siteObj->filerObj->file_mode = '_edit';
        $_siteObj->filerObj->src_type   = '_re-edit';
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * publish _dev-file: _dev-file_name.ext to file_name.ext. 
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     * @return array
     */
    function action_pub(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_to_publish = static::getFileToEdit( $_siteObj, $loadInfo );
        if( file_exists( $file_to_publish ) ) {
            $file_replaced = $loadInfo[ 'file' ];
            static::backup( $file_replaced );
            rename( $file_to_publish, $file_replaced );
            $reload = $ctrl->getPathInfo();
            $ctrl->redirect( $reload );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * delete _dev-file. 
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     * @return array
     */
    function action_del(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_to_publish = static::getFileToEdit( $_siteObj, $loadInfo );
        if( file_exists( $file_to_publish ) ) {
            unlink( $file_to_publish );
            $reload = $ctrl->getPathInfo();
            $ctrl->redirect( $reload );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * purges (i.e. move to backup) file. won't purge if _dev-file exists.  
     * @param \AmidaMVC\Framework\Controller $ctrl
     * @param SiteObj $_siteObj
     * @param array $loadInfo
     * @return array
     */
    function action_purge(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_to_publish = static::getFileToEdit( $_siteObj, $loadInfo );
        if( !file_exists( $file_to_publish ) ) {
            $file_to_purge = $loadInfo[ 'file' ];
            static::backup( $file_to_purge );
            $reload = $ctrl->getPathInfo();
            $ctrl->redirect( $reload );
        }
        $ctrl->setAction( '_pageNotFound' );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function action_fileFolder(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        if( isset( $_POST['_folderName'] ) ) {
            $file_to_edit = static::getFileToEdit( $_siteObj, $loadInfo );
            $folder = dirname( $file_to_edit ) . '/' . $_POST['_folderName'];
            if( file_exists( $folder ) ) {
                $_siteObj->filerObj->error = 'folder_already_exists.';
                $_siteObj->filerObj->err_msg = 'folder: ' . $_POST['_filderName'] .'already exists.';
            }
            elseif( mkdir( $folder, 0777 ) ) {
                $reload = $ctrl->getPathInfo();
                $ctrl->redirect( $reload );
            }
            else {
                $_siteObj->filerObj->error = 'failed_to_add_folder.';
                $_siteObj->filerObj->err_msg = 'maybe folder\'s permission problem?';
            }
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    //  backup methods
    // +-------------------------------------------------------------+
    function action_bkView(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        if( static::findFilerCommand( $_siteObj, $command ) ) {
            list( $cmd, $file_backup ) = explode( ':', $command );
            $file_name = $loadInfo[ 'file' ];
            static::getFileInfo( $file_name, $folder, $base, $file_body, $file_ext );
            $backup_folder = $folder . '/' . static::$backup;
            $file_to_view = "{$backup_folder}/{$file_backup}";
            $loadInfo[ 'file' ] = $file_to_view;
            $_siteObj->filerObj->src_type   = '_bkView:'.$file_backup;
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * backup the replaced file to _Backup folder with datetime. 
     * @param $file_replaced
     * @return mixed
     */
    function backup( $file_replaced ) {
        static::getFileInfo( $file_replaced, $folder, $base, $file_body, $file_ext );
        $backup_folder = $folder . '/' . static::$backup;
        if( !file_exists( $backup_folder ) ) {
            mkdir( $backup_folder, 0777 );
        }
        if( !is_dir( $backup_folder ) ) {
            unlink( $file_replaced );
            return;
        }
        $now       = date( 'YmdHis' );
        $backup_file = "{$backup_folder}/_{$file_body}-{$now}.{$file_ext}";
        rename( $file_replaced, $backup_file );
    }
    // +-------------------------------------------------------------+
    function backupList( $file_name ) {
        static::getFileInfo( $file_name, $folder, $base, $file_body, $file_ext );
        $backup_folder = $folder . '/' . static::$backup;
        if( !file_exists( $backup_folder ) ) {
            return;
        }
        $backup_glob = "{$backup_folder}/_{$file_body}-*.{$file_ext}";
        $backup_list = glob( $backup_glob );
        foreach( $backup_list as &$backup ) {
            $backup = basename( $backup );
        }
        return $backup_list;
    }
    // +-------------------------------------------------------------+
    function getFileInfo( $file_name, &$folder, &$base, &$body, &$ext ) {
        $folder  = dirname(  $file_name );
        $base    = basename( $file_name );
        $ext     = pathinfo( $file_name, PATHINFO_EXTENSION  );
        $body    = pathinfo( $file_name, PATHINFO_FILENAME  );
    }
    // +-------------------------------------------------------------+
}
