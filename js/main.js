// Show pages
    function id2array(id)
    {
        var array = id.split(':');
        if(array.length>3)
        array[3]=array.slice(3).join(':');
        return array;
    }

function showlogin()
{
	page_load();
	div_hide('#content_div');

	$.get('login.php', function(data)
	{
		$('#content_div').html(data); 
		div_fadein('#content_div');
		page_loaded();

		var user_email = $('#user_email_input').val();
		var user_password = $('#user_password_input').val();

		if(user_email != '' && user_password != '')
		{
			setTimeout(function() { $('#login_form').submit(); }, 250);
		}
		else
		{
			input_focus('#user_email_input');
		}
	});
}

function shownew_user()
{
	page_load();
	div_hide('#content_div');
	$.get('login.php?new_user', function(data) { $('#content_div').html(data); div_fadein('#content_div'); page_loaded(); input_focus('#user_name_input'); });
	
}

function showforgot_password()
{
	page_load();
	div_hide('#content_div');
	$.get('login.php?forgot_password', function(data) { $('#content_div').html(data); div_fadein('#content_div'); page_loaded(); });
	
}

function showreservations()
{
	page_load('reservation');
	div_hide('#content_div');

	$.get('reservation.php', function(data)
	{
		$('#content_div').html(data);
		div_fadein('#content_div');

		$.get('reservation.php?week='+global_week_number, function(data)
		{
			$('#reservation_table_div').html(data).slideDown('slow', function() { setTimeout(function() { div_fadein('#reservation_table_div'); }, 250); });
			page_loaded();
		});
	});
}

function showweek(week, option)
{
	if(week == 'next')
	{
		var week = parseInt($('#week_number_span').html()) + 1;
	}
	else if(week == 'previous')
	{
		var week = parseInt($('#week_number_span').html()) - 1;
	}
	else
	{
		var week = parseInt(week);
	}

	if(isNaN(week))
	{
		notify('Invalid week number', 4);
	}
	else
	{
		if(week < 1)
		{
			var week = 52;
		}
		else if(week > 52)
		{
			var week = 1;
		}
        if(week>global_week_number+ global_weeks_forward)
        {
          notify('Il n\'y a pas de réservation plus loin', 4);
          return;  
        }
        if(week<global_week_number-global_weeks_backward)
        {
          notify('Il n\'y a pas de réservation plus avant', 4);
          return;  
        } 
        
		page_load('week');
		div_hide('#reservation_table_div');

		$.get('reservation.php?week='+week, function(data)
		{
			$('#reservation_table_div').html(data);
			
         //   $('#week_number_span').html(week);
            
            $.get('reservation.php?weeknumber='+week, function(data)
            {
                     $('#week_number_span').html(data);
            });
            
			div_fadein('#reservation_table_div');
			page_loaded('week');

			if(week != global_week_number)
			{
				$('#reservation_today_button').css('visibility', 'visible');
			}

			if(option == 'today')
			{
				setTimeout(function() { $('#today_span').animate({ opacity: 0 }, 250, function() { $('#today_span').animate({ opacity: 1 }, 250);  }); }, 500);
			}
		});
	}
}

function showcp()
{
	page_load();
	div_hide('#content_div');
	$.get('cp.php', function(data) { $('#content_div').html(data); div_fadein('#content_div'); page_loaded(); });
}

function showhelp()
{
	page_load();
	div_hide('#content_div');
	$.get('help.php', function(data) { $('#content_div').html(data); div_fadein('#content_div'); page_loaded(); });
}

// Page load

function page_load(page)
{
	// All
	setTimeout(function()
	{
		if($('#content_div').css('opacity') == 0)
		{
			notify('Chargement...', 300);
		}
	}, 500);

	// Individual
	if(page == 'reservation')
	{
		setTimeout(function()
		{
			if($('#reservation_table_div').is(':hidden'))
			{
				notify('Chargement...', 300);
			}
		}, 500);
	}	
	else if(page == 'week')
	{
		setTimeout(function()
		{
			if($('#reservation_table_div').css('opacity') == 0)
			{
				notify('Chargement...', 300);
			}
		}, 500);
	}
}

