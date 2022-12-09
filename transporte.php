<?php

function getRegTransporte($chave,$valor,$campos='')
{
    $aReg = getReg('ems2', 'transporte',$chave,$valor,
    $campos);
    return $aReg;
}
function getCodTranspPorNomeAbrev($nomeAbrev)
{   $nomeAbrev = tratarAspasSimples($nomeAbrev);
    $aTransp = getRegTransporte('"nome-abrev"',"'$nomeAbrev'",'"cod-transp" as cod_transp');
    return getVlIndiceArray($aTransp,'cod_transp',0);
}
function getNomeAbrevTransp($codTransp)
{
   if($codTransp == 0){
       return '';
   } else{
       $aTransp =  getRegTransporte('"cod-transp"',$codTransp,'"nome-abrev" as nome_abrev');
       return getVlIndiceArray($aTransp,'nome_abrev','');
   }

}


?>