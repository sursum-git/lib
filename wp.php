<?php
//__NM__Pesquisas Web__NM__FUNCTION__NM__//
function criarPesquisa($filtro,$pagina,$banco="progress")
{
    /********************************************************
    Objetivo: Criar um registro de pesquisa para o usuario,
    possibilitando assim a rastreabilidade das consultas
    feitas pelos usuários
     *******************************************************/
    //$data   = dataCorrenteProgress();
    $data = getAgora('en');
    $filtro = str_replace("'","|",$filtro);
    //$cod_wp = date('YmddGisu');
    $cod_wp = microtime(true) * 10000;
    if($banco == 'progress'){
        $esquema = "PUB.";
    }else{
        $esquema = "";
    }
    $cmdSql = "insert into ".$esquema."wp(cod_wp,login,data_hora,pagina,filtro)
     values('$cod_wp','".getLoginCorrente()."',
             '$data',
             '$pagina','$filtro')";
    //echo "criação da pesquisa:".$cmdSql;
    if($banco == 'progress'){
        sc_exec_sql($cmdSql,"especw");
    }else{
        sc_exec_sql($cmdSql,"dinamico");
    }

    return $cod_wp;
}
function buscarFiltroWP($wp)
{
    $filtro = '';
    $sql = "select filtro from wp where cod_wp = '$wp'";
    echo "select buscarfiltrowp:".$sql."</br>";
    sc_lookup(wp, $sql,"dinamico");
    if ({wp} === false)
    {
        echo "Erro de acesso dinamico - buscarFiltroWP. Mensagem = " . {wp_erro} ;
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

function getUltWpLogin($login)
{
    $id = '';
    $aRet = getDados('unico','wp','max(cod_wp) as id',
    "login = '$login'",'dinamico');
    if(is_array($aRet) ){
        $id = $aRet[0]['id'];
    }
    return $id;


}

?>
