Page Not Found (Error Code 404)
===============================

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
</style>
<div markdown="1" id="notFound">

Could not find the requested page:
: _<?php echo $_ctrl->getBaseUrl( $_ctrl->getPathInfo() ); ?>_


<div style="text-align: center;">
    <img src="<?php echo $_ctrl->getBaseUrl('/common/img/404notFound.JPG');?>" title="404 page not found" border="0">
</div>
<div id="tryAgain">
    <a href="<?php echo $_ctrl->getBaseUrl();?>">please retry again!</a>
</div>
</div>
