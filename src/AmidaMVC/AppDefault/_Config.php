<?php

class Config extends \AmidaMVC\Component\Config {
    // +-------------------------------------------------------------+
    function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$_siteObj )
    {
        parent::actionDefault( $ctrl, $_siteObj );
        $siteObj = $_siteObj->get( 'siteObj' );
        $option = array(
            'pageNotFound'  => $ctrl->ctrl_root . '/_Config/pageNotFound.md',
            'template_file' => $ctrl->ctrl_root . '/_Config/template.php',
            'loginForm'     => $ctrl->ctrl_root . '/_Config/loginForm.md',
        );
        $siteObj->set( $option ); 
    }
    // +-------------------------------------------------------------+
}
