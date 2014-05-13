<?php

// About

define('global_project_name', 'phpMyReservation');
define('global_project_version', '1.0');
define('global_project_website', 'http://www.olejon.net/code/phpmyreservation/');

// Include necessary files
include_once('db.php');  

include_once('config.php');
include_once('functions.php');

// MySQL

//mysql_connect(global_mysql_server, global_mysql_user, global_mysql_password)or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
//mysql_select_db(global_mysql_database)or die('<span class="error_span"><u>MySQL error:</u> ' . htmlspecialchars(mysql_error()) . '</span>');
//mysql_set_charset('utf8');


define('global_mysql_configuration_table', 'phpmyreservation_configuration');
define('global_mysql_users_table', 'phpmyreservation_users');
define('global_mysql_reservations_table', 'phpmyreservation_reservations');

// Cookies

define('global_cookie_prefix', 'phpmyreservation');

// Start session

session_start();

// Configuration

define('global_price', get_configuration('price'));

// Date
    /**
    * Set the default time zone.
    *
    * @see  http://inducidoframework.org/guide/using.configuration
    * @see  http://php.net/timezones
    */
    date_default_timezone_set('Europe/Paris');

    
    //todo ici il faudrait fr_FR
    setlocale(LC_ALL, 'fr_FR.utf-8');
    //For debian/ubuntu, don't forget the charset UFT8.
    setlocale(LC_ALL, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
    
  //  http://www.finalclap.com/faq/81-php-afficher-date-heure-francais
         setlocale(LC_TIME, 'fra_fra');
    

define('global_year', date('Y'));
//define('global_week_number', ltrim(date('W'), '0'));   //week number of year

define('global_week_number', 26 );   //week number of year

define('global_day_number', date('N'));   //1 (for Monday) through 7 (for Sunday)
define('global_day_name', date('l'));  //Sunday through Saturday
  
  class DateTimeFrench extends DateTime 
{
    public function format($format) 
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'Décember');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, parent::format($format)));
    }
}

    
// User agent

if(isset($_SERVER['HTTP_USER_AGENT']))
{
	define('global_ua', $_SERVER['HTTP_USER_AGENT']);
}
else
{
	define('global_ua', 'CLI');
}

if(strstr(global_ua, 'iPhone') || strstr(global_ua, 'iPod') || strstr(global_ua, 'iPad') || strstr(global_ua, 'Android'))
{
	if(strstr(global_ua, 'AppleWebKit'))
	{
		if(strstr(global_ua, 'OS 5_') || strstr(global_ua, 'Android 2.3') || strstr(global_ua, 'Android 3') || strstr(global_ua, 'Android 4'))
		{
			define('global_css_animations', '1');
		}
	}
}
elseif(strstr(global_ua, 'Chrome') || strstr(global_ua, 'Safari') && strstr(global_ua, 'Macintosh') || strstr(global_ua, 'Safari') && strstr(global_ua, 'Windows') || strstr(global_ua, 'Firefox') || strstr(global_ua, 'Opera') || strstr(global_ua, 'MSIE 10'))
{
	define('global_css_animations', '1');
}
else
{
	define('global_css_animations', '0');
}

// Check stuff

if(strlen(global_salt) != 9)
{
	echo '<script type="text/javascript">window.location.replace(\'error.php?error_code=1\');</script>';
	exit;
}

if(isset($_GET['day_number']))
{
	echo date('N');
}
elseif(isset($_GET['latest_version']))
{
	$latest_version_url = global_project_website . 'latest-version.php?version=' . urlencode(global_project_version);
	$latest_version_url_context = stream_context_create(array('http'=>array('timeout'=>5)));
	@$latest_version = file_get_contents($latest_version_url, false, $latest_version_url_context);
	$latest_version = trim($latest_version);

	if(empty($latest_version) || !is_numeric($latest_version))
	{
		echo '<span class="error_span">Could not get latest version</span>';
	}
	else
	{
		echo 'Latest version: ' . $latest_version;
	}
}



     function filter_string($field)
    {
        //si la variable est pas définie on renvoie true
        if(!isset($field))return $field;

        // Using the filter_var function introduced in PHP 5.2.0        
        if(($field = filter_var($field,FILTER_SANITIZE_STRING))===false)
            return null;

        return filter_var($field,FILTER_SANITIZE_MAGIC_QUOTES);
    }
     
     