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
        $file_to_edit = $this->getFileToEdit( $loadInfo[ 'file' ] );
        if( file_exists( $file_to_edit ) ) {
            //$loadInfo[ 'file' ] = $file_to_edit;
            $this->filerInfo[ 'file_src' ]   = basename( $file_to_edit );
            $this->filerInfo[ 'file_cmd' ][] = '_fEdit';
            $this->filerInfo[ 'file_cmd' ][] = '_fPub';
            $this->filerInfo[ 'file_cmd' ][] = '_fDel';
            $this->filerInfo[ 'file_cmd' ][] = '_fDiff';
        }
        else {
            $this->filerInfo[ 'file_src' ]   = basename( $file_target );
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
        $this->template( $_ctrl, $_pageObj );
        return $loadInfo;
    }
    // +-------------------------------------------------------------+
    /**
     * @param \AmidaMVC\AppSimple\Application $_ctrl
     * @param \AmidaMVC\Framework\PageObj $_pageObj
     */
    function template( $_ctrl, $_pageObj ) {
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
     * @param string $filename
     * @return string
     */
     function getFileToEdit( $filename ) {
        return $filename;
     }
    // +-------------------------------------------------------------+
}

