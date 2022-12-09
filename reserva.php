<?php

function getRegReserva($numReserva,$campos='')
{
    $aReg = getReg('espec','ped-reserva','"num-reserva"', $numReserva,$campos);
    return $aReg;
}
function getClienteReserva($numReserva){
    $aReg = getRegReserva($numReserva,'"cod-emitente" as cod_emitente');
    return getVlIndiceArray($aReg,'cod_emitente',0);
}
function getSitReserva($numReserva){
    $aReg = getRegReserva($numReserva,'situacao');
    return getVlIndiceArray($aReg,'situacao',0);
}
function inserirReserva($cliente,$listaEtq,$codRep=0,$codEstabel='1')
{
    $hoje       = getHoje('en');
    $dtValidade = getHoje('en',7);
    $codRep     = getCodRepresCorrente($codRep);
    $hrReserva  = convHoraEmIntProgress();
    $aInsert    = array('cod-emitente'  => $cliente,
                     'num-reserva'      =>'pub."seq-ped-reserva".NEXTVAL',
                     'cod-rep'          => $codRep,
                     'dt-reserva'       => $hoje,
                     'dt-validade'      => $dtValidade,
                     'hr-reserva'       => $hrReserva,
                     'situacao'         => 1,
                     'usuario'          => getUsuarioCorrente(),
                     'cod-estabel'      => $codEstabel
                     );
    $cmd    = convertArrayEmInsert('ped-reserva',$aInsert,'1,2,3,7',true);
    sc_exec_sql($cmd,"especw");
    $numReserva = buscarVlSequenciaEspec('"seq-ped-reserva"','ped-reserva');
    inserirPedReservaEtqPorLista($numReserva,$listaEtq);
    return $numReserva;


}
function getFiltroReservaTpUsuario($apelido,$logAplicarFiltro=false)
{

    $condicao = '';
    if($apelido == ''){
        $apelido = 'reserva';
    }
    $apelido .= '.';
    switch(getVarSessao('tipo_usuario_id')){
        case getNumTipoUsuarioCliente():
            $condicao = $apelido.'"cod-emitente" = '.getVarSessao('num_cliente');
            break;
        case getNumTipoUsuarioRepresentante():
            $condicao = $apelido.'"cod-rep" = '.getVarSessao('num_rep');
            break;
        //outros tipos de usuários não tem filtro especifico
    }
    if($logAplicarFiltro){
        setCondWhere($condicao);
    }
    return $condicao;
}
function sincrPedReservaItPorChave01($numReserva,$itcodigo,$codRefer,$nrLote,$corteComerc)
{
    $nrSequencia = 0;
    $aReg = getRegPedReservaItPorChave01($numReserva,$itcodigo,$codRefer,$nrLote,$corteComerc,'"nr-sequencia" as nr_sequencia');
    if(is_array($aReg)){
        $nrSequencia  = $aReg[0]['nr_sequencia'];
    }else{
        $nrSequencia =  inserirPedReservaIt($numReserva,$itcodigo,$codRefer,$nrLote,$corteComerc);
    }
    return $nrSequencia;

}
function getRegPedReservaItPorChave01($numReserva,$itcodigo,$codRefer,$nrLote,$corteComerc,$campos='')
{
    $aReg = getReg('espec','ped-reserva-it','"num-reserva","it-codigo","cod-refer","nr-lote","corte-comerc"',
                 "$numReserva,'$itcodigo','$codRefer','$nrLote','$corteComerc'",$campos);
    return $aReg;
}

