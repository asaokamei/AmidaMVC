<!DOCTYPE HTML>
<html lang="en">
<title><?php echo $_siteObj->_title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .mainbody {
    }
</style>
<div class="mainbody">
    <header><a href="<?php echo $_ctrl->getBaseUrl(); ?>"><?php echo $_ctrl->get( 'site_title' ); ?></a></header>
    <content><?php echo $_siteObj->getContent(); ?></content>
</div>
</html>