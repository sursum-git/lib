<?php
function getClassAgrup($agrup)
{
    $lista = '';
    $agrup =  tratarNumero($agrup);
    $aDados = getDados('multi','pub.itens_agrup_class_design',
    'class_design_id',"agrup_class_design_id  = $agrup",
    "espec");
    if(is_array($aDados)){
        $tam = count($aDados);
        for($i=0;$i<$tam;$i++){
            $incr = $aDados[$i]['class_design_id'];
            $lista = util_incr_valor($lista,$incr);
        }
    }
    return $lista;
}

?>