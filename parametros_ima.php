<?php
//__NM____NM__FUNCTION__NM__//
//__NM____NM__FUNCTION__NM__//


function buscarParametroIma($parametro,$vlPadrao='')
{
    if(isset($_SESSION['im_param'][$parametro])){
       $valor = $_SESSION['im_param'][$parametro];
       //echo "<h1>buscou o valor do parametro $parametro da sessão? sim - $valor</h1>";

    }else{
        //echo "<h1>buscou o valor do parametro $parametro da sessão? nao</h1>";
        $valor = '';
        $tabela = 'pub."im-param"';
        $campos = '"val-param"';
        $condicao = "\"cod-param\" = '$parametro'";
        //echo "<h1>oi antes</h1>";
        $aRetorno = retornoSimplesTb01($tabela,$campos,$condicao,"espec");
        //echo "<h1>oi depois</h1>";
        if(is_array($aRetorno)){
            $valor = $aRetorno[0]['"val-param"'];
            //echo "<h1>Parametro: $parametro encontrado? sim - $valor</h1>";

        }else{
            $valor = $vlPadrao;
            //echo "<h1>Parametro: $parametro encontrado? nao - vl.padrao: $valor</h1>";
        }
        $_SESSION['im_param'][$parametro] = $valor;
    }
    //echo "<h1>oi</h1>";
    return $valor;
}

function getMinutosVenctoPe()
{
    //tratamento para tempo especifico da loja
    $listaGrupos = getGruposUsuario(getLoginCorrente());
    //echo "<h1>login atual:[usr_login]</h1>";
    if(strstr($listaGrupos,'2')<> false){ //2 = repres int
        //echo "<h1>entrei no parametro PORTAL_MINUTOS_DESALOCAR_PE_LOJA/h1>";
      $parametro = 'PORTAL_MINUTOS_DESALOCAR_PE_LOJA';
    } else{
        //echo "<h1>não olhei o parametro da LOJA </h1>";
        $parametro = 'PORTAL_MINUTOS_DESALOCAR_PE';
    }

    $ret = buscarParametroIma($parametro);
    //echo "<h1>qt.minutos: $ret</h1>";
    return $ret;

}

function getMinutosVenctoPi()
{
    $parametro = 'PORTAL_MINUTOS_DESALOCAR_PI';
    $ret = buscarParametroIma($parametro);
    return $ret;
}
function getHabilitarBonusRepres()
{
    $logBonus = 0;
    $param = buscarParametroIma('habilitar_bonus_comissao');
    if($param <> ''){
        $logBonus = $param;
    }
    return $logBonus;
}
function getBase()
{
    $base = '';
    $param = buscarParametroIma('base');
    if($param <> ''){
        $base = $param;
    }
    return $base;
}
function getMudancaMoeda()
{
    $logMudaMoeda = 0;
    $param = buscarParametroIma('pv_mudar_moeda');
    if($param <> ''){
        $logMudaMoeda = $param ;
    }
    return $logMudaMoeda;
}
function getParamPrecoTbERP()
{
    $param = buscarParametroIma('buscar_preco_tb_erp',1);
    return $param;
}

