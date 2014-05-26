<?php

include_once('main.php');

if(isset($_GET['login']))
{
	$user_email = filter_string($_POST['user_email']);
	$user_password = filter_string($_POST['user_password']);
	$user_remember = $_POST['user_remember'];
	echo login($user_email, $user_password, $user_remember);
}
elseif(isset($_GET['logout']))
{
	logout();
}
elseif(isset($_GET['create_user']))
{
	$user_name = filter_string(trim($_POST['user_name']));
	$user_email = filter_string($_POST['user_email']);
	$user_password = filter_string($_POST['user_password']);
    $user_secret_code = $_POST['user_secret_code'];
	
    $user_tel = $_POST['user_tel'];
    $user_serie = $_POST['user_serie'];
    $user_numero = $_POST['user_numero'];
    
     
  
	echo create_user($user_name, $user_email, $user_password, $user_secret_code,$user_tel,$user_serie,$user_numero);
}
elseif(isset($_GET['new_user']))
{

?>

	<div class="box_div" id="login_div"><div class="box_top_div"><a href="#">Début</a> &gt; Nouvel utilisateur</div><div class="box_body_div">
	<div id="new_user_div"><div>

	<form action="." id="new_user_form"><p>

	<label for="user_name_input">Nom:</label><br>
	<input type="text" id="user_name_input"><br><br>
	<label for="user_email_input">Email:</label><br>
	<input type="text" id="user_email_input" autocapitalize="off"><br><br>
	<label for="user_password_input">Mot de passe:</label><br>
	<input type="password" id="user_password_input"><br><br>
	<label for="user_password_confirm_input">Confirmer le mot de passe:</label><br>
	<input type="password" id="user_password_confirm_input"><br><br>
    
    <label for="user_tel_input">Téléphone:</label><br>
    <input type="text" id="user_tel_input"><br><br>    
    
    <label for="user_serie_input">Série:</label><br>
    <select id="user_serie_input">
    <option>MP</option>
    <option>PT</option>
    <option>PSI</option>
    <option>PC</option>
    </select>
 <p>
   
      <label for="user_numero_input">Numéro de candidat (concours):</label><br>
    <input type="text" id="user_numero_input"><br><br>    
    </p>

    

<?php

	if(global_secret_code != '0')
	{
		echo '<label for="user_secret_code_input">Secret code: <sup><a href="." id="user_secret_code_a" tabindex="-1">What\'s this?</a></sup></label><br><input type="password" id="user_secret_code_input"><br><br>';
	}

?>

	<input type="submit" value="Créer mon compte">

	</p></form>

	</div><div>
	
	<p class="blue_p bold_p">Information:</p>
	<ul>
                                                                 

	<li>Vous pouvez faire votre réservation en un seul clic</li>
	<li>La connection est automatiquement enregistrée </li>
	<li>Pour information, votre mot de passe est crypté et ne peut pas être décodé</li>
	</ul>

	
	<script type="text/javascript">$('#email_span').html('<a href="mailto:'+$.base64.decode('<?php echo base64_encode(global_webmaster_email); ?>')+'">'+$.base64.decode('<?php echo base64_encode(global_webmaster_email); ?>')+'</a>');</script>

	</div></div>

	<p id="new_user_message_p"></p>

	</div></div>

<?php

}
elseif(isset($_GET['forgot_password']))
{

?>

	<div class="box_div" id="login_div"><div class="box_top_div"><a href="#">Début</a> &gt; Mot de pass oublié</div><div class="box_body_div">

	<p>Contactez l'un des administrateurs par email en indiquant que vous avez oublié votre mot de passe. </p>

	<?php echo list_admin_users(); ?>

	</div></div>

<?php

}
else
{

?>

	<div class="box_div" id="login_div"><div class="box_top_div">Connexion</div><div class="box_body_div">

	<form action="." id="login_form" autocomplete="off"><p>

	<label for="user_email_input">Email:</label><br><input type="text" id="user_email_input" value="<?php echo get_login_data('user_email'); ?>" autocapitalize="off"><br><br>
	<label for="user_password_input">Mot de passe:</label><br><input type="password" id="user_password_input" value="<?php echo get_login_data('user_password'); ?>"><br><br>
	<input type="checkbox" id="remember_me_checkbox" checked="checked"> <label for="remember_me_checkbox">Rester connecté</label><br><br>		
	<input type="submit" value="Se connecter">

	</p></form>

	<p id="login_message_p"></p>
	<p><a href="#new_user">Nouvel utilisateur</a> | <a href="#forgot_password">Mot de passe oublié </a></p>

	</div></div>

<?php

}

?>
