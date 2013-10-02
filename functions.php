<?php

// Configuration

function get_configuration($data)
{
	$query = mysql_query("SELECT * FROM " . global_mysql_configuration_table)or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
	$configuration = mysql_fetch_array($query);
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
	$query = mysql_query("SELECT * FROM " . global_mysql_users_table . " WHERE user_name='$user_name'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

	if(mysql_num_rows($query) > 0)
	{
		return(true);
	}
}

function user_email_exists($user_email)
{
	$query = mysql_query("SELECT * FROM " . global_mysql_users_table . " WHERE user_email='$user_email'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

	if(mysql_num_rows($query) > 0)
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
	
	$query = mysql_query("SELECT * FROM " . global_mysql_users_table . " WHERE user_email='$user_email' AND user_password='$user_password_encrypted' OR user_email='$user_email' AND user_password='$user_password'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

	if(mysql_num_rows($query) == 1)
	{
			$user = mysql_fetch_array($query);

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
		$query = mysql_query("SELECT * FROM " . global_mysql_users_table . " WHERE user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

		if(mysql_num_rows($query) == 1)
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
	session_unset();
	setcookie(global_cookie_prefix . '_user_email', '', time() - 3600);
	setcookie(global_cookie_prefix . '_user_password', '', time() - 3600);
}

function create_user($user_name, $user_email, $user_password, $user_secret_code)
{
	if(validate_user_name($user_name) != true)
	{
		return('<span class="error_span">Name must be <u>letters only</u> and be <u>2 to 12 letters long</u>. If your name is longer, use a short version of your name</span>');
	}
	elseif(validate_user_email($user_email) != true)
	{
		return('<span class="error_span">Email must be a valid email address and be no more than 50 characters long</span>');
	}
	elseif(validate_user_password($user_password) != true)
	{
		return('<span class="error_span">Password must be at least 4 characters</span>');
	}
	elseif(global_secret_code != '0' && $user_secret_code != global_secret_code)
	{
		return('<span class="error_span">Wrong secret code</span>');
	}
	elseif(user_name_exists($user_name) == true)
	{
		return('<span class="error_span">Name is already in use. If you have the same name as someone else, use another spelling that identifies you</span>');
	}
	elseif(user_email_exists($user_email) == true)
	{
		return('<span class="error_span">Email is already registered. <a href="#forgot_password">Forgot your password?</a></span>');
	}
	else
	{
		$query = mysql_query("SELECT * FROM " . global_mysql_users_table . "")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

		if(mysql_num_rows($query) == 0)
		{
			$user_is_admin = '1';
		}
		else
		{
			$user_is_admin = '0';
		}

		$user_password = encrypt_password($user_password);

		mysql_query("INSERT INTO " . global_mysql_users_table . " (user_is_admin,user_email,user_password,user_name,user_reservation_reminder) VALUES ($user_is_admin,'$user_email','$user_password','$user_name','0')")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

		$user_password = strip_salt($user_password);

		setcookie(global_cookie_prefix . '_user_email', $user_email, time() + 3600 * 24 * intval(global_remember_login_days));
		setcookie(global_cookie_prefix . '_user_password', $user_password, time() + 3600 * 24 * intval(global_remember_login_days));

		return(1);
	}
}

function list_admin_users()
{
	$query = mysql_query("SELECT * FROM " . global_mysql_users_table . " WHERE user_is_admin='1' ORDER BY user_name")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

	if(mysql_num_rows($query) < 1)
	{
		return('<span class="error_span">There are no admins</span>');
	}
	else
	{
		$return = '<table id="forgot_password_table"><tr><th>Name</th><th>Email</th></tr>';

		$i = 0;

		while($user = mysql_fetch_array($query))
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
	$query = mysql_query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
	$reservation = mysql_fetch_array($query);
	return($reservation['reservation_user_name']);
}

function read_reservation_details($week, $day, $time)
{
	$query = mysql_query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
	$reservation = mysql_fetch_array($query);

	if(empty($reservation))
	{
		return(0);
		
	}
	else
	{
		return('<b>Reservation made:</b> ' . $reservation['reservation_made_time'] . '<br><b>User\'s email:</b> ' . $reservation['reservation_user_email']);
	}
}

function make_reservation($week, $day, $time)
{
	$user_id = $_SESSION['user_id'];
	$user_email = $_SESSION['user_email'];
	$user_name = $_SESSION['user_name'];
	$price = global_price;

	if($week == '0' && $day == '0' && $time == '0')
	{
		mysql_query("INSERT INTO " . global_mysql_reservations_table . " (reservation_made_time,reservation_week,reservation_day,reservation_time,reservation_price,reservation_user_id,reservation_user_email,reservation_user_name) VALUES (now(),'$week','$day','$time','$price','$user_id','$user_email','$user_name')")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

		return(1);
	}
	elseif($week < global_week_number && $_SESSION['user_is_admin'] != '1' || $week == global_week_number && $day < global_day_number && $_SESSION['user_is_admin'] != '1')
	{
		return('You can\'t reserve back in time');
	}
	elseif($week > global_week_number + global_weeks_forward && $_SESSION['user_is_admin'] != '1')
	{
		return('You can only reserve ' . global_weeks_forward . ' weeks forward in time');
	}
	else
	{
		$query = mysql_query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

		if(mysql_num_rows($query) < 1)
		{
			$year = global_year;

			mysql_query("INSERT INTO " . global_mysql_reservations_table . " (reservation_made_time,reservation_year,reservation_week,reservation_day,reservation_time,reservation_price,reservation_user_id,reservation_user_email,reservation_user_name) VALUES (now(),'$year','$week','$day','$time','$price','$user_id','$user_email','$user_name')")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

			return(1);
		}
		else
		{
			return('Someone else just reserved this time');
		}
	}
}

function delete_reservation($week, $day, $time)
{
	if($week < global_week_number && $_SESSION['user_is_admin'] != '1' || $week == global_week_number && $day < global_day_number && $_SESSION['user_is_admin'] != '1')
	{
		return('You can\'t reserve back in time');
	}
	elseif($week > global_week_number + global_weeks_forward && $_SESSION['user_is_admin'] != '1')
	{
		return('You can only reserve ' . global_weeks_forward . ' weeks forward in time');
	}
	else
	{
		$query = mysql_query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
		$user = mysql_fetch_array($query);

		if($user['reservation_user_id'] == $_SESSION['user_id'] || $_SESSION['user_is_admin'] == '1')
		{
			mysql_query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_week='$week' AND reservation_day='$day' AND reservation_time='$time'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

			return(1);
		}
		else
		{
			return('You can\'t remove other users\' reservations');
		}
	}
}

// Admin control panel

function list_users()
{
	$query = mysql_query("SELECT * FROM " . global_mysql_users_table . " ORDER BY user_is_admin DESC, user_name")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

	$users = '<table id="users_table"><tr><th>ID</th><th>Admin</th><th>Name</th><th>Email</th><th>Reminders</th><th>Usage</th><th>Cost</th><th></th></tr>';

	while($user = mysql_fetch_array($query))
	{
		$users .= '<tr id="user_tr_' . $user['user_id'] . '"><td><label for="user_radio_' . $user['user_id'] . '">' . $user['user_id'] . '</label></td><td>' . $user['user_is_admin'] . '</td><td><label for="user_radio_' . $user['user_id'] . '">' . $user['user_name'] . '</label></td><td><label for="user_radio_' . $user['user_id'] . '">' . $user['user_email'] . '</label></td><td>' . $user['user_reservation_reminder'] . '</td><td>' . count_reservations($user['user_id']) . '</td><td>' . cost_reservations($user['user_id']) . ' ' . global_currency . '</td><td><input type="radio" name="user_radio" class="user_radio" id="user_radio_' . $user['user_id'] . '" value="' . $user['user_id'] . '"></td></tr>';
	}

	$users .= '</table>';

	return($users);
}

function reset_user_password($user_id)
{
	$password = random_password();
	$password_encrypted = encrypt_password($password);

	mysql_query("UPDATE " . global_mysql_users_table . " SET user_password='$password_encrypted' WHERE user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

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
		mysql_query("UPDATE " . global_mysql_users_table . " SET user_is_admin = 1 - user_is_admin WHERE user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

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
			mysql_query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
		}
		elseif($data == 'user')
		{
			mysql_query("DELETE FROM " . global_mysql_users_table . " WHERE user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
			mysql_query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
		}

		return(1);
	}
}

function delete_all($data)
{
	$user_id = $_SESSION['user_id'];

	if($data == 'reservations')
	{
		mysql_query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_user_id!='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
	}
	elseif($data == 'users')
	{
		mysql_query("DELETE FROM " . global_mysql_users_table . " WHERE user_id!='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
		mysql_query("DELETE FROM " . global_mysql_reservations_table . " WHERE reservation_user_id!='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
	}
	elseif($data == 'everything')
	{
		mysql_query("DELETE FROM " . global_mysql_users_table . "")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
		mysql_query("DELETE FROM " . global_mysql_reservations_table . "")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
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
		mysql_query("UPDATE " . global_mysql_configuration_table . " SET price='$price'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
	}

	return(1);
}

// User control panel

function get_usage()
{
	$usage = '<table id="usage_table"><tr><th>Reservations</th><th>Cost</th><th>Current price per reservation</th></tr><tr><td>' . count_reservations($_SESSION['user_id']) . '</td><td>' . cost_reservations($_SESSION['user_id']) . ' ' . global_currency . '</td><td>' . global_price . ' ' . global_currency . '</td></tr></table>';
	return($usage);
}

function count_reservations($user_id)
{
	$query = mysql_query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
	$count = mysql_num_rows($query);
	return($count);
}

function cost_reservations($user_id)
{
	$query = mysql_query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

	$cost = 0;

	while($reservation = mysql_fetch_array($query))
	{
		$cost =+ $cost + $reservation['reservation_price'];	
	}

	return($cost);
}

function get_reservation_reminders()
{
	$user_id = $_SESSION['user_id'];
	$query = mysql_query("SELECT * FROM " . global_mysql_users_table . " WHERE user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
	$user = mysql_fetch_array($query);

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
	mysql_query("UPDATE " . global_mysql_users_table . " SET user_reservation_reminder = 1 - user_reservation_reminder WHERE user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

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
			mysql_query("UPDATE " . global_mysql_users_table . " SET user_name='$user_name', user_email='$user_email' WHERE user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
		}
		else
		{
			$user_password = encrypt_password($user_password);

			mysql_query("UPDATE " . global_mysql_users_table . " SET user_name='$user_name', user_email='$user_email', user_password='$user_password' WHERE user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
		}

		mysql_query("UPDATE " . global_mysql_reservations_table . " SET reservation_user_name='$user_name', reservation_user_email='$user_email' WHERE reservation_user_id='$user_id'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

		$_SESSION['user_name'] = $user_name;
		$_SESSION['user_email'] = $user_email;

		$user_password = strip_salt($user_password);

		setcookie(global_cookie_prefix . '_user_email', $user_email, time() + 3600 * 24 * intval(global_remember_login_days));
		setcookie(global_cookie_prefix . '_user_password', $user_password, time() + 3600 * 24 * intval(global_remember_login_days));

		return(1);
	}
}

?>
