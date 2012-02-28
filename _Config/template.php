<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $head_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Le styles -->
    <link href="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
        .mainbody {
            margin-top: 10px; /* 60px to make the container go all the way to the bottom of the topbar */
        }
    </style>
    <link href="<?php echo $_siteObj->siteObj->base_url; ?>/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="<?php echo $_siteObj->siteObj->base_url; ?>/demo.css"/>
</head>
<body>
<?php 
    if( isset( $_devHeader ) ) { 
        echo $_devHeader; // developer's menu bar 
    } 
?>
<div class="mainbody">
<header><a href="<?php echo $_ctrl->getBaseUrl(); ?>">AmidaMVC Framework</a></header>
<div id="contents">
    <?php if( $title ) { ?>
    <h1><?php echo $title; ?></h1>
    <?php } ?>
    <?php echo $contents; ?>
    <p style="clear: both;"></p>
</div>
<footer>AmidaMVC, yet another micro Framework for PHP.</footer>
<?php 
    if( isset( $_devFooter ) ) { 
        echo $_devFooter;  // developer's menu bar 
    }
?>
</div>
</body>
</html>