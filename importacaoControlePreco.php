<?php

function lerArqCampanhaPreco($caminho)
{
    $aRetorno = array();
    $lAchou = false;
    //$caminho = "/var/www/clients/client1/web2/web/sc94/file/doc/sc_csv.csv";
    if(file_exists($caminho)){
        $arquivo = fopen($caminho, 'r');
        $iBr = 0;
        $iCont = 0;
        while (!feof($arquivo)) {
            $linha = fgets($arquivo, 1024);
            //echo "LInha -> ".$linha . '<br />';
            $ini = substr($linha,0,2);

            if ($ini == ''){
                continue;
            }

            $valor = explode(';', $linha);
            $valor = str_replace(chr(34),'',$valor);
            //echo "<pre>";
            //var_dump($valor);
            if($valor[1] == ''){
                $iBr++;
            }
            if($iBr == 2){
                break;
            }

            $item = $valor[1];
            //echo $item . "<br>";
            $ref = $valor[4];
            //echo $ref . "<br>";
            $precoDigi = $valor[7];
           // echo $precoDigi . "<br>";

            $aIndice = buscarIndice();
            //var_dump($aIndice[3]['indice']);
            $indice = $aIndice[3]['indice'];
            $precoDigi = str_replace(',','.',$precoDigi);
            $precoDigi = (double)$precoDigi;
            if(is_double($precoDigi)){
                $precoBase = $precoDigi / $indice;
            }else{
                continue;
            }
           // echo "<h1>item:$item - Ref:$ref - preco base: $precoBase - preco dig: $precoDigi - indice: $indice</h1>";
            $precoBase = number_format($precoBase,10,',','.');
            //echo $precoBase . "<br>";

            $aRetorno[] = array(

                'item'       => $item,
                'ref'        => $ref,
                'preco_base' => $precoBase
            );
            $lAchou = true;


        }
        fclose($arquivo);
    }
    if($lAchou == false){
        $aRetorno = '';
    }
    //var_dump($aRetorno);
    return $aRetorno;
}

/*function inserirCampanhaPreco($precoBase)
{

    $a = array('preco_real'=>$precoBase,'it_codigo'=>);
    convertArrayEmInsert()
    $cmd = "insert into controle_preco(vl_real)
            values('$precoBase')";
    sc_exec_sql($cmd, "especw");


}*/

function sincrPrecosCamp($idCampanha, $linhaIni=1)
{
    $erro = '';
    $aCamp = getRegCampanha($idCampanha);
    $nomeArquivo = $aCamp[0]['arquivo'];
    //echo $nomeArquivo;
    $dtIni       = $aCamp[0]["dt_inicial"];
    //echo $dtIni;
    $dtFinal     = $aCamp[0]["dt_final"];
    //echo $dtFinal;
    //$dir = getDirArqOutlet();
    //$dir = "/var/www/clients/client1/web2/web/iol_homolog/_lib/file/doc";
    $dir = getCaminhoArqCampanha();
    $arquivoCompleto = juntarDirArq($dir,$nomeArquivo);
    //echo $arquivoCompleto;
    $aReg = lerArqCampanhaPreco("/var/www/clients/client1/web2/web/sc94/file/doc/camp_maio.csv");
    //echo "<pre>";
    //var_dump($aReg);
    if(is_array($aReg)){
        $tam = count($aReg);
        for($i=$linhaIni;$i<=$tam;$i++){
            $itCodigo = $aReg[$i]['item'];
            //echo $itCodigo . "<br>";
            $codRefer = $aReg[$i]['ref'];
            $precoReal = converterDecimal($aReg[$i]['preco_base']);
            //echo "<h1>$itCodigo</h1>";
            //echo "<h1>$codRefer</h1>";
            //echo "<h1>$precoReal</h1>";
            //echo "<br>";
            if ($precoReal == ''){
                continue;
            }
            inserirPreco(1,3,$dtIni,$dtFinal,2,0,$itCodigo,$codRefer,$precoReal,0,$idCampanha);
        }
    }else{
        $erro = "Arquivo Não encontrado";
    }
    //var_dump($aReg);
    return $erro;
}

/*function getRegCampanha($campanhaId){

    $aReg = getReg('espec','campanhas','campanha_id', $campanhaId);
    return $aReg;

}*/
