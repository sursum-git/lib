<?php
$d = '2021-05-15';
$begin = new DateTime( $d );
$end = new DateTime( '2021-05-20' );
$end = $end->modify( '+1 day' );

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin, $interval ,$end);

foreach($daterange as $date){
    echo $date->format("Y-m-d") . "\n";
}
