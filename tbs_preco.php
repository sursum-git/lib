<?php

function getRegTbPreco($id)
{
    $ret = getReg('espec','tbs_preco','tb_preco_id',$id);
    return $ret;
}

function getPercReducComisTbPreco($tbId)
{
    $percRedComis = 0;
    $aRet = getRegTbPreco($tbId);
    if(is_array($aRet)){
        $percRedComis = $aRet[0]['perc_reduc_comis'];
    }
    return $percRedComis;
}

?>
