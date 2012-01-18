<?php

echo 'this is sub/sub2.php<br />';
if( isset( $action ) ) {
    echo "actoin={$action}<br />";
}
$ctrl->debug( 'table', $ctrl, 'ctrl in sub2');