function page_loaded(page)
{
	// All
	$.get('main.php?day_number', function(data)
	{
        if(typeof global_day_number != "undefined")
		if(data != global_day_number)
		{
			notify('Le jour a changé. Refresh...', '300');
			setTimeout(function() { window.location.replace('.'); }, 2000);
		}
	});

	setTimeout(function()
	{
		if($('#notification_inner_cell_div').is(':visible') && $('#notification_inner_cell_div').html() == 'Chargement...')
		{
			notify();
		}
	}, 1000);

	read_reservation_details();

	// Individual

}

// Login

function login()
{
	var user_email = $('#user_email_input').val();
	var user_password = $('#user_password_input').val();

	$('#login_message_p').html('<img src="img/loading.gif" alt="Loading"> Connexion...').slideDown('fast');

	var remember_me_checkbox = $('#remember_me_checkbox').prop('checked');

	if(remember_me_checkbox)
	{
		var user_remember = 1;
	}
	else
	{
		var user_remember = 0;
	}

	$.post('login.php?login', { user_email: user_email, user_password: user_password, user_remember: user_remember }, function(data)
	{
		if(data == 1)
		{
			input_focus();
			setTimeout(function() { window.location.replace('.'); }, 1000);
		}
		else
		{
			if(data == '')
			{
				$('#login_message_p').html('<span class="error_span">Mauvais email et/ou mot de passe</span>');
				$('#user_email_input').val('');
				$('#user_password_input').val('');
				input_focus('#user_email_input');
			}
			else
			{
				$('#login_message_p').html(data);
			}
		}
	});
}

function logout()
{
	notify('Deconnexion...', 300);
	$.get('login.php?logout', function(data) { setTimeout(function() { window.location.replace('.'); }, 1000); });
}

function create_user()
{
	var user_name = $('#user_name_input').val();
	var user_email = $('#user_email_input').val();
	var user_password = $('#user_password_input').val();
	var user_password_confirm = $('#user_password_confirm_input').val();

	if($('#user_secret_code_input').length)
	{
		var user_secret_code =  $('#user_secret_code_input').val();
	}
	else
	{
		var user_secret_code = '';
	}

	if(user_password != user_password_confirm)
	{
		$('#new_user_message_p').html('<span class="error_span">Les mots de passe ne correspondent pas</span>').slideDown('fast');
		$('#user_password_input').val('');
		$('#user_password_confirm_input').val('');
		input_focus('#user_password_input');
	}
	else
	{
		$('#new_user_message_p').html('<img src="img/loading.gif" alt="Loading"> Creation de l\'utilisateur...').slideDown('fast');

		$.post('login.php?create_user', { user_name: user_name, user_email: user_email, user_password: user_password, user_secret_code: user_secret_code }, function(data)
		{
			if(data == 1)
			{
				input_focus();

				setTimeout(function()
				{
					$('#new_user_message_p').html('Utilisateur créé! Connexion... <img src="img/loading.gif" alt="Loading">');
					setTimeout(function() { window.location.replace('#login'); }, 2000);
				}, 1000);
			}
			else
			{
				input_focus();
				$('#new_user_message_p').html(data);
			}
		});
	}
}

// Reservation

