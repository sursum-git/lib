<?php
//__NM____NM__FUNCTION__NM__//
//__NM____NM__FUNCTION__NM__//
function getDirItem($tipoDir,$item,$logMiniatura=false)
{
    $dirFisEst  = getDirFisEstampas($logMiniatura);
    $dirRel     = getDirRelEstampas($logMiniatura);
    $dirFisLiso = getDirFisLiso();
    $dirRelLiso = getDirRelLiso();
    $tipoItem   = getTipoItem($item);

    if($tipoDir == 'fisico'){
        $aDir    = array('estampado' => $dirFisEst, 'liso' => $dirFisLiso);
    }else{
        $aDir    = array('estampado' => $dirRel, 'liso' => $dirRelLiso);
    }


    return $aDir;

}
function retornarImagensItemRef($item,$referencia,$logTipoRetArq='relativo',$logMiniatura=false)
{
    $log = '';
    $qtRegra = 0;
    $imagem = array();
    $referencia = strtoupper($referencia);
    $lImagem = 0;
    $refMenor = '';
    $logBuscaPorAprox = false;
    $tipoBusca = 0; // 1- Por Item/Referencia 2- Por Descrição(não se aplica a esta função)   3- Manual 4- apenas REF

    $aDirFis = getDirItem('fisico',$item,$logMiniatura);
    $aDirRel = getDirItem('relativo',$item,$logMiniatura);

    //tratamento para abarcar referentes de 3 ou mais digitos
    $tam = strlen($referencia);
    if($tam > 2){
        $log .= "<h3>A referencia é maior que 2 - $tam </h3>";
        $tam  -= 1;
    }


    while($qtRegra < 2){
        $qtRegra++;
        switch($qtRegra){
            case 1:
                $logBuscaPorAprox = true;
                if(substr($referencia,0,1) == '0'){
                    $log .= "<h3>O primeiro digito da referencia é zero e por isto foi retirado, pois, o formato utilizado é no formato '99'  </h3>";
                    $refMenor = substr($referencia,1,$tam);
                    //$logZero = true;
                }
                else{
                    $refMenor = $referencia;
                    $log .= "<h3>O primeiro digito da referencia não é zero </h3>";
                    //$logZero = false;
                }
                //primeiro busca no diretorio fisico de estampas com item e a referencia
                $diretorio = $aDirFis['estampado'];
                $dirRel    = $aDirRel['estampado'];
                $log .= "<h3>O caminho $diretorio é de um diretorio </h3>";
                $item      = str_replace('/','//',$item);
                // IMPORTANTE: o espaço no final(" /") é proposital para evitar pegar numeros com 3 digitos que tenham os dois primeiros numeros iguais
                $filtro    = "/$item - $refMenor /";
                $log .= "<h3>vai buscar no diretório o seguinte filtro:$filtro</h3>";
                $tipoBusca = 1;
                $logBuscaPorAprox = true;

                break;
            case 2:
                // depois busca no diretorio fisico do liso apenas com a referencia
                $diretorio = $aDirFis['liso'];
                $dirRel    = $aDirRel['liso'];
                $log .= "<h3>O caminho $diretorio é de um diretorio </h3>";

                $filtro    = "{$referencia}.jpg";

                $log .= "<h3>NAO achou o arquivo do filtro  e vai buscar só pela referencia $filtro</h3>";
                $tipoBusca = 4;
                $logBuscaPorAprox = false;
                break;

        }
        if($logTipoRetArq == 'relativo'){
            $dirFinal = $dirRel;
        }else{ //fisico
            $dirFinal = $diretorio;
        }
        if(is_dir($diretorio)){
            $img = getArquivo($diretorio,$dirRel,$filtro,$log,$logTipoRetArq,$logBuscaPorAprox);
            //var_dump($img);
            if($img <> ''){
                $arquivo = $img['caminho'];
                $log     = $img['log'];
                $log .= "<h3>achou o arquivo: $arquivo</h3>";
                $img['log'] = $log;
                $imagem[0] = $img;
                array_push($imagem[0],array('tipo_busca'=>$tipoBusca,'qt_palavras'=>0));
                $lImagem = 1;
                break;
            }else{
                $log .= "<h3>Não encontrei o arquivo</h3>";
            }
        }else{
            $log .= "<h3>diretorio não encontrado, retornei a imagem em branco</h3>";
            $imagem[0] = array('caminho'=> "$dirFinal/branco.jpg",'log'=>$log, 'tipo_busca' => $tipoBusca,'qt_palavras'=>0,'tipo'=>'');
            $lImagem = 1;
        }
    }
    //echo "<h1>$log</h1>";
    if($lImagem == 0){
        $imagem[0] = array('caminho'=> "$dirFinal/branco.jpg",'log'=>$log,'tipo_busca'=>'','qt_palavras'=>0,'tipo'=>'');
    }
    return $imagem;
}
function retornarBookItem($item,$descricao,$container=0,$ref='')
{

    $tipoBusca = 0; // 1- Por Item/Referencia 2- Por Descrição 3- Manual 4- Apenas REF
    $qtPalavras = 0;
    $log = '';
    $log.= "<h1>Inicio - container $container</h1>";
    //$lAchou = false;
    $book = '';
    //monta caminho da imagem para PI ou PE
    if($container == '' or $container == 0){
        $diretorio  = getDirFisBooks('PE',$item);
        $dirRel     = getDirRelBooks('PE',$item);

        //$dirRel = buscarParametroIma ('dir_rel_books');
    }else{
        $log.="<h3>entrei na busca de diretorio do PI</h3>";
        $diretorio = getDirFisBooks('PI',$item);
        $dirRel    = getDirRelBooks('PI',$item);
    }
    /*var_dump($diretorio);
    var_dump($dirRel);*/
    $dirPrinc       = $diretorio[0];
    $dirSecund      = $diretorio[1];
    $dirRelPrinc    = $dirRel[0];
    $dirRelSecund   = $dirRel[1];

    $log.="<h2>Diretorio Físico Principal:$dirPrinc</h2>";
    $log.="<h2>Diretorio Físico Secundário:$dirSecund</h2>";
    $log.="<h2>Diretorio Relativo Principal:$dirRelPrinc</h2>";
    $log.="<h2>Diretorio Relativo Secundário:$dirRelSecund</h2>";
    $aDirFis = array($dirPrinc,$dirSecund);
    $aDirRel = array($dirRelPrinc,$dirRelSecund);
    //busca imagem na tabela relacionamento item x imagem
    $aDadosImagem = getDadosRelacItemArquivo('1,2',$item,$container,$ref);
    if(is_array($aDadosImagem)){
        $tipo    =  getTipoArquivo($aDadosImagem[0]['cod_tipo']) ;
        /*switch(strtolower($tipo)){
            case 'book':
                $dirRelBd = $dirRel[0];
                break;
            case 'cartela':
                $dirRelBd = $dirRel[1];
                break;
        }*/
        $dirRelBd = $dirRel[0];

        $arquivo =  $dirRelBd."/".$aDadosImagem[0]['arquivo'];
        $log .="<h1>Arquivo encontrado com a regra de exceção</h1>";
        $tipoBusca = 3;
        $book    =  array('caminho' => $arquivo,'tipo_busca' => $tipo,'log' => $log,'qt_palavras'=>0,
            'log_achou'=>1);

    }else{
        //caso não ache a imagem no relacionamento item x imagem busca no diretorio o arquivo conforme detalhado a seguir
        $tipo = '';
        $log .="<h1>Arquivo NAO encontrado com a regra de exceção</h1>";
        $tam = count($aDirFis);
        $log .="<h1>Qt.Diretorios a ser buscada a imagem: $tam</h1>";

        //retirar espaços duplos
        $descricaoAspas = str_replace('  ','*',$descricao);
        $descricao = str_replace('  ',' ',$descricao);
        $log .= "<h1>Espaços duplos foram substituidos por espaços simples</h1>";
        $aDescricao = explode ( ' ' , $descricao );
        $log .= "<h1>Descrição com espaços substituidos por *</h1>";
        $log .= "<h1>$descricaoAspas</h1>";
        for($i=0;$i < $tam;$i++){
            $diretorio = $aDirFis[$i];
            $dirRel = $aDirRel[$i];
            $log .="<h1>Buscando no diretorio de prioridade $i -> $diretorio</h1>";
            if(is_dir($diretorio)){
                $log .="<h1>$diretorio -> é um diretorio valido</h1>";
                $qtRegra = 0;
                while($qtRegra < 3){
                    $qtRegra++;
                    switch ($qtRegra){
                        case 1: //busca o item pelo código do mesmo
                            $item      = str_replace('/','//',$item);
                            //echo "<h1>Filtro Item: $item</h1>";
                            $filtro    = "/$item/";
                            $tipoBusca = 1;
                            $qtPalavras = 0;
                            $log.= "<h1>buscando com o código $item - filtro $filtro </h1>";
                            break;
                        case 2: //não achou pelo código, vai buscar pela descrição com 3 palavras
                            $log .= "<h1>não achei pelo código vou buscar pela descrição com 3 palavras</h1>";
                            if(count($aDescricao) >= 3) {
                                $log .= "<h1>descrição maior ou igual a 3 palavras</h1>";
                                $descItem = ucfirst(strtolower($aDescricao[0])) . ' ' . ucfirst(strtolower($aDescricao[1])) . ' ' . ucfirst(strtolower($aDescricao[2]));
                                $descItem = retirarAcento($descItem);
                                //echo "<h1>filtro:$descItem</h1>";
                                $descItem = str_replace('/', '_', $descItem);
                                $filtro = "/$descItem/";
                                $log .= "<h1>filtro exato:{$filtro}</h1>";
                                $log .= "<h1>Tecido procurado pelo filtro da descrição 3 palavras:$descItem</h1>";
                                $tipoBusca = 2;
                                $qtPalavras = 3;
                            }
                            break;
                        case 3:
                            $log .= "<h1>não achei pela descrição com 3 palavras, vou buscar com 2 palavras</h1>";
                            if(count($aDescricao) >= 2) {
                                // com 2 palavras
                                $descItem = ucfirst(strtolower($aDescricao[0])) . ' ' . ucfirst(strtolower($aDescricao[1]));
                                $descItem = retirarAcento($descItem);
                                $descItem = str_replace('/', '_', $descItem);
                                $filtro = "/$descItem/";
                                $log .= "<h1>2-Tecido encontrado pelo filtro da descrição 2 palavras:$descItem</h1>";
                                $tipoBusca = 2;
                                $qtPalavras = 2;
                            }
                            break;
                    }
                    $book= getArquivo($diretorio,$dirRel,$filtro,$log);
                    if($book <> ''){
                        $log = $book['log'];
                        $arquivo = $book['caminho'];
                        $log.= "<h1>Arquivo encontrado: $arquivo</h1>";
                        $book['log'] = $log;
                        $book['tipo_busca'] = $tipoBusca;
                        $book['qt_palavras'] = $qtPalavras;
                        $book['log_achou'] = 1;
                        break 2;
                    }else{
                        $log.= "<h1>Não Encontrado -> prioridade $i</h1>";
                    }
                }

            }else{
                $log.="<h1>Não encontrado - Sem Diretório </h1>";
                if($i == $tam - 1){
                    $book = array('caminho' => "$dirRel/branco.jpg","tipo_busca"=>'','qt_palavras'=>0,
                        'log' => $log, 'tipo' => $tipo, 'log_achou'=>0);
                }
            }
        }
    }
    if ( $book == '' ) {
        $book = array('caminho' => "$dirRel/branco.jpg","tipo_busca"=>'','qt_palavras'=>0,'log' => $log,
            'tipo' => $tipo ,'log_achou'=>0);

    }
    return $book;
}
function getArquivo($diretorio,$dirRel,$filtro,$log,$logTipoRetArq='relativo',$logBuscaPorAprox=true)
{

    $aRet = '';
    $arquivo = '';
    $lAchou = false;
    //echo "log busca por aprox: $logBuscaPorAprox";
    if($logBuscaPorAprox){

        $dir = new DirectoryIterator( $diretorio );
        //echo "<h1>arq2 - $dir | $filtro</h1>";
        $filtro = new RegexIterator( $dir , $filtro );
        //var_dump($filtro);
        foreach ($filtro as $arquivo) {
            //$log .="<h1>Arquivo:$arquivo </h1>";
            //echo "<h4>Arquivo:$arquivo </h4>";
            $arquivo = limparNomeArquivo($arquivo);
            $lAchou =true;
            break;
        }
    }else{
        $separador = getSeparadorArquivo($diretorio);
        $arquivoBusca = "{$diretorio}{$separador}{$filtro}";
        if(file_exists($arquivoBusca)){
           $lAchou = true;
           $arquivo = $filtro;
        }
    }
    if($lAchou){
        if($logTipoRetArq == 'relativo'){
            $dirFinal = $dirRel;
        }else{ //fisico
            $dirFinal = $diretorio;
        }
        $aRet = array('caminho' => "$dirFinal/$arquivo","tipo"=>'','log' => $log);
    }
    return $aRet;
}

