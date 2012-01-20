<?php

echo 'this is sub/sub2.php<br />';
if( isset( $action ) ) {
    echo "actoin={$action}<br />";
}
Debug::bug( 'table', $ctrl, 'ctrl in sub2');


