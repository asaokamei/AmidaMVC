<?php

class Config extends \AmidaMVC\Component\Config {
    // +-------------------------------------------------------------+
    function actionDefault(
        \AmidaMVC\Framework\Controller $ctrl,
        \AmidaMVC\Component\SiteObj &$data )
    {
        parent::actionDefault( $ctrl, $data );
        $siteDefault = $data->get( 'siteObj' );
        $option = array(
            'pageNotFound' => $ctrl->ctrl_root . '/_Config/pageNotFound.md',
        );
        $siteDefault = array_merge( $siteDefault, $option );
        $data->set( 'siteObj', $siteDefault );
    }
    // +-------------------------------------------------------------+
}
