<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $head_title; ?></title>
    <style>
        body {
            width: 700px;
            border:2px solid #E0E0E0;
            padding: 10px;
            padding-left:15px;
            border-radius: 15px;
            font-family:"ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, Osaka, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif;
            box-shadow: 5px 5px 5px #aaaaaa;
        }
        header {
            background: #F0F0F0;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 15px 5px 15px 5px;
            margin: 0px 0px 10px -5px;
            border-left: 5px solid #d2691e;
            border-right: 5px solid #d2691e;
            box-shadow: 5px 5px 5px #cccccc;
        }
        footer {
            background: #F0F0F0;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            padding: 5px 5px 5px 5px;
            margin: 0px 0px 0px -5px;
            border-left: 2px solid #d2691e;
            border-right: 2px solid #d2691e;
            box-shadow: 2px 2px 2px #cccccc;
        }
        h1 {
            background: #F0F0F0;
            font-size: 16px;
            padding: 5px 5px 5px 5px;
            margin: 15px 5px 5px -2px;
            border-left: 2px solid #d2691e;
            box-shadow: 3px 3px 3px #cccccc;
        }
        table {
            border: #CCCCCC solid 2px;
            border-collapse:collapse;
            box-shadow: 2px 2px 2px #cccccc;
        }
        th {
            background-color: #F0F0F0;
            border: #E0E0E0 solid 1px;
            padding:3px;
        }
        td {
            border: #F0F0F0 solid 1px;
            padding:2px;
        }
    </style>
</head>
<body>
<header>AmidaMVC Demo</header>
<div id="contents">
    <h1><?php echo $title; ?></h1>
    <?php echo $contents; ?>
</div>
<?php if( isset( $debug ) ) { ?>
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