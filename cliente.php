<?php
//__NM__Cliente__NM__FUNCTION__NM__//
/* 
function retornarPessoaCliente($cod_emitente)
{
    $retorno = 0;
    sc_lookup(cliente, "select num_pessoa from pub.clientes where cdn_cliente = $cod_emitente","ems5PROD");
    if (!{cliente} === false && !empty({cliente} )
    {
        $retorno = {cliente[0][0]};
	}
		return $retorno;

}*/
function montarFiltroCliente($aFiltros,$aApelidoTb='')
{ /* chaves aFiltros: 
     filtrar_por,uf,bairro,nome_abrev,nome_emit,cnpj,codigo,cod_rep
    */
    $aFiltroCond = array();
    $tabela ='emitente';
    if(is_array($aApelidoTb) and isset($aApelidoTb[$tabela]) ){
        $tabela = $aApelidoTb[$tabela];
    }
    $afiltro['identific'] = ' <> 2 ';
    

    switch($aFiltros['filtrar_por'])
    {
        case 1: //localidade
               if($aFiltros['cidade'] <> ''){
                      $listaCidades = inserirAspasEmLista($aFiltros['cidade']);                      
                      $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cidade',"($listaCidades)",'in',true );  
                      $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'estado',$aFiltros['uf'] ); 
               }else{                               
                    $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'estado',$aFiltros['uf'] );                 
               }                   
               $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'bairro',$aFiltros['bairro'],'like' );                  
               
               
            break;
        case 2: //nome-abrev                        
            $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nome-abrev',$aFiltros['nome_abrev'],'like' );     
            
            
             break;        
        case 3: //razao_social            
                $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nome-emit',$aFiltros['nome_emit'],'like');
            break;                
        case 4: //cnpj            
                $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cgc',$aFiltros['cnpj'],'like' );
            break;                   
        case 5: //código
            $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-emitente',$aFiltros['codigo'],'=',true );
            break;                          
            
    }
    $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-rep',$aFiltros['cod_rep'],'in',true );  
    
    return convArrayToCondSql($aFiltroCond);
}


function getDadosCliente($codEmitente){


    $cliente  = 0;
    $tipo     = "unico";
    $tabela   = " pub.emitente ";
    $campos   = "\"cod-emitente\" as cod_emit,\"nome-abrev\" as nome_abrev" ;
    $condicao = "  \"cod-emitente\" = $codEmitente";
    $conexao  = "comum";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $cliente = $aDados[0]['nome_abrev'];

    }
    return $cliente;


}


function getTranspPadraoCliente($cliente)
{
    $transp = 0;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.emitente emit ";
    $campos   = " \"cod-transp\" as transp";
    $condicao = "  emit.\"cod-emitente\" = $cliente ";
    $conexao  = "comum";
    $aDados= getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $transp = $aDados[0]['transp'];
    }
    return $transp;

}
function getFinalidadePadraoCliente($cliente)
{
    $finalidade = 0;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.\"ext-emitente\" ext_emit ";
    $campos   = " cod_finalidade_venda ";
    $condicao = "  ext_emit.\"cod-emitente\" = $cliente ";
    $conexao  = "espec";
    $aDados= getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $finalidade = $aDados[0]['cod_finalidade_venda'];
    }
    return $finalidade;

}

function existCliente($codEmitente)
{
    $log 	  = false;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.emitente emitente ";
    $campos   = " \"cod-rep\" as repres ";
    $condicao = "  \"cod-emitente\" = $codEmitente ";
    $conexao  = "comum";
    $aDados= getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $log = true;
    }
    return $log;


}

function existClienteCNPJ($CNPJ)
{
    $CNPJ = str_replace('.','',$CNPJ);
    $CNPJ = str_replace('/','',$CNPJ);
    $CNPJ = str_replace('-','',$CNPJ);
    $codRepres = '';
    $codCliente = 0;
    $logSoFornecedor = false;
    $nomeAbrevRepres = '';
    $nomeAbrevCliente = '';
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.emitente emitente ";
    $campos   = " \"cod-rep\" as repres, \"cod-emitente\" as cod_emitente, \"nome-abrev\" as nome_abrev, identific ";
    $condicao = "  cgc = '$CNPJ'";
    $conexao  = "comum";
    $aDados= getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $codRepres = $aDados[0]['repres'];
        $codCliente = $aDados[0]['cod_emitente'];
        $nomeAbrevCliente = $aDados[0]['nome_abrev'];
        $identific = $aDados[0]['identific'];
        if($identific == 2){
            $logSoFornecedor = true;
        }
        $aRepres = buscarDadosRepres($codRepres,$campos='"nom_abrev" as nome');
        if(is_array($aRepres)){
            $nomeAbrevRepres = $aRepres[0]['nome'];
        }else{
            $nomeAbrevRepres = '';
        }
    }
    $aRetorno = array('cod_rep' => $codRepres,'nome_abrev_repres' => $nomeAbrevRepres,
        'cod_cliente' =>$codCliente, 'nome_abrev_cliente' => $nomeAbrevCliente,
        'so_fornecedor' => $logSoFornecedor);
    return $aRetorno;


}




