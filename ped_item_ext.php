<?php
//__NM____NM__FUNCTION__NM__//
function getDadosPedItemExt($codEstabel,$nrSequencia,$nrPedido,$nomeAbrev)
{
    $preMin   = 0;
    $tipo     = "unico";
    $tabela   = " pub.\"ped-item-ext\" ";
    $campos   = "\"vl-pre-min\" as vl_pre_min";
    $condicao = "  \"cod-estabel\" = $codEstabel and \"nr-sequencia\" = $nrSequencia
	and \"nr-pedcli\" = $nrPedido and \"nome-abrev\" = '$nomeAbrev' ";
    $conexao  = "espec";
	$aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $preMin = $aDados[0]['vl_pre_min'];

    }
    return $preMin;
} 
?>