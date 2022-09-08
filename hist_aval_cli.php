<?php
function getRegHistAvalCli($id,$campos)
{
    $aReg = getReg('espec','hist_aval_cli','hist_aval_cli_id',$id,
    $campos);
    return $aReg;
}

?>
