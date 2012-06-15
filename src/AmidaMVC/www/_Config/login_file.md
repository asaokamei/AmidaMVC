<?php
/** @var $_pageObj \AmidaMVC\Framework\PageObj */
/** @var $_ctrl    \AmidaMVC\Framework\Controller */
$_ctrl->i18n->textSection( '_loginPage' );
?>
#<?php echo $_ctrl->i18n->text('login_title'); ?>


<form name="loginForm" method="post" id="loginForm" action="">
<table>
<tr><th><label><?php echo $_ctrl->i18n->text('login_id'); ?>:</th><td><input type="text" name="auth_name" /></label></td></tr>
<tr><th><label><?php echo $_ctrl->i18n->text('password'); ?>:</th><td><input type="password" name="auth_pass" /></label><br /></td></tr>
<tr><td colspan="2">
<input type="hidden" name="auth_act" value="authNot" />
<input type="submit" name="submit" value="<?php echo $_ctrl->i18n->text('login_button'); ?>" />
</td></tr>
</table>
</form>

    <?php echo $_ctrl->i18n->text('authNot_notice'); ?>