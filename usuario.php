<?php
//__NM____NM__FUNCTION__NM__//
function getGruposUsuario($login)
{
    $listaGrupos = '';
    $tabela   = " pub.usuarios_grupos ";
    $campos   = "cod_grupo";
    $condicao = "login_usuario = '$login' ";
    $aRet = getDados('multi',$tabela,$campos,$condicao,"espec");
    if(is_array($aRet)){
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $listaGrupos = util_incr_valor($listaGrupos,$aRet[$i]['cod_grupo'])  ;
        }

    }
    return $listaGrupos;

}

function getTipoUsuario($login)
{
    $tipo = 0;
    //echo "<h1>antes getDadosUserWeb</h1>";
    $aReg = getDadosUserWeb($login,'"tp-usuario"');
    //echo "<h1>depois getDadosUserWeb</h1>";
    /*1 - Cliente 2- Representante  3- Adm Vendas 4- Administrador 5- Preposto*/
    if(is_array($aReg)){
        $tipo = $aReg[0]['"tp-usuario"'];
    }
    return $tipo;

}
function getTipoUsuarioCorrente()
{
    return getVarSessao(getNomeVarTipoUsuario());
}
function getLinkCarrinho()
{
    $link = '';
    $tpUsuario = getTipoUsuarioCorrente();
    switch ($tpUsuario){
        case '2':
        case '5':
            $link = "cons_ped_web_carrinho";
            break;
        default:
            $link = "cons_ped_web_carrinho_admin";
    }
    if([usr_login] == 'tasilpar'){
        echo "<h1>tp.usuario: $tpUsuario</h1>";
    }
    return $link;
}


function getRegUsuario($login,$campos='',$filtroCompl='')
{
	$aReg = getReg('espec','user-web', 'login',
        "'$login'", $campos,$filtroCompl);
	return $aReg;
}
/*****************
function getPercPartUsuario($login='')
{

     * comentado, pois vou pegar das variaveis de sessão e não mais do banco de dados.

   if($login == ''){
        $login = getLoginCorrente();
    }
    $perc = 0;
    $aRet = getRegUsuario($login);
    if(is_array($aRet)){
       $perc = $aRet[0]['perc_participacao_comis'];
    }

    return $perc;
}
 ************/

function verificGrupoLogin($codGrupo)
{
    $log = false;
    $listaGrupos = getGruposUsuario(getLoginCorrente());

    if($listaGrupos <> ''){
        if(strstr($listaGrupos,(string) $codGrupo) <> false){
            $log = true;
        }
    }
    return $log;
}

function verificarPermissaoAlocacao()
{
    $log =false;
    //verifica se é representante ou adm.vendas
    if(strstr('2,3,5',getTipoUsuarioCorrente()) <> false){
        if(getTipoUsuarioCorrente() == '3'){ //adm.vendas
            $log = verificGrupoLogin('5'); //verifica se é gerencia
        }else{
            $aReg = getDadosUserWeb(getLoginCorrente(),'log_pedido_on_line');
            if(is_array($aReg)){
                $logPedidoOnLine = $aReg[0]['log_pedido_on_line'];
                if($logPedidoOnLine == 1){
                    $log =true;
                }
            }
        }
    }

    return $log;
}
function getDadosUserWeb($login,$campos='',$filtroCompl='')
{

    $aReg = getReg('espec','user-web',
        'login',"'$login'",$campos,
        $filtroCompl,'');

    return $aReg;
}
function getUsuarioERP($login)
{
    $usuario = '';
    $aRet = getDadosUserWeb($login,'usuario');
   if(is_array($aRet)){
       $usuario = $aRet[0]['usuario'];
   }
   return $usuario;

}

function getPermPagtoViaCaixa($login)
{
    $aReg = getDadosUserWeb($login);
    if(is_array($aReg)){
        $logCaixa = $aReg[0]['log_caixa'];
    }else{
        $logCaixa = 0;
    }
    return $logCaixa == 1;

}

function getPermissoesPedWeb($login)
{
    $aReg = getDadosUserWeb($login);
    if(is_array($aReg)){
        $logCaixa = $aReg[0]['log_caixa'];
        $logPrioridade = $aReg[0]['log_prioridade'];
    }else{
        $logCaixa = 0;
        $logPrioridade = 0;
    }
    $aRetorno = array('log_caixa' => $logCaixa == 1 ,'log_prioridade' => $logPrioridade == 1);
    return $aRetorno;
}
function verifVendLoja()
{
    return getVarSessao(getParamVendLoja());
}
function getParamVendLoja()
{
    return 'vend_loja';
}
/** Verifica se o usuario passado por parametro tem ou não
 * o campo prioridade disponivel no pedido de venda web
 * @param $login
 */
