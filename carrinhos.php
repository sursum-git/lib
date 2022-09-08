<?php 

function montarFiltroCarrinho($aFiltros,$aApelidoTb)
{ /*********************************************** 
     chaves aFiltros: 
     log_rp,log_rd,it_codigo,lista_cod_refer
    *********************************************/
    $aFiltroCond    = array();
    $aFiltroLote    = array();
    $aFiltroItem    = array();
    $aFiltroRefer   = array();
    $tabela ='ob-etiqueta';
    $rp          = $aFiltroCond['log_rp'];
    $rd          = $aFiltroCond['log_rd'];
    $itCodigo   = $aFiltroCond['it_codigo'];
    $codRefer   = $aFiltroCond['lista_cod_refer'];


    $aFiltroLote        = inserirArrayCond($aFiltroLote,$tabela,'lote',$rd,'=',false,true,'or');
    $aFiltroLote        = inserirArrayCond($aFiltroLote,$tabela,'lote',$rp,'=',false,true,'or');


    $aFiltroItem        = inserirArrayCond($aFiltroLote,$tabela,'it-codigo',$itCodigo,'=',false,true,'and');
    $aFiltroCodRefer    = inserirArrayCond($aFiltroRefer,$tabela,'cod-refer',$codRefer,'=',false,true,'and');

    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroLote);
    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroItem);
    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroCodRefer);

    return convArrayMultiToCondSql($aFiltroCond);
}



?>