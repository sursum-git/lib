<?php
//__NM__WP para o banco Sql__NM__FUNCTION__NM__//


function criarPesquisaWP($filtro,$pagina,$banco='mysql')
{
	/********************************************************
     Objetivo: Criar um registro de pesquisa para o usuario,
     possibilitando assim a rastreabilidade das consultas 
     feitas pelos usuários
     *******************************************************/
	
	$filtro = str_replace("'","|",$filtro);
	//$cod_wp = date('YmddGisu');	
	$codWp = microtime(true) * 10000;
	$cmdSql = "insert into wp(cod_wp,login,data_hora,pagina,filtro) 
                values('$codWp','".getLoginCorrente()."',CURRENT_TIMESTAMP,'$pagina','$filtro')";
	//echo "criação da pesquisa:".$cmdSql;
    switch ($banco){
        case 'mysql':
            sc_exec_sql($cmdSql,"dinamico");
            break;
        case 'sqlserver':
            sc_exec_sql($cmdSql,"cfsql");
            break;
    }
	return $codWp;
}


function buscarFiltroWP($wp,$conexao="dinamico")
{
	$filtro = '';

	$sql = "select filtro from wp where cod_wp = '$wp'";
	echo "select buscarfiltrowp:".$sql."</br>";

	sc_lookup(wp, $sql,"cfsql");
	if ({wp} === false)
    { 
       echo "Erro de acesso cfsql - buscarFiltroWPSQL. Mensagem = " . {wp_erro} ;
    }
    elseif (empty({wp}))
    {
        $filtro = '';
    }
    else
    {
		$filtro = {wp[0][0]};		
	}
	if($filtro != '')
	   $filtro = str_replace("|","'",$filtro);
	return $filtro;
	
}

	
	
?>
