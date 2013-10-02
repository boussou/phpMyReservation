<?php

include_once('main.php');

if(global_reservation_reminders == '1')
{
	if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']) || isset($_GET['code']) && $_GET['code'] == global_reservation_reminders_code)
	{
		$query = mysql_query("SELECT * FROM " . global_mysql_users_table . " WHERE user_reservation_reminder='1'")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

		while($user = mysql_fetch_array($query))
		{
			$week_number = global_week_number;
			$day_number = global_day_number;
			$user_id = $user['user_id'];

			$query2 = mysql_query("SELECT * FROM " . global_mysql_reservations_table . " WHERE reservation_user_id='$user_id' AND reservation_week='$week_number' AND reservation_day='$day_number' ORDER BY reservation_time")or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');

			if(mysql_num_rows($query2) > 0)
			{
				$time = '';

				while($user2 = mysql_fetch_array($query2))
				{	
					$time .= $user2['reservation_time'] . ', ';
				}

				$time = rtrim($time, ', ');
				$subject = 'Reservation reminder';
				$message = "This is a reservation reminder for today.\r\n\nYou have made a reservation at the following time(s): " . $time . "\r\n\nIf you don't want to receive reservation reminders, you can turn it off in the control panel.\r\n\n" . global_url;
				$headers = "From: " . global_organization . " <" . global_reservation_reminders_email . ">\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/plain; charset=utf-8\r\n";

				mail($user['user_email'], '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);
			}
		}
	}
}

?>
