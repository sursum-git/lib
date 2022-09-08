<?php
function getPastaRaizTpArq($tpArquivo,$logCompleto=0)
{
    $pastaRaiz = getCampoTipoArq($tpArquivo,'pasta_raiz');
    if($logCompleto == 1){
        $pastaRaiz = juntarDirWebArquivo($pastaRaiz);
    }
    return $pastaRaiz;

}
function getTpArqPastaRaiz($pastaRaiz)
{
    $aReg = getReg('espec','tipos_arquivo_design',
        'pasta_raiz',
        "'".$pastaRaiz."'",'tipo_arquivo_design_id');
    if(is_array($aReg)){
        $id = $aReg[0]['tipo_arquivo_design_id'];
    }else{
        $id = 0;
    }
    return $id;

}
function getFuncaoTipoArq($tipo)
{
    $retorno = getCampoTipoArq($tipo,'funcao');
    return $retorno;

}

function getDescrTipoArq($tipo)
{
    $retorno = getCampoTipoArq($tipo,'descricao');
    return $retorno;

}
function getCampoTipoArq($tipo,$campo)
{
    $aReg = retornoSimplesTb01('pub.tipos_arquivo_design',
        $campo,"tipo_arquivo_design_id = $tipo ",
        'espec');
    if(is_array($aReg)){
        $retorno = $aReg[0][$campo];
    }else{
        $retorno = '';
    }
    return $retorno;

}




function juntarDirWebArquivo($arquivo)
{
    $dirWeb = getDirRaizWeb();
    $separador = getSeparadorArquivo($dirWeb);
    $arquivo = join($separador,array($dirWeb,$arquivo));
    return $arquivo;

}
function juntarDirArq($dir,$arquivo){

    $separador = getSeparadorArquivo($dir);
    $arquivo = join($separador,array($dir,$arquivo));
    return $arquivo;
}
function separarArquivoDirWeb($arquivo)
{
    $dirWeb = getDirRaizWeb();
    $separador = getSeparadorArquivo($dirWeb);
    $dirWeb .= $separador;
    $arquivo = str_replace($dirWeb,'',$arquivo);
    return $arquivo;
}

function getTiposComClassificacao()
{
    return '16,17,18';
}
function getTiposComItemUnico()
{
    return '21,9,16,17,18';
}
function verifTpArqTemClassif($tipo)
{
    $retorno = false;
    if(strlen($tipo) == 1){
        $tipo = "0{$tipo}";
    }
    $lista = getTiposComClassificacao();
    //echo "<h1>lista:$lista - tipo: $tipo</h1>";
    if(strstr($lista,$tipo) <> false){
        $retorno = true;
    }
    return $retorno;
}

function verifTpArqItemUnico($tipo)
{
    $retorno = false;
    if(strlen($tipo) == 1){
        $tipo = "0{$tipo}";
    }
    $lista = getTiposComItemUnico();
    //echo "<h1>lista:$lista - tipo: $tipo</h1>";
    if(strstr($lista,$tipo) <> false){
        $retorno = true;
    }
    return $retorno;
}
function verificarTipoArqxTipoItem($tipoArquivo,$nomeArquivo)
{
    $msg = '';
    $aItens = array();
    if(strstr($nomeArquivo,'_') <> false){
        $aItens = extrairItensNomeArquivo($nomeArquivo, '_','-');
    }else{
        if(strstr($nomeArquivo,'_') <> false){
            $aItens = extrairItensNomeArquivo($nomeArquivo, '-');
        }
    }
    if(is_array($aItens)){
        foreach($aItens as $item){
            $tipoItem = getTipoItem($item);
            if($tipoItem == 'estampado' and ($tipoArquivo == 4 or $tipoArquivo == 12 )){
                $msg = 'Erro - Tipo De Arquivo Incompativel com tipo do Item';
            }
            if($tipoItem == 'liso' and ($tipoArquivo <> 4 and $tipoArquivo <> 12 )){
                $msg = 'Erro - Tipo De Arquivo Incompativel com tipo do Item';
            }
        }
    }
    return $msg;

}
function getTiposArqVisualInd()
{
    return '9,21';
}

function verifTpArqVisualInd($tipo)
{
    $retorno = false;
    if(strlen($tipo) == 1){
        $tipo = "0{$tipo}";
    }
    $lista = getTiposArqVisualInd();
    //echo "<h1>lista:$lista - tipo: $tipo</h1>";
    if(strstr($lista,$tipo) <> false){
        $retorno = true;
    }
    return $retorno;
}
function getTiposArqPi(){
    return '1,2,10,13,14,20';
}
function verifTpArqPi($tipo)
{
    $retorno = false;
    if(strlen($tipo) == 1){
        $tipo = "0{$tipo}";
    }
    $lista = getTiposArqPi();
    //echo "<h1>lista:$lista - tipo: $tipo</h1>";
    if(strstr($lista,$tipo) <> false){
        $retorno = true;
    }
    return $retorno;
}

?>