function getPermPrioridade($login)
{
    $aReg = getDadosUserWeb($login);
    if(is_array($aReg)){
        $logPrioridade = $aReg[0]['log_prioridade'];
    }else{
        $logPrioridade = 0;
    }
    return $logPrioridade == 1;
}

/*function getCpsEsconderUsuario($login)
{
	$grupos 		= getGruposUsuario($login);
	$gruposRepres 	= getGruposRepresGeral();


}*/

function getGruposRepresGeral()
{
    //2-vendedor interno 3-representante externo
    return "2,3";
}

function getCpsEsconderPedido($tipo)
{
    $listaCps = '';
    switch($tipo){
        case '2':
            $listaCps = 'dt-useralt,user-alte,user-canc';
            break;
    }
    return $listaCps;
}
/*
function esconderCpsPedido()
{
	$tipo = getTipoUsuario([usr_login]);
	$listaCps = getCpsEsconderPedido($tipo);
	if($listaCps <> ''){
		$aListaCps = explode(',',$listaCps);
		$tam	   = count($aListaCps);
		for($i=0;$i<$tam;$i++){

		}
	}

}
*/

function trocarPerfil($logVoltar,$novoPerfil='')
{

    if($logVoltar){
        $novoPerfil = getVarSessao('perfil_pai');
    }else{
        setVarSessao('perfil_pai',getLoginCorrente()) ;
    }
    limparLogin();
    $aUsuario = getDadosUserWeb($novoPerfil,getCamposUsuario());
    setarVarsLogin($aUsuario);
    /*$aReg           = getDadosUserWeb($novoPerfil);
    $tipoUsuario    = $aReg[0]['tp_usuario'];
    $usr_priv_admin = $aReg[0]['priv_admin'];
    $nomeClienteIni     =  '';
    $nomeClienteFim     =  'zzzzzzzzzzzzzzzzz';
    $prepostoIni        = '';
    $prepostoFim        = 'zzzzzzzzzzzzzzzzzzzz';
    $usr_priv_admin = ( $usr_priv_admin == 'Y') ? TRUE : FALSE;
    $aplicacaoPrincipal = 'container_ini_repres';
    if($logVoltar == true){
        $nomeRepresIni      = '';
        $nomeRepresFim      = 'zzzzzzzzzzzzzzzz';
        $codRepIni          = 0;
        $codRepFim          = 999999;
        $usr_login          = [perfil_pai];
        $perfil_pai         = '';
    }else{
        $usr_login          = strtoupper($novoPerfil) ;
        $usr_name           = $aReg[0]['nome'];
        $usr_email          = $aReg[0]['email'];
        $nomeRepresIni      = $usr_login;
        $nomeRepresFim      = $usr_login;
        $codRepIni          = buscarCodRep($usr_login);
        $codRepFim          = buscarCodRep($usr_login);

    }


    sc_reset_global([usr_login],[usr_priv_admin],[usr_name],[usr_email],[nomeClienteIni],[nomeClienteFim],[nomeRepresIni],[nomeRepresFim],[codrepIni],[codRepFim],
        [prepostoIni],[prepostoFim],[tipoUsuario],[aplicacaoPrincipal]);
    if(isset([perfil_pai])){
        sc_reset_global([perfil_pai]);
    }

    $_SESSION['glo_usr_login']          = $usr_login;
    $_SESSION['glo_usr_priv_admin']     = $usr_priv_admin ;
    $_SESSION['glo_usr_name']           = $usr_name;
    $_SESSION['glo_usr_email']          = $usr_email;
    $_SESSION['glo_nomeClienteIni']     = $nomeClienteIni;
    $_SESSION['glo_nomeClienteFim']     = $nomeClienteFim;
    $_SESSION['glo_nomeRepresIni']      = $nomeRepresIni;
    $_SESSION['glo_nomeRepresFim']      = $nomeRepresFim;
    $_SESSION['glo_codRepIni']          = $codRepIni ;
    $_SESSION['glo_codRepFim']          = $codRepFim;
    $_SESSION['glo_prepostoIni']        = $prepostoIni;
    $_SESSION['glo_prepostoFim']        = $prepostoFim;
    $_SESSION['glo_tipoUsuario']        = $tipoUsuario ;
    $_SESSION['glo_aplicacaoPrincipal'] = $aplicacaoPrincipal;
    $_SESSION['glo_inicio_sessao']      = $perfil_pai;
    */
}

