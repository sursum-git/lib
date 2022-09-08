<?php
//__NM____NM__FUNCTION__NM__//
function descFamilia($estab,$familia)
{
    $descricao = '';
    $tabela = "pub.familia";
    $campo  = "descricao";
    $condicao = "\"fm-codigo\" = '$familia'";
    switch($estab){
        case '1':
            $aRetorno = retornoSimplesTb01($tabela,$campo,$condicao,"ima");
            break;
        case '5':
            $aRetorno = retornoSimplesTb01($tabela,$campo,$condicao,"med");
            break;
    }
    if(is_array($aRetorno)){
        $descricao = $aRetorno[0]['descricao'];
    }
    return $descricao;
}

?>