function toggle_reservation_time(id, week, day, time, from)
{
	if(session_user_is_admin == '1')
	{
        
        if(typeof global_day_number == "undefined")
        return;
        
		if(week < global_week_number || week == global_week_number && day < global_day_number)
		{
			notify('You are reserving back in time. You can do that because you\'re an admin', 4);
		}
		else if(week > global_week_number + global_weeks_forward)
		{
			notify('You are reserving more than '+global_weeks_forward+' weeks forward in time. You can do that because you\'re an admin', 4);
		}
	}

	var user_name = $(id).html();

	if(user_name == '')
	{
		$(id).html('Patientez...'); 

		$.post('reservation.php?make_reservation', { week: week, day: day, time: time }, function(data) 
		{
			if(data == 1)
			{
				setTimeout(function() { read_reservation(id, week, day, time); }, 1000);
			}
			else
			{
				notify(data, 4);
				setTimeout(function() { read_reservation(id, week, day, time); }, 2000);			
			}
		});
	}
	else
	{
		if(offclick_event == 'mouseup' || from == 'details')
		{
			if(user_name == 'Patientez...')
			{
				notify('Un click suffit !', 4);
			}
			else if(user_name == session_user_name || session_user_is_admin == '1')
			{
				if(user_name != session_user_name && session_user_is_admin == '1')
				{
					var delete_confirm = confirm('This is not your reservation, but because you\'re an admin you can remove other users\' reservations. Are you sure you want to do this?');
				}
				else
				{
					var delete_confirm = true;
				}

				if(delete_confirm)
				{
					$(id).html('Patientez...');

					$.post('reservation.php?delete_reservation', { week: week, day: day, time: time }, function(data)
					{
						if(data == 1)
						{
							setTimeout(function() { read_reservation(id, week, day, time); }, 1000);
						}
						else
						{
							notify(data, 4);
							setTimeout(function() { read_reservation(id, week, day, time); }, 2000);
						}
					});
				}
			}
			else
			{
				notify('Vous ne pouvez pas supprimer les reservations d\autres personnes', 2);
			}

			if($('#reservation_details_div').is(':visible'))
			{
				read_reservation_details();
			}
		}
	}
}

function read_reservation(id, week, day, time)
{
	$.post('reservation.php?read_reservation', { week: week, day: day, time: time }, function(data) { $(id).html(data); });
}

function read_reservation_details(id, week, day, time)
{
	if(typeof id != 'undefined' && $(id).html() != '' && $(id).html() != 'Patientez...')
	{
		if($('#reservation_details_div').is(':hidden'))
		{
			var position = $(id).position();
			var top = position.top + 50;
			var left = position.left - 100;

			$('#reservation_details_div').html('Récupération des details...');
			$('#reservation_details_div').css('top', top+'px').css('left', left+'px');
			$('#reservation_details_div').fadeIn('fast');

			reservation_details_id = id;
			reservation_details_week = week;
			reservation_details_day = day;
			reservation_details_time = time;

			$.post('reservation.php?read_reservation_details', { week: week, day: day, time: time }, function(data)
			{
				setTimeout(function()
				{
					if(data == 0)
					{
						$('#reservation_details_div').html('Cette reservation n\'existe plus. Patientez...');
						
						setTimeout(function()
						{
							if($('#reservation_details_div').is(':visible') && $('#reservation_details_div').html() == 'Cette reservation n\'existe plus. Patientez....')
							{
								read_reservation(reservation_details_id, reservation_details_week, reservation_details_day, reservation_details_time);
								read_reservation_details();
							}
						}, 2000);
					}
					else
					{
						$('#reservation_details_div').html(data);

						if(offclick_event == 'touchend')
						{
							if($(reservation_details_id).html() == session_user_name || session_user_is_admin == '1')
							{
								var delete_link_html = '<a href="." onclick="toggle_reservation_time(reservation_details_id, reservation_details_week, reservation_details_day, reservation_details_time, \'details\'); return false">Supprimer</a> | ';
							}
							else
							{
								var delete_link_html = '';
							}

							$('#reservation_details_div').append('<br><br>'+delete_link_html+'<a href="." onclick="read_reservation_details(); return false">Fermer</a>');
						}
					}
				}, 500);
			});
		}
	}
	else
	{
		$('div#reservation_details_div').fadeOut('fast');
	}
}

// Admin control panel

