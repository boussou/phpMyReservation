<?php

include_once('main.php');

if(check_login() != true) { exit; }

if(isset($_GET['make_reservation']))
{
    $week = filter_string($_POST['week']);
    $day = filter_string($_POST['day']);
    $time = filter_string($_POST['time']);
    echo make_reservation($week, $day, $time);
}
elseif(isset($_GET['toggle_reservation']))
{
    $week = filter_string($_POST['week']);
    $day = filter_string($_POST['day']);
    $time = filter_string($_POST['time']);
    $user_id = filter_string($_POST['user_id']);
    echo toggle_reservation($week, $day, $time,$user_id);
}
elseif(isset($_GET['delete_reservation']))
{
    $week = filter_string($_POST['week']);
    $day = filter_string($_POST['day']);
    $time = filter_string($_POST['time']);
    echo delete_reservation($week, $day, $time);
}
elseif(isset($_GET['read_reservation']))
{
    $week = filter_string($_POST['week']);
    $day = filter_string($_POST['day']);
    $time = filter_string($_POST['time']);
    echo read_reservation($week, $day, $time);
}
elseif(isset($_GET['read_reservation_css']))
{
    $week = filter_string($_POST['week']);
    $day = filter_string($_POST['day']);
    $time = filter_string($_POST['time']);
    echo read_reservation_css($week, $day, $time);
}
elseif(isset($_GET['read_reservation_details']))
{
    $week = filter_string($_POST['week']);
    $day = filter_string($_POST['day']);
    $time = filter_string($_POST['time']);
    echo read_reservation_details($week, $day, $time);
}
elseif(isset($_GET['week']))
{
    //displays the reservation grid

    $week = $_GET['week'];

    $startofweek=date('W');


    //  $current=strftime('%A %e',$startofweek);                                                 
    //  echo $current;
    $week_start = new DateTimeFrench();
    $week_start->setISODate(global_year,$week);
    //    echo $week_start->format('d-M-Y');
    //  echo $week_start->format('l j F Y');
    //    $asint=$week_start->getTimestamp();


    //  $current=strftime('%A %e',$asint);  
    //  echo $current;

    echo '<table id="reservation_table"><colgroup span="1" id="reservation_time_colgroup"></colgroup><colgroup span="7" id="reservation_day_colgroup"></colgroup>';

    $days_row = '<tr><td id="reservation_corner_td"><input type="button" class="blue_button small_button" id="reservation_today_button" value="Today"></td><th class="reservation_day_th">Monday</th><th class="reservation_day_th">Tuesday</th><th class="reservation_day_th">Wednesday</th><th class="reservation_day_th">Thursday</th><th class="reservation_day_th">Friday</th><th class="reservation_day_th">Saturday</th><th class="reservation_day_th">Sunday</th></tr>';
    $today_button='<input type="button" class="blue_button small_button" id="reservation_today_button" value="Today">';
    $today_button='';
    $days_row = '<tr><td id="reservation_corner_td">'.$today_button.'</td> ';

    $nb_days_to_display=6;

    for($i=0;$i<$nb_days_to_display;$i++)
    {
        $day=$week_start->format('l j');
        $week_start->add(new DateInterval('P1D'));

        $days_row.= "<th class='reservation_day_th'>$day</th>";
    }


    //    <th class="reservation_day_th">Lundi</th>
    //    <th class="reservation_day_th">Mardi</th>
    //    <th class="reservation_day_th">Mercredi</th>
    //    <th class="reservation_day_th">Jeudi</th>
    //    <th class="reservation_day_th">Vendredi</th>
    //    <th class="reservation_day_th">Samedi</th>
    //    <th class="reservation_day_th">Dimanche</th></tr>';
    $days_row.= '</tr>';



    if($week == global_week_number)
    {
        echo highlight_day($days_row);
    }
    else
    {
        echo $days_row;
    }


    foreach($global_times as $time)  //tranches horaires
    {
        echo '<tr><th class="reservation_time_th">' . $time . '</th>';

        $i = 0;

        $week_start = new DateTimeFrench();
        $week_start->setISODate(global_year,$week);


        while($i <$nb_days_to_display)
        {                       
            $i++;
            $date=$week_start->format('Y/m/d');
            $resacss=read_reservation_css($week, $i, $time) ;                         

            if ($date>max_date||$date<min_date || $time==$grey_keyword
            )
                echo ' <td><div class="reservation_time_div_inactive"><div class="reservation_time_cell_div_inactive" id="div:' . $week . ':' . $i . ':' . $time . '" onclick="void(0)">' .  '</div></div></td>';
            else
                echo '<td><div class="reservation_time_div"><div class="reservation_time_cell_div '.$resacss.'" id="div:' . $week . ':' . $i . ':' . $time . '" onclick="void(0)">' . read_reservation($week, $i, $time) . '</div></div></td>';
                
            $week_start->add(new DateInterval('P1D'));
        }

        echo '</tr>';


    }

    echo '</table>';
}
elseif(isset($_GET['weeknumber']))
{


    $week = $_GET['weeknumber'];
    //    
    //    $week = global_week_number;
    //    
    //    $startofweek=date('W');
    //                                                  

    //  $current=strftime('%A %e',$startofweek);                                                 
    //  echo $current;
    $week_start = new DateTimeFrench();
    $week_start->setISODate(global_year,$week);
    //    echo $week_start->format('d-M-Y');
    //    $wk= $week_start->format('l j F Y');
    $wk= $week_start->format('F Y');
    //    $asint=$week_start->getTimestamp();
    //               
    echo $week ." -  $wk";

}
else
{
    //entete de table

    //    
    $week = global_week_number;
    //    
    //    $startofweek=date('W');
    //                                                  

    //  $current=strftime('%A %e',$startofweek);                                                 
    //  echo $current;
    $week_start = new DateTimeFrench();
    $week_start->setISODate(global_year,$week);
    //    echo $week_start->format('d-M-Y');
    //    $wk= $week_start->format('l j F Y');
    $wk= $week_start->format('F Y');
    //    $asint=$week_start->getTimestamp();
    //               


    echo '</div><div class="box_div" id="reservation_div"><div class="box_top_div" id="reservation_top_div"><div id="reservation_top_left_div"><a href="." id="previous_week_a">&lt; Semaine précédente</a></div>
    <div id="reservation_top_center_div">Semaine  <span id="week_number_span">' . global_week_number ." -  $wk".
    (isset($_SESSION['logged_in'])?'<a href="#logout" class="bigred">Se déconnecter et recevoir le mail de confirmation</a>':'')

    .'</span></div>

    <div id="reservation_top_right_div"><a href="." id="next_week_a">Semaine suivante &gt;</a></div></div>
    <div class="box_body_div"><div id="reservation_table_div"></div></div></div><div id="reservation_details_div">';
}

?>
