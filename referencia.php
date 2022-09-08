<?php
//__NM____NM__FUNCTION__NM__//
function getOrdemCodRefer($codRefer)
{
    $ordem = 0;
    $aRet =  getDados('unico','pub.referencia','"int-2" as ordem',
        "\"cod-refer\" = '$codRefer'",'med' );
    if(is_array($aRet)){
        $ordem = $aRet[0]['ordem'];
        inserirLogDb('achou ordem ref',"SIM - $ordem",__FUNCTION__);
    }else{
        inserirLogDb('achou ordem ref',"NAO - ref buscada -> $codRefer",__FUNCTION__);
    }
    return $ordem;
}



?>