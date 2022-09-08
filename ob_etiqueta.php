<?php
//__NM____NM__FUNCTION__NM__//
/**
 * Created by PhpStorm.
 * User: sursum_corda
 * Date: 08/08/2018
 * Time: 17:41
 */

function buscarTamEtiqueta($itCodigo,$codRefer)
{
    /********************************************************************************
    Busca o total do saldo por item,containe(origem),estabelecimento
     *******************************************************************************/
    //3(estoque) 4(reservada)
    $tabela 	= 'pub."ob-etiqueta"';
    $campos 	= 'count(quantidade) as qt, quantidade';
    $condicao	=  " situacao in (3,4) and quantidade <> 0 and \"it-codigo\" = '$itCodigo' and \"cod-refer\" = '$codRefer' group by quantidade order by quantidade"  ;
	
	  
    $aQt		= retornoMultReg($tabela,$campos,$condicao,'espec');
    return $aQt;
}
	
function buscarSaldoEtiqueta($itCodigo,$codRefer='')
{
    /********************************************************************************
    Busca o total do saldo por item,containe(origem),estabelecimento
     *******************************************************************************/
    //3(estoque) 4(reservada)
    $tabela 	= 'pub."ob-etiqueta"';
    $estab	    = "";
    if($codRefer <> ''){
        $condCodRefer = " and \"cod-refer\" = '$codRefer'";
    }else{
        $condCodRefer = '';
    }

    $campos 	= 'sum(quantidade) as qt, "nr-container" as origem, "it-codigo" as item, situacao, "cod-estabel"';
    $condicao	=  " situacao in (3,4)  and quantidade <> 0 and \"it-codigo\" = '$itCodigo'  $condCodRefer
	group by \"nr-container\", \"it-codigo\",  situacao, \"cod-estabel\" ";
    $aQt		= retornoMultReg($tabela,$campos,$condicao,'espec');
    return $aQt;
}

function buscarSaldoEtqContainer($nrContainer,$itCodigo,$codRefer='')
{
    /********************************************************************************
    Busca o total do saldo por item,containe(origem),estabelecimento
     *******************************************************************************/
    //3(estoque) 4(reservada)
    $qt = 0;
    $tabela 	= 'pub."ob-etiqueta"';
    $estab	    = "";
    if($codRefer <> ''){
        $condCodRefer = " and \"cod-refer\" = '$codRefer'";
    }else{
        $condCodRefer = '';
    }

    $campos 	= 'sum(quantidade) as qt';
    $condicao	=  " situacao in (3,4)  and quantidade <> 0 and \"it-codigo\" = '$itCodigo'  $condCodRefer
	and \"nr-container\" =  $nrContainer ";
    $aQt		=  getDados('unico',$tabela,$campos,$condicao,'espec');
    if(is_array($aQt)){
        $qt= $aQt[0]['qt'];
    }
    return $qt;
}


function buscarAnaliseSaldo($itCodigo,$qtSaldoEstoq,$containerId,$codRefer='')
{
    /********************************************************************
    Compara o saldo em estoque passado por parametro ao saldo geral
    das etiquetas retornando as seguintes informações em um array:
    qt_diferenca -> diferença entre o saldo em estoque(tabela saldo-estoq) e
    o total das etiquetas em estoque ou reservadas(separadas para faturamento).
    qt_container -> quantidade total de etiquetas do container Corrente.
    qt_restante  -> quantidade total de etiquetas que não são do container,
    podendo ser de outro container ou podendo estar em branco.
     **********************************************************************/

    $qtDiferenca = $qtSaldoEstoq;
    $aSaldo = buscarSaldoEtiqueta($itCodigo,$codRefer);
    $qtContainer = 0;
    $qtRestante  = 0;
    if(is_array($aSaldo)){
        $tam = count($aSaldo);
        for($i=0;$i < $tam;$i++){
            if(	$aSaldo[$i]['origem'] == $containerId){
                $qtContainer += $aSaldo[$i]['qt'];
            }
            else{
                $qtRestante  += $aSaldo[$i]['qt'];
            }
        }
        //echo "<h1>qtDiferenca:$qtDiferenca - qtContainer:$qtContainer -  qtRestante:$qtRestante<h1>";

        $qtDiferenca = $qtDiferenca - $qtContainer - $qtRestante;

    }
    $aRetorno[] = array('qt_diferenca' => $qtDiferenca, 'qt_container' => $qtContainer,
        'qt_restante' => $qtRestante);

    return $aRetorno;
}

