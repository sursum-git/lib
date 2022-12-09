<?php
function getRegCorteComerc($corteComerc,$campos='')
{
    $aReg = getReg('espec','"corte-comerc"','codigo',"'$corteComerc'",$campos);
    return $aReg;
}
function getDescrCorteComerc($corteComerc)
{
    $aReg = getRegCorteComerc($corteComerc,'descricao');
    return getVlIndiceArray($aReg,'descricao','');
}
function getDescrQtValidasCorteComerc($corteComerc,$nrLote)
{
    $descr = '';
    $aCorte = getRegCorteComerc($corteComerc,'"compr-min" as compr_min,
        "compr-med" as compr_med,
        "tp-embalag" as tp_embalag ');
    //var_dump($aCorte);
    if(is_array($aCorte)) {
        $comprMin = getVlIndiceArrayDireto($aCorte[0], 'compr_min', 0);
        $comprMed = getVlIndiceArrayDireto($aCorte[0], 'compr_med', 0);
        $tpEmbalag = getVlIndiceArrayDireto($aCorte[0], 'tp_embalag', 0);
        if(strtolower($nrLote) == 'rp'){
            //echo "<h3>entrei RP - tp.embalagem: $tpEmbalag</h3>";
            if($tpEmbalag == '1' or $tpEmbalag == '2'){
               $descr = "Qt.Minima:$comprMin - Multiplo Válido: $comprMed";
                //echo "<h3>entrei tp.embalgem</h3>";
            }

        }
    }
    return $descr;
}

?>
