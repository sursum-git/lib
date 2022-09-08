<?php

function verifNfXMlPorChave()
{

}
/*function getXmlNFPorEstabData ($estab,$data,$listaChavesDesconsiderar)
{


}*/

function getXmlNfPorChaveTss($estab,$chave,$tipoRetorno,$logApenasDocFisc=true)
{
    $contXml = '';
    $logBaixar = false;
    $aReg = getReg('tss',
        'SPED156',
        'docchv',
        "'$chave'",
    "encode(docxmlret| 'escape')::text as xml"
        );

    if(is_array($aReg)){
        $contXml = $aReg[0]['xml'];
        $contXml = str_replace('\000','',$contXml);
        //echo "<h1>chave: $chave - NFE ".strtolower(substr($contXml,1,6))."</h1>";
        //echo "<h1>chave: $chave - CTE ".strtolower(substr($contXml,40,6))."</h1>";
        if($logApenasDocFisc){
            //nfe
            if(strtolower(substr($contXml,1,6)) == 'nfeproc'){
                $logBaixar = true;
            }
            //cte
            if(strtolower(substr($contXml,40,6)) == 'cteproc'){
                $logBaixar = true;
            }
        }else{
            $logBaixar = true;
        }

        if($logBaixar){
            switch ($tipoRetorno){
                case 'arquivo':
                    $nomeArquivo = "{$chave}.xml";
                    return gravarArquivoDirXml($estab,$nomeArquivo,$contXml);
                    break;
                case 'conteudo':
                    return $contXml;
                    break;
            }
        }

    }
}


