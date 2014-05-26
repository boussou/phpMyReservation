<?php

// Configuration

function get_configuration($data)
{

    
    $sql = ("SELECT * FROM " . global_mysql_configuration_table);
    $configuration = DB::query_single_row($sql);
   
	return($configuration[$data]);
}

// Password

function random_password()
{
	$password = rand('1001', '9999');
	return $password;
}

function encrypt_password($password)
{
	$password = crypt($password, '$1$' . global_salt);
	return($password);
}

function add_salt($password)
{
	$password = '$1$' . substr(global_salt, 0, -1) . '$' . $password;
	return($password);
}

function strip_salt($password)
{
	$password = str_replace('$1$' . substr(global_salt, 0, -1) . '$', '', $password);
	return($password);	
}

// String manipulation

function modify_email($email)
{
	$email = str_replace('@', '(at)', $email);
	$email = str_replace('.', '(dot)', $email);
	return($email);
}

// String validation

function validate_user_name($user_name)
{
	if(preg_match('/^[a-z æøåÆØÅ]{2,12}$/i', $user_name))
	{
		return(true);
	}
}

function validate_user_email($user_email)
{
	if(filter_var($user_email, FILTER_VALIDATE_EMAIL) && strlen($user_email) < 51)
	{
		return(true);
	}
}

function validate_user_password($user_password)
{
	if(strlen($user_password) > 3 && trim($user_password) != '')
	{
		return(true);
	}
}

function validate_price($price)
{
	if(is_numeric($price))
	{
		return(true);
	}
}

// User validation

function user_name_exists($user_name)
{

    $sql = ("SELECT * FROM " . global_mysql_users_table . " WHERE user_name='$user_name'") ;
    $res = DB::query_all($sql);
    
	if(count($res) > 0)
	{
		return(true);
	}
}

function user_email_exists($user_email)
{

	$query = ("SELECT * FROM " . global_mysql_users_table . " WHERE user_email='$user_email'")    ;
    $res = DB::query_all($query);
                                
    if(count($res) > 0)
	{
		return(true);
	}
}

// Login

function get_login_data($data)
{
	if($data == 'user_email' && isset($_COOKIE[global_cookie_prefix . '_user_email']))
	{
		return($_COOKIE[global_cookie_prefix . '_user_email']);
	}
	elseif($data == 'user_password' && isset($_COOKIE[global_cookie_prefix . '_user_password']))
	{
		return($_COOKIE[global_cookie_prefix . '_user_password']);
	}
}

function login($user_email, $user_password, $user_remember)
{
	$user_password_encrypted = encrypt_password($user_password);
	$user_password = add_salt($user_password);
	
	$query = ("SELECT * FROM " . global_mysql_users_table . " WHERE user_email='$user_email' AND user_password='$user_password_encrypted' OR user_email='$user_email' AND user_password='$user_password'");
       $all = DB::query_all($query);
 
    if(count($all) == 1)
	{
			$user = $all[0];

			$_SESSION['user_id'] = $user['user_id'];
			$_SESSION['user_is_admin'] = $user['user_is_admin'];
			$_SESSION['user_email'] = $user['user_email'];
			$_SESSION['user_name'] = $user['user_name'];
			$_SESSION['user_reservation_reminder'] = $user['user_reservation_reminder'];
			$_SESSION['logged_in'] = '1';

			if($user_remember == '1')
			{
				$user_password = strip_salt($user['user_password']);

				setcookie(global_cookie_prefix . '_user_email', $user['user_email'], time() + 3600 * 24 * intval(global_remember_login_days));
				setcookie(global_cookie_prefix . '_user_password', $user_password, time() + 3600 * 24 * intval(global_remember_login_days));
			}

			return(1);
	}
}

