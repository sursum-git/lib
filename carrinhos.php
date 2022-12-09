<?php 
function montarFiltroCarrinho($aFiltros,$aApelidoTb)
{ /*********************************************** 
     chaves aFiltros: 
     rp,_rd,it_codigo,lista_cod_refer
    *********************************************/
    $aFiltroCond        = array();
    $aFiltroLote        = array();
    $aFiltroItem        = array();
    $aFiltroRefer       = array();
    $aFiltroEtqCarrinho = array();
    $tabela =setApelidoTbFiltro($aApelidoTb,'ob-etiqueta');

    $rp               = $aFiltros['rp'];
    $rd               = $aFiltros['rd'];
    $itCodigo         = $aFiltros['it_codigo'];
    $codRefer         = $aFiltros['lista_cod_refer'];
    $codRefer         = inserirAspasEmLista($codRefer);
    $listaEtqCarrinho = getListaEtqCarrinhoPorCodRep('');

    $aFiltroLote        = inserirArrayCond($aFiltroLote,$tabela,'nr-lote',$rd,'=',false,true,'and');
    $aFiltroLote        = inserirArrayCond($aFiltroLote,$tabela,'nr-lote',$rp,'=',false,true,'or');


    $aFiltroItem        = inserirArrayCond($aFiltroItem,$tabela,'it-codigo',$itCodigo,'=',false,false,'and');
    $aFiltroRefer       = inserirArrayCond($aFiltroRefer,$tabela,'cod-refer',$codRefer,'in',true,false,'and');

    $aFiltroEtqCarrinho = inserirArrayCond($aFiltroEtqCarrinho,$tabela,'num-etiqueta',$listaEtqCarrinho,'not_in',true,false,'and');


    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroLote);
    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroRefer);
    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroItem);
    $aFiltroCond        = inserirArrayCondMultiNivel($aFiltroCond,$aFiltroEtqCarrinho);
   /* echo "<pre>";
    var_dump($aFiltroCond);*/

    return convArrayMultiToCondSql($aFiltroCond);
}

function getFiltroCarrinhoTpUsuario($apelido,$logAplicarFiltro=false)
{

    $condicao = '';
    if($apelido == ''){
        $apelido = 'carrinhos';
    }
    $apelido .= '.';
    switch(getVarSessao('tipo_usuario_id')){
        case getNumTipoUsuarioCliente():
            $condicao = $apelido.'"cod_usuario" = '.getUsuarioCorrente();
            break;
        case getNumTipoUsuarioRepresentante():
            $condicao = $apelido.'"cod_rep" = '.getVarSessao('num_rep');
            break;
        //outros tipos de usuários não tem filtro especifico
    }
    if($logAplicarFiltro){
        setCondWhere($condicao);
    }
    return $condicao;
}




/*
function retirarEtqsExcluidas($listaEtq,$listaEtqExcluida)
{
    $aEtq = array();
    if($listaEtq <> ''){
        $aEtq = explode(',',$listaEtq);
        foreach($aEtq as $chave=>$etq){
            if( stristr($listaEtqExcluida,$etq)){
                unset($aEtq[$chave]);
            }
        }
        $aEtq = array_values($aEtq);
    }
    return $aEtq;
}
*/
function inserirRegsCarrinhoPorLista($listaEtq,$listaEtqExcluida=''):array
{
    $listaEtqIncluidas   = '';
    $listaEtqNaoIncluidas = '';
    $aEtq= explode(',',$listaEtq);
    if(is_array($aEtq)){
        foreach($aEtq as $etq){
            $logDisp = verifDispEtq($etq);
            if($logDisp){
                if(!verifExistCarrinho($etq)){
                    inserirRegCarrinhoPorEtq($etq);
                }
                $listaEtqIncluidas = util_incr_valor($listaEtqIncluidas,$etq);
            }else{
                $listaEtqNaoIncluidas = util_incr_valor($listaEtqNaoIncluidas,$etq);
            }

        }
    }
    return array('incluidas'=>$listaEtqIncluidas,'nao_incluidas'=>$listaEtqNaoIncluidas);
}

