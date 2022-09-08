<?php
//__NM____NM__FUNCTION__NM__//
function buscarnatOperacaoFat()
{
    $listaNaturezas = '';
    $sqlNatOperacao = 'select "nat-operacao" from pub."natur-oper" where "tp-rec-desp" = 1 and "tipo-compra" <> 3 ';
    sc_select(nat_oper , $sqlNatOperacao, "med");
    if ({nat_oper} === false){
    echo "Erro de acesso ou Sintaxe - função buscarNatOperacaoFat";
}
     else{
    while (!$nat_oper->EOF){
        if($listaNaturezas == ''){
            $listaNaturezas = "'".$nat_oper->fields[0]."'" ;
        }
        else{
            $listaNaturezas .= ",'".$nat_oper->fields[0]."'" ;
        }
        $nat_oper->MoveNext();
    }
    $nat_oper->Close();
}
	return $listaNaturezas;
}
function buscarnatOperacaoFatDevol()
{
    $listaNaturezas = '';
    $sqlNatOperacao = 'select "nat-operacao" from pub."natur-oper" where "tipo-compra" = 3 and "tp-rec-desp" = 1 ';
    sc_select(nat_oper , $sqlNatOperacao, "med");
    if ({nat_oper} === false){
    echo "Erro de acesso ou Sintaxe - função buscarNatOperacaoFat";
}
     else{
    while (!$nat_oper->EOF){
        if($listaNaturezas == ''){
            $listaNaturezas = "'".$nat_oper->fields[0]."'" ;
        }
        else{
            $listaNaturezas .= ",'".$nat_oper->fields[0]."'" ;
        }
        $nat_oper->MoveNext();
    }
    $nat_oper->Close();
}
	return $listaNaturezas;
}

function buscarDadosNatOperacaoEmp($empresa,$natureza){

    $retorno = array();
    $lAchou = false;
    $sql = "select \"denominacao\",\"cd-trib-icm\", \"aliquota-icm\",\"perc-red-icm\", \"cod-cfop\" from pub.\"natur-oper\" 
	where \"nat-operacao\" = '$natureza' ";
    if($empresa == '1'){
        sc_lookup(meus_dados, $sql,"ima");
    }
    else{
        sc_lookup(meus_dados, $sql,"med");
    }
    if ({meus_dados} === false){
        echo "Erro de acesso função buscarDadosNatOperacaoEmp . Mensagem = " . {meus_dados_erro};
	}
	elseif (empty({meus_dados})){
        //echo "Comando select não retornou dados ";
    }
	else{

            $denominacao  = {meus_dados[0][0]};
			$cdTribIcm    = {meus_dados[0][1]};
			$aliquotaIcm  = {meus_dados[0][2]};
		    $percRedIcm   = {meus_dados[0][3]};
		    $codCfop  	  = {meus_dados[0][4]};
			$retorno[] = array("denominacao" => $denominacao, "cd_trib_icm" => $cdTribIcm,
                "aliquota_icm" => $aliquotaIcm, "perc_red_icm" => $percRedIcm, "cod_cfop" => $codCfop);
			$lAchou = true;
	}
	if($lAchou == false){
	   $retorno = '';
    }
	//var_dump($retorno);
	return $retorno;
}
?>