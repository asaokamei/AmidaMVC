<?php

$ctrl->prependComponent( 'appDemo', 'model' );

class appDemo
{
    static function actionDefault( $ctrl, &$data ) {
        Debug::bug( 'head', 'app1Model::Default action' );
        $data->setContents( "app1Model::Default, action=".$ctrl->currAct() );
        $ctrl->setAction( '_pageNotFound' );
    }
    static function actionList( $ctrl, &$data ) {
        Debug::bug( 'head', 'app1Model::List action' );
        $data->setContents( "Model::List action=".$ctrl->currAct() );
    }
}

