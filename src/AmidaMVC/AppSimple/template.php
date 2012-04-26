<?php
/** @var $_pageObj \AmidaMVC\Framework\PageObj */
/** @var $_ctrl \AmidaMVC\AppSimple\Application */
?>
<!DOCTYPE HTML>
<html lang="en">
<title><?php echo $_pageObj->_title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        margin:0;
        padding:0;
    }
    p {
        margin: 0; padding: 0;
    }
    .mainbody {
        width: 700px;
        margin: auto;
    }
    header {
        padding: 14px 25px;
        height: 50px;
        background-color: #da4f49;
        font-size: 25px ;
    }
    header a {
        text-decoration: none;
        color: #FFF;
    }
    div#content {
        line-height: 1.3em;
    }
    footer {
        margin: 50px 0px;
        font-size: 12px;
        background-color: #da4f49;
        padding: 10px 25px;
        color: #fff;
    }
</style>
<div class="mainbody">
    <header><a href="<?php echo $_ctrl->getBaseUrl(); ?>"><?php echo $_ctrl->getOption( 'site_title' ); ?></a></header>
    <div id="content" ><?php echo $_pageObj->getContent(); ?></div>
    <footer>
        <p>AppSimple Suites by AmidaMVC.</p>
        <p>gitHub:https://github.com/asaokamei/AmidaMVC</p>
    </footer>
</div>
</html>
