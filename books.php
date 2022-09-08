<?php
function criarBookCust($filtros,$logOnlina=0,$dtHrAgendamento='')
{



}
function inserirFiltroBookCust($bookId,$tipoFiltro,$valor,$indOperacao)
{

}

function getItensRefsClassif($classif)
{

    $aItemRef = array();
    $itemAnterior = '';
    $listaRefs = '';
    $logCrieiReg = false;
    $aDados = getDados('multi','pub.class_item_ref',
        ' distinct it_codigo as it_codigo,cod_refer',
    "class_design_id = $classif",'espec');

    if(is_array($aDados)){
        $tam = count($aDados);
        for($i=0;$i<$tam;$i++){
            $itCodigo = $aDados[$i]['it_codigo'];
            $codRefer = $aDados[$i]['cod_refer'];
            //echo "<h1>class = $itCodigo - $codRefer</h1>h1>";
            inserirLogDb('posicao - item - item anterior -  ref - lista',
                "$i - $itCodigo - $itemAnterior -  $codRefer - $listaRefs  ",__FUNCTION__);

            if(
               ($itCodigo <> $itemAnterior and $itemAnterior <> '')
               or ($tam > 1 and $i == $tam - 1)
            ){
                if($i == $tam - 1){
                    $listaRefs    = util_incr_valor($listaRefs,"'$codRefer'");
                }
                //echo "<h1>item anterior: $itemAnterior - item: $itCodigo</h1>";
                $aItemRef[] = array('it_codigo'=>$itemAnterior, 'lista_cod_refer'=>$listaRefs);
                $listaRefs = '';
                $logCrieiReg = true;
            }

            $listaRefs    = util_incr_valor($listaRefs,"'$codRefer'");
            $itemAnterior = $itCodigo;
        }
        if($logCrieiReg ==false){ // no caso de ter apenas um item
            //echo "<h1>entrei no logcrieireg == false</h1>";
            $aItemRef[] = array('it_codigo'=>$itemAnterior,'lista_cod_refer'=>$listaRefs);
        }
    }
    //var_dump($aItemRef);
    return $aItemRef;
}
