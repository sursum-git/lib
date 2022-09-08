<?php

function incluirArquivosTransacao($idTransacao,$tipoArq,$acao){

    $aRegs = getDados('multi','PUB.arquivos_transacao_img','upload, arq_transacao_img_id',"transacoes_img_id = $idTransacao",'espec');
    if(is_array($aRegs)){
        foreach ($aRegs as $reg){
            $nomeArquivo = $reg['upload'];
            $idArq = $reg['arq_transacao_img_id'];
            setArqTransacaoImg($idArq);
            execAcaoArqDesign($nomeArquivo,$tipoArq,$acao);
        }
    }
    limparArqTransacaoImg();



}

?>