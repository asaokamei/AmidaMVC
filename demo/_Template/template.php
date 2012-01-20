<!DOCTYPE HTML>
<html>
<head>
    <title>AmidaMVC Demo</title>
    <style>
        body {
            width: 700px;
            border:2px solid #E0E0E0;
            padding: 10px;
            padding-left:15px;
            border-radius: 15px;
            font-family:"ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, Osaka, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif;
            box-shadow: 5px 5px 5px rgba(0,0,0,0.25);
        }
        h1 {
            background: #F0F0F0;
            font-size: 18px;
            padding: 15px 5px 15px 5px;
            border-left: 5px solid #d2691e;
            box-shadow: 5px 5px 5px rgba(0,0,0,0.25);
            margin-left: -5px;
        }
        table {
            border: #CCCCCC solid 2px;
            border-collapse:collapse;
            box-shadow: 2px 2px 2px rgba(0,0,0,0.25);
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
<header>AmidaMVC Demo Site</header>
<div id="contents">
    <h1><?php echo $title; ?></h1>
    <?php echo $contents; ?>
</div>
<div><?php echo $debug;?></div>
<footer>AmidaMVC, yet another micro Framework for PHP.</footer>
</body>
</html>