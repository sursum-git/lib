<?php
//__NM____NM__FUNCTION__NM__//
/*function getApiTotvs($api,$metodo,$aQueryParam,$aBody='')
{
    $url = getUrlServico();
    $url = util_incr_valor($url,$api,'');
    include_once sc_url_library("prj", "guzzle", "autoload.php");
    $client = new GuzzleHttp\Client();
    $usuario = getUserApiTotvs();
    $senha  = getSenhaApiTotvs();
    $res = $client->request($metodo,
        $url, [
        'auth' => [$usuario, $senha],
        'query'=> $aQueryParam,
        'body' => $aBody
    ]);
    //echo $res->getStatusCode();
// "200"
    //echo $res->getHeader('content-type')[0];
// 'application/json; charset=utf8'
    $j = json_decode($res->getBody());
    echo "<br>";
    //var_dump($j->items);
    $objItens = $j->items;
    //$objItens = $objItens[0];
    //var_dump($objItens);
    $obj = $j;
    $aRet = array();
    $lRetorno = false;
    $lFim = true;
    $qtWhile = 0;
    $qt = 0;

    foreach( $objItens as $chave =>$valor ){
            $retorno = $valor->ttRetorno;
            //var_dump($retorno);
            foreach ($retorno as $chave =>$valor){
                $msg = $valor->descricao;

            }

        }


   return $msg;
}*/

function getApiTotvsOld($api,$metodo,$aQueryParam,$aBody='')
{
    $url = getUrlServico();
    $url = util_incr_valor($url,$api,'');

    $autoLoad = sc_url_library("prj", "guzzle", "autoload.php");
    //echo "<h1>$autoLoad</h1>";
    include_once $autoLoad;

    $client = new GuzzleHttp\Client();
    $usuario = getUserApiTotvs();
    $senha  = getSenhaApiTotvs();
    $res = $client->request($metodo,
        $url, [
            'auth' => [$usuario, $senha],
            'query'=> $aQueryParam,
            'body' => $aBody
        ]);
    //echo $res->getStatusCode();
// "200"
    //echo $res->getHeader('content-type')[0];
// 'application/json; charset=utf8'

    $j = json_decode($res->getBody());
    return $j;
}
function trocarVersaoApi($api,$novaVersao)
{
    $novaApi = '';
    $aApi = explode('/',$api);
    if(is_array($aApi)){
       $tam = count($aApi);
       for($i=0;$i<$tam;$i++){
           if($i == $tam){
               $separador ="";
           } else{
               $separador ="/";
           }
           if($i == 1){ // posição versão
               $novaApi = util_incr_valor($novaApi,$novaVersao,$separador);
           }else{
              $novaApi = util_incr_valor($novaApi,$aApi[$i],$separador);
           }
       }
    }
    return $novaApi;
}

function getApiTotvs($api,$metodo,$params)
{
    //se o ambiente é de homologação muda a api para versão 99 ou a versão do parametro
    $lHomolog = false;
    $uri = $_SERVER["REQUEST_URI"];
    if(strpos($uri,'homolog') <> false){
        $lHomolog =true;
    }
    if($lHomolog){
        $api = trocarVersaoApi($api,getVersaoApiHomolog());
    }

    $url = getUrlServico();
    $url = util_incr_valor($url,$api,'');
    include_once sc_url_library("prj", "guzzle", "autoload.php");
    $client = new GuzzleHttp\Client();
    $usuario = getUserApiTotvs();
    $senha  = getSenhaApiTotvs();
    switch($metodo){
        case 'GET':
            $chave= 'query';
            break;
        case 'POST':
            $chave= 'json';
            break;
    }
    $res = $client->request($metodo,
        $url, [
            'auth' => [$usuario, $senha],
             $chave => $params
        ]);
   /* echo "<h1>".$res->getStatusCode()."</h1>";
    echo "<h1>".$res->getReasonPhrase()."</h1>";
    echo "<h1>".$res->getProtocolVersion()."</h1>";
*/
// "200"
    //echo $res->getHeader('content-type')[0];
// 'application/json; charset=utf8'
    $j = json_decode($res->getBody());
    return $j;
}

function getDadosApis($caminho,$metodo,$aParams,$campos=''){

    $aRet = getApiTotvs($caminho,$metodo,$aParams);
    $aDadosLogs = getValsArrayMultiDin($aRet,$campos);
    return $aDadosLogs;
}


?>
