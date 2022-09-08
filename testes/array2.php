<?php
$a[] = array('item'=> '130015','ref'=>'001','qt'=>1);
$a[] = array('item'=> '130015','ref'=>'002','qt'=>10);
$a[] = array('item'=> '130016','ref'=>'001','qt'=>20);
unset($a[1]);
array_values($a);
var_dump($a);