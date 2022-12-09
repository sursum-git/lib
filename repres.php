<?php 
function getNomeAbrevRepres(int $codRep)
{    
    $codRep = tratarNumero($codRep);
    $aReg = getRegRepres($codRep,'"nome-abrev" as nome_abrev');
    return getVlIndiceArray($aReg,'nome_abrev','');


}
function getRegRepres(int $codRep, string $campos='')
{
    return getReg('ems2','repres','"cod-rep"',$codRep,$campos);
}

?>