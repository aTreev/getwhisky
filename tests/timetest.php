<?php 
$discounted = true;
$end_date = "2021-10-20 08:57:00";
if (strtotime($end_date) <= time()) {
    $discounted = false;
}
if ($discounted) {
    echo "Discount still applied!";
} else {
    echo "Discount ended";
}

$other_end_date = "2021-11-04 13:00";
$time_remaining = strtotime($other_end_date) - time();
if ($time_remaining < 86400) {
    echo "<br>";
    if (floor($time_remaining/60/60) > 0) {
        echo "<h5 style='margin:auto;display:block;font-size:2rem;font-family:sans-serif;'>Deal ends in ".floor($time_remaining/60/60). " hours!</h5>";
    } else {
        echo "<h5 style='margin:auto;display:block;font-size:2rem;font-family:sans-serif;'>Deal Ended! :(</h5>";
    }
} 
?>