function getPrecoTbLiquidaIma()
{
    $param = buscarParametroIma('buscar_preco_tb_liquida_ima',1);
    return $param;
}
function getParamGrupoAvalAdm()
{
    $param = buscarParametroIma('grupo_aval_adm',12);
    return $param;

}
function getPercRedTbRubi()
{
    $param = buscarParametroIma('perc_reduc_tb_rubi',50);
    return $param;


}
function getIdTbRubi()
{
    $param = buscarParametroIma('tb_rubi_id',2);
    return $param;

}
function getAutLogDb()
{
    $param = buscarParametroIma('aut_log_db','670477Im@');
    return $param;
}
function getUserLogDb()
{
    $param = buscarParametroIma('user_log_db','sa');
    return $param;
}
function credBdConsultas()
{
    $param = buscarParametroIma('cred_bd_consulta','670477Im@');
    return $param;

}
function getDirArqOutlet()
{
    $param = buscarParametroIma('dir_arq_outlet','');
    return $param;

}
function getCaminhoArqCampanha()
{
    $param = buscarParametroIma('dir_arq_campanha',
        '/var/www/clients/client1/web2/web/iol_homolog/_lib/file/doc');
    return $param;
}
function getVlMinFreteCIFSudeste($moeda)
{
    if($moeda == 1 or $moeda == 'real'){
        $param = buscarParametroIma('vl_min_cif_sudeste',
            '1500');
    }else{
        $param = buscarParametroIma('vl_min_cif_sudeste_dolar',
            '300');

    }
    return $param;
}
function getVlMinFreteCIF($moeda)
{
    if($moeda == 1 or $moeda == 'real'){
        $param = buscarParametroIma('vl_min_cif',
            '2500');
    }else{
        $param = buscarParametroIma('vl_min_cif_dolar',
            '600');

    }
    return $param;
}
function getDirTmpBook()
{
    $param = buscarParametroIma('dir_tmp_book',
        '/var/www/clients/client1/web2/web/tmp');
    return $param;

}
function getUrlServico()
{
    $param = buscarParametroIma('url_api_totvs',
        '192.168.0.38:8180/api/');
    return $param;
}
function getUserApiTotvs()
{
    $param = buscarParametroIma('usuario_api_totvs',
        'apitotvs');
    return $param;

}
function getSenhaApiTotvs()
{
    $param = buscarParametroIma('senha_api_totvs',
        'api');
    return $param;
}
function getVersaoApiHomolog()
{
    $param = buscarParametroIma('versao_api_homolog',
        'v99');
    return $param;

}

function getApiHistAvalPedVenda()
{
    $param = buscarParametroIma('api_histAvalPedVenda',
        'pdp/v1/histAvalPedVenda');
    return $param;

}

function getAprovGerenciaPedWebPe()
{
    $param = buscarParametroIma('aprovar_ger_ped_web_pe',
        '1');
    return $param;

}

function getAprovGerenciaPedWebPi()
{
    $param = buscarParametroIma('aprovar_ger_ped_web_pi',
        '1');
    return $param;

}

function getAprovGerenciaPedWeb($tipoPed)
{
    if($tipoPed == 'pe'){
        $ret =getAprovGerenciaPedWebPe();
    }else{
      $ret = getAprovGerenciaPedWebPi();
    }
    return $ret;
}

function getParametros()
{
    $aRet = getDados('multi',
                    'pub."im-param"',
                    '"cod-param","val-param"',
                    '1=1',
            'api');
    return $aRet;
}

function atualizarParametros()
{
    limparVarSessao(getNomeVarParams());
    setParamsSessao();
}
function setParamsSessao()
{
    $aParams = getParametros();
    //var_dump($aParams);
    foreach($aParams as $reg)
    {
        $codParam = $reg['cod-param'];
        $vlParam  = $reg['val-param'];
        //echo "<h1>$codParam - $vlParam</h1>";
        setVarSessao($codParam,$vlParam,0,0,getNomeVarParams());

    }
}
function getNomeVarParams()
{
    return 'im_param';
}
function getConsiderarDepositoFechado()
{
    $param = buscarParametroIma('considerar_deposito_fechado','1');
    return $param;
}
function getEstabDepositoFechado()
{
    $param = buscarParametroIma('cod_estab_deposito_fechado','504');
    return $param;
}

function getCodDeposDepositoFechado()
{
    $param = buscarParametroIma('cod_depos_deposito_fechado','ter');
    return $param;
}
function getDirAnexosEmail($estab)
{
    $param = buscarParametroIma('dir_anexo_email_'.$estab,'/var/www/clients/client1/web2/web/dfe');
    return $param;
}
function getDirRelAnexosEmail($estab)
{
    $param = buscarParametroIma('dir_rel_anexo_email_'.$estab,'../../dfe');
    return $param;
}

function getCaixaNFe($estab)
{
    $param = buscarParametroIma('caixa_nfe_'.$estab,'{outlook.office365.com:993/imap/ssl}INBOX');
    return $param;
}
function getUsuarioCaixaNFe($estab){
    $param = buscarParametroIma('usr_caixa_nfe_'.$estab,'nfe.medtextil@imatextil.com.br');
    return $param;
}

function getSenhaCaixaNFe($estab){
    $param = buscarParametroIma('senha_caixa_nfe_'.$estab,'IMATEXTIL@0123');
    return $param;
}
function getQtDiasBuscaDocManif()
{
    $param = buscarParametroIma('qt_dias_busca_doc_manif',90);
    return $param;
}
?>