<?php
//__NM____NM__FUNCTION__NM__//
function getNomeVarLoginSessao()
{
    return "glo_usr_login";
}

function getNomeVarPercParComis()
{
    return "perc_participacao_comis";
}
function getNomeVarTodosCliente()
{
    return "log_todos_cliente";
}

function getCamposUsuario()
{
    return  'priv_admin,ativo,nome,email,"tp-usuario" as tp_usuario,usuario,
		"nome-ab-cli" as nome_ab_cli,login,usuario,log_pedido_on_line,
                 log_aprovador,id,perc_participacao_comis,log_todos_clientes';
}

function validarLogin($login,$pswd)
{
    $login = retirarAcentoSimples($login);
    $erro = '';
    $slogin = sc_sql_injection($login);
    $spswd  = sc_sql_injection($pswd);
    $logSetarVariaveis = false;
    $campos = getCamposUsuario();
    $slogin = str_replace("'","",$slogin);
    $spswd  = str_replace("'","",$spswd);

    $aUsuario = getDadosUserWeb($slogin,$campos," senha = '$spswd' ");
    $vendLoja = 0;
    if(! is_array($aUsuario)){
        sc_log_add('login Falhou', "Falhou a tentativa de logar com o login:$login");
        $erro = 'Informe um usuário e uma senha válida ';
    }else{
        $reg = $aUsuario[0];
        $ativo              = $reg['ativo'];
        if($ativo <> 1){
            $erro = "Usuário Inativo";
        }
    }
    return array('array'=>$aUsuario,'erro'=>$erro);

}

function setarVarsLogin($aUsuario)
{
    //var_dump($aUsuario);
    $vendLoja = 0;
    $reg = $aUsuario[0];
    $privAdmin          = $reg['priv_admin'];
    $usr_priv_admin     = ($privAdmin == 'Y') ? TRUE : FALSE;
    //$ativo              = $reg['ativo'];
    $usr_name           = $reg['nome'];
    $usr_email          = $reg['email'];
    $tipoUsuario        = $reg['tp_usuario'];
    $nomeAbCli          = $reg['nome_ab_cli'];
    $usr_login          = strtoupper($reg['login']);
    $usuario            = $reg['usuario'];
    //$pedidoOnLine       = $reg['log_pedido_on_line'];
    $aprovador          = $reg['log_aprovador'];
    $idUsuario          = $reg['id'];
    $percPartComis      = $reg['perc_participacao_comis'];
    $logTodosCliente    = $reg['log_todos_clientes'];
    $nomeClienteIni     =  '';
    $nomeClienteFim     =  'zzzzzzzzzzzzzzzzz';
    $nomeRepresIni      = '';
    $nomeRepresFim      = 'zzzzzzzzzzzzzzzzzzzz';
    $prepostoIni        = '';
    $prepostoFim        = 'zzzzzzzzzzzzzzzzzzzz';
    $codRepIni		    = 0;
    $codRepFim		    = 99999;
    $aplicacaoPrincipal = 'container_ini_repres';

    switch($tipoUsuario)
    {
        case 1: /*Cliente*/
            $nomeClienteIni  = $nomeAbCli;
            $nomeClienteFim  = $nomeAbCli;
            break;
        case 2: /*Representante*/
            $nomeRepresIni      = 	$usr_login;
            $nomeRepresFim      = 	$usr_login;
            $codRepIni          = buscarCodRep($usr_login);
            $codRepFim          = $codRepIni;
            //echo "<h1>rep: $codRepIni</h1>";
            $classe             = getClasseVendedor($codRepIni);

            //verificar se é loja e gravar em variavel
            if(strstr(getClasseVendInterno(), $classe) <> false) {
                $vendLoja = 1;
            }
            break;

        case 5: /*Preposto*/
            $nomeRepresIni       = 	$usuario;
            $nomeRepresFim       = 	$usuario;
            $prepostoIni         = 	$usr_login;
            $prepostoFim         =  $usr_login;
            $codRepIni		  	 = buscarCodRep($usuario);
            $codRepFim		     = $codRepIni;
            break;
    }
    $lExistDb = sincrBdConsulta($idUsuario);

    $inicio_sessao = date('d-m-Y H:i:s');
    $nomeVarLogin = getNomeVarLoginSessao();
    $_SESSION[$nomeVarLogin]            = $usr_login;
    $_SESSION['glo_usr_priv_admin']     = $usr_priv_admin;
    $_SESSION['glo_usr_name']           = $usr_name;
    $_SESSION['glo_usr_email']          = $usr_email;
    $_SESSION['glo_nomeClienteIni']     = $nomeClienteIni;
    $_SESSION['glo_nomeClienteFim']     = $nomeClienteFim;
    $_SESSION['glo_nomeRepresIni']      = $nomeRepresIni;
    $_SESSION['glo_nomeRepresFim']      = $nomeRepresFim;
    $_SESSION['glo_codRepIni']          = $codRepIni;
    $_SESSION['glo_codRepFim']          = $codRepFim;
    $_SESSION['glo_prepostoIni']        = $prepostoIni;
    $_SESSION['glo_prepostoFim']        = $prepostoFim;
    $_SESSION['glo_tipoUsuario']        = $tipoUsuario;
    $_SESSION['glo_aplicacaoPrincipal'] = $aplicacaoPrincipal;
    $_SESSION['glo_inicio_sessao']      = $inicio_sessao;
    $_SESSION['glo_aprovador']          = $aprovador;

    setVarSessao(getParamVendLoja(),$vendLoja);
    setVarSessao('id_usuario',$idUsuario);
    setVarSessao(getNomeVarPercParComis(),$percPartComis);
    setVarSessao(getNomeVarTodosCliente(),$logTodosCliente);



}