function validarClienteNovo($CNPJ)
{
    $msg =array();
    if($CNPJ <> ''){
        $log = true; //validarCNPJ($CNPJ); validação foi passada para o formulário
        if($log <> true){
            $msg['erro'] = 'Este CNPJ é inválido, favor conferir o numero digitado.';
        }else{
            $aRepres = existClienteCNPJ($CNPJ);
            if(getTipoUsuarioCorrente()== 2){ //representante
                $codRepres 		= $aRepres['cod_rep'];
                $codCliente		= $aRepres['cod_cliente'];
                $nomeRepres 	= $aRepres['nome_abrev_repres'];
                $nomeCliente	= $aRepres['nome_abrev_cliente'];
                $logSoFornecedor = $aRepres['so_fornecedor'];
                if(getLoginCorrente() <> $nomeRepres and strtoupper($nomeRepres) <> 'IMA'){
                    if($codCliente <> ''){
                        if($logSoFornecedor == true){
                            $msg['aviso'] = "Este CNPJ pertence a um fornecedor do Grupo IMA. 
                            O código do fornecedor é $codCliente. Solicite ao cadastro a transformação 
                            deste fornecedor em cliente.";
                        }else{
                            $msg['erro'] = "Cliente $codCliente - $nomeCliente já cadastrado no sistema com
                             este CNPJ ";
                        }
                    }
                }else{
                    $msg['erro'] = "Este CNPJ pertence ao cliente $nomeCliente que consta na sua
                     lista de clientes. Desmarque a opção Novo Cliente e selecione
                      o cliente pelo campo 'CLIENTE'.";
                }
            }
        }
    }
    return $msg;
}

function getRegCliente($codEmitente,$campos='',$filtroCompl='')
{
    $aReg = getReg('comum','emitente','"cod-emitente"',
        $codEmitente,$campos,$filtroCompl );
    return $aReg;
}

function getRegContatoCliente($codEmitente,$campos='',$filtroCompl='')
{
    $aReg = getReg('comum','cont-emit','"cod-emitente"',
        $codEmitente,$campos,$filtroCompl );
    return $aReg;
}

function getRegClientePorNomeAbrev($nomeAbrev,$campos='',$filtroCompl='')
{
    $aReg = getReg('comum','emitente','"nome-abrev"',
        "'$nomeAbrev'",$campos,$filtroCompl );
    return $aReg;
}



function getRegClienteExt($codEmitente,$campos='',$filtroCompl='')
{
    $aReg = getReg('espec','ext-emitente','"cod-emitente"',
        $codEmitente,$campos,$filtroCompl );
    return $aReg;
}

function getSitVendaCliente($cliente)
{
    $situacao = 0;
    $aReg = getRegClienteExt($cliente,'situacao');
    if(is_array($aReg)){
        $situacao = $aReg[0]['situacao'];
    }
    return $situacao;

}
function getSitClienteInativo()
{
    return 2;
}

function getNomeAbrevRepresCliente($cliente)
{
    $nomeAbrev = '';
    $aCli = getRegCliente($cliente,'"cod-rep" as cod_rep');
    $codRep = $aCli[0]['cod_rep'];
    $aRep = buscarDadosRepres($codRep,'"nom_abrev" as nome_abrev');
    if(is_array($aRep)){
        $nomeAbrev = $aRep[0]['nome_abrev'];
    }
    return $nomeAbrev;

}
function validarClienteRepres($cliente)
{

    $erro = '';
    if($cliente <> 0){
        $situacao = getSitVendaCliente($cliente);
        if($situacao <> 2){
            $nomeAbrevRepres = getNomeAbrevRepresCliente($cliente);
            if( strtoupper($nomeAbrevRepres)  <> strtoupper([nomeRepresIni]) ){
                $erro = "Este Cliente não está como Inativo e não pertence a sua carteira 
                        de clientes. <br>Repres Atual: $nomeAbrevRepres .";
                $erro.= " - nome abrev:".strtoupper($nomeAbrevRepres) ;
                $erro.= " - login atual:".strtoupper(getLoginCorrente()) ;
            }
        }
    }

    return $erro ;
}


