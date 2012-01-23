<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $head_title; ?></title>
    <link rel="stylesheet" href="demo.css" />
</head>
<body>
<header>AmidaMVC Demo</header>
<div id="contents">
    <h1><?php echo $title; ?></h1>
    <?php echo $contents; ?>
</div>
<?php if( !empty( $debug ) ) { ?>
<style>
    div.debugInfo {
        font-size: 12px;
        margin: 10px;
        background-color: #F0F0F0;
    }
    div.debugInfo table {
        border:1px solid gray; font-size: 11px; border-collapse: collapse;
    }
    div.debugInfo td,th { border: 1px dotted gray; vertical-align: top; }
    div.debugInfo th { background-color: #E0E0E0; }
</style>
<hr>
<div class='debugInfo'><?php echo $debug;?></div>
</div>
<?php } ?>
<footer>AmidaMVC, yet another micro Framework for PHP.</footer>
</body>
</html>