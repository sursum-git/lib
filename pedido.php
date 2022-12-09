<?php
//__NM____NM__FUNCTION__NM__// 
function montarFiltroPedido($aFiltros,$aApelidoTb)
{ /* chaves aFiltros: 
     cod_estab,cod_emitente,nr_pedido,nf,dt_inicial,
     dt_final,nome_abrev_rep, sit_cred, sit_preco, sit_ped,cod_rep
    */
    $aFiltroCond = array();
    $tabela =setApelidoTbFiltro($aApelidoTb,'ped-venda');
    $logNF       = false;
    $logPedido   = false;
    $logFiltrar  = true;
    if($aFiltros['nf'] <> ''){
        $logNF = true;
        $logFiltrar = false;
    } 
    if($aFiltros['nr_pedido'] <> ''){
        $logPedido = true;
        $logFiltrar = false;
    } 
    if($logFiltrar){
        $listaNomeAbrevRep = inserirAspasEmLista($aFiltros['nome_abrev_rep']);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-estabel',$aFiltros['cod_estab']);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-emitente',$aFiltros['cod_emitente'],'=',true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'dt-implant',$aFiltros['dt_inicial'],'>=');
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'dt-implant',$aFiltros['dt_final'],'<=');
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-sit-aval',$aFiltros['sit_cred'],'in',true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-sit-preco',$aFiltros['sit_preco'],'in', true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-sit-ped',$aFiltros['sit_ped'],'in',true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'no-ab-reppri',$listaNomeAbrevRep,'in',true );  
    }
    if(!$logNF){
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nr-pedido',$aFiltros['nr_pedido'],'=',true);
    }    
    if($aFiltros['nf'] <> ''){
       $listaPedidos =  getPedidoNF($aFiltros['cod_estab'],$aFiltros['nf']);
       $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nr-pedido',$listaPedidos,'in',true);
    }   
    
    return convArrayToCondSql($aFiltroCond);
}


function getDescrSitPed($sit)
{
    $retorno = '';
    switch($sit){
        case 1:
            $retorno = 'Aberto';
            break;
        case 2:
            $retorno = 'Atend.Parcial';
            break;    
        case 3:
            $retorno = 'Faturado';
            break;
        case 4:
            $retorno = 'Pendente';
            break;
        case 5:
            $retorno = 'Suspenso';
            break;
        case 6:
            $retorno = 'Cancelado';
            break;            
        case 7:
            $retorno = 'Fat.Balcão';
            break;                

    }
    return $retorno;


}
function getDescrSitAval($sit)
{
    $retorno = '';
    switch($sit){
        case 1:
            $retorno = 'Não Avaliado';    
            break;        
        case 2:
            $retorno = 'Aprovado';
            break;     
        case 3:
            $retorno = 'Avaliado';
            break;    
        case 4:
            $retorno = 'Reprovado';
            break;
        case 5:
            $retorno = 'Pend.Informação';
            break;
        case 6:
            $retorno = 'Em Analise';
            break;
        case 6:
            break;            

    }
    return $retorno;

}
function getDescrSitPreco($sit)
{
    $retorno = '';
    switch($sit){
        case 1:
            break;
        case 2:
            break;    
        case 3:
            break;
        case 4:
            break;
        case 4:
            break;
        case 6:
            break;            

    }
    return $retorno;

}
function getCodsPedAberto()
{
    return '1,2,4,5,7';
}
function getCodsFaturados()
{
    return '3';
}
function getCodsCancelado()
{
    return '6';

}
function getCodsPedNaoAval()
{
   return '1,2,5';

}
function getCodsReprov()
{
    return '4';

}
function getCodsAprov()
{
    return '3';

}

function getHtmlSitPed($sitPed,$descricao)
{    
    $classe= '';
    if(stristr(getCodsPedAberto(),$sitPed) <> false){
        $avatar = 'bi bi-cart-fill';
        $classe = 'aberto';
    }else{
        if(stristr(getCodsFaturados(),$sitPed) <> false){            
            $classe = 'faturado';
        }else{
            $classe = 'cancelado';
        }
    }
    $descrSit = getDescrSitPed($sitPed);

    return ' <span class="'.$classe.'"> <i class="bi bi-cart-fill"></i>&nbsp;&nbsp; '.$descrSit.' | '.$descricao.'</span>';


    
}

