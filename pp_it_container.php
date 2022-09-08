<?php
//__NM____NM__FUNCTION__NM__//

function verificarEstPINovoFormatoRef()
{
    $retorno = 0;
    $lista = retornarListaLegenda();
    $sql = " select count(pub.\"pp-it-container\".\"cod-refer\")  from
	pub.\"pp-container\", pub.\"pp-it-container\"	
	where pub.\"pp-container\".\"nr-container\" = pub.\"pp-it-container\".\"nr-container\" and  pub.\"pp-container\".situacao = 1 and
			 INSTR ('$lista', substr(pub.\"pp-it-container\".\"cod-refer\",1,1)) > 0 ";
    sc_lookup(cod_refer,$sql,"esp_pro");
    if (!{cod_refer} === false && !empty({cod_refer} )){
    $retorno = {cod_refer[0][0]};
	}
	return $retorno;
}

function getPriChegItem($itemParam)
{
    //echo "<h1>dentro getPriChegItem</h1>";
    $retorno = '';
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.\"pp-container\" container, pub.\"pp-it-container\" item_container ";
    $campos   = " top 1 \"dt-prev-chegada\" as dt,container.\"nr-container\" as nr_container ";
    $condicao = "  container.\"nr-container\" = item_container.\"nr-container\"
                    and container.situacao = 1 and item_container.\"it-codigo\" = '$itemParam' order by  \"dt-prev-chegada\" ";
    $conexao  = " espec ";
    //echo "<h1>dentro getPriChegItem 1 </h1>";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    //echo "<h1>dentro getPriChegItem 2</h1>";
    if(is_array($aDados)){
        $dtPrevChegada = $aDados[0]['dt'];
        $dtPrevChegada = sc_date($dtPrevChegada, "yyyy-mm-dd", "+", 7, 0, 0);
        $nrContainer   = $aDados[0]['nr_container'];
        if($dtPrevChegada == ''){
            $retorno = " $nrContainer(não informada)";
        }else{
            //$retorno = $dtPrevChegada;
            //echo "<h1>dentro getPriChegItem 1 </h1>";
            $retorno = getQuinzenaMesAnoData($dtPrevChegada);
            //echo "<h1>dentro getPriChegItem 2 </h1>";
        }
    }
    return $retorno;

}

function getItensContainer($nrContainer)
{
    $aReg = getDados('multi','pub."pp-it-container" ','distinct "it-codigo" as it_codigo',
        " \"nr-container\" = $nrContainer ");
    $listaItens = '';
    if(is_array($aReg)){
        $tam = count($aReg);
        for($i=0;$i< $tam; $i++){
            $item = $aReg[$i]['it_codigo'];
            $listaItens = util_incr_valor($listaItens,$item,',',true);
        }
    }
    return $listaItens;
}

function getMoedaPriPrecoContainer($nrContainer)
{
    $aReg = getReg('espec','pp-it-container','"nr-container"',$nrContainer,
        'top 1 "mo-codigo" as moeda');
    if(is_array($aReg)) {
        $moeda = $aReg[0]['moeda'];
    }else{
        $moeda = 0;
    }
    if($moeda == 0){
        $moeda = "real";
    }else{
        $moeda  = "dolar";
    }
    return $moeda;
}

function getListaContainersAbertoItem($item)
{
    $listaContainers = '';
    $aContainers = getDados('multi',' "pp-it-container" item, "pp-container" container',
        ' distinct  container."nr-container" as nr_container, item."it-codigo" as it_codigo ',
        " container.situacao = 1 and item.\"it-codigo\" = '$item' ");
    if(is_array($aContainers)){
        $tam = count($aContainers);
        for($i=0;$i< $tam; $i++){
            $container = $aContainers[$i]['nr_container'];
            $listaContainers =  util_incr_valor($listaContainers,$container,',') ;
        }
    }
    return $listaContainers;

}

function getContainersAbertoItem($item)
{
    //$listaContainers = '';


    $aContainers = getDados('multi',' pub."pp-it-container" item, pub."pp-container" container',
        ' distinct  container."nr-container" as nr_container, item."it-codigo" as it_codigo ',
        " container.situacao = 1 and item.\"it-codigo\" = '$item' and item.\"nr-container\" = container.\"nr-container\" ",
        "espec");
    $codRepres = getVarSessao('glo_codRepIni');
    if($codRepres <> 0){ // eh um repres ou preposto
        if(is_array($aContainers)){
            foreach($aContainers as $key => $reg){
                $container =$reg['nr_container'];
                $exclusivo = vericaContainerExclusivo($container);
                if($exclusivo == 1){
                    $aContainersPermis = getRegContainerPermissao($container,$codRepres);
                    if(!is_array($aContainersPermis)){
                        unset($aContainers[$key]);
                    }
                }
            }
        }
    }
    if(is_array($aContainers)) {
        $aContainers = array_values($aContainers);
    }


    return $aContainers;

}

function getLinkBooksContainerItem($item,$indBookDinamico=0)
{
    $tipoItem = getTipoItem($item);
    $linkCompleto = "";
    $aContainers = getContainersAbertoItem($item);
    if(is_array($aContainers)){
        $tam = count($aContainers);
        for($i=0;$i< $tam; $i++){
            $item       = $aContainers[$i]['it_codigo'];
            $container  = $aContainers[$i]['nr_container'];
            $aItem      = buscarDadosItem($item);
            /*echo "<h1>vardump - $container</h1>";
            var_dump($aItem);*/
            if(is_array($aItem) ){
                $descItem   = $aItem[0]['desc-item'];
                $aArquivo   = retornarBookItem($item,$descItem,$container);
                //var_dump($aArquivo);
                if(is_array($aArquivo)){
                    $arqCompleto = $aArquivo['caminho'];
                    //echo "<h1>caminho - $arqCompleto</h1>";
                    $logAchouBook = $aArquivo['log_achou'];
                }else{
                    $arqCompleto = '';
                    $logAchouBook = 0;
                }
                /*echo "<h1>arquivo completo: $arqCompleto <br>" .
                    $aArquivo['log']
                    . "</h1>";*/
                switch ($indBookDinamico){
                    case 0:
                        $caminhoArquivo = $arqCompleto;
                        break;
                    case 1:
                        if($logAchouBook){
                            $caminhoArquivo = $arqCompleto;
                        }else{
                            $caminhoArquivo = "../bl_gerar_book/bl_gerar_book.php?itens_refs_sel=$item&nr_container_corrente=$container";
                        }
                        break;
                    case 2:
                        /*
                        if($tipoItem == 'liso'){
                            $caminhoArquivo = "../bl_gerar_book/bl_gerar_book.php?itens_refs_sel=$item&nr_container_corrente=$container";
                        }else{
                            $caminhoArquivo = "../ctrl_tipo_book/ctrl_tipo_book.php?arq_completo=$arqCompleto&itens_refs_sel=$item&nr_container_corrente=$container";
                        }*/
                        $caminhoArquivo = "../ctrl_tipo_book/ctrl_tipo_book.php?arq_completo=$arqCompleto&itens_refs_sel=$item&nr_container_corrente=$container";

                }
                $link       = "<a href='$caminhoArquivo' target='_blank'>$container</a>";
                $linkCompleto = util_incr_valor($linkCompleto,$link,'<br/>',true);
            }else{
                $linkCompleto = '';
            }
        }
    }
    return $linkCompleto;

}
?>
