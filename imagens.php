<?php
function criarImgTempl2Cortada($arquivo)
{
    $image =  new ImageResize($arquivo);
    $arqPuro = getArquivoPuro($arquivo);
    $dir = getDiretorioArquivo($arquivo);
    $separador = getSeparadorArquivo($arquivo);
    $dir .= $separador."cortado".$separador;
    $image->crop( getLarguraTp1PX(), getAlturaTp1PX(),true, ImageResize::CROPBOTTOM);
    $novoArquivo = $dir.$arqPuro;
    $image->save($novoArquivo);
    inserirTpArqRefTp2CortEst($arqPuro);
    return $novoArquivo;
}
function sincrImgTempl2Cortada($arquivo,$item,$ref,$container=0)
{
    $arqT2Cort = getArqRefTempl2Cort($item,$ref,$container);
    if($arqT2Cort == ''){
        inserirLogDb('Nao Achei o arquivo cortado para o (item - ref - container) e vou cria-lo',
            "( $item - $ref - $container ) ",__FUNCTION__);
        $arqT2Cort= criarImgTempl2Cortada($arquivo);
        inserirLogDb('Nao Achei o arquivo cortado e criei outro',
            "( item: $item - ref: $ref - container: $container - arquivo criado: $arqT2Cort ) "
            ,__FUNCTION__);
    }else{
        inserirLogDb('ACHEI o arquivo cortado',
            "( item: $item - ref: $ref - container: $container - arquivo encontrado: $arqT2Cort ) "
            ,__FUNCTION__);
    }
    return $arqT2Cort;
}