function list_users()
{
	$.get('cp.php?list_users', function(data) { $('#users_div').html(data); });
}
   
function reset_user_password()
{
	if(typeof $(".user_radio:checked").val() !='undefined')
	{
		var user_id = $(".user_radio:checked").val();

		$('#user_administration_message_p').html('<img src="img/loading.gif" alt="Loading"> Resetting password...').slideDown('fast');

		$.post('cp.php?reset_user_password', { user_id: user_id }, function(data)
		{
			if(data == 0)
			{
				$('#user_administration_message_p').html('<span class="error_span">You can change your password at the bottom of this page</span>').slideDown('fast');
			}
			else
			{
				setTimeout(function() { $('#user_administration_message_p').html(data); }, 1000);
			}
		});
	}
	else
	{
		$('#user_administration_message_p').html('<span class="error_span">You must pick a user</span>').slideDown('fast');
	}
}

function change_user_permissions()
{
	if(typeof $(".user_radio:checked").val() !='undefined')
	{
		var user_id = $(".user_radio:checked").val();

		$('#user_administration_message_p').html('<img src="img/loading.gif" alt="Loading"> Changing permissions...').slideDown('fast');

		$.post('cp.php?change_user_permissions', { user_id: user_id }, function(data)
		{
			if(data == 1)
			{
				setTimeout(function()
				{
					list_users();
					$('#user_administration_message_p').html('Permissions changed successfully. The user must re-login to get the new permissions');
				}, 1000);
			}
			else
			{
				$('#user_administration_message_p').html(data);
			}
		});
	}
	else
	{
		$('#user_administration_message_p').html('<span class="error_span">You must pick a user</span>').slideDown('fast');
	}
}

function delete_user_data(delete_data)
{
	if(typeof $(".user_radio:checked").val() !='undefined')
	{
		var delete_confirm = confirm('Are you sure?');

		if(delete_confirm)
		{
			var user_id = $(".user_radio:checked").val();

			$('#user_administration_message_p').html('<img src="img/loading.gif" alt="Loading"> Deleting...').slideDown('fast');

			$.post('cp.php?delete_user_data', { user_id: user_id, delete_data: delete_data }, function(data)
			{
				if(data == 1)
				{
					setTimeout(function()
					{
						$('#user_administration_message_p').slideUp('fast', function()
						{
							if(delete_data == 'reservations')
							{
								list_users();
								get_usage();
							}
							else if(delete_data == 'user')
							{
								list_users();
							}
						});
					}, 1000);
				}
				else
				{
					$('#user_administration_message_p').html(data);
				}
			});
		}
	}
	else
	{
		$('#user_administration_message_p').html('<span class="error_span">You must pick a user</span>').slideDown('fast');
	}
}

function delete_all(delete_data)
{
	if(delete_data == 'reservations')
	{
		var delete_confirm = confirm('Are you sure you want to delete ALL reservations? Database backup is a good idea!');
	}
	else if(delete_data == 'users')
	{
		var delete_confirm = confirm('Are you sure you want to delete ALL users? Database backup is a good idea!');
	}
	else if(delete_data == 'everything')
	{
		var delete_confirm = confirm('Are you sure you want to delete EVERYTHING (including you)? The first user created afterwards will become admin. Database backup is a good idea!');
	}

	if(delete_confirm)
	{
		$('#database_administration_message_p').html('<img src="img/loading.gif" alt="Loading"> Deleting...').slideDown('fast');

		$.post('cp.php?delete_all', { delete_data: delete_data }, function(data)
		{
			if(data == 1)
			{
				setTimeout(function()
				{
					if(delete_data == 'everything')
					{
						window.location.replace('#logout');
					}
					else
					{
						list_users();
						$('#database_administration_message_p').slideUp('fast');
					}
				}, 1000);
			}
			else
			{
				$('#database_administration_message_p').html(data);
			}
		});
	}
}
               
// User control panel

