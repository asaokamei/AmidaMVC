<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $head_title; ?></title>
    <link rel="stylesheet" href="<?php echo $_siteObj->siteObj->base_url; ?>/demo.css" />
</head>
<body>
<header><a href="<?php echo $_ctrl->getBaseUrl(); ?>">AmidaMVC Framework</a></header>
<div id="contents">
    <?php if( $title ) { ?>
    <h1><?php echo $title; ?></h1>
    <?php } ?>
    <?php echo $contents; ?>
    <p style="clear: both;"></p>
</div>
<footer>AmidaMVC, yet another micro Framework for PHP.</footer>
</body>
</html>