function getDadosRelacItemArquivo($tipoParam, $itemParam, $nrContainer=0, $ref='')
{
    //echo "<h1>tipo:$tipoParam</h1>";
    inserirLogDb('parametros - Tipo - item - NrContainer - ref',"$tipoParam - $itemParam - $nrContainer - $ref",__FUNCTION__);
    $tabela = 'pub.relacs_item_arquivo';
    $campos = 'arquivo,cod_tipo,relac_item_arquivo_id,it_codigo,cod_refer';
    $condicao = "it_codigo = '$itemParam' and cod_refer='$ref' and container_id = $nrContainer 
    and cod_tipo in ($tipoParam) and dt_hr_exclusao is null ";
    $aRetorno = retornoMultReg($tabela,
        $campos,
        $condicao,"espec",'',false);

    return $aRetorno;
}

function getTipoArquivo($tipo)
{
    $retorno = 'Book';
    switch ($tipo){
        case 1:
            $retorno = 'Book';
            break;
        case 2:
            $retorno = 'Cartela';
            break;
    }
    return $retorno;
}
function limparNomeArquivo($arquivo)
{
    if(substr($arquivo,0,2) == '._'){
        $arquivo = substr($arquivo,2, strlen($arquivo) - 2);
    }
    return $arquivo ;

}

