AmidaMVC Shadow Page
====================

This is a shadow index page for AmidaMVC.

This CMS like demo provides a pukiWiki like CMS using AmidaMVC Framework.

*   This file is at: <?php echo __FILE__; ?>

*   Root directory is: <?php echo $_ctrl->getLocation(); ?>

Developer's Mode
----------------

Developer's mode let you edit the content files that are written in
PHP code, html, markdown, and text.
Login from the footer.

Using Framework Variables
-------------------------

Current implementation uses pure PHP as template system.
Just include PHP code anywhere in PHP file as well as in markdown text,
or just simple text files. All of the files are included to AmidaMVC
framework as PHP code.

get base URL
: baseURL: '<?php echo $_ctrl->getBaseUrl(); ?>'.
: ../bootstrap: '<?php echo $_ctrl->getBaseUrl( '../bootstrap' ); ?>'.
