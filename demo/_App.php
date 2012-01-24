<?php

class appDemo
{
    static function actionDefault( $ctrl, &$data ) {
        Debug::bug( 'head', 'app1Model::Default action' );
        $data->setContents( "app1Model::Default, action=".$ctrl->currAct() );
        $ctrl->nextModel( 'pageNotFound' );
    }
    static function actionList( $ctrl, &$data ) {
        Debug::bug( 'head', 'app1Model::List action' );
        $data->setContents( "Model::List action=".$ctrl->currAct() );
    }
}

$ctrl->prependModel( 'appDemo', 'model' );

