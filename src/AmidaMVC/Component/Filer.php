<?php
namespace AmidaMVC\Component;

class Filer
{
    // +-------------------------------------------------------------+
    function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj, 
        array $loadInfo )
    {
        // do nothing as default. 
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function getFileToEdit( $siteObj, $loadInfo ) {
        $file_name = $loadInfo[ 'file' ];
        $folder    = dirname( $file_name );
        $basename  = basename( $file_name );
        $curr_mode = $siteObj->siteObj->mode;
        $file_to_edit  = "{$folder}/{$curr_mode}.{$basename}";
        return $file_to_edit;
    }
    // +-------------------------------------------------------------+
    function action_edit(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj,
        array $loadInfo )
    {
        $file_to_edit = static::getFileToEdit( $siteObj, $loadInfo );
        if( file_exists( $file_to_edit ) ) {
            $loadInfo[ 'file' ] = $file_to_edit;
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function action_put(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj,
        array $loadInfo )
    {
        // do nothing as default. 
        $file_to_edit = static::getFileToEdit( $siteObj, $loadInfo );
        // TODO: verify input! Security alert!
        if( isset( $_POST[ '_putContent' ] ) ) {
            $content = $_POST[ '_putContent' ];
            file_put_contents( $file_to_edit, $content );
            $loadInfo[ 'file' ] = $file_to_edit;
        }
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function action_pub(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj,
        array $loadInfo )
    {
        // do nothing as default. 
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    function action_del(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$siteObj,
        array $loadInfo )
    {
        // do nothing as default. 
        return $loadInfo;
    }
}
