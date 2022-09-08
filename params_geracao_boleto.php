<?php
function inserirParamGeracaoBoleto($dtIniValid,$dtFimValid,$codEstab,$portador,$carteira,$modulo='ACR',$finalidEcon='CORRENTE')
{
    $aInsert = array(
           'param_geracao_boleto_id'=>'pub.seq_param_geracao_boleto.NEXTVAL',
           'cod_estab'=> $codEstab,
            'cod_portador'=> $portador,
            'cod_cart_bcia'=> $carteira,
            'cod_modul_dtsul'=> $modulo,
            'cod_finalid_econ'=> $finalidEcon,
            'dt_ini_validade'=> $dtIniValid,
            'dt_fim_validade'=> $dtFimValid
        );

    $cmd = convertArrayEmInsert('pub.params_geracao_boleto',
    $aInsert,'1');
    sc_exec_sql($cmd,"especw");
    $id = buscarVlSequenciaEspec('seq_param_geracao_boleto','params_geracao_boleto');
    return $id;
}
function getRegParamGeracaoBoleto($dtIniValid,$codEstab,$portador,$carteira,$modulo='ACR',$finalidEcon='CORRENTE')
{
    $aReg = getReg('espec',
        'params_geracao_boleto',
    'dt_ini_valid,cod_finalid_econ,cod_cart_bcia,cod_portador,cod_estab,cod_modul_dtsul',
    "'$dtIniValid','$finalidEcon','$carteira','$portador','$codEstab','$modulo'" );
    return $aReg;

}
function sincrParamGeracaoBoleto($dtIniValid,$dtFimValid,$codEstab,$portador,$carteira,$modulo='ACR',$finalidEcon='CORRENTE')
{
    $aReg = getRegParamGeracaoBoleto($dtIniValid,$codEstab,$portador,$carteira,$modulo,$finalidEcon);
    if(! is_array($aReg)){
        $id = inserirParamGeracaoBoleto($dtIniValid,$dtFimValid,$codEstab,$portador,$carteira,$modulo,$finalidEcon);
    }else{
        $id = $aReg[0]['param_geracao_boleto_id'];
    }
    return $id;
}

function getValidIntermParamGeracaoBoleto($idCorrente,$dtIniValid,$dtFimValid,$codEstab,$portador,$carteira,$modulo='ACR',$finalidEcon='CORRENTE')
{
    $logAchou = false;
    $aReg = getDados('multi',
        'pub.params_geracao_boleto',
    '',
        " cod_finalid_econ  ='$finalidEcon' and 
                  cod_cart_bcia     ='$carteira'    and
                  cod_portador      = '$portador'   and
                  cod_estab         = '$codEstab'   and
                  cod_modul_dtsul   = '$modulo'     and
                  dt_fim_validade  >= '$dtIniValid' and
                  dt_ini_validade  <= '$dtFimValid' and
                  param_geracao_boleto_id <> $idCorrente                                    
                  order by dt_ini_validade ",
    'espec');
    //var_dump($aReg);
    if(is_array($aReg)){
        foreach($aReg as $reg){
            $dtIniCorr = $reg['dt_ini_validade'];
            $dtFimCorr = $reg['dt_fim_validade'];
            if($dtIniValid >= $dtIniCorr and $dtIniValid <= $dtFimCorr){
                $logAchou = true;
                break;
            }
            if($dtFimValid >= $dtIniCorr and $dtFimValid <= $dtFimCorr){
                $logAchou = true;
                break;
            }

        }
    }
    return $logAchou;
}

?>
