<?php

function setTransacaoImg($idTrans){
    setVarSessao(getNomeVarTransImgCorrente(),$idTrans);
}

function getTransacaoImg(){
    $var = getVarSessao(getNomeVarTransImgCorrente());
    $var = tratarNumero($var);
    return $var;
}
function limparTransacaoImg(){
    limparVarSessao(getNomeVarTransImgCorrente());
}

function getNomeVarTransImgCorrente()
{
    return 'transacao_img_corrente';
}

function setArqTransacaoImg($idArqTrans){
    setVarSessao(getNomeVarArqTransImgCorrente(),$idArqTrans);
}

function getArqTransacaoImg(){
    $var = getVarSessao(getNomeVarArqTransImgCorrente());
    $var = tratarNumero($var);
    return $var;
}
function limparArqTransacaoImg(){
    limparVarSessao(getNomeVarArqTransImgCorrente());
}

function getNomeVarArqTransImgCorrente()
{
    return 'arq_transacao_img_corrente';
}


?>