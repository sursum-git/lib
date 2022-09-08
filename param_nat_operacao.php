<?php
//__NM____NM__FUNCTION__NM__//
function buscarValoresParamNat($param)
{
	$tabela 	= "pub.param_nat_operacao";
	$campo  	= "cod_estab,cod_tipo_atividade,cod_finalidade_venda";
    $condicao	= " cod_param_nat_operacao = $param";
	$aRetorno = retornoSimplesTb01($tabela,$campo,$condicao,"espec");
	return $aRetorno;	
}

function verificarUtilizParam($codParam)
{
	$mensagem = '';
	$tabela 	= 'pub."ped-venda-ext"';
	$campo  	= "cod_param_nat_operacao";
	$condicao	= " cod_param_nat_operacao = $codParam  ";
	$aRetorno = retornoSimplesTb01($tabela,$campo,$condicao,"espec");
	if(is_array($aRetorno)){  	
		$mensagem = "Este parametro já foi utilizado por um pedido de venda";			
	}
	$tabela 	= "pub.param_nat_operacao";
	$campo  	= "cod_param_nat_operacao_pai";
	$condicao	= " cod_param_nat_operacao_pai = $codParam  ";
	$aRetorno = retornoSimplesTb01($tabela,$campo,$condicao,"espec");
	if(is_array($aRetorno)){  	
		$mensagem = "Este parametro é de venda triangular e possui parametros filhos. Apague primeiramente os parâmetros filhos.";			
	}	
	return $mensagem;
	
}
function qtPedidosCodNatOperacao($codNatOperacao)
{
	$qt = 0; 
	$campo    = "count(cod_param_nat_operacao) as qt ";
	$tabelas  = 'pub."ped-venda-ext"';
	$condicao = "  cod_param_nat_operacao =	$codNatOperacao";
    $aRetorno = retornoSimplesTb01($tabelas,$campo,$condicao,"espec");
	if(is_array($aRetorno)){
		$qt = $aRetorno[0]['qt'];	
	}
	return $qt;
}
	

	
?>