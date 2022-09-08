<?php
//__NM____NM__FUNCTION__NM__//
Function getHistTit($codEstab, $numTitId){   

    $hist  = '';
    $tipo     = "unico";
    $tabela   = " pub.histor_tit_movto_ap, pub.movto_tit_ap ";
    $campos   = "des_text_histor as hist" ;
    $condicao = "  pub.histor_tit_movto_ap.num_id_tit_ap = $numTitId        
                   and pub.movto_tit_ap.cod_estab = $codEstab
                   and pub.movto_tit_ap.num_id_tit_ap = $numTitId  
                   and pub.histor_tit_movto_ap.num_id_movto_tit_ap = pub.movto_tit_ap.num_id_movto_tit_ap 
                   and pub.movto_tit_ap.ind_trans_ap = 'implantação'
                   and histor_tit_movto_ap.cod_livre_1 = ''";	
    $conexao  = "ems5BKPPRO";
	$aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $hist = $aDados[0]['hist'];

    }
    return $hist;	
	
	
}

Function getHistorTit($codEstab, $numTitId){   

    $hist  = '';
    $tipo     = "unico";
    $tabela   = " pub.histor_tit_movto_ap";
    $campos   = " top 1 des_text_histor as hist" ;
    $condicao = "  pub.histor_tit_movto_ap.num_id_tit_ap = $numTitId        
                   and pub.histor_tit_movto_ap.cod_estab = $codEstab
                   and pub.histor_tit_movto_ap.cod_livre_1 = ''";
    $conexao  = "ems5BKPPRO";
	$aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $hist = $aDados[0]['hist'];

    }
    return $hist;
}
?>