function get_usage()
{
	$.get('cp.php?get_usage', function(data) { $('#usage_div').html(data); });
}

function get_reservation_reminders()
{
	$.get('cp.php?get_reservation_reminders', function(data) { $('#reservation_reminders_span').html(data); });
}



function toggle_reservation_reminder()
{
	$('#settings_message_p').html('<img src="img/loading.gif" alt="Loading"> Sauvegarde...').slideDown('fast');

	$.post('cp.php?toggle_reservation_reminder', function(data)
	{
		if(data == 1)
		{
			setTimeout(function()
			{
				if($('#users_div').length)
				{
					list_users();		
				}

				get_reservation_reminders();
				$('#settings_message_p').slideUp('fast');
			}, 1000);
		}
		else
		{
			$('#settings_message_p').html(data);
		}
	});
}

function change_user_details()
{
	var user_name = $('#user_name_input').val();
	var user_email = $('#user_email_input').val();
	var user_password = $('#user_password_input').val();
	var user_password_confirm = $('#user_password_confirm_input').val();

	if(user_password != user_password_confirm)
	{
		$('#user_details_message_p').html('<span class="error_span">Passwords do not match</span>').slideDown('fast');
		$('#user_password_input').val('');
		$('#user_password_confirm_input').val('');
		input_focus('#user_password_input');
	}
	else
	{	
		$('#user_details_message_p').html('<img src="img/loading.gif" alt="Loading"> Saving and refreshing...').slideDown('fast');

		$.post('cp.php?change_user_details', { user_name: user_name, user_email: user_email, user_password: user_password }, function(data)
		{
			if(data == 1)
			{
				input_focus();
				setTimeout(function() { window.location.replace('.'); }, 1000);
			}
			else
			{
				input_focus();
				$('#user_details_message_p').html(data);
			}
		});
	}
}

// UI

function div_fadein(id)
{
	setTimeout(function()
	{
        if(typeof global_css_animations == "undefined")
//		if(global_css_animations == 1)
		{
			$(id).addClass('div_fadein');
		}
		else
		{
			$(id).animate({ opacity: 1 }, 250);
		}
	}, 1);
}

function div_hide(id)
{
	$(id).removeClass('div_fadein');
	$(id).css('opacity', '0');
}

function notify(text, time)
{
	if(typeof text != 'undefined')
	{
		if(typeof notify_timeout != 'undefined')
		{
			clearTimeout(notify_timeout);
		}

		$('#notification_inner_cell_div').css('opacity', '1');

		if($('#notification_div').is(':hidden'))
		{
			$('#notification_inner_cell_div').html(text);
			$('#notification_div').slideDown('fast');
		}
		else
		{
			$('#notification_inner_cell_div').animate({ opacity: 0 }, 250, function() { $('#notification_inner_cell_div').html(text); $('#notification_inner_cell_div').animate({ opacity: 1 }, 250); });
		}

		notify_timeout = setTimeout(function() { $('#notification_inner_cell_div').animate({ opacity: 0 }, 250, function() { $('#notification_div').slideUp('fast'); }); }, 1000 * time);
	}
	else
	{
		if($('#notification_div').is(':visible'))
		{
			$('#notification_inner_cell_div').animate({ opacity: 0 }, 250, function() { $('#notification_div').slideUp('fast'); });
		}
	}
}

function input_focus(id)
{
	if(offclick_event == 'touchend')
	{
		$('input').blur();
	}
	if(typeof id != 'undefined')
	{
		$(id).focus();
	}
}

// Document ready

