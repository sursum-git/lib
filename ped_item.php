<?php
//__NM____NM__FUNCTION__NM__//
function getItensPedVenda($nrPedido)
{
    $aRet = getDados('multi',
             ' pub."ped-venda" ped,  pub."ped-item" item',
             '"it-codigo" as it_codigo,"cod-refer" as cod_refer,"qt-pedida" as qt_pedida,
              "vl-tot-it" as vl_tot_it',
              " ped.\"nr-pedido\" = $nrPedido and  ped.\"nr-pedcli\" = item.\"nr-pedcli\"
                and ped.\"nome-abrev\" = item.\"nome-abrev\" ",
        'med');
    return $aRet;

}
function getItensAtivosPedVenda($nrPedido)
{
    $aRet = getDados('multi',
        ' pub."ped-venda" ped,  pub."ped-item" item',
        '"it-codigo" as it_codigo,"cod-refer" as cod_refer,"qt-pedida" as qt_pedida,
              "vl-tot-it" as vl_tot_it',
        " ped.\"nr-pedido\" = $nrPedido and  ped.\"nr-pedcli\" = item.\"nr-pedcli\"
                and ped.\"nome-abrev\" = item.\"nome-abrev\" and item.\"cod-sit-item\" <> 6 ",
        'med');
    return $aRet;

}


