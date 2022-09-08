<?php 

function montarFiltroCarrinho($aFiltros,$aApelidoTb)
{ /*********************************************** 
     chaves aFiltros: 
     rp,_rd,it_codigo,lista_cod_refer
    *********************************************/
    $aFiltroCond    = array();
    $aFiltroLote    = array();
    $aFiltroItem    = array();
    $aFiltroRefer   = array();
    $tabela ='ob-etiqueta';
    $rp          = $aFiltros['rp'];
    $rd          = $aFiltros['rd'];
    $itCodigo   = $aFiltros['it_codigo'];
    $codRefer   = $aFiltros['lista_cod_refer'];


    $aFiltroLote        = inserirArrayCond($aFiltroLote,$tabela,'lote',$rd,'=',false,true,'or');
    $aFiltroLote        = inserirArrayCond($aFiltroLote,$tabela,'lote',$rp,'=',false,true,'');


    $aFiltroItem        = inserirArrayCond($aFiltroItem,$tabela,'it-codigo',$itCodigo,'=',false,true,'and');
    $aFiltroRefer       = inserirArrayCond($aFiltroRefer,$tabela,'cod-refer',$codRefer,'=',false,true,'and');

    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroLote);
    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroItem);
    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroRefer);
    echo "<pre>";
    var_dump($aFiltroCond);

    return convArrayMultiToCondSql($aFiltroCond);
}



?>