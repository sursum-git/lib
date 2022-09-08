<?php
function getDocsManifSemIntegr($qtDiasBusca)
{
    $aRegs = getDados('multi',
    'docto-manif-destin',
    /*'"cod-estab" as cod-estab,
              "dat-emis-nf-eletro" as data,
              "cod-chave-nfe as chave "'*/
        '',
    '"cod-chave-nfe" not in
        (select  "cod-chave-aces-nf-eletro"
            from pub."docum-est" docto
            where docto."dt-emissao"  = "dat-emis-nf-eletro")
            and "dat-emis-nf-eletro" >= curdate() - '.$qtDiasBusca
        ,
    'med');
    //var_dump($aRegs);
    foreach($aRegs as $reg){
        $codEstab   = $reg['"cod-estab"'];
        $dtEmissao  = $reg['"dat-emis-nf-eletro"'];
        $chave      = $reg['"cod-chave-nfe"'];
        $aArq       = getArqXml($codEstab,$chave);
        if($aArq["cod_sit_xml"] == -1 ){ //Não Baixado
            //echo "<h1>estab: $codEstab - chave: $chave  </h1>";
            //var_dump($aArq);
            $arquivo = getXmlNfPorChaveTss($codEstab,$chave,'arquivo');
            echo "<h2>$arquivo</h2>";
        }
    }
}
