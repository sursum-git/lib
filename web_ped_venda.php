<?php
function inserirWebPedVenda($pedidoWebId)
{
    $nrPedido = 0;
    $aReg = getRegPedsWeb($pedidoWebId);
    if(is_array($aReg)){
        /*CAMPOS TABELA
         * id,dt_hr_registro,ind_sit_ped_web,log_operac_triang,
         * log_a_vista,cliente_id,cliente_triang_id,log_cond_pagto_especial,
    cond_pagto_id,dias_cond_pagto_esp,transp_id,transp_redesp_id,cod_estabel,
        perc_comis,repres_id,comentario,cod_tipo_pedido,
    login,login_digitacao,login_preposto,tp_entrega,nr_pedido_cliente,
        nr_pedido_repres,data_tp_entrega,reserva_id,cod_tipo_frete
         */

        $aReg = $aReg[0];
        $aChaves = array_keys($aReg);
        //var_dump($aChaves);
        foreach($aChaves as $cp){
            $cpVar  = convNomeVar($cp);
            $$cpVar = $aReg[$cp];
        }
        //echo "<h1>$transpId</h1>";
        $nomeAbrev = tratarAspasSimples(getNomeAbrevCliente($clienteId));
        $nrPedido = incrementarVlSequenciaProgress('ems2w','seq-nr-pedido','ped-venda');

        $aInsert = array('nr-pedido'        =>$nrPedido,
                         'cod-estabel'      =>$codEstabel,
                         'tp-pedido'        => convNumeroParaDescTpPedido($codTipoPedido),
                         'cod-emitente'     =>$clienteId,
                         'nome-abrev'       => $nomeAbrev ,
                         'nome-abrev-tri'   => tratarAspasSimples(getNomeAbrevCliente($clienteTriangId))  ,
                         'dt-implant'       => 'curdate()',
                         'dt-emissao'       => 'curdate()',
                         'tp-entrega'       => convNumeroParaDescTpEntrega($tpEntrega),
                         'dt-entrega'       => $dataTpEntrega,
                         'cod-cond-pag'     => $condPagtoId,
                         'tp-frete'         => convNumeroParaDescrTpFrete($codTipoFrete),
                         'nome-transp'      =>  tratarAspasSimples(getNomeAbrevTransp($transpId)),
                         'nome-tr-red'      =>  tratarAspasSimples(getNomeAbrevTransp($transpRedespId)),
                         'num-reserva'      => $reservaId,
                         'observacoes'       => tratarAspasSimples($comentario),
                         'nr-pedcli'        => $nrPedidoCliente,
                         'nr-pedrep'        => $nrPedidoRepres,
                         'no-ab-reppri'     => $login         ,
                         'usuario'          => getUsuarioCorrente(),
                         'compl-observ'     =>'Dt.Hr.Registro:'.$dtHrRegistro
            );
        $cmd = convertArrayEmInsert('pub."web-ped-venda"',
            $aInsert,
            '1,4,7,8,11,15'
        );
        sc_exec_sql($cmd,"especw");
        $aItens = getItensPedWeb($pedidoWebId);
        $nrSequencia = 0;
        if(is_array($aItens)){
            foreach($aItens as $reg){
                $id            = $reg['id'];
                $pedWeb        = $reg['ped_web_id'];
                $itCodigo      = $reg['it_codigo'];
                $codRefer      = $reg['cod_refer'];
                $qtPedida      = $reg['qt_pedida'];
                $dtHrCriacao   = $reg['dt_hr_criacao'];
                $vlInformado   = $reg['vl_informado'];
                $nrLote        = $reg['nr_lote'];
                $corteComerc   = $reg['corte_comerc'];
                $nrSequencia   += 10;
                $aInsertItens = array('nome-abrev'      => $nomeAbrev,
                                      'nr-pedcli'       => $nrPedido,
                                      'it-codigo'       => $itCodigo,
                                      'cod-refer'       => $codRefer,
                                      'nr-sequencia'    => $nrSequencia,
                                      'lote'            => $nrLote,
                                      'corte-comerc'    => $corteComerc,
                                      'vl-preori'       => $vlInformado,
                                      'qt-pedida'       => $qtPedida,
                                      'observacao'      => "Dt.Hr.Criação:".$dtHrCriacao
                );
                $cmd = convertArrayEmInsert('pub."web-ped-item"',$aInsertItens,'5,8,9');
                sc_exec_sql($cmd,"especw");

            }
            excluirPedWeb($pedidoWebId);
        }


    }
    return $nrPedido;

}

?>