$(document).ready( function()
{
	// Detect touch support
	if('ontouchstart' in document.documentElement)
	{
		onclick_event = 'touchstart';
		offclick_event = 'touchend';
	}
	else
	{
		onclick_event = 'mousedown';
		offclick_event = 'mouseup';
	}

	// Visual feedback on click
	$(document).on(onclick_event, 'input:submit, input:button, .reservation_time_div', function() { $(this).css('opacity', '0.5'); });
	$(document).on(offclick_event+ ' mouseout', 'input:submit, input:button, .reservation_time_div', function() { $(this).css('opacity', '1.0'); });

	// Buttons
	$(document).on('click', '#reservation_today_button', function() { showweek(global_week_number, 'today'); });
	$(document).on('click', '#reset_user_password_button', function() { reset_user_password(); });
	$(document).on('click', '#change_user_permissions_button', function() { change_user_permissions(); });
	$(document).on('click', '#delete_user_reservations_button', function() { delete_user_data('reservations'); });
	$(document).on('click', '#delete_user_button', function() { delete_user_data('user'); });
	$(document).on('click', '#delete_all_reservations_button', function() { delete_all('reservations'); });
	$(document).on('click', '#delete_all_users_button', function() { delete_all('users'); });
	$(document).on('click', '#delete_everything_button', function() { delete_all('everything'); });

    
	// Checkboxes
	$(document).on('click', '#reservation_reminders_checkbox', function() { toggle_reservation_reminder(); });

	// Forms
	$(document).on('submit', '#login_form', function() { login(); return false; });
	$(document).on('submit', '#new_user_form', function() { create_user(); return false; });
	$(document).on('submit', '#system_configuration_form', function() { save_system_configuration(); return false; });
	$(document).on('submit', '#user_details_form', function() { change_user_details(); return false; });

	// Links
	$(document).on('click mouseover', '#user_secret_code_a', function() { div_fadein('#user_secret_code_div'); return false; });
	$(document).on('click', '#previous_week_a', function() { showweek('previous'); return false; });
	$(document).on('click', '#next_week_a', function() { showweek('next'); return false; });

	// Divisions
	$(document).on('mouseout', '.reservation_time_cell_div', function() { read_reservation_details(); });

    
	$(document).on('click', '.reservation_time_cell_div', function()
	{
        
        var array = id2array(this.id);

//		var array = this.id.split(':');
//        if(array.length>3)
//        array[3]=array.slice(3).join(':');
//        function toggle_reservation_time(id, week, day, time, from)
		toggle_reservation_time(this, array[1], array[2], array[3], array[0]);
	});

	$(document).on('mousemove', '.reservation_time_cell_div', function()
	{
//		var array = this.id.split(':');
        var array = id2array(this.id);
        
		read_reservation_details(this, array[1], array[2], array[3]);
	});

	// Mouse pointer
	$(document).on('mouseover', 'input:button, input:submit, .reservation_time_div', function() { this.style.cursor = 'pointer'; });
});

// Hash change

function hash()
{
	var hash = window.location.hash.slice(1);
   // $('#debug').text('hash='+hash);
    
    
	if(hash == '')
	{
		if(typeof session_logged_in != 'undefined')
		{
			showreservations();
		}
		else
		{
			showlogin();
		}
	}
	else
	{
	 if(hash == 'new_user')
		{
			shownew_user();
		}
		else if(hash == 'forgot_password')
		{
			showforgot_password();
		}
		else if(hash == 'help')
		{
			showhelp();
		}
		else if(hash == 'cp')
		{
			showcp();
		}
		else if(hash == 'logout')
		{
			logout();
		}
		else
		{
			window.location.replace('.');
		}
	}
}

// Window load

$(window).load(function()
{
	// Make sure cookies are enabled
	$.cookie(global_cookie_prefix+'_cookies_test', '1');
	var test_cookies_cookie = $.cookie(global_cookie_prefix+'_cookies_test');

	if(test_cookies_cookie == null)
	{
		window.location.replace('error.php?error_code=3');
	}
	else
	{
		$.cookie(global_cookie_prefix+'_cookies_test', null);

		hash();

		$(window).bind('hashchange', function ()
		{
			hash();
		});
	}
});

// Settings

$(document).ready( function()
{
	$.ajaxSetup({ cache: false });
});
