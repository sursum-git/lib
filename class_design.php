<?php
function getDescrClassDesign($classId,$indRetorno=0)
{
    $descricao = '';
    $aReg = getReg('espec','class_design',
    'class_design_id',$classId,'descricao');
    if(is_array($aReg)){
        $descricao = $aReg[0]['descricao'];
        switch ($indRetorno){
            case 1: // utf8
                $descricao = utf8_encode($descricao);
                break;
            case 2: // sem acento
                $descricao = retirarAcentoSimples($descricao);
                break;
        }
    }
    return $descricao;

}
function getDescrsClassDesign($listaClass,$indRetorno=0,$sepEntrada=',',$sepSaida='_')
{
    $descr = '';
    if($listaClass<>''){
        $aListaClass = explode($sepEntrada,$listaClass);
        if(is_array($aListaClass)){
            $tam = count($aListaClass);
            for($i=0;$i<$tam;$i++){
                $descrCorrente = getDescrClassDesign($aListaClass[$i],$indRetorno) ;
                $descr = util_incr_valor($descr,$descrCorrente,$sepSaida);
            }
        }
    }

    return $descr;

}
?>
