<?php 

function montarFiltroPedido($aFiltros,$aApelidoTb)
{ /*********************************************** 
     chaves aFiltros: 
     log_rp,log_rd,it_codigo,lista_cod_refer
    *********************************************/
    $aFiltroCond = array();
    $tabela ='ob-etiqueta';
    $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nr-pedido',$listaPedidos,'in',true);
        

    
    return convArrayToCondSql($aFiltroCond);
}



?>