function getDirFisBooks($tipoParam="PE",$item)
{
    //$aTipoItem = array();
    $tipoItem = getTipoItem($item);
    //echo "<h1>tipo item</h1>";
    //echo var_dump($tipoItem);
    if($tipoItem == 'estampado'){
        $tipoItem = 'estampados';
        $aTipoItem=array('estampados','lisos');
    }else{
        $tipoItem = 'lisos';
        $aTipoItem= array('lisos','estampados');
    }
    //echo "<h1>tipo:$tipoParam</h1>";
    //echo "<h1>tipo comp:".strtoupper(trim($tipoParam))."</h1>";
    if(strtoupper(trim($tipoParam))=="PI"){
        //echo "<h1>entrei no PI</h1>";
        $pasta = "/PI";
        $param = "_PI";
    }else{
        //echo "<h1>entrei no PE</h1>";
        $pasta= "/PE";
        $param = '_PE';
    }
    $tam= count($aTipoItem);
    for($i=0;$i < $tam;$i++){
        $tpItemCorrente = $aTipoItem[$i];
        $parametro = "diretorio_fisico_books_{$tpItemCorrente}{$param}";
        //echo "<h1>parametro que foi buscado: $parametro</h1>";
        $paramDirFisBooks = buscarParametroIma($parametro);
        if($paramDirFisBooks == ''){
            $diretorio[$i] = "/var/www/clients/client1/web2/web/estampas/books_{$tpItemCorrente}$pasta";
        }else{
            $diretorio[$i] = $paramDirFisBooks;
            //echo '<h1>encontrei o parametro</h1>';
            //var_dump($diretorio);
        }
    }

    return $diretorio;
}

