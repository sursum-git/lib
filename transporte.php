<?php
//__NM____NM__FUNCTION__NM__//
/**
 * Created by PhpStorm.
 * User: tasil
 * Date: 21/05/2019
 * Time: 16:00
 */

function getRegTransporte($transp)
{
    $aTransp = getReg('comum','transporte','"cod-transp"',$transp);
    return $aTransp;
}
function getNomeTransp($transp)
{
    $aTransp = getRegTransporte($transp);
    $nome = $aTransp[0]['nome'];
    return $nome;
}
?>