function check_login()
{
	if(isset($_SESSION['logged_in']))
	{
		$user_id = $_SESSION['user_id'];
		$query = ("SELECT * FROM " . global_mysql_users_table . " WHERE user_id='$user_id'");
        
        
    $all = DB::query_all($query);
 
        
        if(count($all) == 1)
		{
			return(true);
		}
		else
		{
			logout();
			echo '<script type="text/javascript">window.location.replace(\'.\');</script>';
		}
	}
	else
	{
		logout();
		echo '<script type="text/javascript">window.location.replace(\'.\');</script>';
	}
}

function logout()
{
         $user_id = $_SESSION['user_id'];

        if($user_id)
        {

            $query  = DB::query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'");
            //
            $users = "";
//            $users = "Reservations:\n";
            $email="";
            foreach($query as $user)
            {
//            $users .= 'Le ' . $user['reservation_date'] . ' à ' . $user['reservation_time'] . "\n";
            $users .=  $user['reservation_date'] . ' à ' . $user['reservation_time'] . "\n";
            $email=$user['reservation_user_email'];
            }
            if(!empty($users))
            {
                                        
            $time = $user['reservation_made_time'] ;
            $time = '';
          
            $subject = 'Confirmation Jury ISEN-Toulon';
            $message = "Ceci est une confirmation de réservation pour votre entretien de recrutement qui aura lieu à l'ISEN-Toulon à la maison des technologies (Adresse:  Place Georges Pompidou, 83000 Toulon)\r\n\nVotre entretien aura bien lieu le " 
            //global_title. ' '.  global_organization .'\r\n\n'
            . $time . "\r\n\n$users\r\n\n"
            .' Merci de vous présenter à 8h30 ou 13h30 (selon votre demi journée de jury) pour une présentation de l\'école et de ses programmes.\nN\'oubliez pas de vous munir de votre carte d\'identité.'. "\r\n\n"
            .'Pour votre information quelques chiffres sur l\'ISEN Toulon: https://www.facebook.com/ISEN.Toulon/photos/a.232708453464172.53543.201213143280370/660728240662189/?type=1&theater'
            . "\r\n\n"
            .'Bien cordialement.' 
            . "\r\n\n"
            . "Cellule admission ISEN-Toulon."
            . "\r\n\n"
            . "Pour plus d'information: Veronique Casiez : 04 94 03 89 59 - veronique.casiez@isen.fr";
            

            $headers = "From: " . global_organization . " <" . global_reservation_reminders_email . ">\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/plain; charset=utf-8\r\n";
            $headers .= 'Cc: '.global_reservation_reminders_email . "\r\n";

            mail($email, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);
            }
        }

    
	session_unset();
	setcookie(global_cookie_prefix . '_user_email', '', time() - 3600);
	setcookie(global_cookie_prefix . '_user_password', '', time() - 3600);
}


function validerNumero($telATester) {
    //Retourne le numéro s'il est valide, sinon false.
    return preg_match('`^0[1-9]([-. ]?[0-9]{2}){4}$`', $telATester) ? $telATester : false;
}

