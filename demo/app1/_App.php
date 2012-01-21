<?php

class modelApp1
{
    static function actionDefault( $ctrl, &$data ) {
        $data->setContents( "app1Model::Default, action=".$ctrl->currAct() );
        $ctrl->nextModel( 'pageNotFound' );
    }
    static function actionList( $ctrl, &$data ) {
        //
        $data->setContents( "Model::List action=".$ctrl->currAct() );
    }
}

$ctrl->prependModel( 'modelApp1', 'model' );

