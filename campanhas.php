<?php
function getRegCampanha($campanhaId){

    $aReg = getReg('espec','campanhas','campanha_id', $campanhaId);
    return $aReg;
    var_dump($aReg);

}
function verifDatasCamp($campanhaId,$dtIni,$dtFim)
{
    $erro = array();
    $lErro = false;
    $dtIniFormat = sc_date_conv($dtIni,"aaaa-mm-dd","dd/mm/aaaa");
    $dtFimFormat = sc_date_conv($dtFim,"aaaa-mm-dd","dd/mm/aaaa");
    $aCamp = getRegCampanha($campanhaId);
    if(is_array($aCamp)){
       $dtIniCamp = $aCamp[0]['dt_inicial'];
       $dtFimCamp = $aCamp[0]['dt_final'];

       if($dtIni < $dtIniCamp){
           $lErro = true;
           $dtIniCampBr = sc_date_conv($dtIniCamp,"aaaa-mm-dd","dd/mm/aaaa");
           $erro[] = "Dt. Inicial($dtIniFormat) menor que a data inicial($dtIniCampBr) 
           da campanha $campanhaId";
       }
        if($dtFim > $dtFimCamp){
            $lErro = true;
            $dtFimCampBr = sc_date_conv($dtFimCamp,"aaaa-mm-dd","dd/mm/aaaa");
            $erro[] = "Dt. Final($dtFimFormat) maior que a data final($dtFimCampBr)
             da campanha $campanhaId";
        }
    }
    if($lErro == false){
        $erro = '';
    }
    return $erro;
}