function gravarSaldoEtiqueta($itCodigo,$itemCustoId)
{
    $aQt = buscarSaldoEtiqueta($itCodigo);
    if(is_array($aQt)){
        $tam = count($aQt);
        for($i=0;$i < $tam;$i++){
            $qt 		= $aQt[$i]['qt'];
            $origem 	= $aQt[$i]['origem'];
            $itCodigo 	= $aQt[$i]['item'];
            $situacao 	= $aQt[$i]['situacao'];
            $estab		= $aQt[$i]['"cod-estabel"'];
            inserirSaldoEtiqueta($itCodigo,$itemCustoId,$origem,$estab,$qt,$situacao);
        }
    }
}
function inserirSaldoEtiqueta($itCodigo,$itemCustoId,$origem,$estab,$qt,$situacao)
{
    $cmd = "insert into pub.item_custo_etq(item_custo_id,it_codigo,estab,origem,situacao,quantidade)
	values($itemCustoId,'$itCodigo','$estab',$origem,$situacao,$qt)";
    sc_exec_sql($cmd,"especw");
}

function getQtEtqContainer($nrContainer,$item,$refs)
{
    $tabela     = " pub.\"ob-etiqueta\" ob";
    $campos   = " sum(ob.quantidade) as qt";
    $condicao = " ob.\"nr-container\" = $nrContainer 
                 and ob.\"it-codigo\" = '$item' 
                 and ob.\"cod-refer\" = '$refs'
                 and ob.situacao = 5 " ;
    $aDados = getDados('multi', $tabela, $campos, $condicao,'espec');

    return $aDados;
}
function getQtEtqContainerEntrada($nrContainer,$item,$refs)
{
    $tabela     = " pub.\"ob-etiqueta\" ob";
    $campos   = " sum(ob.quantidade) as qt";
    $condicao = " ob.\"nr-container\" = $nrContainer 
                 and ob.\"it-codigo\" = '$item' 
                 and ob.\"cod-refer\" = '$refs' " ;
    $aDados = getDados('multi', $tabela, $campos, $condicao,'espec');
    if(is_array($aDados)){
        $qt =$aDados[0]['qt'];
    }else{
        $qt = 0;
    }

    return $qt ;
}


function getVlMedioEtqContainer($nrContainer,$item,$refs)
{
    $qtTotal = 0;
    $vlTotal = 0;
    $tabela     = " pub.\"ob-etiqueta\" etq";
    $campos     = " etq.quantidade as qt_atendida, peditem.\"vl-preori\" as vl_preori";
    $condicao   = " etq.\"cod-estabel\" = 5
                    and   etq.\"nr-container\"    = $nrContainer
                    and   etq.situacao          = 5
                    and   peditem.\"it-codigo\"   = '$item'
                    and   peditem.\"cod-refer\"  = '$refs' " ;
        $inner    = 'inner join pub."ped-item-rom" rom on etq."num-etiqueta" = rom."num-etiqueta"
                     inner join med.pub."ped-item" peditem 
                     on   peditem."nome-abrev"     = rom."nome-abrev"
                     and  peditem."nr-pedcli"    = rom."nr-pedcli"
                     and peditem."nr-sequencia"  = rom."nr-sequencia"
                     and peditem."it-codigo"     = etq."it-codigo"
                     and peditem."cod-refer"     = etq."cod-refer"';
    $aReg = getDados('multi', $tabela, $campos, $condicao,'multi',$inner);
    //var_dump($aReg);
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


//pub.\"ped-item\" peditem, pub.\"ped-item-rom\" rom"
?>
