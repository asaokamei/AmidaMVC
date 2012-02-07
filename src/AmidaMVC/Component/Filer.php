<?php
namespace AmidaMVC\Component;

class Filer
{
    static $file_list = array( '_edit', '_put', '_pub', '_del' );
    // +-------------------------------------------------------------+
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
                $_ctrl->setMyAction( $cmd );
                $_siteObj->filerObj->file_mode = $cmd;
                return $loadInfo;
            }
        }
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
    function getFileToEdit( $_siteObj, $loadInfo ) {
        $file_name = $loadInfo[ 'file' ];
        $folder    = dirname( $file_name );
        $basename  = basename( $file_name );
        $curr_mode = $_siteObj->siteObj->mode;
        $file_to_edit  = "{$folder}/{$curr_mode}-{$basename}";
        return $file_to_edit;
    }
    // +-------------------------------------------------------------+
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
    function action_put(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        // do nothing as default. 
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
    function action_pub(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj,
        array $loadInfo )
    {
        $file_to_publish = static::getFileToEdit( $_siteObj, $loadInfo );
        if( file_exists( $file_to_publish ) ) {
            $file_replaced = $loadInfo[ 'file' ];
            unlink( $file_replaced );
            rename( $file_to_publish, $file_replaced );
            $reload = $ctrl->getPathInfo();
            $ctrl->redirect( $reload );
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
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
}