function logar($login,$pswd)
{

    $vendLoja = 0;
    $aRet = validarLogin($login,$pswd);
    $aUsuario = $aRet['array'];
    $erro = $aRet['erro'];

    if($erro == ''){
      setarVarsLogin($aUsuario);
      atualizarParametros();
    }

	return $erro;

}


function getNomeRepresSessao()
{
    $nomeRepresIni = getVarSessao('glo_nomeRepresIni');
    if($nomeRepresIni == ''){
        $nomeRepresFim = 'zzzzzzzzzzzzzzzz';
    }else{
        $nomeRepresFim = $nomeRepresIni;
    }
    $aRetorno = array('nome_repres_ini' => $nomeRepresIni, 'nome_repres_fim' => $nomeRepresFim);
    return  $aRetorno;
}



function aplicarPermissoesSC($login)
{



    $sql = "SELECT 
		nome_aplicacao,
		acesso_permitido,
		insercao_permitida,
		exclusao_permitida,
		alteracao_permitida,
		exportacao_permitida,
		impressao_permitida
	      FROM PUB.grupos_aplicacoes
	      WHERE cod_grupo IN
	          (SELECT
		       cod_grupo
		   FROM
		       PUB.usuarios_grupos 
		   WHERE
		       login_usuario = '$login')
		       and (acesso_permitido ='Y' or insercao_permitida = 'Y' or 
		            exclusao_permitida = 'Y' or alteracao_permitida = 'Y' or 
		            exportacao_permitida = 'Y' or impressao_permitida = 'Y')";
    $aRegs = getRegsSqlLivre($sql,
        'nome_aplicacao,
		        acesso_permitido,
		        insercao_permitida,
		        exclusao_permitida,
		        alteracao_permitida,
		        exportacao_permitida,
		        impressao_permitida',
        'espec') ;
    if(is_array($aRegs)){
        foreach ($aRegs as $prog) {
            $nomeAplicacao   	 = $prog['nome_aplicacao'];
            $acessoPermitido 	 = $prog['acesso_permitido'] ;
            $insercaoPermitida 	 = $prog['insercao_permitida'];
            $exclusaoPermitida 	 = $prog['exclusao_permitida'];
            $alteracaoPermitida	 = $prog['alteracao_permitida'];
            $exportacaoPermitida = $prog['exportacao_permitida'];
            $impressaoPermitida  = $prog['impressao_permitida'];

            if( $acessoPermitido == 'Y')
            {
                sc_apl_status($nomeAplicacao, 'on');
                $aApl[$nomeAplicacao] = '' ;
                //echo "permitido - ".$rs->fields[0]."<br>";
            }
            else
            {   if(!isset($aApl[$nomeAplicacao])){
                    sc_apl_status($nomeAplicacao, 'off');
                //echo "não permitido - ".$rs->fields[0]."<br>";
                }else{
                    continue;
                }
            }

            sc_apl_conf($nomeAplicacao, 'insert', has_priv($insercaoPermitida));
            sc_apl_conf($nomeAplicacao, 'delete', has_priv($exclusaoPermitida));
            sc_apl_conf($nomeAplicacao, 'update', has_priv($alteracaoPermitida));
            //export
            $export_permission = 'btn_display_'. has_priv($exportacaoPermitida);
            sc_apl_conf($nomeAplicacao, $export_permission, 'xls');
            sc_apl_conf($nomeAplicacao, $export_permission, 'word');
            sc_apl_conf($nomeAplicacao, $export_permission, 'pdf');
            sc_apl_conf($nomeAplicacao, $export_permission, 'xml');
            sc_apl_conf($nomeAplicacao, $export_permission, 'csv');
            sc_apl_conf($nomeAplicacao, $export_permission, 'rtf');
            //export
            $export_permission = 'btn_display_'. has_priv($impressaoPermitida);
            sc_apl_conf($nomeAplicacao, $export_permission, 'print');
        }
        sc_log_add('login', "Login OK");
    }







	/*echo "<h1>tipo usuario: [tipoUsuario]</h1>";
	echo "<h1>nome abrev ini: [nomeRepresIni] - nome abrev fim: [nomeRepresFim] </h1>";*/

}

