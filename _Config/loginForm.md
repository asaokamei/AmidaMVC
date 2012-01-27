Login for Developer's Zone
==========================

<form name="loginForm" method="post" id="loginForm" action="">
<label>login_id:<input type="text" name="auth_name" /></label><br />
<label>password:<input type="password" name="auth_pass" /></label><br />
<input type="hidden" name="auth_act" value="authNot" />
<input type="submit" name="submit" value="Login" />
</form>

AuthNot module requires to have same login_id and password.


example: id=admin, pw=admin