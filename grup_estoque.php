<?php
//__NM____NM__FUNCTION__NM__//
function descGrupoEstoque($estab,$grupo)
{
    $descricao = '';
    $tabela = "pub.grup_estoque";
    $campo  = "descricao";
    $condicao = "\"ge-codigo\" = '$grupo'";
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