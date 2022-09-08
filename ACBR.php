<?php
function setCmdAcbr($comando,$idRand=''){
   if($idRand == ''){
       $idRand = rand();
   }
   $conteudo = $comando;
   $dir = $this->Ini->path_doc;
   $nomeArquivo = $dir."/$idRand.txt";
   $arquivo = fopen($nomeArquivo,'w');
   fwrite($arquivo,$conteudo);
   fclose($arquivo);
   $destino = "/var/www/clients/client1/web2/web/acbr/entrada/$idRand.txt";
   rename($nomeArquivo,$destino);
   $arq = "$idRand-resp.txt";
    return array('id'=> $idRand,'arquivo'=>$arq);

}
function getRetCmdAcbr($arquivo)
{

    $arqResp = "/var/www/clients/client1/web2/web/acbr/saida/$arquivo";
    $arqExiste = 0;
    $i = 1;
    $iLinha = 1;
    while($i < 500000 and $arqExiste == 0){

        if (file_exists($arqResp)) {
            $arqExiste = 1;
        } else {
            //echo "<br> O arquivo NÂO existe - $i";
        }
        $i++;

    }
    $dadosTxt = file_get_contents($arqResp);
    $aDadosCnae2 = explode('CNAE2=',$dadosTxt);
    if(array_key_exists( 1, $aDadosCnae2 )) {
        $aDadosCnaesSecs = explode('Cidade=', $aDadosCnae2[1]);
    }
    //var_dump($aDadosCnaesSecs);
    if($arqExiste == 1){
        $resp = fopen($arqResp,'r');
        $a = array();
        while(!feof($resp)){
            $linha = fgets($resp, 1024);
            //echo "LInha -> ".$linha . '<br />';
            $inicio   = substr($linha,0,2);
            $iniChave = "[";
            $pos = strpos($linha,$iniChave);
            if($inicio == "ER"){
                $a = 'erro';
                $aDadosCnaesSecs = '';
            }else if($inicio == "OK"){
                continue;
            }
            if($pos === false){
                //echo "<h1>Não Achei</h1>";
            }else{
                continue;
            }
            $aDados = explode('=', $linha);
            //echo "<pre>";
            //var_dump($aDados);
            if(is_array($aDados)){
               if(array_key_exists( 1, $aDados )){
                   $dado = $aDados[1];
                   array_push($a,$iLinha,$dado);
               }
            }
            $iLinha++;
        }
        fclose($resp);
        return array('dados'=> $a,'cnaes_sec'=>$aDadosCnaesSecs);
        //return $a;
    }

}

function getDadosCNPJ($cnpj){
    $campos = 'transacao_id,
   status_cli,
   tipo,
   abertura,
   nome_cli,
   nome_fantasia,
   natureza_juridica,
   logradouro,
   num_logradouro,
   complemento,
   cep,
   bairro,
   municipio,
   uf,
   email,
   telefone,
   situacao,
   dt_sit_cadastral,
   sit_especial,
   nome_abrev,
   cnpj';
    $aDados = getDados('multi','dados_clientes',$campos,"cnpj=$cnpj",'integracoes');
    return $aDados;

}

function sincrDadosCNPJ($programa,$dadosCnpj,$cnpj)
{
    //var_dump($dadosCnpj);
    $cnae1        = $dadosCnpj["dados"][7];
    $cnae2        = $dadosCnpj["cnaes_sec"][0];
    iniciarTransacao($programa);
    //$aDados = array();
    /*$abertura     = $dadosCnpj["dados"][1];
    $bairro       = $dadosCnpj["dados"][3];
    $cep          = $dadosCnpj["dados"][5];
    $cnae1        = $dadosCnpj["dados"][7];
    $cnae2        = $dadosCnpj["cnaes_sec"][0];
    $cidade       = $dadosCnpj["dados"][11];
    $complemento  = $dadosCnpj["dados"][13];
    $tipoEmpresa  = $dadosCnpj["dados"][15];
    $endereco     = $dadosCnpj["dados"][17];
    $nomeFantasia = $dadosCnpj["dados"][19];
    $natJuridica  = $dadosCnpj["dados"][21];
    $numero       = $dadosCnpj["dados"][23];
    $razaoSocial  = $dadosCnpj["dados"][25];
    $situacao     = $dadosCnpj["dados"][27];
    $uf           = $dadosCnpj["dados"][29];*/


    /*inserirDadosClientesCnpj(1,'','',$tipoEmpresa,$abertura,$razaoSocial,$nomeFantasia,$natJuridica,$endereco,
        $numero,$complemento,$cep,$bairro,$cidade,$uf,'','','',
        $situacao,'','','','',$cnpj);*/

    $aCnaePrincipal   = explode(' - ',$cnae1);
    $codCnaePrincipal  = $aCnaePrincipal[0];
    $descCnaePrincipal = $aCnaePrincipal[1];
    inserirAtividadesClientes($codCnaePrincipal, $descCnaePrincipal, 1,$cnpj);
    $aCnaesSec = explode('|',$cnae2);
    foreach($aCnaesSec as $cnaesSec){
        $aCnaesSec2 = explode(' - ',$cnaesSec);
        if(array_key_exists( 0, $aCnaesSec2 )) {
            $codCnaeSec = $aCnaesSec2[0];
        }
        if(array_key_exists(1,$aCnaesSec2)){
            $descCnaeSec = $aCnaesSec2[1];
        }
        inserirAtividadesClientes($codCnaeSec, $descCnaeSec, 0,$cnpj);
    }

    finalizarTransacao();

    /*$aDados[] = array('abertura'=>$abertura,
                      'bairro'=>$bairro,
                      'cep'=>$cep,
                      'cnae1'=>$cnae1,
                      'cnae2'=>$cnae2,
                      'cidade'=>$cidade,
                      'complemento'=>$complemento,
                      'tipo_empresa'=>$tipoEmpresa,
                      'endereco'=>$endereco,
                      'nome_fantasia'=>$nomeFantasia,
                      'natureza_juridica'=>$natJuridica,
                      'numero'=>$numero,
                      'razao_social'=>$razaoSocial,
                      'situacao'=>$situacao,
                      'uf'=>$uf,);

    return $aDados;*/



}