function getHtmlSitAval($sitAval)
{    /*
        bi bi-question-square-fill -> não avaliado e Limbo
        bi bi-exclamation-square-fill -> reprovado
        bi bi-check-square-fill -> aprovado

    
    */
    $classe= '';
    $avatar = '';
    if(stristr(getCodsAprov(),$sitAval) <> false){
        $avatar = 'bi bi-check-square-fill';
        $classe = 'aprovado';
    }else{
        if(stristr(getCodsReprov(),$sitAval) <> false){            
            $classe = 'reprovado';
            $avatar = 'bi bi-exclamation-square-fill';
        }else{
            $classe = 'naoavaliado';
            $avatar = 'bi bi-question-square-fill';
        }
    }
    $descrSit = getDescrSitAval($sitAval);

    return ' <span class="'.$classe.'"> <i class="'.$avatar.'"></i>&nbsp;&nbsp; Pedido '.$descrSit.'</span>';

}
function getFiltroPedidoTpUsuario($apelido='ped',$logAplicarFiltro=false)
{
    $condicao = '';
    if($apelido == ''){
        $apelido = 'ped';
    }
    $apelido .= '.';
    switch(getVarSessao('tipo_usuario_id')){
        case getNumTipoUsuarioCliente():
            $condicao = $apelido.'"cod-emitente" = '.getVarSessao('num_cliente');
            break;
        case getNumTipoUsuarioRepresentante():
            $condicao = $apelido."\"no-ab-reppri\" ='".getVarSessao('nome_abrev_repres')."'";
            break;
        //outros tipos de usuários não tem filtro especifico
    }
    if($logAplicarFiltro){
        setCondWhere($condicao);
    }
    return $condicao;


}

function getTotPedAberto():float
{
    $aFiltro = array();	 
    $aFiltro = inserirArrayCond($aFiltro,'ped','cod-sit-ped','1,2,4,5','in',true );
	$aFiltro = inserirArrayCond($aFiltro,'ped','cod-estabel','1','=',false );
	//$aFiltro = inserirArrayCond($aFiltro,'item_ped','cod-sit-item','1,2','in',true );
    $aFiltro = inserirArrayCond($aFiltro,'nat','cod-esp','DP','=',false );
	
	
    $cond    = convArrayToCondSql($aFiltro);
    $cond    = util_incr_valor($cond,getFiltroProgTipoUsuario('pedido','ped'), " AND ",
    true)        ;

    /*' coalesce(sum(item_ped."qt-pedida" * item_ped."vl-preuni")|0) as vl_tot_ped',*/
    $aVl = getDados('unico',
               '"ped-venda" as ped',
      'coalesce(sum(ped."vl-liq-abe")|0) as vl_tot_ped',
		   $cond,
           "ems2",
      'inner join pub."natur-oper" nat 
        on nat."nat-operacao" = ped."nat-operacao"'
        );
   return getVlIndiceArray($aVl,'vl_tot_ped',0.0);

}
function getTranspETpFreteUltPedCliente($nomeAbrevCliente)
{
    //echo "<h1>nome abrev cliente: $nomeAbrevCliente   </h1>";
    $aPed       = getPedETranspUltPedVendaPorCliente($nomeAbrevCliente);

    $nrPedido   = getVlIndiceArrayDireto($aPed[0],'nr_pedido',0);
    $nomeTransp = getVlIndiceArrayDireto($aPed[0],'nome_transp','');
    $codTransp  = getCodTranspPorNomeAbrev($nomeTransp);
    $tpFrete    = getTipoFretePedVendaExt($nrPedido);

    return array('cod_transp'=>$codTransp,'tp_frete'=> convDescrTpFreteParaNumero($tpFrete));


}
function getPedETranspUltPedVendaPorCliente($nomeAbrevCliente)
{
    $nomeAbrevCliente = tratarAspasSimples($nomeAbrevCliente);
    $campos = 'top 1 "nome-transp" as nome_transp,"nr-pedido" as nr_pedido';
    $aReg = getDadosUltPedVendaPorCLiente($nomeAbrevCliente,$campos);
    return $aReg;

}
function getRegPedVendaExt($chave,$valor,$campos)
{
    $aReg = getReg('espec', 'pub."ped-venda-ext"',$chave,$valor,
        $campos);
    return $aReg;
}
function getTipoFretePedVendaExt($nrPedido)
{
    $aReg = getRegPedVendaExt('"nr-pedido"',$nrPedido,'"tp-frete" as tp_frete');
    return getVlIndiceArray($aReg,'tp_frete','');
}

function getDadosUltPedVendaPorCLiente($nomeAbrevCliente,$campos='')
{
    $nomeAbrevCliente = tratarAspasSimples($nomeAbrevCliente);
    $aReg = getDados('unico','pub."ped-venda"',
        $campos," \"nome-abrev\" = '$nomeAbrevCliente' 
    and \"cod-sit-ped\" <> 6 order by \"nr-pedido\" desc",
        'ems2');
    return $aReg;
}

function getNomeTranspUltPedVendaPorCLiente($nomeAbrevCliente)
{
    $nomeAbrevCliente = tratarAspasSimples($nomeAbrevCliente);
    $aReg = getDados('unico','pub."ped-venda"',
    '"nome-transp" as nome_transp,"nr-pedido" as nr_pedido'," \"nome-abrev\" = '$nomeAbrevCliente' 
    and \"cod-sit-ped\" <> 6 order by \"nr-pedido\" desc",
    'ems2');
    return getVlIndiceArray($aReg,'nome_transp','');
}
function getCodTranspUltPedVendaPorCLiente($nomeAbrevCliente)
{
    $nomeTransp = getNomeTranspUltPedVendaPorCLiente($nomeAbrevCliente);
    return getCodTranspPorNomeAbrev($nomeTransp);
}

?>