function getNomeAbrevCliente($codEmitente,$logCodigo=false)
{   $nomeAbrev = '';
    $aReg = getRegCliente($codEmitente,'"nome-abrev"','"cod-emitente" > 0');
    if($aReg <> ''){
        $nomeAbrev = $aReg[0]['"nome-abrev"'];
        if($logCodigo == true){
            $nomeAbrev = "$codEmitente - $nomeAbrev";
        }
    }
    return $nomeAbrev;
}

function getUfCliente($codEmitente)
{
    $uf = '';
    $aReg = getRegCliente($codEmitente,'estado');
    if(is_array($aReg)){
        $uf = $aReg[0]['estado'];
    }
    return $uf;
}
function getSitCliente($codEmitente)
{
    $sit = '';
    $aReg = getRegCliente($codEmitente,'"ind-cre-cli"');
    if(is_array($aReg)){
        $sit = $aReg[0]['"ind-cre-cli"'];
    }
    return $sit;

}
function getEmailCliente($codEmitente)
{
    $email = '';
    $aReg = getDados('unico','pub."cont-emit"','"e-mail"',
        "\"cod-emitente\" = $codEmitente 
					 and \"area\" = 'Comercial' ",'comum');
    if(is_array($aReg)){
        $email = $aReg[0]['"e-mail"'];
    }
    return $email;

}

function getclientesInativos()
{
    $lista = '';
    //echo "<h1>tipo:[tipoUsuario]</h1>";
    if(getTipoUsuarioCorrente() == getNumTpUsuarioRepres()) { //representante
        $aRegs = getDados('multi','espec.pub."ext-emitente" ext , comum.pub.emitente emitente
        , espec.pub.ufs_repres ufs_repres',
            'ext."cod-emitente" as cod_emitente',
            "ext.situacao = 2 and emitente.estado = ufs_repres.uf
        and ufs_repres.nome_abrev = '".getLoginCorrente()."'
        and emitente.\"cod-emitente\" = ext.\"cod-emitente\"",'multi');
        if(is_array($aRegs)){
            $tam = count($aRegs);
            for($i=0;$i<$tam;$i++){
                $codEmitente = $aRegs[$i]['cod_emitente'];
                $lista = util_incr_valor($lista,$codEmitente);
            }
        }
    }else{
        $lista =0;
    }
    return $lista;
}