function getFoto()
{
    $caminho = "#";
    $aReg = getDadosUserWeb(getLoginCorrente(),'foto,sexo');

       if(is_array($aReg)){
        $foto = $aReg[0]['foto'];
        $sexo = $aReg[0]['sexo'];
        if($foto <> ''){
            $caminho = "../_lib/file/imgfotos/$foto";
        }else if($foto == '' and $sexo == 1 ) {
            $caminho = "../_lib/file/imgfotos/male.png";
        }else if($foto == '' and $sexo == 2 ) {
            $caminho = "../_lib/file/imgfotos/female.png";
        }
    }
    return $caminho;
}

function atualizarFoto($login,$foto)
{
    $cmd = "update pub.\"user-web\" set foto ='$foto'
            where login = '$login' ";
    sc_exec_sql($cmd,"especw");
}

function verGrpAvalAdm()
{
    $log = verificGrupoLogin(getParamGrupoAvalAdm());
    return $log;
}

function getListaRepresGer($indiceRep='nome',$excecao=''){

    if($excecao == 'sp'){
        $loginGer = 'GESSICA';//Exceção para usuários que só podem ver dados de alguma cidade especifica
    }else{
        $loginGer = getLoginCorrente();
        //echo "<h1>Login = $loginGer</h1>";
    }
    if($indiceRep == 'nome'){
        $varSessao = 'lista_nom_repres_ger';
    }else{
        $varSessao = 'lista_cod_repres_ger';
    }
    $lista = '';
    $listaSessao = getVarSessao($varSessao);
    //echo "<h1>Lista = $listaSessao</h1>";
    if($listaSessao == '' or $excecao == 'sp'){
        $aDados = getDados('multi','comum.pub.repres repres','hierarq."cod-depend" as rep',
            " \"nome-abrev\" = '".$loginGer."'",'multi',
            " inner join pub.\"cm-hierarquia\" hierarq on hierarq.\"cod-rep\" = repres.\"cod-rep\" ");
        //var_dump($aDados);
        if(is_array($aDados)){
            foreach($aDados as $reg){
                if($indiceRep == "nome"){
                    $aRepres = getDadosRepresEms2($reg['rep'],'"nome-abrev" as nome');
                    if(is_array($aRepres)){
                        $nomeRepres = $aRepres[0]['nome'];
                    }else{
                        $nomeRepres = '';
                    }
                }else{
                    $aRepres = getDadosRepresEms2($reg['rep'],'"cod-rep" as cod_rep');
                    if(is_array($aRepres)){
                        $nomeRepres = $aRepres[0]['cod_rep'];
                    }else{
                        $nomeRepres = '';
                    }
                }

                $lista = util_incr_valor($lista,"'$nomeRepres'");


            }
        }
        if($lista == ''){

            $lista = "''";
        }
        setVarSessao($varSessao,$lista);
    }else{
        $lista = $listaSessao;
    }
    return $lista;
}


function getNomeVarTipoUsuario()
{
    return 'glo_tipoUsuario';
}

function getNumTpUsuarioRepres()
{
    return '2';
}
function getNumTpUsuarioPreposto()
{
    return '5';
}

function getNumTpUsuarioAdmVendas()
{
    return '3';
}
function getNumTpUsuarioAdm()
{
    return '4';
}

function verifPermisAprovGerencial($codGrupo,$usuario)
{
    $log = false;
    $listaGrupos = getGruposUsuario("$usuario");
    if($listaGrupos <> ''){
        if(strstr($listaGrupos,(string) $codGrupo) <> false){
            $log = true;
        }
    }
    return $log;
}

function getNomeGerPorUsuarErp($usuarioErp){
    $nomeGer = '';
    switch($usuarioErp){
        case "CJESUS":
            $nomeGer = 'Celeno Jesus';
            break;

        case "jmarcelo":
            $nomeGer = 'Marcelo';
            break;

        case "gantunes":
            $nomeGer = 'Guilherme';
            break;

        case "Gessica":
            $nomeGer = 'Gessica';
            break;

        case "aviana":
            $nomeGer = 'Alan Viana';
            break;

    }
    return $nomeGer;
}

?>
