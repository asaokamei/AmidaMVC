<?php
namespace AmidaMVC\Editor;

interface IfEditor
{
    public function __construct( $cmd='_fPut' );
    public function edit( $title, $self, $contents );
    public function page( $_pageObj );
}