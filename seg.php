<?php

function limparVarsSessao()
{

    sc_reset_global([usr_login], [usr_email], [empresa], [modelo_menu],[tipo_usuario_id],[cod_usuario_erp],[num_repres],[nome_abrev_repres],
                    [nome_abrev_repres_ini],[nome_abrev_repres_fim],[login_ad],[num_cliente],
    [num_repres_ini],[num_repres_fim]);
    /*limparVarSessao('usr_login,empresa,modelo_menu,tipo_usuario_id,cod_usuario_erp,
    num_repres,nome_abrev_repres,nome_abrev_repres_ini,nome_abrev_repres_fim,login_ad,num_cliente') ;*/

}
function logar($loginParam,$senhaParam,$loginTroca)
{
    if(!$loginTroca){
       $erro =  validarLogin($loginParam,$senhaParam);
       if($erro <> ""){
            exibirMsgErro($erro);
       }else{
            aplicarPermissoes($loginParam);
       }
    }    
}

function validarLogin($loginParam,$senhaParam,$loginTroca='')
{
    $erro = "";
    if(sc_logged_is_blocked()){
        $erro = "Usuário Bloqueado";
    }else{
        $nome_abrev_repres        = '';
        $nome_abrev_repres_ini    = '';
        $nome_abrev_repres_fim    = 'zzzzzzzzzzzzzz';
        $slogin = sc_sql_injection($loginParam);
        $spswd = sc_sql_injection((hash("md5",$senhaParam)));
        if($loginTroca <> ''){
            $cpsChave = 'login';
            $vlsChave = $slogin;
            $login_troca = $loginTroca;
            sc_set_global($login_troca);
            setVarSessao('login_troca',$login_troca);

        }else{
            $cpsChave = 'login,pswd';
            $vlsChave = "$slogin,$spswd";

            $aReg = getReg('seg',
                'sec_users a',
                $cpsChave,
                $vlsChave,
                'activation_code,
                active,
                name,
                email,                
                id_empresa,
                menu_tipo,
                tipo_usuario_id,
                cod_usuario_erp,
                num_repres,
                login_ad,
                num_cliente,
                priv_admin');


        }
        if(!is_array($aReg)){
            sc_log_add('login Fail', {lang_login_fail} . $loginParam);
            sc_logged_in_fail($loginParam);
            $erro= {lang_error_login};
    }else{
            $aReg = $aReg[0];
            $ativo     = getVlIndiceArrayDireto($aReg,'active','');
            if($ativo == 'Y' or $loginTroca <> ''){
                $usr_login          = getVlIndiceArrayDireto($aReg,'login','');
                $usr_priv_admin     = getVlIndiceArrayDireto($aReg,'priv_admin','') == 'Y' ? TRUE : FALSE;
                $usr_name           = getVlIndiceArrayDireto($aReg,'name','');
                $usr_email          = getVlIndiceArrayDireto($aReg,'login','');
                $emp                = getVlIndiceArrayDireto($aReg,'id_empresa',0);
                $menuTipo           = getVlIndiceArrayDireto($aReg,'menu_tipo','');
                $tipo_usuario_id    = getVlIndiceArrayDireto($aReg,'tipo_usuario_id',0);
                $cod_usuario_erp    = getVlIndiceArrayDireto($aReg,'cod_usuario_erp','');
                $num_repres         = tratarNumero(getVlIndiceArrayDireto($aReg,'num_repres',0));
                $login_ad           = getVlIndiceArrayDireto($aReg,'login_ad','');
                $num_cliente        = tratarNumero(getVlIndiceArrayDireto($aReg,'num_cliente',0));
                $grupos             = getGruposLogin($usr_login);
                if($num_repres <> 0){
                    $nome_abrev_repres         = getNomeAbrevRepres($num_repres);
                    $nome_abrev_repres_ini     = $nome_abrev_repres;
                    $nome_abrev_repres_fim     = $nome_abrev_repres;
                    $num_repres_ini            = $num_repres;
                    $num_repres_fim            = $num_repres;
                }else{
                    $nome_abrev_repres         = "";
                    $nome_abrev_repres_ini     = "";
                    $nome_abrev_repres_fim     = "zzzzzzzzzzzzzzzz";
                    $num_repres_ini            = 0;
                    $num_repres_fim            = "9999999";
                }
                if($menuTipo == 'V'){
                    [modelo_menu] = 'menu_lateral';
                }else{
                    [modelo_menu] = 'quantum_navbar_menu';
                }

                //atribuição variável global

                sc_set_global($grupos);
                setVarSessao('grupos',$grupos);

                sc_set_global($usr_login);
                setVarSessao('usr_login',$usr_login);

                sc_set_global($usr_priv_admin);
                setVarSessao('usr_priv_admin',$usr_priv_admin);

                sc_set_global($usr_name);
                setVarSessao('usr_name',$usr_name);

                sc_set_global($usr_email);
                setVarSessao('usr_email',$usr_email);

                sc_set_global($tipo_usuario_id);
                setVarSessao('tipo_usuario_id',$tipo_usuario_id);

                sc_set_global($cod_usuario_erp);
                setVarSessao('cod_usuario_erp',$cod_usuario_erp);

                sc_set_global($num_repres);
                setVarSessao('num_repres',$num_repres);

                sc_set_global($num_repres_ini);
                setVarSessao('num_repres_ini',$num_repres_ini);

                sc_set_global($num_repres_fim);
                setVarSessao('num_repres_fim',$num_repres_fim);

                sc_set_global($nome_abrev_repres);
                setVarSessao('nome_abrev_repres',$nome_abrev_repres);

                sc_set_global($nome_abrev_repres_ini);
                setVarSessao('nome_abrev_repres_ini',$nome_abrev_repres_ini);

                sc_set_global($nome_abrev_repres_fim);
                setVarSessao('nome_abrev_repres_fim',$nome_abrev_repres_fim);

                sc_set_global($login_ad);
                setVarSessao('login_ad',$login_ad);

                sc_set_global($num_cliente);
                setVarSessao('num_cliente',$num_cliente);

            }else{
                $erro = {lang_error_not_active};
            }
        }
    }
    return $erro;
}

function getGruposLogin($login)
{
    $aGrupos = getDados('multi','sec_users_groups','group_id',
    " login = '$login' ",'seg');
    return convArrayMultParaLista($aGrupos,'group_id');

}
function aplicarPermissoes($login)
{

    $sql = "SELECT app_name,priv_access,priv_insert,priv_delete,priv_update,priv_export,priv_print    FROM sec_groups_apps
	      WHERE group_id IN(SELECT group_id  FROM  sec_users_groups  WHERE login = '$login')";
    $log = '<table><th><td>Aplicação</td><td>Acesso</td><td>Inserir</td><td>Deletar</td><td>Alterar</td><td>Exportar</td><td>Imprimir</td></th>';
    $aRegs = getRegsSqlLivre($sql,
    'app_name,priv_access,priv_insert,priv_delete,priv_update,priv_export,priv_print','seg');
     if(is_array($aRegs)){
         foreach($aRegs as $reg){
             $appName           = $reg['app_name'];
             $privAccess        = $reg['priv_access'];
             $privInsert        = $reg['priv_insert'];
             $privDelete        = $reg['priv_delete'];
             $privUpdate        = $reg['priv_update'];
             $privExport        = $reg['priv_export'];
             $privPrint         = $reg['priv_print'];
             $termo             = "<tr><td>$appName</td><td>$privAccess</td><td>$privInsert</td><td>$privDelete</td><td>$privUpdate</td>
                                    <td>$privExport</td><td>$privPrint</td></tr> ";
             $log = util_incr_valor($log,$termo,"");
             if(strtolower($privAccess) == 'y'){
                 sc_apl_status($appName, 'on');
             }

             if(strtolower($privInsert) == 'y'){
                 $permInsert = 'on';
             }else{
                 $permInsert = 'off';
             }

             if(strtolower($privDelete) == 'y'){
                 $permDelete = 'on';
             }else{
                 $permDelete = 'off';
             }

             if(strtolower($privUpdate) == 'y'){
                 $permUpdate = 'on';
             }else{
                 $permUpdate = 'off';
             }


             sc_apl_conf($appName, 'insert',$permInsert);
             sc_apl_conf($appName, 'delete',$permDelete);
             sc_apl_conf($appName, 'update',$permUpdate);




             if(strtolower($privExport) == 'y'){
                 $permExport = 'btn_display_on';
             }else{
                 $permExport = 'btn_display_off';
             }
             sc_apl_conf($appName, $permExport, 'xls');
             sc_apl_conf($appName, $permExport, 'word');
             sc_apl_conf($appName, $permExport, 'pdf');
             sc_apl_conf($appName, $permExport, 'xml');
             sc_apl_conf($appName, $permExport, 'csv');
             sc_apl_conf($appName, $permExport, 'rtf');

             if(strtolower($privPrint) == 'y'){
                 $permPrint = 'btn_display_on';
             }else{
                 $permPrint = 'btn_display_off';
             }
             sc_apl_conf($appName, $permPrint, 'print');

         }
     }
     $log = util_incr_valor($log,'</table>','');
     $logMobile = verificMobile();
     if($logMobile){
        [modelo_menu] = 'bl_inicio_mobile';
     }else{
        [modelo_menu] = 'menu_tear';
     }

	/*if(sc_logged({login})):
		sc_log_add('login', {lang_login_ok});
		sc_user_logout('logged_user', 'logout', 'app_Login');
	endif;*/
    $lArquivoOk = gravarConteudoArquivo("$login.html",$log);
    return $log;

}
/*function convYToPerm($tipo,$valor)
{
    $retorno = '';
    switch (strtolower($tipo)){
        case 'acao':
            if(strtolower($valor) == 'y'){
                $retorno =  'on';
            }else{
                $retorno = 'off';
            }
            break;
        case 'botao':
            if(strtolower($valor) == 'y'){
                $retorno =  'btn_display_on';
            }else{
                $retorno =  'btn_display_off';
            }
            break;
    }
    return $retorno;
}*/



function setFiltroTituloTpUsuario($apelido)
{

}
function setFiltroCotaTpUsuario()
{

}

function getFiltroProgTipoUsuario($programa,$apelido='')
{
    $retorno = '';
    switch($programa){
          case 'cliente':
                $retorno = getFiltroClienteTpUsuario($apelido);
            break;
           case 'pedido' :
               $retorno = getFiltroPedidoTpUsuario($apelido);
             break;  
           case 'nf' :
               $retorno = getFiltroNFTpUsuario($apelido);
             break;    
           case 'titulo' :
              // $retorno = setFiltroTituloTpUsuario();
             break;      
           case 'cota' :
             //  $retorno = setFiltroCotaTpUsuario();
           break;
        case 'carrinho' :
            $retorno = getFiltroCarrinhoTpUsuario($apelido);
            break;
        case 'reserva' :
            $retorno = getFiltroReservaTpUsuario($apelido);
            break;
        case 'peds_web' :
            $retorno = getFiltroPedsWebTpUsuario($apelido);
            break;
    }
    return $retorno;
}

function getUsuarioCorrente($login='')
{
    $usuario = '';
    if(isset([usr_login]) and $login == ''){
        $usuario =  [usr_login];
    }
    return $usuario;
}
function getCodRepresCorrente()
{
    return getVarSessao('num_repres');

}
function getNumTipoUsuarioCliente()
{
    return 1;
}

function getNumTipoUsuarioRepresentante()
{
    return 2;
}
function getNumTipoUsuarioAdmVenda()
{
    return 3;
}
function getNumTipoUsuarioDiretoria()
{
    return 4;
}
function getNumTipoUsuarioTI()
{
    return 5;
}
?>