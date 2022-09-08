<?php

function getUtilizDiaCalend($data){

    $diaUtil = 0;
    $calend = buscarParametroIma('calend_global','fiscal');
    $aReg= getReg('ems5',
        'dia_calend_glob',
        'cod_calend',
        "'$calend'",
        'log_dia_util',
    "dat_calend='$data'" );
    if(is_array($aReg)){
        $diaUtil = $aReg[0]['log_dia_util'];
    }
    return $diaUtil;
}