function getCpsFilPorCodNome($filtro,$logUTF8=0)
{


    //$filtro = " and $filtro ";
    $campos = "emitente.\"cod-emitente\" as codigo , \"nome-emit\" as nome,
                 ltrim(to_char(emitente.\"cod-emitente\")) + '-' + \"nome-abrev\" as cli_busca , 
                 emitente.\"ind-cre-cli\" as cred_cli";
    if(getTipoUsuarioCorrente() == getNumTpUsuarioRepres()
        or getTipoUsuarioCorrente() == getNumTpUsuarioPreposto()){ //representante
        $repres = getVarSessao('glo_nomeRepresIni');
        $sql = "select $campos from espec.pub.\"ext-emitente\" ext , 
                 comum.pub.emitente emitente, espec.pub.ufs_repres ufs_repres 
                 where $filtro  
                 and  ext.situacao = 2 and emitente.estado = ufs_repres.uf and ufs_repres.nome_abrev = '$repres' 
                 and emitente.\"cod-emitente\" = ext.\"cod-emitente\" 
                 union
                 select   emitente.\"cod-emitente\" as codigo , \"nome-emit\" as nome,
                 ltrim(to_char(emitente.\"cod-emitente\")) + '-' + \"nome-abrev\" as cli_busca , 
                 emitente.\"ind-cre-cli\" as cred_cli from comum.pub.emitente emitente
                 where  $filtro
                 AND emitente.identific in(1,3) AND emitente.\"cod-emitente\" > 0 and emitente.\"cod-rep\" in 
                 ( Select \"cod-rep\" from comum.PUB.repres where \"nome-abrev\" = '$repres')";
        $aDados = getRegsSqlLivre($sql,$campos,'multi',1);

    }else{
       $filtro .= " and emitente.identific in(1,3) ";
       $aDados = getDados('multi',
           'pub.emitente emitente',
            $campos,
            $filtro,
       'comum',
       '',
       1);

    }
    if(is_array($aDados)){
        $tam = count($aDados);
        for($i=0;$i< $tam; $i++){
            $cliente = $aDados[$i]['codigo'];
            $email = getEmailCliente($cliente);
            $aDados[$i]['email'] = $email  ;
        }
    }
    return $aDados;


}

function validarCliente($codEmitente)
{
    $msgErro = '';
    $msgAviso = '';
    $iSit = getSitCliente($codEmitente);
    $lErro = false;
    $lAviso = false;
    switch($iSit){
        case 4:
            $msgErro = 'Este Cliente está suspenso, verifique com o  setor cadastro a situação do cliente ';
            $aRet = array('erro' => $msg,'aviso' =>'' );
            $lErro = true;
            break;
        case 5:
            $msgAviso = 'Este cliente só pode comprar a vista e por este motivo não será possível informar uma condição de pagamento diferente de "à vista"';
            break;
    }
    $lBloqueado = getSitCliAdmVendas($codEmitente);
    if($lBloqueado){
        $msgErro = util_incr_valor($msg,'Cliente Bloqueado pela Adm.Vendas',"<br/>");

    }
    $aRet = array('erro' => $msgErro,'aviso' =>$msgAviso );
    return $aRet;
}
function atuHistAvalCli($cliente,$histAvalCliId)
{
    $array = array('hist_aval_cli_id'=>$histAvalCliId);
    $cmd = convertArrayEmUpdate('"ext-emitente"',$array,"\"cod-emitente\" = $cliente");
    sc_exec_sql($cmd,"especw");

}
function getSitCliAdmVendas($cliente)
{
    $lBloqueado = 0;
    $aCliente = getRegClienteExt($cliente,'hist_aval_cli_id');
    if(is_array($aCliente)){
        $histAvalCliId = $aCliente[0]['hist_aval_cli_id'];
        $aHist = getRegHistAvalCli($histAvalCliId,'cod_sit_aval');
        if(is_array($aHist)){
            $lBloqueado = $aHist[0]['cod_sit_aval'];
        }
    }
    return $lBloqueado;
}
function verificarClienteInativo($cliente)
{
    $sitVenda = getSitVendaCliente($cliente);
    if($sitVenda == getSitClienteInativo()){
        $ret = 1;
    }else{
        $ret = 0;
    }
    return $ret;

}

function verifCliOutroRepres($clienteParam, $login='')
{
    $ret = 0;
    if($login == ''){
        $login = getVarSessao(getNomeVarLoginSessao());
    }
    $nomeRepresCli 	= getNomeAbrevRepresCliente($clienteParam);
    //echo "<h1>nome repres cli: $nomeRepresCli - cliente: $clienteParam</h1>";
    $situacao  		= getSitVendaCliente($clienteParam);
    $lCliOutroVend  = $nomeRepresCli <> $login;
    if($lCliOutroVend  and $situacao <> getSitClienteInativo() ){
        $lCliOutroVend = 1;
    }else{
        $lCliOutroVend = 0;
    }
    return array('log_outro_vend'=>$lCliOutroVend, 'nome_repres_cli'=>$nomeRepresCli);
}

function getMsgErroSitCliente($sit, $logAvista, $clienteId){
    $msg = '';
    switch($sit){
        case 4:
            $msg1 = 'Este Cliente está suspenso, verifique com o  setor cadastro a situação do cliente ';
            $msg = util_incr_valor($msg,$msg1,"</br>");
            break;
        case 5:
			$logAvista == 1 ? $msg1 = '': $msg1 = 'Este cliente só pode comprar a vista e por este motivo não será possível informar 
			uma condição de pagamento diferente de "à vista"';
            $msg = util_incr_valor($msg,$msg1,"</br>");
		break;
        default:
            $msg = '';
    }

    $sitAvalCli = getSitCliAdmVendas($clienteId);
	if($clienteId <> 0 and $sitAvalCli == 1){
        $msg1 = 'Cliente Bloqueado pela Adm.Vendas';
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }
    return $msg;

}

function getDadoContatoArea($codEmitente,$tipo,$area)
{
    $retorno = '';
    switch ($tipo){
        case 'e-mail':
            $cp = 'e-mail';
            break;
        case 'telefone':
            $cp = 'telefone';
            break;
    }
    $aRet = getReg('comum','"cont-emit"','"cod-emitente"',
    $codEmitente,"$cp,sequencia","\"area\" = '$area' " );
    if(is_array($aRet)){
        $retorno = $aRet[0][$cp];
        $sequencia = $aRet[0]['sequencia'];

        if($retorno == ''){
            $aTels = getTelefonesExtContato($codEmitente,$sequencia);
            if(is_array($aTels)){
                if($aTels[0]['celular1']<> ''){
                    $retorno =$aTels[0]['celular1'];
                }else{
                    if($aTels[0]['celular2']<> ''){
                        $retorno =$aTels[0]['celular2'];
                    }else{
                        if($aTels[0]['celular3']<> ''){
                            $retorno = $aTels[0]['celular3'];
                        }
                    }
                }
            }
        }
    }
    return $retorno;
}
function getTelefonesExtContato($codEmitente,$sequencia)
{
    $aDados = getDados('multi','pub."ext-cont-emit"',
        'celular1,celular2, celular3,aplicativo1,aplicativo2,aplicativo3,messenger1,messenger2,messenger3',
    "\"cod-emitente\" = $codEmitente and sequencia = $sequencia","espec");
    return $aDados;


}

function getTelComercialContato($codEmitente,$campos='',$area){


    $tel = '';
    $aRet = getReg('comum','"cont-emit"','"cod-emitente"',
        $codEmitente,$campos,"\"area\" = '$area' " );

    if(is_array($aRet)){
        $tel = $aRet[0]['telefone'];
    }
    return $tel;

}

function getLimitesCredCli($cliente,$campos){

    $aParams = array('ind_limite'=> 'todos_limites',
        'cod_emitente' => $cliente);
    $aDados = getDadosApis('pdp/v1/histLimitCredCli','GET',$aParams,$campos);
    return $aDados;
}

function getUltLimitCred($cliente,$campos){

    $aParams = array('ind_limite'=> 'todos_limites',
        'cod_emitente' => $cliente);
    $aDados = getDadosApis('pdp/v1/histLimitCredCli','GET',$aParams,$campos);
    $aReg= end($aDados);
    return $aReg;

}
function incluiLimitesCredCli($cliente,$login,$dtValidade,$vlCredito,$hist){

    $aParams = array('ind_limite'=>'incluir',
        'cod_emitente' => $cliente,
        'cod_usuario' => $login,
        'dt_validade' => $dtValidade,
				    'vl_credito' => $vlCredito,
				    'historico' => $hist);
     getDadosApis('pdp/v1/histLimitCredCli','GET',$aParams);
}

function verificaRestrPrioridade($codEmitente){

    $codPriori = '';
    $aRet = getReg('espec','"ext-emitente"','"cod-emitente"',
        $codEmitente,"\"restr-priorid\"");
    if(is_array($aRet)){
        $codPriori = $aRet[0]['"restr-priorid"'];
        $codPriori = str_replace('"','',$codPriori);
    }

    return $codPriori;

}

function getSitClientesRep($sit,$codRepIni,$codRepFim,$mes){
    //echo "<h1>mes = $mes</h1>";
    $qtCliSit = 0;
    $listaRepres = 0;
    //$dia = date('d') - 1;
    //$mes = date('m');
    $ano = date('Y');
    $qtDiasMes = cal_days_in_month ( CAL_GREGORIAN , $mes , $ano );
    $dataRef = date("Y-$mes-$qtDiasMes");
    $codRepLogin = buscarCodRep([usr_login]);
    $codRepGer = $codRepLogin;
    $tipoUsuario = getTipoUsuario([usr_login]);
    $hierarq = getTpHierarquiaGer($codRepGer);
    if($tipoUsuario <> 2){
        if($hierarq == 3 and $codRepIni <> $codRepFim){
            $listaRepres = getListaRepresGer('cod');
            $condicao = 'sit_cli.cod_repres in('.$listaRepres.') and cod_sit_cli  = '.$sit.' and dt_referencia = '."'$dataRef'";

        }else{
            $condicao = 'sit_cli.cod_repres >= '.$codRepIni.' and sit_cli.cod_repres <= '.$codRepFim.' and cod_sit_cli  = '.$sit.' and dt_referencia = '."'$dataRef'";
        }
    }else{
        $condicao = 'sit_cli.cod_repres = '.$codRepLogin.' and cod_sit_cli  = '.$sit.' and dt_referencia = '."'$dataRef'";
    }


    $aDados = getDados('multi','PUB.repres_sit_cli sit_cli','qt_sit_cli',$condicao,'espec');
    //var_dump($aDados);
    if(is_array($aDados)){
        if($codRepIni == $codRepFim){
            $qtCliSit = $aDados[0]["qt_sit_cli"];
        }else{
            foreach($aDados as $dados){
                $qtsClisSit = $dados["qt_sit_cli"];
                $qtCliSit += $qtsClisSit;
            }
        }

    }
    return $qtCliSit;

}
?>
