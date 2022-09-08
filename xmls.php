<?php
function criarArqXmlNf($estab,$chave,$xml): string
{
    $dir = getDirAnexosEmail($estab);
    $nomeArquivo = "$chave.xml";
    $nomeArquivo = juntarDirArq($dir,$nomeArquivo);
    gravarConteudoArquivo($nomeArquivo,$xml);
    return $nomeArquivo;

}
function inserirXml($estab,$dtEmisNota,$chave,$origem,$tipoDocto,$xml)
{
    $arqXml = criarArqXmlNf($estab,$chave,$xml);
    $aInsert = array('cod_estab'=>$estab,
        'dt_emissao'=>$dtEmisNota,
        'chave'=>$chave,
        'conteudo_xml'=>$xml,
        'arquivo_xml'=>$arqXml);
}
function getDescrSitXml($sit)
{
    $sitXml = '';
    switch ($sit){
        case 0:
            $sitXml = "XML Baixado";
            break;
        case 1:
            $sitXml = "Lido - XML Em Integração";
            break;
        case 2:
            $sitXml = "Lido - XML Com Erro";
            break;
        case 3:
            $sitXml = "Lido - XML Inválido";
            break;
    }
    return $sitXml;
}
function getListaChavesPorEstabData($estab,$data)
{

    $aResult = getDados('multi',
    'xmls','chave',"where cod_estab = '$estab'
    and dt_emissao = '$data' ","wdfe");
    return convArrayMultParaLista($aResult,'chave',true);
}
function getArqXml($estab,$chave): array
{
    $sitXml = "XML NÃO Baixado";
    $codSitXml = -1 ;
    $linkArquivo = '';
    $logBaixado = false;
    $arquivoXml = '';

    $dir                = getDirAnexosEmail($estab);
    $dirRel             = getDirRelAnexosEmail($estab);
    $listaSubDir    = getSubDirAnexosXml();
    $aListaSubDir = explode(",",$listaSubDir);
    for($i=0;$i < count($aListaSubDir);$i++) {
        if ($aListaSubDir[$i] == '') {
            $dir = juntarDirArq($dir, $aListaSubDir[$i]);
            $dirRel = juntarDirArq($dirRel, $aListaSubDir[$i]);
        }
        //echo "<h3>diretorio para procurar: $dir</h3>";
        $aArquivo = getArquivo($dir,
            $dirRel,
            "/$chave/",
            'relativo',
            true);
        if(is_array($aArquivo)){
            $linkArquivo = $aArquivo['caminho'];
            $aLinkArquivo = explode('.',$linkArquivo);
            $logXML = compararVlUltPosicaoArquivo($aLinkArquivo,'xml');
            if($linkArquivo <> '' and $logXML){
                $logBaixado = true;
                $sitXml = getDescrSitXml($i);
                $codSitXml = $i;
                /*echo "<h3>cod sit.xml:$codSitXml     </h3>";
                echo "<h3>sit.xml:$sitXml     </h3>";*/
                break;
            }else{
                $linkArquivo = '';
            }
        }
    }
    if($linkArquivo <> ''){
        $linkArquivoWeb = "<a href='$linkArquivo' target='_blank'>Baixar</a>";
    }else{
        $linkArquivoWeb = '';
    }
    return  array('arquivo_xml'=>  $arquivoXml,
        'link'=>  $linkArquivo,
        'log_baixado'=>  $logBaixado,
        'cod_sit_xml'=>  $codSitXml,
        'sit_xml'=>      $sitXml,
        'link_web'=>     $linkArquivoWeb);


}
function getSubDirAnexosXml(): string
{
    return ",Lidos,Error,invalidos";
}
function getSitXmlPorChave($estab,$chave): array
{
    $sitXml = "XML NÃO Baixado";
    $codSitXml = 0 ;
    $linkArquivo = '';
    $logBaixado = false;
    $logIntegrERP = verifExistDocPorChave($chave);
    $arquivoXml = '';
    $reg =  getReg('wdfe',
            'xmls',
                "chave",
                "'$chave'",
            "arquivo_xml" );
    if(is_array($reg)){
        $arquivoXml = $reg[0]['arquivo_xml'];
    }
    $dir                = getDirAnexosEmail($estab);
    $dirRel             = getDirRelAnexosEmail($estab);
    $aRet = getArqXml($estab,$chave);
    if($logIntegrERP){
        $aRet['cod_sit_xml'] = "Integrado ao ERP";
        $aRet['sit_xml'] = 4;
    }

    return $aRet;
}
function gravarArquivoDirXml($estab,$nomeArquivo,$conteudo): string
{
    $dir                = getDirAnexosEmail($estab);
    $nomeArquivo        = juntarDirArq($dir,$nomeArquivo);
    gravarConteudoArquivo($nomeArquivo,$conteudo);
    return $nomeArquivo;
}
?>
