<?php

function validarExclusaoParamGeracaoBoleto($idParam)
{
    $logAchou = false;
    $aRet = getDados('unico','pub.boletos','top 1 boleto_id',
    " param_geracao_boleto_id = $idParam ");
    if(is_array($aRet)){
        $logAchou = true;
    }
    return $logAchou;
}

function validarAltDataValidParamGeracaoBoleto($idParam,$dtFimValidade)
{
    $dtUltCriacao = '';
    $logDtAnterior = false;
    $aRet = getDados('unico','pub.boletos','max(dt_hr_criacao) as dt_ult_criacao',
        " param_geracao_boleto_id = $idParam ");
    if(is_array($aRet)){
        $dtUltCriacao = $aRet[0]['dt_ult_criacao'];
    }
    if($dtUltCriacao <> ''){
        $dtUltCriacao = substr($dtUltCriacao,0,10);
        if($dtUltCriacao > $dtFimValidade){
             $logDtAnterior = true;
        }
    }
    $aRetorno = array('log_dt_anterior'=>$logDtAnterior,'data'=>$dtUltCriacao);
    return $aRetorno;
}


?>
