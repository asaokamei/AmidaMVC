CMS Demo
========

This CMS like demo demonstrates AmidaMVC can be used to
construct a pukiWiki like CMS.

Developer's Mode
----------------

<?php if( \AmidaMVC\Tools\AuthNot::isLoggedIn( 'authDev' ) ) { ?>
*   [logout from developer's mode](dev_logout)
<?php } else { ?>
*   [log in for developer's mode](dev_login)
<?php } ?>

