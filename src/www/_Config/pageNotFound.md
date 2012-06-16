<?php
/** @var $_pageObj \AmidaMVC\Framework\PageObj */
/** @var $_ctrl    \AmidaMVC\Framework\Controller */
$_ctrl->i18n->textSection( '_pageNotFound' );
?>
#<?php echo $_ctrl->i18n->text('page_not_found'); ?>


<style type="text/css">
    #notFound{
        width: 80%;
        margin: 20px auto 20px auto;
        padding: 10px;
        border: 1px solid #CCCCCC;
        box-shadow: 5px 5px 5px #E0E0E0;
    }
    em {
        color: #d2691e;
    }
    #tryAgain{
        margin: 20px auto 20px auto;
        padding: 10px;
        width: 450px;
        text-align: center;
        border: 1px solid #d2691e;
        background-color: #fbeed5;
    }
    #NotImage {
        border: 0px;
        border-radius: 15px;
    }
</style>
<div markdown="1" id="notFound">

<?php echo $_ctrl->i18n->text('not_found_message'); ?>:
: _<?php echo $_ctrl->getBaseUrl( $_ctrl->getPathInfo() ); ?>_


<div style="text-align: center;">
    <img src="<?php echo $_ctrl->getBaseUrl('/common/img/404notFound.JPG');?>" title="404 page not found" id="NotImage">
</div>
<div id="tryAgain">
    <a href="<?php echo $_ctrl->getBaseUrl();?>"><?php echo $_ctrl->i18n->text('try_again'); ?></a>
</div>
</div>