function create_user($user_name, $user_email, $user_password, $user_secret_code,
            $user_tel,
  $user_serie ,
  $user_numero 
)
{
	if(validate_user_name($user_name) != true)
	{
		return('<span class="error_span">Le nom doit être <u>composé de lettres uniquement </u> et être longues <u> de 2 à 12 lettres </ u>. Si votre nom est plus long, utilisez une forme  abrégée</span>');
	}
	elseif(validate_user_email($user_email) != true)
	{
		return('<span class="error_span">L\'E-mail doit être une adresse e-mail valide et pas plus de 50 caractères</span>');
	}
	elseif(validate_user_password($user_password) != true)
	{
		return('<span class="error_span">Le mot de passe doit comporter au moins 4 caractères</span>');
	}
	elseif(global_secret_code != '0' && $user_secret_code != global_secret_code)
	{
		return('<span class="error_span">Wrong secret code</span>');
	}
	elseif(user_name_exists($user_name) == true)
	{
		return('<span class="error_span">Ce nom est déjà utilisé. Si vous avez le même nom que quelqu\'un d\'autre, utilisez une autre orthographe qui vous identifie tout de même</span>');
	}
	elseif(user_email_exists($user_email) == true)
	{
		return('<span class="error_span">Cet mail est déjà enregistré. <a href="#forgot_password">Avez vous oublié votre mot de passe? </a></span>');
	}
    elseif(empty($user_tel))
    {
        return('<span class="error_span">Il manque le téléphone!</span>');
    }  
    elseif(empty($user_serie))
    {
        return('<span class="error_span">Il manque la série!</span>');
    }    
    elseif(empty($user_numero))
    {
        return('<span class="error_span">Il manque le numéro de candidat!</span>');
    }          
    
        elseif(!validerNumero($user_tel))
    {
        return('<span class="error_span">le numéro de téléphone n\'est pas au bon format (tiret, point ou espace comme séparateur)</span>');
    }          
	else
	{
		$query = ("SELECT * FROM " . global_mysql_users_table . "");
           $all = DB::query_all($query);
           if(count($all) == 1)
		{
			$user_is_admin = '1';
		}
		else
		{
			$user_is_admin = '0';
		}

		$user_password = encrypt_password($user_password);

		db::query("INSERT INTO " . global_mysql_users_table . " (user_is_admin,user_email,user_password,user_name,user_reservation_reminder,tel,serie,numero) VALUES ($user_is_admin,'$user_email','$user_password','$user_name','0','$user_tel','$user_serie','$user_numero')");

		$user_password = strip_salt($user_password);

		setcookie(global_cookie_prefix . '_user_email', $user_email, time() + 3600 * 24 * intval(global_remember_login_days));
		setcookie(global_cookie_prefix . '_user_password', $user_password, time() + 3600 * 24 * intval(global_remember_login_days));

		return(1);
	}
}

function list_admin_users()
{
	$query = DB::query("SELECT * FROM " . global_mysql_users_table . " WHERE user_is_admin='1' ORDER BY user_name");

    if(count($query) == 0)
	{
		return('<span class="error_span">Il n`\'y a pas d\'admin</span>');
	}
	else
	{
		$return = '<table id="forgot_password_table"><tr><th>Nom</th><th>Email</th></tr>';

		$i = 0;

    foreach($query as $user)

		{
			$i++;

			$return .= '<tr><td>' . $user['user_name'] . '</td><td><span id="email_span_' . $i . '"></span></td></tr><script type="text/javascript">$(\'#email_span_' . $i . '\').html(\'<a href="mailto:\'+$.base64.decode(\'' . base64_encode($user['user_email']) . '\')+\'">\'+$.base64.decode(\'' . base64_encode($user['user_email']) . '\')+\'</a>\');</script>';
		}

		$return .= '</table>';

		return($return);
	}
}

// Reservations

function highlight_day($day)
{
	$day = str_ireplace(global_day_name, '<span id="today_span">' . global_day_name . '</span>', $day);
	return $day;
}


