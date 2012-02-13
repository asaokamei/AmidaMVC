<?php

// filer's mode; _edit, _put, _pub, _del,...
$file_mode = (isset($_siteObj->filerObj->file_mode)) ? $_siteObj->filerObj->file_mode : FALSE;
;
// myself.
$self = $_ctrl->getPath( $_ctrl->getPathInfo() );
$base = $_ctrl->getBaseUrl();

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $head_title; ?></title>
    <link rel="stylesheet" href="<?php echo $_siteObj->siteObj->base_url; ?>/demo.css"/>
</head>
<body>
<header><a href="<?php echo $_ctrl->getBaseUrl(); ?>">AmidaMVC Framework</a></header>
<div id="contents">
    <?php if ($title) { ?>
    <h1><?php echo $title; ?></h1>
    <?php } ?>
    <?php
    if (!isset($_siteObj->filerObj)) {
        echo $contents;
    }
    else
        if ($_siteObj->filerObj->file_mode == '_edit') {
            // show form to edit contents.
            ?>
            <form method="post" name="_editFile" action="<?php echo $self?>/_put">
                <textarea name="_putContent" style="width:95%; height:350px; font-family: courier;"
                    ><?php echo htmlspecialchars($contents); ?></textarea>
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
<?php
if (isset($_siteObj->filerObj)) { ?>
<script type="text/javascript">
    function toggle( id ) {
        var ele = document.getElementById( id );
        if ( ele.style.display == undefined || ele.style.display == 'none' || ele.style.display == '' ) {
            ele.style.display = 'block';
        }
        else {
            ele.style.display = 'none';
        }
    }
</script>
<style>
    div#filerMenu {
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div#filerMenu ul {
        margin: 0px;
        padding: 0px;
        list-style-type: none;
        height: 25px;
    }
    div#filerMenu li {
        float: left;
        margin: 2px 5px 2px 5px;
    }
    div#filerBackUpList {
        display: none;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div#filerError {
        margin: 10px;
        padding: 2px 2px 2px 25px;
        border: 2px solid #d59392;
        box-shadow: 3px 3px 3px #d59392;
    }
    div#filerError dt {
        font-weight: bold;
        color: red;
    }
    div#filerAddFolder {
        display: none;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div#filerNewForm {
        display: none;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
    div#filerDirList {
        display: none;
        margin: 10px;
        padding: 5px 5px 5px 25px;
        border: 1px solid #cccccc;
        box-shadow: 2px 2px 2px #cccccc;
    }
</style>
<div id="filerDivArea">
    <!-- menu for Filer Component  -->
    <div id="filerMenu">
        <p>source_type: <?php echo $_siteObj->filerObj->src_type;?></p>
        <ul>
            <?php foreach( $_siteObj->filerObj->file_cmd as $cmd ) { ?>
            <li><a href="<?php echo "$self/$cmd";?>"><?php echo $cmd;?></a></li>
            <?php } ?>
            <li><a href="javascript:toggle('filerNewForm')">add file▼</a></li>
            <li><a href="javascript:toggle('filerAddFolder')">new folder▼</a></li>
            <?php if( !empty( $_siteObj->filerObj->file_list) ) {
            echo '<li><a href="javascript:toggle(\'filerDirList\');">file list▼</a></li>';  } ?>
            <?php if( !empty( $_siteObj->filerObj->backup_list) ) {
            echo '<li><a href="javascript:toggle(\'filerBackUpList\');">backups▼</a></li>';  } ?>
            <?php if( !empty( $debug ) ) {
            echo '<li><a href="javascript:toggle(\'debugInfo\');">debug info▼</a></li>';  } ?>
        </ul>
    </div>
    <!-- adding new folder -->
    <div id="filerAddFolder">
        <form method="post" name="_addFolder" action="<?php echo $self?>/_fileFolder">
            <input type="text" name="_folderName" width="30">
            <input type="submit" name="submit" value="add new folder"/><br />
            creates new folder at <strong>'/<?php echo $_siteObj->filerObj->curr_folder; ?>'</strong>.
        </form>
    </div>
    <!-- adding new file; show empty edit page -->
    <div id="filerNewForm">
        <form method="post" name="_showNewForm" action="<?php echo $self?>/_fileNew">
            <input type="text" name="_newFileName" width="30">
            <input type="submit" name="submit" value="edit new file"/><br />
            creates new file at <strong>'/<?php echo $_siteObj->filerObj->curr_folder; ?>'</strong>.
        </form>
    </div>
    <!-- show error message from _dev components -->
    <?php if (isset($_siteObj->filerObj->error)) { ?>
    <div id="filerError">
        <dl>
            <dt>Error: <?php echo $_siteObj->filerObj->error; ?></dt>
            <dd><?php echo $_siteObj->filerObj->err_msg; ?></dd>
        </dl>
    </div>
    <?php } ?>
    <!-- show backup file list -->
    <?php if( !empty( $_siteObj->filerObj->file_list ) ) { ?>
    <div id="filerDirList">
        File/Folder Lists at <?php echo $curr_folder = $_siteObj->filerObj->curr_folder; ?>:
        <?php foreach( $_siteObj->filerObj->file_list as $file ) {  ?>
        <li>
            <?php echo $file;?>:
            [<a href="<?php echo "{$base}/{$curr_folder}/{$file}";?>">view</a>]
        </li>
        <?php } ?>
    </div>
    <?php } ?>
    <!-- show backup file list -->
    <?php if (!empty($_siteObj->filerObj->backup_list)) { ?>
    <div id="filerBackUpList">
        Back Up File List:
        <ul>
            <?php foreach ($_siteObj->filerObj->backup_list as $backup) { ?>
            <li>
                <?php echo $backup;?>:
                [<a href="<?php echo "{$self}/_bkView:{$backup}";?>">view</a>]
                [<a href="<?php echo "{$self}/_bkDiff:{$backup}";?>">diff</a>]
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
    <!-- show debug info -->
    <?php if (!empty($debug)) { ?>
    <style>
        div#debugInfo {
            display: none;
            font-size: 12px;
            color: #666666;
            margin: 10px;
            padding: 5px 5px 5px 25px;
            border: 1px solid #cccccc;
            box-shadow: 2px 2px 2px #cccccc;
        }

        div#debugInfo h3 {
            margin: 0px;
            padding: 2px;
        }

        div#debugInfo .debugTraceInfo {
            float: left;
            margin: 0px 0px 0px -20px;
        }

        div#debugInfo table {
            border: 1px solid gray;
            font-size: 11px;
            border-collapse: collapse;
        }

        div#debugInfo td, th {
            border: 1px dotted gray;
            vertical-align: top;
        }

        div#debugInfo th {
            background-color: #E0E0E0;
        }
    </style>
    <div id='debugInfo'><?php echo $debug;?></div>
    <?php } ?>
</div>
<?php }   // end of if on isset( $_siteObj->filerObj ) //  ?>
</body>
</html>