function has_priv($param)
{
    return ($param == 'Y' ? 'on' : 'off');

}

function limparLogin($logLimparPerfilPai=false)
{
    if($logLimparPerfilPai){
        limparVarSessao('perfil_pai');
    }
    /*sc_reset_apl_conf("ap_form_add_users");
    sc_reset_apl_conf("ap_retrieve_pswd");*/
    sc_reset_apl_status();
    sc_reset_global([usr_login], [usr_email],[usr_priv_admin],[usr_name],[nomeClienteIni],[nomeClienteFim],
        [nomeRepresIni],[nomeRepresFim],[codRepIni],[codRepFim],[prepostoIni],[prepostoFim],[tipoUsuario],
        [aplicacaoPrincipal],[inicio_sessao]);
    sc_apl_conf('ap_form_add_users', 'start', 'new');

    //unset($_SESSION[$indFinancSessao]);
    $listaVar = "glo_usr_priv_admin,glo_usr_name,
    glo_usr_email,glo_nomeClienteIni,glo_nomeClienteFim,glo_nomeRepresIni,
    glo_nomeRepresFim,glo_codRepIni,glo_codRepFim,glo_prepostoIni,
    glo_prepostoFim,glo_tipoUsuario,glo_aplicacaoPrincipal,glo_inicio_sessao,id_usuario,lista_nom_repres_ger,lista_cod_repres_ger";
    $listaVar = util_incr_valor($listaVar,getNomeVarPercParComis()) ;
    $listaVar = util_incr_valor($listaVar,getNomeVarLoginSessao()) ;
    $listaVar = util_incr_valor($listaVar,getParamVendLoja()) ;
    $listaVar = util_incr_valor($listaVar,getNomeVarIndFinan()) ;
    $listaVar = util_incr_valor($listaVar,'lista_cps_definida') ;
    limparListaCpsSessao();
    limparVarSessao($listaVar);

}
function inicioAplicacao()
{
    sc_reset_apl_conf("ap_form_add_users");
    sc_reset_apl_conf("ap_retrieve_pswd");
    sc_apl_conf('ap_form_add_users', 'start', 'new');

}
function getLoginCorrente()
{
    return getVarSessao(getNomeVarLoginSessao());

}
function getPercPartComisCorrente()
{
    return getVarSessao(getNomeVarPercParComis());
}
function getTodosClienteCorrente()
{
    return getVarSessao(getNomeVarTodosCliente());
}
function limparListaCpsSessao()
{
    $aLista =getVarSessao('lista_cps_definida');
    if(is_array($aLista)){
        $tam = count($aLista);
        for($i=0;$i<$tam;$i++){
            $variavel = $aLista[$i];
            unset($_SESSION[$variavel]);
        }
    }
}

function verifUsuarioCorrenteAdmin()
{
    $log= 0;

    $tipo = getTipoUsuarioCorrente();
    if($tipo <> 2 and $tipo <> 5){
        $log = 1;
    }
    return $log;
}
?>
