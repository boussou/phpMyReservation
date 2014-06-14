
<div id="header_logout_div">
<?php

if(isset($_SESSION['logged_in']))
{
        if($_SESSION['user_is_admin'] == '1')
    {
        echo '<a href="#cp">Control panel</a> | ';
    }
        
//	echo '<a href="#logout">Se déconnecter et recevoir un mail de confirmation</a>';
}
else
{
//	echo 'Non connecté';
}

?>
</div>