function getDirRelBooks($tipo="PE",$item)
{
    $tipoItem = getTipoItem($item);
    if($tipoItem == 'estampado'){
        $tipoItem = 'estampados';
        $aTipoItem=array('estampados','lisos');
    }else{
        $tipoItem = 'lisos';
        $aTipoItem= array('lisos','estampados');
    }

    if(strtoupper($tipo)=="PI"){
        $pasta = "/PI";
        $param = "_PI";
    }else{
        $pasta= "/PE";
        $param = '_PE';
    }
    $tam= count($aTipoItem);
    for($i=0;$i < $tam;$i++){
        $tpItemCorrente = $aTipoItem[$i];
        $paramDirRelBooks = buscarParametroIma("diretorio_relativo_books_{$tpItemCorrente}$param");
        if($paramDirRelBooks == ''){
            $dirRel[$i]    =  "../../estampas/books_{$tpItemCorrente}$pasta";
        }
        else{
            $dirRel[$i]    = $paramDirRelBooks;
        }

    }

    return $dirRel;
}

function getDirFisEstampas($logMiniatura=false)
{
    if($logMiniatura == true){
        $dirFinal = '106x150';
        $param = "diretorio_fisico_estampas_mini";
    }else{
        $dirFinal = '424x600';
        $param = "diretorio_fisico_estampas";
    }
    $paramDirFisEst = buscarParametroIma($param);

    if($paramDirFisEst == ''){
        $diretorio = "/var/www/clients/client1/web2/web/estampas/estampas/{$dirFinal}";
    }else{
        $diretorio = $paramDirFisEst;
    }
    return $diretorio;
}
function getDirRelEstampas($logMiniatura)
{
    if($logMiniatura == true){
        $dirFinal = '106x150';
        $param = "diretorio_relativo_estampas_mini";
    }else{
        $dirFinal = '424x600';
        $param = "diretorio_relativo_estampas";
    }

    $paramDirRelEst = buscarParametroIma($param);
    if($paramDirRelEst == ''){
        $diretorio = "../../estampas/estampas/{$dirFinal}";
    }else{
        $diretorio = $paramDirRelEst;
    }
    return $diretorio;

}


function getDirFisLiso()
{
    $paramDirFisEst = buscarParametroIma("diretorio_fisico_liso");
    if($paramDirFisEst == ''){
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books_lisos/REF';
    }else{
        $diretorio = $paramDirFisEst;
    }
    return $diretorio;
}
function getDirRelLiso()
{

    $paramDirRelEst = buscarParametroIma("diretorio_relativo_liso");
    if($paramDirRelEst == ''){
        $diretorio = "../../estampas/books_lisos/REF";
    }else{
        $diretorio = $paramDirRelEst;
    }
    return $diretorio;

}

function getDirRaizWeb()
{
    $paramDirRelEst = buscarParametroIma("diretorio_raiz_web");
    if($paramDirRelEst == ''){
        $diretorio = "/var/www/clients/client1/web2/web/";
    }else{
        $diretorio = $paramDirRelEst;
    }
    return $diretorio;

}
/*
function getSubDirLiso()
{
    $paramSubDirLiso = buscarParametroIma("subdiretorio_liso");
    if($paramSubDirLiso == ''){
        $diretorio = 'etiqueta_cores';
    }else{
        $diretorio = $paramSubDirLiso;
    }
    return $diretorio;
}
*/
?>