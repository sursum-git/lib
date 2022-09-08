<?php

function buscarNotaMaeContainer($nrContainer)
{
    $aRetorno = retornoSimplesTb01("pub.nfs_container",
        "documento, serie, estab, nat_operacao, cod_emitente",
        "container_id = $nrContainer and nat_operacao in('31201','31201M')",'espec');
    return $aRetorno;
}
