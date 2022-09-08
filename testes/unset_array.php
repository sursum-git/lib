<?php
$a[] = array('it-codigo'=>'123547', 'saldo'=> 5);
$a[] = array('it-codigo'=>'123548', 'saldo'=> 10);
$a[] = array('it-codigo'=>'123549', 'saldo'=> 20);

foreach($a as $key => $cp){
    echo "<h1>{$cp['it-codigo']} - $key</h1>";
    if($cp['saldo'] == 10){
        unset($a[$key]);
    }
}
$a = array_values($a);

var_dump($a);