function read_reservation($week, $day, $time)
{
    $user_id = $_SESSION['user_id'];
    $query = ("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'");
    $reservation = DB::query_all($query);

        $resa4user = DB::query_single_row("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'
        AND reservation_user_id=$user_id
        ");
        
    if(!empty($resa4user))
    return($resa4user['reservation_user_name']);
    
    if (count($reservation)==max_reservation_per_cell) return "Plus disponibles";    
    
   ///  echo $user_id; print_r($reservation);
  
    if (count($reservation)==1) 
//    if($reservation[0]['reservation_user_id']==$user_id)
//    return($reservation[0]['reservation_user_name']);
//    else
//    return "1/".max_reservation_per_cell. " réservé";
    return "1 place sur ".max_reservation_per_cell. " réservée";
    
    return "disponible (2 places)";
}

function read_reservation_css($week, $day, $time)
{
    $user_id = $_SESSION['user_id'];
	$query = ("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'");
    $reservation = DB::query_all($query);
    
    if (count($reservation)==max_reservation_per_cell) return "resa_full";    
    
   ///  echo $user_id; print_r($reservation);
  
    if (count($reservation)>0) 
    return "resa_partial";

    return "";
}

function read_reservation_details($week, $day, $time)
{
	$reservations = DB::query_all("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'");

	if(empty($reservations))
	{
		return(0);		
	}
	else
	{
        $s="";
        $i=1;
        foreach($reservations as $reservation)
        if($_SESSION['user_is_admin'])
        $s.=($i>1?"<br>":"").($i++.' <b>Reservation faite le:</b> ' . $reservation['reservation_made_time'] . '<br><b>Email de l\'utilisateur:</b> ' . $reservation['reservation_user_email']."<br>");
        else
		$s.=($i++.' <b>Reservation faite le:</b> ' . $reservation['reservation_made_time'] . "<br>");
                
        return $s;
	}
}

function toggle_reservation($week, $day, $time,$userid)
{

    //make ou delete?
    $user_id = $_SESSION['user_id'];
    $user_email = $_SESSION['user_email'];
    $user_name = $_SESSION['user_name'];
    $price = global_price;

        
        $query = DB::query_single_row("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'
        AND reservation_user_id=$user_id
        ");
   if(empty($query))
return make_reservation($week, $day, $time,$user_id);
else
return delete_reservation($week, $day, $time,$user_id);

}


function make_reservation($week, $day, $time)
{

	$user_id = $_SESSION['user_id'];
	$user_email = $_SESSION['user_email'];
	$user_name = $_SESSION['user_name'];
	$price = global_price;

    define('this_week_number', ltrim(date('W'), '0'));   //week number of year
    
    if($week < this_week_number && $_SESSION['user_is_admin'] != '1' || $week == this_week_number && $day < global_day_number && $_SESSION['user_is_admin'] != '1')
	{
		return("Vous ne pouvez réserver dans le passé");
	}
	elseif($week > global_week_number + global_weeks_forward && $_SESSION['user_is_admin'] != '1')
	{
		return('Vous ne pouvez réserver ' . global_weeks_forward . ' semaine en avant');
	}
	else
	{
        $nb_resa=count_reservations($user_id);
        if($nb_resa>=max_reservation_per_user)
        {
            $resa=get_first_reservation($user_id) ;
             $resa=$resa['reservation_date'].' à '.$resa['reservation_time'];
             return "Vous avez déja une autre réservation le $resa. Veuillez l'annuler d'abord";
        }
               
		$query = DB::query_all("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'");

        if(count($query) < max_reservation_per_cell)
		{
			$year = global_year;
           
            $week_start = new DateTimeFrench();
            $week_start->setISODate($year,$week);
            $week_start->add(new DateInterval("P".($day-1)."D"));
//            $datefull=$week_start->format('l j F Y');           
            $datefull=$week_start->format('Y-m-d');         
                                                      
//            echo $datefull;
           
            

			DB::query("INSERT INTO " . global_mysql_reservations_table . " (reservation_made_time,reservation_year,reservation_week,reservation_day,reservation_date,reservation_time,reservation_price,reservation_user_id,reservation_user_email,reservation_user_name) VALUES (datetime('now','localtime'),'$year','$week','$day','$datefull','$time','$price','$user_id','$user_email','$user_name')");

			return(1);
		}
		else
		{
			return("Quelqu'un d'autre vient juste de réserver ce créneau horaire");
		}
	}
}

function delete_reservation($week, $day, $time,$user_id)
{

//	if($week < global_week_number && $_SESSION['user_is_admin'] != '1' || $week == global_week_number && $day < global_day_number && $_SESSION['user_is_admin'] != '1')
//	{
//                return("Vous ne pouvez réserver dans le passé" );
//
//	}
//	else
    if($week > global_week_number + global_weeks_forward && $_SESSION['user_is_admin'] != '1')
	{
        return('Vous ne pouvez réserver ' . global_weeks_forward . ' semaine en avant');
	}
	else
	{
		$user = DB::query_single_row("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'
                            AND reservation_user_id=$user_id
        ");

		if($user['reservation_user_id'] == $_SESSION['user_id'] || $_SESSION['user_is_admin'] == '1')
		{
			DB::query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'
                    AND reservation_user_id=$user_id
                    ");

			return(1);
		}
		else
		{
			return('Vous ne pouvez pas supprimer la reservation de quelqu\'un d\'autre');
		}
	}
}

// Admin control panel

function list_users()
{
	$query = DB::query("SELECT * FROM " . global_mysql_users_table . " ORDER BY user_is_admin DESC, user_name");

	$users = '<table id="users_table"><tr><th>ID</th><th>Admin</th><th>Name</th><th>Email</th><th>Reminders</th><th>Usage</th><th></th></tr>';

    foreach($query as $user)
	{
		$users .= '<tr id="user_tr_' . $user['user_id'] . '"><td><label for="user_radio_' . $user['user_id'] . '">' . $user['user_id'] . '</label></td><td>' . $user['user_is_admin'] . '</td><td><label for="user_radio_' . $user['user_id'] . '">' . $user['user_name'] . '</label></td><td><label for="user_radio_' . $user['user_id'] . '">' . $user['user_email'] . '</label></td><td>' . $user['user_reservation_reminder'] . '</td><td>' . count_reservations($user['user_id']) . '</td><td>' . '</td><td><input type="radio" name="user_radio" class="user_radio" id="user_radio_' . $user['user_id'] . '" value="' . $user['user_id'] . '"></td></tr>';
	}

	$users .= '</table>';

	return($users);
}

function reset_user_password($user_id)
{
	$password = random_password();
	$password_encrypted = encrypt_password($password);

	DB::query("UPDATE " . global_mysql_users_table . " SET user_password='$password_encrypted' WHERE user_id='$user_id'");

	if($user_id == $_SESSION['user_id'])
	{
		return(0);
	}
	else
	{
		return('The password to the user with ID ' . $user_id . ' is now "' . $password . '". The user can now log in and change the password');
	}
}

function change_user_permissions($user_id)
{
	if($user_id == $_SESSION['user_id'])
	{
		return('<span class="error_span">Sorry, you can\'t use your superuser powers to remove them</span>');
	}
	else
	{
		DB::query("UPDATE " . global_mysql_users_table . " SET user_is_admin = 1 - user_is_admin WHERE user_id='$user_id'");

		return(1);
	}
}

function delete_user_data($user_id, $data)
{
	if($user_id == $_SESSION['user_id'] && $data != 'reservations')
	{
		return('<span class="error_span">Sorry, self-destructive behaviour is not accepted</span>');
	}
	else
	{
		if($data == 'reservations')
		{
			DB::query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'");
		}
		elseif($data == 'user')
		{
			DB::query("DELETE FROM " . global_mysql_users_table . " WHERE user_id='$user_id'");
			DB::query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'");
		}

		return(1);
	}
}

function delete_all($data)
{
	$user_id = $_SESSION['user_id'];

	if($data == 'reservations')
	{
		DB::query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_user_id!='$user_id'");
	}
	elseif($data == 'users')
	{
		DB::query("DELETE FROM " . global_mysql_users_table . " WHERE user_id!='$user_id'");
		DB::query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_user_id!='$user_id'");
	}
	elseif($data == 'everything')
	{
		DB::query("DELETE FROM " . global_mysql_users_table . "");
		DB::query("DELETE FROM " . global_mysql_reservations_table . "");
	}

	return(1);
}

function save_system_configuration($price)
{
	if(validate_price($price) != true)
	{
		return('<span class="error_span">Price must be a number (use . and not , if you want to use decimals)</span>');
	}
	else
	{
		DB::query("UPDATE " . global_mysql_configuration_table . " SET price='$price'");
	}

	return(1);
}

// User control panel

function get_usage()
{                                                                                                                  
    $user_id = $_SESSION['user_id'];
   $query  = DB::query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'");
//
    $users = '<table id="usage_table"><tr><th>Reservations</th><th>When</th></tr>';

    foreach($query as $user)
    {
        $users .= '<tr><td>' . $user['reservation_date'] . '</label></td><td>' . $user['reservation_time'] . '</td>'.
        '</tr>';
    }

    
    $users .= '</table>';

    return($users);
}

function count_reservations($user_id)
{
	$count  = DB::query_single_field("SELECT count(*) FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'");
//	$count = mysql_num_rows($query);
	return($count);
}

function get_first_reservation($user_id)
{
    $query = DB::query_single_row("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id' LIMIT 1");

    return $query;
    
    
}

function cost_reservations($user_id)
{
	$query = DB::query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'");

	$cost = 0;

        foreach($query as $reservation)

	{
		$cost =+ $cost + $reservation['reservation_price'];	
	}

	return($cost);
}

function get_reservation_reminders()
{
	$user_id = $_SESSION['user_id'];
	$user = DB::query_single_row("SELECT * FROM " . global_mysql_users_table . " WHERE user_id='$user_id'");

	if($user['user_reservation_reminder'] == 1)
	{
		$return = '<input type="checkbox" id="reservation_reminders_checkbox" checked="checked">';
	}
	else
	{
		$return = '<input type="checkbox" id="reservation_reminders_checkbox">';
	}

	return($return);
}

function toggle_reservation_reminder()
{
	$user_id = $_SESSION['user_id'];
	DB::query("UPDATE " . global_mysql_users_table . " SET user_reservation_reminder = 1 - user_reservation_reminder WHERE user_id='$user_id'");

	return(1);
}

function change_user_details($user_name, $user_email, $user_password)
{
	$user_id = $_SESSION['user_id'];

	if(validate_user_name($user_name) != true)
	{
		return('<span class="error_span">Name must be <u>letters only</u> and be <u>2 to 12 letters long</u>. If your name is longer, use a short version of your name</span>');
	}
	if(validate_user_email($user_email) != true)
	{
		return('<span class="error_span">Email must be a valid email address and be no more than 50 characters long</span>');
	}
	elseif(validate_user_password($user_password) != true && !empty($user_password))
	{
		return('<span class="error_span">Password must be at least 4 characters</span>');
	}
	elseif(user_name_exists($user_name) == true && $user_name != $_SESSION['user_name'])
	{
		return('<span class="error_span">Name is already in use. If you have the same name as someone else, use another spelling that identifies you</span>');
	}
	elseif(user_email_exists($user_email) == true && $user_email != $_SESSION['user_email'])
	{
		return('<span class="error_span">Email is already registered</span>');
	}
	else
	{
		if(empty($user_password))
		{
			DB::query("UPDATE " . global_mysql_users_table . " SET user_name='$user_name', user_email='$user_email' WHERE user_id='$user_id'");
		}
		else
		{
			$user_password = encrypt_password($user_password);

			DB::query("UPDATE " . global_mysql_users_table . " SET user_name='$user_name', user_email='$user_email', user_password='$user_password' WHERE user_id='$user_id'");
		}

		DB::query("UPDATE " . global_mysql_reservations_table . " SET reservation_user_name='$user_name', reservation_user_email='$user_email' WHERE reservation_user_id='$user_id'");

		$_SESSION['user_name'] = $user_name;
		$_SESSION['user_email'] = $user_email;

		$user_password = strip_salt($user_password);

		setcookie(global_cookie_prefix . '_user_email', $user_email, time() + 3600 * 24 * intval(global_remember_login_days));
		setcookie(global_cookie_prefix . '_user_password', $user_password, time() + 3600 * 24 * intval(global_remember_login_days));

		return(1);
	}
}

