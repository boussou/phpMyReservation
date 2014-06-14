<?php 

include_once('main.php');

    
    
if(check_login() != true) { exit; }

if($_SESSION['user_is_admin'] == '1' && isset($_GET['list_users']))
{
	echo list_users();
}
elseif($_SESSION['user_is_admin'] == '1' && isset($_GET['reset_user_password']))
{
	$user_id = filter_string($_POST['user_id']);
	echo reset_user_password($user_id);
}
elseif($_SESSION['user_is_admin'] == '1' && isset($_GET['change_user_permissions']))
{
	$user_id = filter_string($_POST['user_id']);
	echo change_user_permissions($user_id);
}
elseif($_SESSION['user_is_admin'] == '1' && isset($_GET['delete_user_data']))
{
	$user_id = filter_string($_POST['user_id']);
	$data = $_POST['delete_data'];
	echo delete_user_data($user_id, $data);
}
elseif($_SESSION['user_is_admin'] == '1' && isset($_GET['delete_all']))
{
	$data = $_POST['delete_data'];
	echo delete_all($data);
}
elseif($_SESSION['user_is_admin'] == '1' && isset($_GET['save_system_configuration']))
{
	$price = filter_string($_POST['price']);
	echo save_system_configuration($price);
}
elseif(isset($_GET['get_usage']))
{
	echo get_usage();
}
elseif(isset($_GET['get_reservation_reminders']))
{
	echo get_reservation_reminders();
}
elseif(isset($_GET['toggle_reservation_reminder']))
{
	echo toggle_reservation_reminder();
}
elseif(isset($_GET['change_user_details']))
{
	$user_name = filter_string(trim($_POST['user_name']));
	$user_email = filter_string($_POST['user_email']);
	$user_password = filter_string($_POST['user_password']);
	echo change_user_details($user_name, $user_email, $user_password);
}
else
{
	echo '<div class="box_div" id="cp_div"><div class="box_top_div"><a href="#">Start</a> &gt; Control panel</div><div class="box_body_div">';

	if($_SESSION['user_is_admin'] == '1')
	{

?>

		<h3>Gestion des utilisateurs</h3>

		<div id="users_div"><?php echo list_users(); ?></div>

		<p class="center_p"><input type="button" class="small_button blue_button" id="reset_user_password_button" value="Reset password"> <input type="button" class="small_button blue_button" id="change_user_permissions_button" value="Change permissions"> <input type="button" class="small_button" id="delete_user_reservations_button" value="Delete reservations"> <input type="button" class="small_button" id="delete_user_button" value="Delete user"></p>
		<p class="center_p" id="user_administration_message_p"></p>

		<hr>

		<h3>Administration de base de données</h3>

		<p class="smalltext_p">Celles-ci nécessiteront une confirmation. Votre utilisateur et réservations ne seront pas supprimés sauf si vous supprimez tout.</p>

		<p><input type="button" class="small_button" id="delete_all_reservations_button" value="Delete all reservations"> <input type="button" class="small_button" id="delete_all_users_button" value="Delete all users"> <input type="button" class="small_button" id="delete_everything_button" value="Delete everything"></p>

		<p id="database_administration_message_p"></p>

		<hr>
        

		<hr class="blue_hr thick_hr">

<?php

	}

?>

	<h3>votre utilisation</h3>

	<p class="smalltext_p">Si vous avez eu une utilisation sans faire de réservation en premier lieu, cliquez sur le bouton ci-dessous. Cela ne pourra pas être annulé.</p>

	<div id="usage_div"><?php echo get_usage(); ?></div>

 
	<p id="usage_message_p"></p>

	<hr>

<?php

	if(global_reservation_reminders == '1')
	{

?>

	<h3>vos paramètres</h3>

	<p class="smalltext_p">Avant de modifier un paramètre, veuillez vérifier que vos coordonnées ci-dessous sont correctes.</p>
	<p><span id="reservation_reminders_span"><?php echo get_reservation_reminders(); ?></span> <label for="reservation_reminders_checkbox">Envoyez-moi un rappel de réservation par email</label></p>

	<p id="settings_message_p"></p>

	<hr>

<?php

	}

?>

	<h3>vos détails</h3>

	<p class="smalltext_p">Si vous changez votre email, vous devez utiliser le nouveau mot de passe pour vous connecter - laissez le vide pour le laisser inchangé.</p>

	<form action="." id="user_details_form" autocomplete="off"><p>

	<div id="user_details_div"><div>
	<label for="user_name_input">Nom:</label><br>
	<input type="text" id="user_name_input" value="<?php echo $_SESSION['user_name']; ?>"><br><br>
	<label for="user_email_input">Email:</label><br>
	<input type="text" id="user_email_input" autocapitalize="off" value="<?php echo $_SESSION['user_email']; ?>">
	</div><div>
	<label for="user_password_input">Mot de passe:</label><br>
	<input type="password" id="user_password_input"><br><br>
	<label for="user_password_confirm_input">Confirmer le mot de passe:</label><br>
	<input type="password" id="user_password_confirm_input">
	</div></div>

	<p><input type="submit" class="small_button blue_button" value="Update my details"></p>

	</p></form>

	<p id="user_details_message_p"></p>

	</div></div>

<?php

}

?>