function inserirRegCarrinhoPorEtq($numEtq,$codEstabel='1')
{
    $aEtq  = getRegObEtq($codEstabel,$numEtq);
    if(is_array($aEtq)){
        $aEtq = $aEtq[0];

        $codEstabel    = getVlIndiceArrayDireto($aEtq,'"cod-estabel"','');
        $itCodigo      = getVlIndiceArrayDireto($aEtq,'"it-codigo"','');
        $codRefer      = getVlIndiceArrayDireto($aEtq,'"cod-refer"','');
        $nrLote        = getVlIndiceArrayDireto($aEtq,'"nr-lote"','');
        $corteComerc   = getVlIndiceArrayDireto($aEtq,'"corte-comerc"','');
        $localizacao   = getVlIndiceArrayDireto($aEtq,'localizacao','');
        $quantidade    = getVlIndiceArrayDireto($aEtq,'quantidade','');
        inserirRegCarrinho($codEstabel,$numEtq,$itCodigo,$codRefer,$nrLote,$corteComerc,$localizacao,$quantidade);

    }
}
function inserirRegCarrinho($codEstabel,$numEtq,$itCodigo,$codRefer,$nrLote,$corteComerc,$localizacao,$quantidade)
{
    $codRep = getCodRepresCorrente();
    $codUsuario = getUsuarioCorrente();
    $aInsert = array('cod_estabel' =>$codEstabel,
                     'num_etiqueta'=>$numEtq,
                     'it_codigo'   =>$itCodigo ,
                     'cod_refer'   =>$codRefer,
                     'nr_lote'     =>$nrLote ,
                     'corte_comerc'=>$corteComerc,
                     'localizacao' =>$localizacao,
                     'quantidade'  =>$quantidade,
                     'cod_rep'     =>$codRep,
                     'cod_usuario' =>$codUsuario,
                     'dt_hr_registro'=> getAgora()
    );
    $cmd = convertArrayEmInsert('carrinhos',$aInsert,'2,8');
    sc_exec_sql($cmd,"aux");
}
function getRegsCarrinhoPorCodRep($codRep)
{
    $codRep = getCodRepresCorrente($codRep);

    $aReg = getDados('multi','carrinhos','cod_estabel,num_etiqueta'," cod_rep = '$codRep'",'aux');

    return $aReg;
}
function getListaEtqCarrinhoPorCodRep($codRep)
{
    $aReg = getRegsCarrinhoPorCodRep($codRep);
    return convArrayMultParaLista($aReg,'num_etiqueta',false);
}
function excluirCarrinhoEtq($codRep,$numEtq,$codEstabel='1')
{
    $cmd = "delete from carrinhos where num_etiqueta = $numEtq and cod_estabel = '$codEstabel' and cod_rep= '$codRep' ";
    sc_exec_sql($cmd,"aux");
}
function excluirEtqsNaoDispPorCodRep($codRep=0):string
{
    $login = getUsuarioCorrente($codRep);

    $listaEtqExcluidas = '';
    $aRegs = getRegsCarrinhoPorCodRep($codRep);
    if(is_array($aRegs)){
        foreach($aRegs as $reg) {
            $numEtq = $reg['num_etiqueta'];
            $codEstab = $reg['cod_estabel'];
            $lDisp = verifDispEtq($numEtq, $codEstab);
            if (!$lDisp) {
                excluirCarrinhoEtq($codRep, $numEtq, $codEstab);
                $listaEtqExcluidas = util_incr_valor($listaEtqExcluidas, $numEtq);
            }
        }
    }

    return $listaEtqExcluidas;
}



function msgEtqExcluidas($listaEtqExcluidas,$logApenasMsg=false)
{
    $retorno = '';
    $frase  = 'As seguintes etiquetas foram excluidas por não estarem mais disponíveis:'. $listaEtqExcluidas;
    if($listaEtqExcluidas <> ''){
        if($logApenasMsg){
            $retorno = $frase;
        }else{
            $retorno = desenharAlertBS('danger',$frase);
        }

    }
    return $retorno;

}
function msgEtqNaoIncluidas($listaEtqNaoIncluidas,$logApenasMsg= false)
{
    $retorno = '';
    $frase = 'As seguintes Peças(Nr.Etiquetas) NÃO foram incluídas por não estarem mais disponíveis:'. $listaEtqNaoIncluidas;
    if($listaEtqNaoIncluidas <> ''){
        if($logApenasMsg){
            $retorno = $frase;
        }else{
            $retorno = desenharAlertBS('warning',$frase);
        }
    }
    return $retorno;

}

function msgEtqIncluidas($aEtq,$logApenasMsg=false)
{
    $retorno = '';
    $listaEtqIncluidas     =  $aEtq['incluidas'];
    $listaNaoIncluidas     = $aEtq['nao_incluidas'];
    $fraseEtqIncluidasParc = 'As seguintes Peças(Nr.Etiquetas) foram incluídas com SUCESSO:'. $listaEtqIncluidas;
    $fraseEtqIncluidas     = 'Todas as peças foram incluidas com SUCESSO !!! ';
    if($listaEtqIncluidas <> ''){
        if($listaNaoIncluidas  <> ''){
            $retorno     = desenharAlertBS('success',$fraseEtqIncluidasParc);
            $retornoPuro = $fraseEtqIncluidasParc;
        }else{
            $retorno = desenharAlertBS('success',$fraseEtqIncluidas);
            $retornoPuro = $fraseEtqIncluidas;
        }
        if($logApenasMsg){
            $retorno = $retornoPuro;
        }
    }
    return $retorno;
}
function getRegCarrinho($etiq,$codRep=0,$codEstabel='1',$campos='num_etiqueta')
{
    $codRep  =getCodRepresCorrente($codRep);
    $aReg = getReg('aux','carrinhos',
        'cod_rep,num_etiqueta,cod_estabel',
        "'$codRep',$etiq,'$codEstabel'",$campos);
    return $aReg;

}
function verifExistCarrinho($etiq,$codRep=0,$codEstabel='1')
{
    return is_array(getRegCarrinho($etiq,$codRep,$codEstabel));
}
function limparCarrinho($codRep=0)
{
    $codRep = getCodRepresCorrente($codRep);
    $cmd= "delete from carrinhos where cod_rep = $codRep";
    sc_exec_sql($cmd,"aux");
}
function excluirCarrinhoPorListaEtq($listaEtq)
{
    $codRep = getCodRepresCorrente();
    $aListaEtq = explode(',',$listaEtq);
    foreach($aListaEtq as $etq){
        excluirCarrinhoEtq($codRep,$etq);
    }
}
/*
function deletarEtqCarrinho($etiq,$codRep=0,$codEstabel='1')
{
    $login = getCodRepresCorrente($codRep);
    $cmd = "delete from carrinhos where num_etiqueta = $etiq  and cod_estabel = '$codEstabel' and cod_rep = '$codRep'";
    sc_exec_sql($cmd,"aux");

}*/
?>