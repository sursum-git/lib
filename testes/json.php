<?php

$a['op'][0] = '0001';
$a['op'][1] = '0002';
echo json_encode($a);
echo "\n";
$a = array();
$a[] = array('op'=>'0001');
$a[] = array('op'=>'0002');
echo json_encode($a);

