<?php
namespace AmidaMVC\Component;

class Filer
{
    static $file_list = array( '_edit', '_put', '_pub', '_del' );
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
        );
        $_siteObj->set( 'filerObj', $filerObj );
        // see if Filer command is in.  
        $command   = $_siteObj->siteObj->command;
        foreach( static::$file_list as $cmd ) {
            if( in_array( $cmd, $command ) ) {
                // set file_mode and dispatch $cmd as myAction. 
                $_ctrl->setMyAction( $cmd );
                $_siteObj->filerObj->file_mode = $cmd;
                return $loadInfo;
            }
        }
        // standard Filer mode. setup mode, and add available command. 
        $file_to_edit = static::getFileToEdit( $_siteObj, $loadInfo );
        if( file_exists( $file_to_edit ) ) {
            $loadInfo[ 'file' ] = $file_to_edit;
            $_siteObj->filerObj->file_cmd[] = '_edit';
            $_siteObj->filerObj->file_cmd[] = '_pub';
            $_siteObj->filerObj->file_cmd[] = '_del';
        }
        else {
            $_siteObj->filerObj->file_cmd[] = '_edit';
            $_siteObj->filerObj->file_cmd[] = '_purge';
        }
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
            file_put_contents( $file_to_edit, $content );
            $loadInfo[ 'file' ] = $file_to_edit;
            $reload = $ctrl->getPathInfo();
            $ctrl->redirect( $reload );
        }
        $_siteObj->filerObj->file_mode = '_filer';
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
    function backup( $file_replaced ) {
        $folder    = dirname( $file_replaced );
        $backup_folder = $folder . '/' . static::$backup;
        if( !file_exists( $backup_folder ) ) {
            mkdir( $backup_folder, 0777 );
        }
        if( !is_dir( $backup_folder ) ) {
            unlink( $file_replaced );
            return;
        }
        $file_ext  = pathinfo( $file_replaced, PATHINFO_EXTENSION  );
        $file_body = pathinfo( $file_replaced, PATHINFO_FILENAME  );
        $now       = date( 'YmdHis' );
        $backup_file = "{$backup_folder}/{$file_body}-{$now}.{$file_ext}";
        rename( $file_replaced, $backup_file );
    }
    // +-------------------------------------------------------------+
}
