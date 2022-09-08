<?php
//__NM__sitPedRepre__NM__FUNCTION__NM__//
function getSitComisRepres($nrPedido,$repres){   

    $sitComis = "";
    $tipo     = "unico";
    $tabela   = " pub.\"ped-repre\" ";
    $campos   = "\"cod-classificador\" as sit_comis";
    $condicao = " \"nr-pedido\" = $nrPedido and \"nome-ab-rep\" = '$repres'";	
    $conexao  = "med";
	$aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $sitComis = $aDados[0]['sit_comis'];

    }
	
	switch($sitComis){
	
		case '':
			$sitComis = "Aprovado";
			break;
		
		case 'NAO_AVALIADO':
			$sitComis = "Não Avaliado";
			break;
		
		case 'REPROVADO':
			$sitComis = "Reprovado";
			break;
			
		default:
			$sitComis = $sitComis;
			break;			
	
	}
	
    return $sitComis;		
	
}

function getSitPedRepresSeparacao($pedido, $nomeAbrev){

    $sitPed = "";
    $tipo     = "unico";
    $tabela   = " pub.\"ped-item-rom\" ";
    $campos   = "\"nr-pedcli\" as ped";
    $condicao = " \"nr-pedcli\" = $pedido and \"nome-abrev\" = '$nomeAbrev' and \"cod-estabel\" = 5";
    $conexao  = "espec";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $sitPed = $aDados[0]['ped'];
    }
    return $sitPed;

}
?>