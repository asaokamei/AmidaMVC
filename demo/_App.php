<?php

class topApp1
{
    static function actionDefault( $ctrl, &$data ) {
        $data->setTitle( "Generic Action" );
        $data->setContents( "topApp1::Default, action=".$ctrl->currAct() );
    }
    static function actionList( $ctrl, &$data ) {
        $data->setTitle( "List of Something" );
        $data->setContents( "topApp1::List, action=".$ctrl->currAct() );
    }
}

$ctrl->prependModel( 'topApp1', 'model' );

