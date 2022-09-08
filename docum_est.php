<?php
function getQtItemDocEst($serie,$documento,$natOperacao,$codEmitente, $it_codigo,$codRefer)
{
    $qt = 0;
    $aItemDocEst = getReg('med','"item-doc-est"',
        '"cod-refer","it-codigo","nat-operacao","cod-emitente","nro-docto" ,"serie-docto"',
    "'$codRefer','$it_codigo','$natOperacao',$codEmitente,'$documento','$serie'",
    'quantidade');
    if(is_array($aItemDocEst)){
        $qt = $aItemDocEst[0]['quantidade'];
    }
    return $qt;
}
function verifExistDocPorChave($chave){

    $aRet = getReg('med',
        '"docum-est"',
        '"cod-chave-aces-nf-eletro"',
        "'$chave'",
    '"cod-chave-aces-nf-eletro"');
    return is_array($aRet);

}


?>