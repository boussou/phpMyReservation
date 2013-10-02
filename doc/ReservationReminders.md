phpMyReservation Email Reminders
================
[Documentation from Google Code site](http://code.google.com/p/phpmyreservation/wiki/ReservationReminders)

If you choose to enable reservation reminders in config.php, read the following carefully to make it work.

Reservation reminders will be sent by email to those who have reservations the current day and have enabled reservation reminders in the control panel. The script that sends out reservation reminders is reminders.php, and the idea is to run the script daily (early in the morning).

There are different ways to to this, depending on operating system etc.

If you have access to the command line on the web server and the web server is running Linux (which it most likely does), you can do this:

1) Log in to the command line on the web server

2) Run these commands:

  export EDITOR=nano
  crontab -e

3) Add this line

 0 6 * * * php /full/path/to/phpmyreservation/reminders.php

This will run the script daily, at 06:00. Make sure that you use the correct path.

4) Save with Ctrl+O and then return, exit with Ctrl+X

If you don't have access to the command line:

If you don't have access to the command line, your web host provider may have a control panel or similar where you can add scheduled events.

If you have another computer/server that is running 24/7, you can set it up to run the reminders.php script daily over HTTP (for example using cron & cURL).

To run the script over HTTP you must supply the code that you set in config.php (to avoid that everyone is able to run the script). You must run it like this:

 http://your.server/phpmyreservation/reminders.php?code=CODE

If it doesn't work

There are several reasons why this may not work. Your web server may be unable to send emails (depending on your web host provider). If you're running phpMyReservation on domain.com, you should edit config.php to use whatever@domain.com (an email address that you own) as sender for reservation reminders, or else it may not work (rejected or marked as spam). 