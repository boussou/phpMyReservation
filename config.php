<?php

### IF YOU ARE GOING TO USE THE CHARACTER ' IN ANY OF THE OPTIONS, ESCAPE IT LIKE THIS: \' ###

// MySQL details
//define('global_mysql_server', 'localhost');
//define('global_mysql_user', 'root');
//define('global_mysql_password', '');
//define('global_mysql_database', 'phpmyreservation');

// Salt for password encryption. Changing it is recommended. Use 9 random characters
// This MUST be 9 characters, and must NOT be changed after users have been created
define('global_salt', 'k4i8pa2m5');

// Days to remember login (if the user chooses to remember it)
define('global_remember_login_days', '180');

// Title. Used in page title and header
define('global_title', 'Entretiens X3');

// Organization. Used in page title and header, and as sender name in reservation reminder emails
define('global_organization', 'ISEN de Toulon');

// Secret code. Can be used to only allow certain people to create a user
// Set to '0' to disable
define('global_secret_code', 0);

// Email address to webmaster. Shown to users that want to know the secret code
// To avoid spamming, JavaScript & Base64 is used to show email addresses when not logged in
define('global_webmaster_email', 'your@email.address');

// Set to '1' to enable reservation reminders. Adds an option in the control panel
// Check out the wiki for instructions on how to make it work
define('global_reservation_reminders', '1');

// Reservation reminders are sent from this email
// Should be an email address that you own, and that is handled by your web host provider
define('global_reservation_reminders_email', 'nadir.boussoukaia@isen.fr');

// Code to run the reservation reminders script over HTTP
// If reservation reminders are enabled, this MUST be changed. Check out the wiki for more information
define('global_reservation_reminders_code', '1234');

// Full URL to web site. Used in reservation reminder emails
define('global_url', 'http://your.server/phpmyreservation/');

// Currency (short format). Price per reservation can be changed in the control panel
// Currency should not be changed after reservations have been made (of obvious reasons)
define('global_currency', '€');

// How many weeks forward in time to allow reservations
define('global_weeks_forward', '1');
define('global_weeks_backward', 0);


define('max_date','2014/07/01');
define('min_date','2014/06/25');
define('max_reservation_per_user',1);
define('max_reservation_per_cell',2);



// Possible reservation times. Use the same syntax as below (TimeFrom-TimeTo)
$global_times = array('09-10', '10-11', '11-12', '12-13', '13-14', '14-15', '15-16', '16-17', '17-18', '18-19', '19-20', '20-21');
$global_times = array('09-12', '12-14', '14-18', '19-23');

$from=9;
$to=17;
$granularite=2; // x par heure
$shift=1;  // a value between 0 and $granularite


$global_times = array();
for($i=$from;$i<$to;$i++)
{
if ($i>=12 &&$i<14) continue;
for($j=0;$j<$granularite;$j++)
{
    $intervalles=60/$granularite*$j;
    $low=str_pad($i,2,'0',STR_PAD_LEFT) ;
    $intervalles=str_pad($intervalles,2,'0',STR_PAD_LEFT) ;
    $global_times[]="$low:$intervalles";
    
}
}
//--------------------------------------------------
$global_times = array();
$grey_hours=array(12,12.5,13,13.5);
$grey_hours=array(12*$granularite);
$grey_keyword="Pause";
for($i=$from*$granularite+$shift;$i<$to*$granularite;$i++)
{
 if(  in_array($i,$grey_hours) )
  {  $global_times[]=$grey_keyword; continue; }
   
if ($i>=12*$granularite && $i<14*$granularite) continue;
  
//    $global_times[]="$intervalles";

    $intervalles=(60/$granularite)*$i;
    $intervalles=$i/$granularite;
    $low=str_pad((int)($i/$granularite),2,'0',STR_PAD_LEFT) ;
    $intervalles=str_pad(($i%$granularite)*60/$granularite,2,'0',STR_PAD_LEFT) ;
    $global_times[]="$low:$intervalles";

//    $global_times[]="$intervalles";
    

}

