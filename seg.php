<?php

function limparVarsSessao()
{
    sc_reset_global([usr_login], [usr_email], [empresa], [modelo_menu],[tipo_usuario_id],[cod_usuario_erp],[num_repres],[nome_abrev_repres],
                    [nome_abrev_repres_ini],[nome_abrev_repres_fim],[login_ad],[num_cliente]);
    limparVarSessao('usr_login,empresa,modelo_menu,tipo_usuario_id,cod_usuario_erp,num_repres,nome_abrev_repres,nome_abrev_repres_ini,nome_abrev_repres_fim,login_ad,num_cliente') ;

}
function logar($loginParam,$senhaParam,$loginTroca)
{
    if(!$logTroca){
       $aRet =  validarLogin($loginParam,$senhaParam);
       if(isset($aRet['erro_login'])){
            exibirMsgErro($aReg['erro_login']);
       }else{
            aplicarPermissoes($loginParam);
       }
    }    
}

function validarLogin($loginParam,$senhaParam,$loginTroca='')
{
    $retorno = array();
    if(sc_logged_is_blocked()){
         sc_error_exit();   
    }
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
        $vlsChave = "$sLogin,$spswd";

    }
    $aReg = getReg('seg',
                    'sec_users a',
                $cpsChave,
                $vlsChave,
                'activation_code,
                active,
                name,
                email,
                group_id,
                id_empresa,
                menu_tipo,
                tipo_usuario_id,
                cod_usuario_erp,
                num_repres,
                login_ad,
                num_cliente,
                priv_admin');
    if(!is_array($aReg)){
        sc_log_add('login Fail', {lang_login_fail} . $loginParam);
        sc_logged_in_fail($loginParam);        
        $retorno['erro_login'] = {lang_error_login};
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
            $num_cliente        = tratarNumeo(getVlIndiceArrayDireto($aReg,'num_cliente',0));          
            if($num_repres <> 0){
                $nome_abrev_repres         = getNomeAbrevRepres($num_repres);
                $nome_abrev_repres_ini     = $nome_abrev_repres;
                $nome_abrev_repres_fim     = $nome_abrev_fim;
            }    
            if($menuTipo == 'V'){
                [modelo_menu] = 'menu_lateral';
            }else{
                [modelo_menu] = 'quantum_navbar_menu';
            }

            //atribuição variável global

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
            $retorno['erro_login'] = {lang_error_not_active};                        
        }   
    }           
}           


function aplicarPermissoes()
{
    /* adaptar */
    $sql = "SELECT 
		app_name,
		priv_access,
		priv_insert,
		priv_delete,
		priv_update,
		priv_export,
		priv_print
	      FROM sec_groups_apps
	      WHERE group_id IN
	          (SELECT
		       group_id
		   FROM
		       sec_users_groups 
		   WHERE
		       login = '". [usr_login] ."')";
		
	
sc_select(rs, $sql);

$arr_default = array(
					'access' => 'off',
					'insert' => 'off',
					'delete' => 'off',
					'update' => 'off',
					'export' => 'btn_display_off',
					'print'  => 'btn_display_off',
					);
if ({rs} !== false)
{
	$arr_perm = array();
	while (!$rs->EOF)
	{
		$app = $rs->fields[0];
		
		if(!isset($arr_perm[$app]))
		{
		   $arr_perm[$app] = $arr_default;
		}
		if( $rs->fields[1] == 'Y')
		{
			$arr_perm[$app][ 'access' ] = 'on';
		}
		if($rs->fields[2] == 'Y')
		{
			$arr_perm[$app][ 'insert' ] = 'on';
		}
		if($rs->fields[3] == 'Y')
		{
			$arr_perm[$app][ 'delete' ] = 'on';
		}
		if($rs->fields[4] == 'Y')
		{
			$arr_perm[$app][ 'update' ] = 'on';
		}
		if($rs->fields[5] == 'Y')
		{
			$arr_perm[$app]['export'] =  'btn_display_on';
		}
		if($rs->fields[6] == 'Y')
		{
			$arr_perm[$app]['print'] =  'btn_display_on';
		}


		$rs->MoveNext();	
	}
	$rs->Close();
		   
	foreach($arr_perm as $app => $perm)
	{
		sc_apl_status($app, $perm['access']);
		
		sc_apl_conf($app, 'insert', $perm['insert']);
		sc_apl_conf($app, 'delete', $perm['delete']);
		sc_apl_conf($app, 'update', $perm['update']);
		sc_apl_conf($app, $perm['export'], 'xls');
		sc_apl_conf($app, $perm['export'], 'word');
		sc_apl_conf($app, $perm['export'], 'pdf');
		sc_apl_conf($app, $perm['export'], 'xml');
		sc_apl_conf($app, $perm['export'], 'csv');
		sc_apl_conf($app, $perm['export'], 'rtf');
		sc_apl_conf($app, $perm['print'], 'print');

	}

				
	if(sc_logged({login})):
		sc_log_add('login', {lang_login_ok});
		sc_user_logout('logged_user', 'logout', 'app_Login');
        
		sc_redir([modelo_menu]);	
	endif;
  }
}




function setFiltroTituloTpUsuario()
{

}
function setFiltroCotaTpUsuario()
{

}

function setFiltroProgTipoUsuario($programa)
{
    switch($tabela){
          case 'cliente': 
                setFiltroClienteTpUsuario();
            break;
           case 'pedido' :
                setFiltroPedidoTpUsuario();
             break;  
           case 'nf' :
                setFiltroNFTpUsuario();
             break;    
           case 'titulo' :
               setFiltroTituloTpUsuario();
             break;      
           case 'cota' :
              setFiltroCotaTpUsuario();
           break;        



    }

}
?>