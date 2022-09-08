<?php

function getCodigoProg($id)
{
    $aRetorno = retornoSimplesTb01('programas','codigo', "cod_programa=$id",'ticontrole' ) ;
    if(is_array($aRetorno))
    {
        $retorno = $aRetorno[0]["codigo"];
    }
    return $retorno;
}
function getTitPorCodigo($codigo)
{
    $retorno = "$codigo - Programa Não cadastrado";
    $aRetorno = retornoSimplesTb01('programas','titulo', "codigo='$codigo'",'ticontrole' ) ;
    if(is_array($aRetorno))
    {
        $retorno = $aRetorno[0]["titulo"];
    }
    return $retorno;
}

function getRegProgramaPorCodigo($prog)
{
    $aReg = getReg('ticontrole','programas','codigo',"'$prog'");
    return $aReg;

}
