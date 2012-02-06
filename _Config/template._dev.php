<?php

// filer's mode; _edit, _put, _pub, _del,...
$file_mode = $_siteObj->siteObj->file_mode;
// myself.
$self = $_ctrl->getBaseUrl() . $_ctrl->getPathInfo();
    
?>
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
    <?php 
    if( $file_mode == '_edit' ) {
        // show form to edit contents.
?>
        <form method="post" name="_editFile" action="<?php echo $self?>/_put">
            <textarea name="_putContent" style="width:95%; height:350px; font-family: courier;"><?php echo htmlspecialchars( $contents ); ?></textarea>
            <input type="submit" name="submit" value="put contents"/>
        </form>
<?php 
    }
    else {
        echo $contents;
    }
    
    ?>
    <p style="clear: both;"></p>
</div>
<footer>AmidaMVC, yet another micro Framework for PHP.</footer>
<?php if( $_siteObj->loadInfo->loadMode == '_view' ) { ?>
    <div><a href="<?php echo $self;?>/_edit" >edit</a> </div>
<?php } ?>
<?php if( !empty( $debug ) ) { ?>
<style>
    div.debugInfo {
        font-size: 12px;
        color: #666666;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div.debugInfo h3 {
        margin: 0px;
        padding: 2px;
    }
    div.debugInfo .debugTraceInfo {
        float: left;
        margin: 0px 0px 0px -20px;
    }
    div.debugInfo table {
        border:1px solid gray; font-size: 11px; border-collapse: collapse;
    }
    div.debugInfo td,th { border: 1px dotted gray; vertical-align: top; }
    div.debugInfo th { background-color: #E0E0E0; }
</style>
<div class='debugInfo'><?php echo $debug;?></div>
</div>
    <?php } ?>
</body>
</html>