<?php 
function getPedidoNFIndPri($estab,$serie,$nf)
{
        
    $nrPedido = 0; 
    $aCond = array();   
    $aCond = inserirArrayCond($aCond,'nf','serie',$serie,'=');
    $aCond = inserirArrayCond($aCond,'nf','nr-nota-fis',$nf,'=');
    $aCond = inserirArrayCond($aCond,'nf','cod-estabel',$estab,'=');
    
    
    $cond = convArrayToCondSql($aCond);

    $aRegs = getDados('unico',
                      'pub."nota-fiscal" as nf',
                      '"nr-pedcli" as nr_pedido',
                      $cond,
                      'ems2'
                     );    
    return getVlIndiceArray($aRegs,'nr_pedido',0)  ;    
     
}
function getPedidoNF($estab,$nf)
{
    
    $aCond = array();
    if($estab == ''){
        $estabIni = '';
        $estabFim = 'zzzzz';
    }else{
        $estabIni = $estab;
        $estabFim = $estab;
    }
    
    $aCond = inserirArrayCond($aCond,'nf','nr-pedcli','','<>',false,true);
    $aCond = inserirArrayCond($aCond,'nf','nr-nota-fis',$nf,'=');
    $aCond = inserirArrayCond($aCond,'nf','cod-estabel',$estabIni,'>=');
    $aCond = inserirArrayCond($aCond,'nf','cod-estabel',$estabFim,'<=');
    
    $cond = convArrayToCondSql($aCond);

    $aRegs = getDados('multi',
                      'pub."nota-fiscal" as nf',
                      '"nr-pedcli" as nr_pedido',
                      $cond,
                      'ems2'
                     );    
    return convArrayMultParaLista($aRegs,'nr_pedido',true)     ;    
     
}
function getNFPedido($estab,$nrPedcli,$nomeAbrev)
{
    $aCond = array();
    $nomeAbrev = tratarAspasSimples($nomeAbrev);
    $aCond = inserirArrayCond($aCond,'nf','nr-pedcli',$nrPedcli,'=',false);
    $aCond = inserirArrayCond($aCond,'nf','nome-ab-cli',$nomeAbrev,'=');
    $aCond = inserirArrayCond($aCond,'nf','cod-estabel',$estab,'=');
    $cond = convArrayToCondSql($aCond);
    $aRegs = getDados('unico',
                      'pub."nota-fiscal" as nf',
                      '"nr-nota-fis" as nota',
                      $cond,
                      'ems2'
                     );    
    return getVlIndiceArray($aRegs,'nota','');

}

function montarFiltroNF($aFiltros,$aApelidoTb)
{ /* chaves aFiltros: 
     cod_estab,cod_emitente,nr_pedido,nf,dt_inicial,
     dt_final,cod_rep, log_faturada, log_cancelada
    */
    $aFiltroCond = array();
    $tabela = setApelidoTbFiltro($aApelidoTb,'nota-fiscal');

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
    //apenas notas que geram duplicatas
    $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'emite-duplic','1','=',true);
    //echo "log filtrar:".getVlLogico($logFiltrar) ;
    if($logFiltrar){
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-estabel',$aFiltros['cod_estab']);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-emitente',$aFiltros['cod_emitente'],'=',true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'dt-emis-nota',$aFiltros['dt_inicial'],'>=');
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'dt-emis-nota',$aFiltros['dt_final'],'<=');                
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-rep',$aFiltros['cod_rep'],'in',true );  
    }
    if(!$logNF and $logPedido){
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nr-pedido',$aFiltros['nr_pedido'],'=',true);
    }    
    if($aFiltros['nf'] <> ''){
       $listaPedidos =  getPedidoNF($aFiltros['cod_estab'],$aFiltros['nf']);
       $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nr-pedido',$listaPedidos,'in',true);
    }   
    if($aFiltros['log_faturada'] == 1 and $aFiltros['log_cancelada'] <>  1 ){
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'idi-sit-nf-eletro','3','=',true);        
    }
    if($aFiltros['log_cancelada']== 1 and $aFiltros['log_faturada'] <> 1){
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'"dt-cancela"',' null ',' is not ',true);        
    }
    return convArrayToCondSql($aFiltroCond);
}

function getHtmlSitNF($sitNota,$descricao)
{    
    // sitNota -> FATURADO/CANCELADO
    $classe= '';
    //echo "<h1>sit nota: $sitNota</h1>";
    switch(trim($sitNota)){
       case 'FATURADO':
            $classe = 'faturado';
        break;
       case 'CANCELADO':
            $classe = 'cancelado';
        break;      
    } 
    

    return ' <span class="'.$classe.'"> <i class="bi bi-card-list"></i>&nbsp;&nbsp; '.$sitNota.' | '.$descricao.'</span>';


    
}
function getFiltroNFTpUsuario($apelido,$logAplicarFiltro=false)
{
    $condicao = '';
    if($apelido == ''){
        $apelido = 'nf';
    }
    $apelido .= '.';
    switch(getVarSessao('tipo_usuario_id')){
        case getNumTipoUsuarioCliente():
            $condicao = $apelido.'"cod-emitente" = '.getVarSessao('num_cliente');
            break;
        case getNumTipoUsuarioRepresentante():
            $condicao = $apelido."\"no-ab-reppri\" = '".getVarSessao('nome_abrev_repres')."'";
            break;
        //outros tipos de usuários não tem filtro especifico
    }
    if($logAplicarFiltro){
        setCondWhere($condicao);
    }
    return $condicao;

}

function getVlFatMesCorrente()
{
    $aDt = getDatasMesCorrente();
    $aDt = $aDt[0];
    $dtIni = $aDt['dtIni'];
    $dtFim = $aDt['dtFim'];
    $aFiltro = array();
    $aFiltro = inserirArrayCond($aFiltro,'nf','dt-emis-nota',"'$dtIni' and '$dtFim'",'between',true );
    $aFiltro = inserirArrayCond($aFiltro,'nf','dt-cancela'," is null ",'',true );
    $aFiltro = inserirArrayCond($aFiltro,'nat','tipo',"1",'<>',true );
    $aFiltro = inserirArrayCond($aFiltro,'nat','cod-esp',"DP",'=' );
    $aFiltro = inserirArrayCond($aFiltro,'nf','cod-estabel',"1",'=' );

    $cond    = convArrayToCondSql($aFiltro);
    $cond    = util_incr_valor($cond,
        getFiltroProgTipoUsuario('nf',
            'nf'),
        " AND ",
        true);
    $inner = 'inner join pub."ped-venda" ped on
nf."nr-pedcli" = ped."nr-pedcli"
and nf."nome-ab-cli" = ped."nome-abrev" 
inner join pub."it-nota-fisc" item_nota
on item_nota."cod-estabel" = nf."cod-estabel"
and item_nota."serie" = nf."serie"
and item_nota."nr-nota-fis" = nf."nr-nota-fis"
inner join pub."natur-oper" nat
on nat."nat-operacao" = nf."nat-operacao"
';
    $aVl = getDados('unico',
               '"nota-fiscal" nf',
           ' coalesce(sum(item_nota."vl-tot-item")|0) as vl_tot_nota',
           $cond,
           "ems2",
            $inner
        );
   return getVlIndiceArray($aVl,'vl_tot_nota',0.0);
}
?>