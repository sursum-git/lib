<?php
function processarArquivo($acao,$arquivo)
{
    switch ($acao){
        case 1: //inclusao de novo arquivo
        case 3: // modificação
            $ret = execAcaoArqDesign($arquivo,'',1);
            break;
        case 2: // exclusão de arquivo
            $ret = execAcaoArqDesign($arquivo,'',2);
            break;
    }
    return $ret;

}

/**
 * @param $arquivo
 * @return int
 * @comment busca o tipo de arquivo design a partir do caminho do arquivo passado por parametro.
 * Como o caminho do tipo é informado a partir da pasta raiz da web e o arquivo passado por parametro
 * tem o caminho completo, foi necessário retirar a parte do caminho até o diretório raiz web.
 */
function getTipoRegPorCaminho($arquivo):int
{

    $diretorio   = getDiretorioArquivo($arquivo);

    inserirLogDb('diretorio do arquivo',$diretorio,__FUNCTION__);
    $dirRaizWeb  = getDirRaizWeb();
    inserirLogDb('diretorio raiz web',$dirRaizWeb,__FUNCTION__);
    $diretorio   = str_replace($dirRaizWeb,'',$diretorio);
    inserirLogDb('Diretorio sem dir raiz',$diretorio,__FUNCTION__);
    $tipoReg   = getTpArqPastaRaiz($diretorio);
    inserirLogDb('Tipo de Registro a partir do arquivo sem dir raiz web ',$tipoReg,__FUNCTION__);
    return $tipoReg;

}


function getDiretorioArquivo($arquivo)
{
    $retorno = '';
    $logPriSep = false;
    $separador = getSeparadorArquivo($arquivo);
    $aPastas = explode($separador,$arquivo);
    inserirLogDb('array dir',$aPastas,__FUNCTION__);
    if(is_array($aPastas)){
        $tam = count($aPastas);
        incrNivelCorrenteLogDb();
        for($i=0;$i<$tam - 1;$i++){
             $diretorio = $aPastas[$i];
             $retorno = util_incr_valor($retorno,$diretorio,$separador,false);
             if($retorno == '' and $i == 0){
                 $logPriSep = true;
             }
             inserirLogDb('diretorio_parcial'.$i,$retorno,__FUNCTION__);
        }
        decrNivelCorrenteLogDb();
    }
    /*if(substr($arquivo,0,1) == $separador){
        $retorno = "{$separador}{$arquivo}";
    }*/
    if($logPriSep == true){
        inserirLogDb('Primeira posição do array branca','acrescimo de separador no inicio',__FUNCTION__);
        $retorno = $separador.$retorno;
    }

    return $retorno;


}
function getSeparadorArquivo($arqParam)
{
    if(!empty($arqParam)){
        //var_dump($arqParam);
        if(strstr($arqParam,'/') <> false){
            $separador ='/';
        }else{
            $separador = '\\';
        }
    }else{
        $separador ='';
    }

    return $separador;

}

function getArquivoPuro($arquivo)
{
    $arquivoPuro = '';
    if($arquivo <> ''){
        inserirLogDb('Arquivo diferente de branco:',$arquivo,__FUNCTION__);
        $separador = getSeparadorArquivo($arquivo);
        inserirLogDb('separador de diretorio',$separador,__FUNCTION__);
        $arquivo = str_replace($separador.$separador,$separador,$arquivo);
        $aArquivo = explode($separador,$arquivo);
        inserirLogDb('Array com as pastas do arquivo',$aArquivo,__FUNCTION__);
        if(is_array($aArquivo)){
            $tam = count($aArquivo);
            $arquivoPuro = $aArquivo[$tam - 1 ];
            inserirLogDb('arquivo puro atribuido ao tamanho do array - 1',$arquivoPuro,__FUNCTION__);
        }
    }else{
        inserirLogDb('Arquivo igual a branco','sim',__FUNCTION__);
    }
    return $arquivoPuro;
}
?>