function getAlocPedsItemRef($itCodigo,$codRefer,$container)
{
    if($container == 0){
        $tipoPedido = 'PE';
        $containerIni = 0;
        $containerFim = 999999;
    }else{
        $tipoPedido = 'PI';
        $containerIni  = $container;
        $containerFim  = $container;
    }
    $aPed = array();
    $lAchou = false;
    $sitsAlocado = getSitsPedWebAlocados();
    $sql = "
        select  4 as situacao, ped_venda.\"dt-implant\" as data, ped_venda.\"nr-pedido\" as pedido , 
        ped_venda.\"cod-emitente\" as cod_cliente, ped_item.\"it-codigo\" as item ,
    ped_item.\"cod-refer\" as refer , ped_item.\"qt-pedida\"  as qt_pedida
    from med.pub.\"ped-item\" ped_item 
    inner join med.pub.\"ped-venda\" ped_venda
    on ped_venda.\"nome-abrev\" = ped_item.\"nome-abrev\"
    and ped_venda.\"nr-pedcli\" = ped_item.\"nr-pedcli\"
    inner join espec.pub.\"ped-venda-ext\" ext
    on ext.\"cod-estabel\" = ped_venda.\"cod-estabel\"
    and ext.\"nr-pedido\" = ped_venda.\"nr-pedido\"
    where ped_item.\"it-codigo\" = '$itCodigo'
    and   ped_item.\"cod-refer\" = '$codRefer'
    and    ped_venda.\"tp-pedido\"   =  '$tipoPedido'
    and   ped_item.\"cod-sit-item\" = 1
    and ext.\"nr-container\" >=  $containerIni
    and ext.\"nr-container\" <=  $containerFim
    union
    select ped.ind_sit_ped_web as situacao, to_date(to_char(ped.dt_hr_registro)) as data,ped.ped_web_id as pedido, ped.cliente_id as cod_cliente, itens.it_codigo as item,
    itens.cod_refer as refer, itens.qt_pedida as qt_pedida from espec.pub.itens_ped_web itens
    inner join espec.pub.peds_web ped 
    on ped.ped_web_id = itens.ped_web_id
    where ped.ind_sit_ped_web in ($sitsAlocado)
    and itens.it_codigo = '$itCodigo'
    and itens.cod_refer = '$codRefer'
    and ped.cod_tipo_pedido = '$tipoPedido'
    and ped.nr_container >=	$containerIni
    and ped.nr_container <= $containerFim
        ";
    sc_select(peds,$sql,"multi");
    if ({peds} === false)
    {
        echo "getAlocPedsItemRef. Messagem de Erro =". {peds_erro};
    }
    else
    {
        while (!$peds->EOF)
        {
            $sit        = $peds->fields[0];
            $data       = $peds->fields[1];
            $nrPedido   = $peds->fields[2];
            $emitente   = $peds->fields[3];
            $item       = $peds->fields[4];
            $refer      = $peds->fields[5];
            $qtPedida   = $peds->fields[6];
            $aPed[]     = array('cod_situacao' => $sit,
                                 'data'        => $data,
                                'cod_emitente' => $emitente,
                                'nr_pedido'    => $nrPedido,
                                'it_codigo'    => $item,
                                'cod_refer'    => $refer,
                                'qt_pedida'    => $qtPedida);
            $lAchou = true;

            $peds->MoveNext();
        }
        $peds->Close();

    }
    if($lAchou == false){
        $aPed = '';
    }
    return $aPed;
}
function getTbAlocPedsItemRef($item,$refer,$tipoPedido)
{
     $aPed = getAlocPedsItemRef($item,$refer,$tipoPedido);
     $qtTotal = 0;
     $tb   = '';
     if(is_array($aPed)){
         $tb .='<table width="100%" class="table table-striped">
				<tr><td>Situação</td><td>Data</td><td>Emitente</td><td>Nr.Pedido</td><td>Item</td><td>Refer.</td><td>Qt.Pedida</td><tr>';
         $tam = count($aPed);
         for($i=0;$i< $tam; $i++){
             $sit        = $aPed[$i]['cod_situacao'];
             $sit        .="-".getDescrIndSitPed($sit);
             $data       = $aPed[$i]['data'];
             $data       = sc_date_conv($data,"yyyy-mm-dd","dd/mm/yyyy");
             $nrPedido   = $aPed[$i]['nr_pedido'];
             $itCodigo   = $aPed[$i]['it_codigo'];
             $itCodigo   .="-".getDescrItem($itCodigo);
             $refer      = $aPed[$i]['cod_refer'];
             $qtPedida   = $aPed[$i]['qt_pedida'];
             $qtTotal   += $qtPedida;
             $qtPedida   = formatarNumero($qtPedida,2) ;
             $emitente   = $aPed[$i]['cod_emitente'];
             $emitente   = getNomeAbrevCliente($emitente,true);

             $tb.="<tr><td>$sit</td><td>$data</td><td>$emitente</td><td>$nrPedido</td><td>$itCodigo</td><td>$refer</td><td style='text-align:right'>$qtPedida</td></tr>";

         }
         $qtTotal = formatarNumero($qtTotal);
         $tb.= "<tr><td colspan='6'>Quantidade TOTAL</td><td style='font-weight:900;'>$qtTotal</td></tr></table>";
     }
     return $tb;
}
function getQtVendidaPorItemRefContainer($container,$itCodigo,$codRefer)
{
    $qtTotal = 0;
    $vlTotal = 0;
    //IMPORTANTE: o campo vl-preori tem o valor cheio, sem o desconto da prioridade
    $aReg = getDados('multi','med.pub."ped-venda" pedido','ped_item."qt-atendida" as qt_atendida,
    ped_item."vl-tot-it" as vl_tot_item,pedido."cod-priori", ped_item."vl-preori" as vl_preori',
        "ped_ext.\"nr-container\" = $container and ped_item.\"it-codigo\" = '$itCodigo'
         and ped_item.\"cod-refer\" = '$codRefer' and ped_item.\"cod-sit-item\" = 3
         and pedido.\"cod-sit-ped\" = 3",
    "multi",
    'inner join pub."ped-venda-ext" ped_ext on pedido."nr-pedido" = ped_ext."nr-pedido"
          inner join med.pub."ped-item" ped_item on pedido."nome-abrev" = ped_item."nome-abrev"
          and pedido."nr-pedcli" = ped_item."nr-pedcli"');
    if(is_array($aReg)){
        foreach($aReg as $reg){

            $qtPedida = $reg['qt_atendida'];
            $vlUnitComDesconto = $reg['vl_preori'];
            $vlTotalItem = $qtPedida * $vlUnitComDesconto;
            $vlTotal += $vlTotalItem;
            $qtTotal += $qtPedida;
        }
    }
    if($vlTotal > 0 and $qtTotal > 0){
        $vlPrecoMedio = $vlTotal / $qtTotal;
    }else{
        $vlPrecoMedio = 0;
    }
    $aRet = array('qt_total'=>$qtTotal,'vl_total'=> $vlTotal,'vl_preco_medio'=>$vlPrecoMedio);
    return $aRet;
}

?>
