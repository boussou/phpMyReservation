<?php

include_once('main.php');

if(isset($_GET['login']))
{
	$user_email = mysql_real_escape_string($_POST['user_email']);
	$user_password = mysql_real_escape_string($_POST['user_password']);
	$user_remember = $_POST['user_remember'];
	echo login($user_email, $user_password, $user_remember);
}
elseif(isset($_GET['logout']))
{
	logout();
}
elseif(isset($_GET['create_user']))
{
	$user_name = mysql_real_escape_string(trim($_POST['user_name']));
	$user_email = mysql_real_escape_string($_POST['user_email']);
	$user_password = mysql_real_escape_string($_POST['user_password']);
	$user_secret_code = $_POST['user_secret_code'];
	echo create_user($user_name, $user_email, $user_password, $user_secret_code);
}
elseif(isset($_GET['new_user']))
{

?>

	<div class="box_div" id="login_div"><div class="box_top_div"><a href="#">Start</a> &gt; New user</div><div class="box_body_div">
	<div id="new_user_div"><div>

	<form action="." id="new_user_form"><p>

	<label for="user_name_input">Name:</label><br>
	<input type="text" id="user_name_input"><br><br>
	<label for="user_email_input">Email:</label><br>
	<input type="text" id="user_email_input" autocapitalize="off"><br><br>
	<label for="user_password_input">Password:</label><br>
	<input type="password" id="user_password_input"><br><br>
	<label for="user_password_confirm_input">Confirm password:</label><br>
	<input type="password" id="user_password_confirm_input"><br><br>

<?php

	if(global_secret_code != '0')
	{
		echo '<label for="user_secret_code_input">Secret code: <sup><a href="." id="user_secret_code_a" tabindex="-1">What\'s this?</a></sup></label><br><input type="password" id="user_secret_code_input"><br><br>';
	}

?>

	<input type="submit" value="Create user">

	</p></form>

	</div><div>
	
	<p class="blue_p bold_p">Information:</p>
	<ul>
	<li>With just a click you can make your reservation</li>
	<li>Your usage is stored automatically</li>
	<li>Your password is encrypted and can't be read</li>
	</ul>

	<div id="user_secret_code_div">Secret code is used to only allow certain people to create a new user. Contact the webmaster by email at <span id="email_span"></span> to get the secret code.</div>

	<script type="text/javascript">$('#email_span').html('<a href="mailto:'+$.base64.decode('<?php echo base64_encode(global_webmaster_email); ?>')+'">'+$.base64.decode('<?php echo base64_encode(global_webmaster_email); ?>')+'</a>');</script>

	</div></div>

	<p id="new_user_message_p"></p>

	</div></div>

<?php

}
elseif(isset($_GET['forgot_password']))
{

?>

	<div class="box_div" id="login_div"><div class="box_top_div"><a href="#">Start</a> &gt; Forgot password</div><div class="box_body_div">

	<p>Contact one of the admins below by email and write that you've forgotten your password, and you will get a new one. The password can be changed after logging in.</p>

	<?php echo list_admin_users(); ?>

	</div></div>

<?php

}
else
{

?>

	<div class="box_div" id="login_div"><div class="box_top_div">Log in</div><div class="box_body_div">

	<form action="." id="login_form" autocomplete="off"><p>

	<label for="user_email_input">Email:</label><br><input type="text" id="user_email_input" value="<?php echo get_login_data('user_email'); ?>" autocapitalize="off"><br><br>
	<label for="user_password_input">Password:</label><br><input type="password" id="user_password_input" value="<?php echo get_login_data('user_password'); ?>"><br><br>
	<input type="checkbox" id="remember_me_checkbox" checked="checked"> <label for="remember_me_checkbox">Remember me</label><br><br>		
	<input type="submit" value="Log in">

	</p></form>

	<p id="login_message_p"></p>
	<p><a href="#new_user">New user</a> | <a href="#forgot_password">Forgot password</a></p>

	</div></div>

<?php

}

?>
