<script type="text/javascript">

<?php

// About

echo 'global_project_name = \'' . global_project_name . '\';';
echo 'global_project_version = ' . global_project_version . ';';
echo 'global_project_website = \'' . global_project_website . '\';';

// Cookies

echo 'global_cookie_prefix = \'' . global_cookie_prefix . '\';';

// User agent

echo 'global_css_animations = ' . global_css_animations . ';';

// Configuration

echo 'global_weeks_forward = ' . global_weeks_forward . ';';

// Date

echo 'global_year = ' . global_year . ';';
echo 'global_week_number = ' . global_week_number . ';';
echo 'global_day_number = ' . global_day_number . ';';

// Login

if(isset($_SESSION['logged_in']))
{
	echo 'session_logged_in = 1;';
	echo 'session_user_id = \'' . $_SESSION['user_id'] . '\';';
	echo 'session_user_name = \'' . $_SESSION['user_name'] . '\';';
	echo 'session_user_is_admin = \'' . $_SESSION['user_is_admin'] . '\';';
}

?>

</script>
