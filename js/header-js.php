<script type="text/javascript">

<?php

// About

echo 'var global_project_name = \'' . global_project_name . '\';'."\n";
echo 'var global_project_version = ' . global_project_version . ';'."\n";
echo 'var global_project_website = \'' . global_project_website . '\';'."\n";

// Cookies

echo 'var global_cookie_prefix = \'' . global_cookie_prefix . '\';'."\n";

// User agent

echo 'var global_css_animations = ' . global_css_animations . ';'."\n";

// Configuration

echo 'var global_weeks_forward = ' . global_weeks_forward . ';'."\n";
echo 'var global_weeks_backward = ' . global_weeks_backward . ';'."\n";

// Date

echo 'var global_year = ' . global_year . ';'."\n";
echo 'var global_week_number = ' . global_week_number . ';'."\n";
echo 'var global_day_number = ' . global_day_number . ';'."\n";

echo 'var cell_default_text = "' . cell_default_text . '";'."\n";


// Login

if(isset($_SESSION['logged_in']))
{
	echo 'var session_logged_in = 1;'."\n";
	echo 'var session_user_id = \'' . $_SESSION['user_id'] . '\';'."\n";
	echo 'var session_user_name = \'' . $_SESSION['user_name'] . '\';'."\n";
	echo 'var session_user_is_admin = \'' . $_SESSION['user_is_admin'] . '\';'."\n";
}

?>

</script>
