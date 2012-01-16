<?php
require_once( __DIR__ . '/../src/AmidaMVC/Component/Debug.php');

$debug = new AmidaMVC\Component\Debug();
$debug->head( '_index.php is here' );
$debug->table( array( 'val'=>'a value', TRUE, 'num' => 0, 'false' => false, 'array' => array( 'a', 'b', NULL, '', FALSE ) ) );

$debug->table( $debug );

