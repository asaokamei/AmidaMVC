CMS Demo
========

This CMS like demo provides a pukiWiki like CMS using AmidaMVC Framework.

*   [link to test.md for testing](test.md)
*   [link to subdir](subdir/)  
    try link w/o trailing slash: [link to subdir](subdir)

Developer's Mode
----------------

Developer's mode let you edit the content files that are written in
PHP code, html, markdown, and text.

<?php if( \AmidaMVC\Tools\AuthNot::isLoggedIn( 'authDev' ) ) { ?>
*   [logout from developer's mode](dev_logout)
<?php } else { ?>
*   [log in for developer's mode](dev_login)
<?php } ?>

Using Framework Variables
-------------------------

Current implementation uses pure PHP as template system.
Just include PHP code anywhere in PHP file as well as in markdown text,
or just simple text files. All of the files are included to AmidaMVC
framework as PHP code.

get base URL
: baseURL: '<?php echo $_ctrl->getBaseUrl(); ?>'.
: ../bootstrap: '<?php echo $_ctrl->getBaseUrl( '../bootstrap' ); ?>'.

[back to Top](../)