<?php

function getRegReferencia($codRefer,$campos='')
{
    $aReg = getReg('ems2','referencia','"cod-refer"',"'$codRefer'",$campos);
    return $aReg;
}
function getDescrRef($codRefer)
{
    $aReg = getRegReferencia($codRefer,'descricao');
    return getVlIndiceArray($aReg,'descricao','');

}

?>