function getUltSeqPedReservaIt($numReserva)
{
    $ultSeq = 0;
    $aReg = getReg('espec','ped-reserva-it','"num-reserva"',
        $numReserva, 'max("nr-sequencia") as nr_sequencia');
    return getVlIndiceArray($aReg,'nr_sequencia',0);
}
function inserirPedReservaIt($numReserva,$itcodigo,$codRefer,$nrLote,$corteComerc)
{
    $nrSeq = getUltSeqPedReservaIt($numReserva) + 10;
    $aInsert = array('num-reserva'=>$numReserva,
                     'nr-sequencia'=>$nrSeq,
                     'it-codigo'=>$itcodigo,
                     'cod-refer'=>$codRefer,
                    'nr-lote'=>$nrLote,
                    'corte-comerc'=>$corteComerc);
    $cmd = convertArrayEmInsert('pub."ped-reserva-it"',$aInsert,'1,2');
    sc_exec_sql($cmd,"especw");
    return $nrSeq;
}
function inserirPedReservaEtqPorLista($numReserva,$listaEtq)
{
    $codRep = getCodRepresCorrente();
    $aRegs = getRegsEtqPorLista($listaEtq);
    $listaInseridos    = '';
    $listaNaoInseridos = '';
    if(is_array($aRegs)){
        foreach($aRegs as $reg){
            $numEtq         = $reg['"num-etiqueta"'];
            $itCodigo       = $reg['"it-codigo"'];
            $codRefer       = $reg['"cod-refer"'];
            $nrLote         = $reg['"nr-lote"'];
            $corteComerc    = $reg['"corte-comerc"'];
            $lDisp = verifDispEtq($numEtq);
            if($lDisp){
                $nrSequencia    = sincrPedReservaItPorChave01($numReserva,$itCodigo,$codRefer,$nrLote,$corteComerc);
                inserirPedReservaEtq($numReserva,$nrSequencia,$numEtq);
                setSitObEtiqueta($numEtq,4);
                $listaInseridos = util_incr_valor($listaInseridos,$numEtq);
            }else{
                $listaNaoInseridos = util_incr_valor($listaNaoInseridos,$numEtq);
            }
            excluirCarrinhoEtq($codRep,$numEtq);
        }
    }
    return array('lista_inseridos'=>$listaInseridos,'lista_nao_inseridos'=>$listaNaoInseridos);
}
function inserirPedReservaEtq($numReserva,$nrSequencia,$numEtq)
{
    $aInsert = array('num-reserva'=>$numReserva,
                     'nr-sequencia'=>$nrSequencia,
                    'num-etiqueta'=>$numEtq    );
    $cmd= convertArrayEmInsert('pub."ped-reserva-etq"',$aInsert,'1,2,3');
    sc_exec_sql($cmd,"especw");
}
function getListaEtqReservaSeq($numReserva,$numSequencia)
{
    $listaEtqs = '';
    $aEtq = getDados('multi','pub."ped-reserva-etq"',
        '"num-etiqueta" as etq',
        "\"num-reserva\" = $numReserva and \"nr-sequencia\"  = $numSequencia",
    "espec");
    if(is_array($aEtq)){
        foreach($aEtq as $etq){
            $listaEtqs = util_incr_valor($listaEtqs,$etq['etq']);
        }
    }
    return $listaEtqs;
}
function getQtTotalReservaSeq($numReserva,$numSequencia)
{
        $listaEtqs = getListaEtqReservaSeq($numReserva,$numSequencia);
        $qtTotal = getTotalQtPorListaEtq($listaEtqs);
        return $qtTotal;

}

function excluirPedReserva($numReserva)
{
    $logExcluir = false;
    $sitReserva = getSitReserva($numReserva);
    //echo "<h1>situacao:$sitReserva</h1>";
    if($sitReserva == 1){
        excluirPedReservaEtq($numReserva);
        excluirPedReservaIt($numReserva);
        $cmd = "delete from pub.\"ped-reserva\" where \"num-reserva\" = $numReserva ";
        sc_exec_sql($cmd,"especw");
        $logExcluir = true;
    }
    return $logExcluir;
}
function excluirPedReservaIt($numReserva)
{
    $cmd = "delete from pub.\"ped-reserva-it\" where \"num-reserva\" = $numReserva ";
    sc_exec_sql($cmd,"especw");


}
function excluirPedReservaEtq($numReserva)
{
    $cmd = "delete from pub.\"ped-reserva-etq\" where \"num-reserva\" = $numReserva ";
    sc_exec_sql($cmd,"especw");

}

function getItensReserva($numReserva)
{
    $aRegs = getDados('multi','ped-reserva-it','',
        "\"num-reserva\" = $numReserva",'espec');
    return $aRegs;
}
function fecharReserva($numReserva)
{
    $cmd = "update pub.\"ped-reserva\" set situacao = 2 where \"num-reserva\"= $numReserva";
    sc_exec_sql($cmd,"especw");
}

?>
