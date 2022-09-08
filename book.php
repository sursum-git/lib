<?php
//__NM____NM__FUNCTION__NM__//
function getItensComTRSemRef()
{
    // primeiros 3 digitos do item
    $retorno = '237';
    $vlParam = buscarParametroIma('itens_com_tr_sem_ref');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;


}
function getDirCapaClass()
{
    $retorno = 'capas_books_customizados';
    $vlParam = buscarParametroIma('dir_capa_class');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}
function getDirCompletoCapaClass()
{
    $dirCapa      = getDirFisCapa();
    $dirCapaClass = getDirCapaClass();
    $dirCompleto  = juntarDirArq($dirCapa,$dirCapaClass);
    return $dirCompleto;
}
function getArqCapaClass($class)
{
    $dir = getDirCompletoCapaClass();
    $arq = "{$class}.jpg";
    $retorno = juntarDirArq($dir,$arq);
    return $retorno;
}
function desenharCapaClass($class,$pdf)
{
    $arq = getArqCapaClass($class);
    $pdf = desenharImgPgCompleta($arq,$pdf);
    return $pdf;

}

function getQtImgPorPgliso()
{
    $retorno = 60;
    $vlParam = buscarParametroIma('qt_img_por_pg_liso');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}
function validarItemComTRSemRef($item)
{
    $item = is_array($item) ? $item[0]:$item;
    $retorno = false;
    $inicioItem = getItensComTRSemRef();
    //echo"<h1>Inicioitem = $inicioItem</h1>";
    $aInicioItens = explode(',',$inicioItem);
    //var_dump($aInicioItens);
    if(is_array($aInicioItens)){
        $tam = count($aInicioItens);
        for($i=0;$i<$tam;$i++){
            $inicioIt = $aInicioItens[$i];
            $it = substr($item,0,3);
            if(substr($item,0,3) == $inicioIt){
                $retorno = true;
                break;
            }
        }
    }
    return $retorno;
}

function getAlturaTp1PX()
{
    $retorno = 275 ;
    $vlParam = buscarParametroIma('altura_tp1_px');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}
function getEspacamentoEstMM()
{
    $retorno = 5 ;
    $vlParam = buscarParametroIma('espacamento_est_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}

function getAlturaTp1MM()
{
    $retorno = 72 ;
    $vlParam = buscarParametroIma('altura_tp1_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;

}
function getAlturaTp2MM()
{
    $retorno = 112 ;
    $vlParam = buscarParametroIma('altura_tp2_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}
function getAlturaTp3MM()
{
    $retorno = 297 ;
    $vlParam = buscarParametroIma('altura_tp3_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}
function getLarguraTp1PX()
{
    $retorno = 680 ;
    $vlParam = buscarParametroIma('largura_tp1_px');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}

function getLarguraTp1MM()
{
    $retorno = 180 ;
    $vlParam = buscarParametroIma('largura_tp1_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;

}
function getLarguraTp2MM()
{
    $retorno = 180 ;
    $vlParam = buscarParametroIma('largura_tp2_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}
function getLarguraTp3MM()
{
    $retorno = 210 ;
    $vlParam = buscarParametroIma('largura_tp3_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}

function getPosXIniBookEst()
{
    $retorno = 15 ;
    $vlParam = buscarParametroIma('posicao_x_ini_book_est_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}

function getPosXIniBookLiso()
{
    $retorno = 15 ;
    $vlParam = buscarParametroIma('posicao_x_ini_book_liso_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}
function getPosYIniBookEst()
{
    $retorno = 50 ;
    $vlParam = buscarParametroIma('posicao_y_ini_book_est_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}

function getPosYIniBookLiso()
{
    $retorno = 50 ;
    $vlParam = buscarParametroIma('posicao_y_ini_book_liso_mm');
    if($vlParam <> ''){
        $retorno = $vlParam;
    }
    return $retorno;
}
function desenharPDFCust($classif,$pdf)
{
    $aClassif = explode(',',$classif);
    inserirLogDb('Inicio','Antes de buscar os itens e referencias - class:'.$classif,__FUNCTION__);
    if(is_array($aClassif)){
        $tamClass = count($aClassif);
        for($j=0;$j<$tamClass;$j++){
            $classifCorrente = $aClassif[$j];
            $pdf  =desenharCapaClass($classifCorrente,$pdf);
            inserirLogDb('Classificacao corrente:',$classifCorrente,
                __FUNCTION__);
            $aItensRef = getItensRefsClassif($classifCorrente);
            array_multisort($aItensRef,3);
            //var_dump($aItensRef);
            //inserirLogDb('array com os itens e refs da classificacao',$aItensRef,__FUNCTION__);
            if(is_array($aItensRef)){
                $tam = count($aItensRef);
                for($i=0;$i<$tam;$i++){
                    $itCodigo = $aItensRef[$i]['it_codigo'];
                    $listaCodRefer = $aItensRef[$i]['lista_cod_refer'];
                    inserirLogDb('Item - Refs',
                        "$itCodigo - $listaCodRefer",
                        __FUNCTION__);
                    $pdf = desenharPDF($itCodigo,
                        $pdf,0,
                        $listaCodRefer,
                        true);
                }
                inserirLogDb('FINAL PDF CUST',"QT.itens: $tam",__FUNCTION__);

            }
        }
    }

    return $pdf;
}
function desenharPDFAgrupPE($listaItens,$pdf,$refs='',$nrContainers='',$verificEst=1)
{
    //echo "<h1>listaItens:$listaItens</h1>";
    /***************
    Esta função trata o recebimento de vários itens a serem agrupados em um unico PDF para itens PE
     ***********************/
    $aRefs = explode(',',$refs);
    //var_dump($aRefs);
    if($listaItens <> ''){
        $aListaItens = explode(',',$listaItens);
        if(is_array($aListaItens)){
            $tam = count($aListaItens);
            for($i=0;$i<$tam;$i++){
                $item = $aListaItens[$i];
                if($refs <> ''){
                    $ref  = $aRefs[$i];
                    //echo "<h1>Ref=$ref</h1>";
                    $aRef = explode("|",$ref);
                    //var_dump($aRef);
                    $ref = implode(',',$aRef);
                    //echo "<h1>Ref=$ref</h1>";
                }else{
                    $ref='';
                }
                $pdf = desenharPDF($item, $pdf,0,$ref,'',$verificEst);
            }
        }

    }
    return $pdf;

}
function desenharPDF($aItemRefs, $pdf,$nrContainer=0,$listaCodRef='',$logBookCust=false,
                     $logValidarEst=1)
{

    $item = '';
    $aRefs = array();
    $aItens = array();
    $lAchouSaldo = false;
    //echo "<h1>entrei_Refs = $listaCodRef</h1>";
    //echo "<h1>log = $logValidarEst</h1>";


    //passou um item apenas por parametro
    if(!is_array($aItemRefs) and $aItemRefs <> ''){
        inserirLogDb('Nao eh um array','sim',__FUNCTION__);
        $item = $aItemRefs;
        $item = trim($item);
        inserirLogDb('ITEM PRINCIPAL',$item,__FUNCTION__);
        if($logValidarEst == 1){
            $aItensSaldo = getItemRefsComSaldo($item,$nrContainer,$listaCodRef,$logValidarEst);
            //var_dump($aItensSaldo);
        }else{
            $aItensSaldo = getArrayItemRef($item,$listaCodRef);
        }
        //var_dump($aItensSaldo);
        //$descArray = print_r($aItensSaldo,true);
        //inserirLogDb('array de saldo',$aItensSaldo,__FUNCTION__);
        $aItemRefs = $aItensSaldo['saldo_itens'];
        $itemRelac = $aItensSaldo['item_relac'];
    }else{
        inserirLogDb('Nao eh um array','nao',__FUNCTION__);
        $itemRelac = '';
    }

    $itensRelac = "$item,$itemRelac";
    inserirLogDb('Itens Relacionados',$itensRelac,__FUNCTION__);
    //inserirLogDb('Item Relac?',$itemRelac,__FUNCTION__);
    if (is_array($aItemRefs)) {
        $tam = count($aItemRefs);
        $itemAnt = '';
        //$imagemAnt = '';
        $qtItens= getQtItens($aItemRefs);
        //echo "<br>itens $qtItens<br>";
        //echo "<br>tam $tam<br>";
        inserirLogDb('array itens/ref','INICIO',__FUNCTION__);
        incrNivelCorrenteLogDb();
        for ($i = 0; $i < $tam; $i++) {
            $lAchouSaldo = true;
            //inserirLogDb('posicao',$i,__FUNCTION__);
            //echo "entrei";
            $item = $aItemRefs[$i]['item'];
            $ref  = $aItemRefs[$i]['ref'];
            //echo "entrei";
            //echo "<h1>$ref</h1>";
            inserirLogDb('posicao',$i,__FUNCTION__);
            inserirLogDb('item',$item,__FUNCTION__);
            inserirLogDb('referencia',$ref,__FUNCTION__);
            //$desenho = $aItemRef[$i]['desenho'];
            array_push($aRefs,$ref);
            array_push($aItens,$item);

            inserirLogDb('item anterior:',$itemAnt,__FUNCTION__);
            inserirLogDb('itens Relac:',$itensRelac,__FUNCTION__);

            if ($item <> $itemAnt and $itemAnt <> '' and strstr($itensRelac,$item) == false ) {
                //$descARefs   = print_r($aRefs,true);
                //$descAItens  = print_r($aItens,true);

                inserirLogDb('ultima referencia de um item ',
                    ' desde que o proximo nao seja o item relac',__FUNCTION__);
                //inserirLogDb('array de referencias antes da funcao desenharBook', $aRefs,__FUNCTION__);
                //inserirLogDb('array de itens(quando tem relacs) antes da funcao desenharBook', $aItens,__FUNCTION__);

                $pdf = desenharBook($item,$aRefs,'',$pdf,$nrContainer,$logBookCust);
                $aRefs = array();
                $aItens= array();
            }

            //echo "<h1>$qtItens</h1>";
            if((($qtItens == 1 or $itemRelac <> '') and $i == $tam - 1)){
                $descARefs   = print_r($aRefs,true);
                $descAItens = print_r($aItens,true);
                inserirLogDb('apenas um item ou mais de um item sendo o segundo item relac',
                    'sim',__FUNCTION__);
                //$logItemUnico = 0;
                if($itemRelac <> ''){
                    inserirLogDb('Tem item relac?','sim',__FUNCTION__);
                    //inserirLogDb('array de referencias antes da funcao desenharBook',$descARefs,__FUNCTION__);
                    //inserirLogDb('array de itens(quando tem relacs) antes da funcao desenharBook',$descAItens,__FUNCTION__);
                    $pdf = desenharBook($item,$aRefs,$aItens,$pdf,$nrContainer,$logBookCust);

                }else{
                    inserirLogDb('Tem item relac?','nao',__FUNCTION__);
                    //inserirLogDb('array de referencias antes da funcao desenharBook',$descARefs,__FUNCTION__);
                    //inserirLogDb('array de itens(quando tem relacs) antes da funcao desenharBook',$descAItens,__FUNCTION__);
                    $pdf = desenharBook($item,$aRefs,'',$pdf,$nrContainer,$logBookCust);
                }

                $aRefs = array();
                $aItens= array();
            }

            $itemAnt = $item;

            /*var_dump($aRefs);
            echo "<br>$ref</br>";*/

            //$imagemAnt = $aRet['imagem_ant'];

            //var_dump($aRefs);

            //chamar funcao variantes

        }
        if($lAchouSaldo == false){
            inserirLogDb('Não Achou Saldo','sim',__FUNCTION__);
            $pdf = desenharPDFNaoDisponivel($pdf);

        }
        inserirLogDb('array itens/ref','FIM',__FUNCTION__);
        decrNivelCorrenteLogDb();
    }else{
        inserirLogDb('Não é array','sim',__FUNCTION__);

        //echo "nao e array";
        //var_dump($aItemRefs);
    }

    return $pdf;
}
function desenharPDFNaoDisponivel($pdf)
{
    $imgNaoDisponivel = getImgPgNaoDisponivel();
    $pdf = desenharImgPgCompleta($imgNaoDisponivel,$pdf);
    return $pdf;
}
function convArrayPiPe($array,$origem)
{
    //inserirLogDb('array recebido por parametro:',$array,__FUNCTION__);
    //var_dump($array);
    $campoQtSaldo = $origem == 'pe' ? 'qt_saldo_venda':'qt_saldo_com_carrinho';
    $retorno = array();
    $lAchou  = false;
    if(is_array($array)){
        $tam = count($array);
        for($i=0;$i<$tam;$i++){
            $lAchou = true;
            //var_dump($array[$i]['cod_refer']);
            $retorno[] = array('it_codigo'=> $array[$i]['it_codigo'],
                'cod_refer'=> $array[$i]['cod_refer'],
                'ordem_ref' => $array[$i]['ordem_ref'],
                'desc_item' => $array[$i]['desc_item'],
                'qt_saldo' => $array[$i][$campoQtSaldo]);
        }
    }
    if($lAchou == false){
        $retorno = '';
    }
    return $retorno;
}
function getItemRefsComSaldo($item,$nrContainer=0,$listaCodRef='',$logValidarEst=1)
{
    $qtMinKg = 0;
    $qtMinMt = 0;
    $aItemRefs = array();
    $lAchou    = false;
    $tipoItem  = getTipoItem($item);
    $itemRelac = getItemRelac($item);
    $ordenacao = getVarSessao(getNomeVarOrdenacaoBook());
    if($ordenacao == ''){
        $ordenacao = 4;
    }
    if($nrContainer <> 0 ){
        setVarSessao(getNomeVarOrdenacaoBookPi(),1);
    }else{
        setVarSessao(getNomeVarOrdenacaoBookPi(),0);
    }
    inserirLogDb('Item - Item Relacionado encontrado',"$item - $itemRelac",__FUNCTION__);

    if($itemRelac <> ''){
        $filtroItem = "in('$item','$itemRelac')";
        $filtroItemPI = "'$item','$itemRelac'";
    }else{
        $filtroItem = " = '$item'";
        $filtroItemPI = "'$item'";
    }
    inserirLogDb('nr_container',$nrContainer,__FUNCTION__);
    //echo "<h1>tadeu10</h1>";
    if(isset([qt_min_kg])){
        $qtMinKg = [qt_min_kg];
    }

    if(isset([qt_min_mt])){
        $qtMinMt = [qt_min_mt];
    }
    $qtMinKg = tratarNumero($qtMinKg);
    $qtMinMt = tratarNumero($qtMinMt);


    if($nrContainer == 0){
        $refsDesconsiderar = getRefsADesconsiderar();
        if($refsDesconsiderar <> ''){
            $filtroItem .= " and \"cod-refer\" not in ($refsDesconsiderar )  ";
        }
        $filtro = " and saldo.\"it-codigo\" $filtroItem ";
        if(strlen($listaCodRef) == 1){
            $listaCodRef = '';
        }

        if($listaCodRef <> ''){
            $filtro.= " and saldo.\"cod-refer\" in($listaCodRef)";
        }
        $aPrecoEstoque = buscarSaldoEstoque('5', $filtro,
            false,
            2, //1-item 2- item /ref
            true,
            true,
            $listaCodRef );
        //echo "<h1>tadeu13</h1>";


        if($qtMinKg + $qtMinMt > 0){
            $aPrecoEstoque = filtrarPorQtRef($aPrecoEstoque, $qtMinKg,$qtMinMt,'qt_saldo_venda');
        }
        //echo "<h1>tadeu14</h1>";
        $aSlItemRef = convArrayPiPe($aPrecoEstoque,'pe');
        //echo "<h1>tadeu15</h1>";
        //var_dump($aPrecoEstoque);
    }else{
        //echo "<h1>cheguei até aqui 3 </h1>";
        $aSlPI = getQtsPI('referencia',$filtroItemPI,'',$nrContainer,'',
            false,$logValidarEst);

        if($qtMinKg + $qtMinMt > 0){
            $aSlPI = filtrarPorQtRef($aSlPI,$qtMinKg,$qtMinMt,'qt_saldo_com_carrinho');
        }
        /*echo "<br>saldo pi<br>";
        var_dump($aSlPI);*/
        $aSlItemRef = convArrayPiPe($aSlPI,'pi');
        /*echo "<br>saldo pi após conversao<br>";
        var_dump($aSlItemRef);*/
        //echo "<h1>cheguei até aqui 4 </h1>";
    }
    //inserirLogDb('array convertido',$aSlItemRef,__FUNCTION__);

    /*ordena o array por referencia*/
    if(is_array($aSlItemRef)){
        // Obtem a lista de colunas
        foreach ($aSlItemRef as $key => $row) {
            $ordemRef[$key]  = $row['ordem_ref'];

        }
        $ordenacaoPi = getVarSessao(getNomeVarOrdenacaoBookPi());
        if($ordenacaoPi == 1){
            $ordenacao = 4;
        }
        if($ordenacao == 3 and $tipoItem <> 'liso'){
            array_multisort( $ordemRef, 3, $aSlItemRef);
        }else{
            array_multisort( $ordemRef, 4, $aSlItemRef);
        }

    }
    //echo "<br>depois da ordenação<br>";
    //inserirLogDb('array convertido e ordenado por ref',$aSlItemRef,__FUNCTION__);
    if(is_array($aSlItemRef)){
        $tam = count($aSlItemRef);
        $aItemRefs = array();
        incrNivelCorrenteLogDb();
        $iCont = 0;
        for($i=0;$i< $tam; $i++){
            // $aItemRefs[$i]['item']       = $item; //força o item principal no caso de existir item relacionado(ex.branco e tinto)
            $item = $aSlItemRef[$i]['it_codigo'];
            $aItem = buscarDadosItem($item);
            $unidItem = $aItem[0]['un'];
            switch ( strtolower($unidItem) ){
                case 'kg':
                    $qtMinima = getQtMinimaKgBook();
                    inserirLogDb('qt.minima kg',$qtMinima,__FUNCTION__);
                    break;
                case 'm':
                    $qtMinima = getQtMinimaMtBook();
                    inserirLogDb('qt.minima mt',$qtMinima,__FUNCTION__);
                    break;
                default:
                    $qtMinima = 1;
                    inserirLogDb("qt.minima $unidItem",$qtMinima,__FUNCTION__);
            }
            $qtSaldoVenda = $aSlItemRef[$i]['qt_saldo'];
            $ref = $aSlItemRef[$i]['cod_refer'];
            //echo "<h1>entrei_Refs = $ref</h1>";
            if($qtSaldoVenda >= $qtMinima){

                inserirLogDb("Item: $item - Ref.: $ref - saldo $qtSaldoVenda - container:$nrContainer ","CONSIDERADO - saldo maior ou igual que qt. minima",__FUNCTION__);
                $aItemRefs[$iCont]['item']       = $aSlItemRef[$i]['it_codigo'];
                $aItemRefs[$iCont]['ref']        = $ref;
                $aItemRefs[$iCont]['desc_item']  = $aSlItemRef[$i]['desc_item'];
                $lAchou = true;
                $iCont++;
            }else{
                inserirLogDb("Item: $item - Ref.: $ordemRef - saldo $qtSaldoVenda  - container:$nrContainer","DESCONSIDERADO - saldo menor que qt. minima",__FUNCTION__);
            }
        }
        decrNivelCorrenteLogDb();
    }
    if($lAchou == false){
        $aItemRefs = '';
        //setErros("<h1>Não existem referencias com saldo</h1>");
    }
    $aRetorno = array('saldo_itens'=>$aItemRefs,'item_relac'=> $itemRelac );
    return $aRetorno;
}

function getArrayItemRef($item,$listaCodRef){

    $aRefs = explode(',',$listaCodRef);
    $descItem = getDescrItem($item);
    foreach ($aRefs as $refs){
        $codRefer = $refs;
        $ordemRef       = getOrdemCodRefer($codRefer);
        $aItensRefers[] = array(
            "item"            => $item,
            "ref"            => $codRefer,
            "ordem_ref"            => $ordemRef,
            "desc_item"            => $descItem,
            "qt_saldo"             => 0
        );

    }
    $aRetorno = array(
        "saldo_itens"            => $aItensRefers,
        "item_relac"            => 0
    );
    return $aRetorno;

}
function desenharBook($item,$aRefs,$aItens,$pdf,$container=0,$logBookCust=false)
{
    //echo '<h1>AQQQUI3</h1>';
    //var_dump($aRefs);
    $tipoItem = getTipoItem($item);
    inserirLogDb('tipo Item',$tipoItem,__FUNCTION__);
    switch ($tipoItem){
        case 'estampado':
            if($logBookCust == false){
                $pdf = desenharCapa($item, $pdf,$container);
                $pdf = desenharFichaTecnica($item, $pdf);
                $pdf = desenharSugAplic($item, $pdf,$container);
            }

            //$pdf = desenharTamanhoReal($item,$pdf);
            //tamanho real
            //De se tiver
            //$pdf = desenharVarEstamp($item, $aRefs, $pdf);
            if($aItens <>''){ //quando tem item relacionado
                //$descArray = print_r($aItens,true);
                inserirLogDb('Mais de Um Item:',$aItens,__FUNCTION__);
                $pdf = desenharVarEstamp($aItens, $aRefs, $pdf);
            }else{
                inserirLogDb('Apenas um item:',$item,__FUNCTION__);
                $pdf = desenharVarEstamp($item, $aRefs, $pdf);
            }
            // desabilitado a pedido da Jessica em 16/06/2021
            /*if($container <> 0){
                $pdf = desenharSugCompose($item,$pdf,$container);
            }*/
            break;
        case 'liso':
            if($aItens <>''){ //quando tem item relacionado
                //$descArray = print_r($aItens,true);
                inserirLogDb('Mais de Um Item:',$aItens,__FUNCTION__);
                $pdf = desenharVarLiso($aItens, $aRefs, $pdf,$container);
            }else{
                inserirLogDb('Apenas um item:',$item,__FUNCTION__);
                $pdf = desenharVarLiso($item, $aRefs, $pdf,$container);
            }
            $pdf = desenharFichaTecnicaLiso($item,$pdf);
            break;
    }
    return $pdf;
}
function getQtItens($aParam)
{
    //echo "<h1>array unique</h1>";
    $aItem = array_column($aParam, 'item');
    //var_dump($aItem);
    $aItem = array_unique($aItem);
    //var_dump($aItem);
    $qtItens = count($aItem);
    return $qtItens;
}
function getDirFisCapa()
{
    $tipoArqDesign = 5;
    /*$param = buscarParametroIma("dir_fis_capa");
    if ($param == '') {
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books/PE/Capa';
    } else {
        $diretorio = $param;
    }*/
    $diretorio = getPastaRaizTpArq($tipoArqDesign,1);
    return $diretorio;
}
function getImgPgNaoDisponivel()
{
    $param = buscarParametroIma("img_pg_nao_disponivel");
    if ($param == '') {
        $img = '/var/www/clients/client1/web2/web/estampas/books/PE/branco.jpg';
    } else {
        $img = $param;
    }
    return $img;

}
function getQtMinimaKgBook()
{
    $param = buscarParametroIma("qt_minima_kg_book");
    if ($param == '') {
        $qt = 2;
    } else {
        $qt = $param;
    }
    return $qt;
}
function getQtMinimaMtBook()
{
    $param = buscarParametroIma("qt_minima_mt_book");
    if ($param == '') {
        $qt = 5;
    } else {
        $qt = $param;
    }
    return $qt;
}

function getRefsADesconsiderar()
{
    $param = buscarParametroIma("refs_desconsiderar");
    if ($param == '') {
        $diretorio = "'LED','999', '998', 'SDC' ";
    } else {
        $diretorio = $param;
    }
    return $diretorio;

}

function getRefsTR($itemArquivoId)
{
    //echo "<h1>ID Arquivo: $itemArquivoId</h1>";
    $aRetorno = '';
    $aItemRefTR = array();
    $lAchou = false;
    $qt = 0;
    $ordem = '';
    $ordenacao = getVarSessao(getNomeVarOrdenacaoBook());
    $ordenacaoPi = getVarSessao(getNomeVarOrdenacaoBookPi());
    if($ordenacaoPi == 1){
        $ordenacao = 4;
    }
    if($ordenacao == 3){
        $ordem = 'desc';
    }
    /*if(isset([id_arquivo_corrente][8])){
        $itemArquivoId = [id_arquivo_corrente][8];
    }else{
        $itemArquivoId = 0;
    }*/
    inserirLogDb('Item Arquivo Id Corrente',$itemArquivoId,__FUNCTION__);
    $aDados = getDados('multi','pub.relacs_item_ref relac','it_codigo,cod_refer',
        " relac.relac_item_arquivo_id =$itemArquivoId
             order by ref.\"int-2\" $ordem ","multi",
        'inner join med.pub.referencia ref on relac.cod_refer = ref."cod-refer"');
    if(is_array($aDados)){
        $tam = count($aDados);
        //echo "<h1>retorno sql relacs_item_ref</h1>";
        //var_dump($aDados);
        for($i=0;$i<$tam;$i++){
            $lAchou = true;
            $aItemRefTR[] = array('item' => $aDados[$i]['it_codigo'],'ref' => $aDados[$i]['cod_refer']);
            $qt++;
        }
    }
    if($lAchou == false){
        $aItemRefTR = '';
    }else{
        $aRetorno = array('qt'=>$qt,'dados'=>$aItemRefTR);
    }
    return $aRetorno;

}

function desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir,$container=0,$logOpcional=false)
{
    inserirLogDb('Tipo Arq - Item - dir - container - opcional',
        "$tipoArqDesign - $item - $dir - $container - $logOpcional ",__FUNCTION__);
    $aArquivo = getDadosRelacItemArquivo($tipoArqDesign, $item, $container, '');
    //var_dump($aArquivo);
    if(is_array($aArquivo)){
        foreach ($aArquivo as $arq) {

            $arquivo = $arq['arquivo'];
            $idArquivo = $arq['relac_item_arquivo_id'];
            $imgFile = "$dir/$arquivo";
            inserirLogDb('Achou o Arquivo?',"SIM - $arquivo",__FUNCTION__);

            if(is_file($imgFile)){
                inserirLogDb('É um arquivo?','SIM',__FUNCTION__);
                $pdf = desenharImgPgCompleta($imgFile,$pdf);
            }else{
                inserirLogDb('Achou o Arquivo?','NAO',__FUNCTION__);
                if($logOpcional == false){
                    inserirLogDb('É opcional?','SIM',__FUNCTION__);
                    $descTipoArquivo = getDescrTipoArq($tipoArqDesign);
                    setErros("<h4>Não existe o tipo de arquivo '$descTipoArquivo' cadastrado para o item:{$item} .</h4>") ;
                }else{
                    inserirLogDb('É opcional?','NAO',__FUNCTION__);
                }
            }
        }
    }else{
        inserirLogDb('Achou o Arquivo?','NAO',__FUNCTION__);
        $imgFile = '';
        $idArquivo = '';
    }
    //joga os ids dos arquivos para serem utilizados globalmente caso necessário
    //[id_arquivo_corrente][$tipoArqDesign] = $idArquivo;



    return $pdf;

}
function getArqItemTipoArq($tipoArqDesign,$item,$dir,$logRetornarId=false,$ref='',$container=0)
{
    //$aRetorno = '';
    $aArquivo = getDadosRelacItemArquivo($tipoArqDesign, $item, $container, $ref);
    if(is_array($aArquivo)){
        $arquivo = $aArquivo[0]['arquivo'];
        $idArquivo = $aArquivo[0]['relac_item_arquivo_id'];
        $imgFile = "$dir/$arquivo";
    }else{
        $imgFile = '';
    }
    if($logRetornarId){
        $aRetorno = array('id'=> $idArquivo, 'arquivo'=>$imgFile);
    }else{
        $aRetorno = $imgFile;
    }
    return $aRetorno;
}
function getArqsItemTipoArq($tipoArqDesign,$item,$dir)
{
    $aArqCompl = array();
    $lAchou = false;
    $separador = getSeparadorArquivo($dir);
    $aArquivo = getDadosRelacItemArquivo($tipoArqDesign, $item, 0, '');
    if(is_array($aArquivo)){
        $tam = count($aArquivo);
        inserirLogDb('qt.TRs encontradas',$tam,__FUNCTION__);
        $lAchou = true;
        incrNivelCorrenteLogDb();
        for($i=0;$i<$tam;$i++){
            $aArqCompl[$i]['arquivo'] = $dir.$separador.$aArquivo[$i]['arquivo'];
            $aArqCompl[$i]['id']      = $aArquivo[$i]['relac_item_arquivo_id'];
            inserirLogDb('posicao - arquivo = id',
                $i."-".$aArqCompl[$i]['arquivo']."-".$aArqCompl[$i]['id']
                ,__FUNCTION__);
        }
        decrNivelCorrenteLogDb();
    }
    if($lAchou == false){
        $aArqCompl = '';
    }
    return $aArqCompl;

}

function desenharCapa($item,$pdf,$container=0)
{
    $tipoArqDesign = getTipoArqCapa($container);
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return  desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir,$container);

}
function getTipoArqCapa($container)
{
    if($container == 0){
        $tipoArqDesign = getTipoCapaEst();
    }else{
        $tipoArqDesign = getTipoCapaPIEst();
    }
    return $tipoArqDesign;
}
function getCapa($item,$container=0)
{
    $tipoArqDesign = getTipoArqCapa($container);
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return getArqItemTipoArq($tipoArqDesign,$item,$dir,false,'',$container);

}
function desenharSugAplic($item,$pdf,$container=0)
{
    $tipoArqDesign = getTipoArqSugAplic($container);
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return  desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir,$container,true);

}
function getTipoArqSugAplic($container)
{
    if($container == 0){
        $tipoArqDesign = getTipoSAEst();
    }else{
        $tipoArqDesign = getTipoSAPIEst();
    }
    return $tipoArqDesign;
}
function getSugAplic($item,$container=0)
{
    $tipoArqDesign = getTipoArqSugAplic($container);
    //$dir = getDirFisEstampasSa();
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return getArqItemTipoArq($tipoArqDesign,$item,$dir,false,'',$container);

}

function getSugCompose($item,$pdf,$container)
{
    $tipoArqDesign = getTipoSC();
    //$dir = getDirFisEstampasSa();
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return getArqItemTipoArq($tipoArqDesign,$item,$dir,false,'',$container);

}
function desenharSugCompose($item,$pdf,$container=0)
{
    $tipoArqDesign = getTipoSC();
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return  desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir,$container,true);

}

function desenharFichaTecnica($item,$pdf)
{
    $tipoArqDesign = 6;
    $dir = getDirFisEstampasFt();
    return  desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir);

}
function getFichaTecnica($item)
{
    $tipoArqDesign = 6;
    $dir = getDirFisEstampasFt();
    return getArqItemTipoArq($tipoArqDesign,$item,$dir);

}
function desenharTamanhoReal($item,$pdf)
{
    $tipoArqDesign = getTipoTR();
    $dir = getDirFisEstampasTr();
    return  desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir);
}
function getTamanhoReal($item)
{
    $tipoArqDesign = getTipoTR();
    $dir = getDirFisEstampasTr();
    return getArqsItemTipoArq($tipoArqDesign,$item,$dir);

}

function desenharFichaTecnicaLiso($item,$pdf)
{
    $tipoArqDesign = 12;
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return  desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir);
}

function getFichaTecnicaLiso($item)
{
    $tipoArqDesign = 12;
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return getArqItemTipoArq($tipoArqDesign,$item,$dir);
}


function desenharDE($item,$pdf)
{
    $tipoArqDesign = getTipoDE();
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return  desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir);
}
function getDE($item)
{
    $tipoArqDesign = getTipoDE();
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return getArqItemTipoArq($tipoArqDesign,$item,$dir);

}

function desenharFundoRefEst($item,$pdf)
{
    $tipoArqDesign = getTipoFundoRefEst();
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return  desenharItemTipoArqPgInteira($tipoArqDesign,$item,$pdf,$dir);

}
function getFundoRefEst($item)
{
    $tipoArqDesign = getTipoFundoRefEst();
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return getArqItemTipoArq($tipoArqDesign,$item,$dir,false);
}
function getArqRefTempl2Cort($item,$ref,$container=0)
{
    $tipoArqDesign = getTipoTempl2Cort();
    $dir = getPastaRaizTpArq($tipoArqDesign,1);
    return getArqItemTipoArq($tipoArqDesign,$item,$dir,false,$ref,$container);
}
function getArqRefTempl($item,$ref,$container=0)
{
    $aRetorno = '';
    $lAchei = false;
    $listaTipos = '16,17,18';
    $aListaTipos = explode(',',$listaTipos);
    $tam = count($aListaTipos);
    for($i=0;$i<$tam;$i++){
        $aReg = getRegRelacItemArquivo($item,$ref, $container,$aListaTipos[$i]);
        $dir = getPastaRaizTpArq($aListaTipos[$i],1);
        if(is_array($aReg)) {
            $lAchei = true;
            $separador = getSeparadorArquivo($dir);
            $arquivo = $dir.$separador.$aReg[0]['arquivo'];
            $aRetorno = array('template' => $i + 1, 'arquivo' => $arquivo );
            break;
        }
    }
    if($lAchei == false){
        setErros("<h1>Imagem da figura do item $item - referencia: $ref nao encontrada.</h1> ");
    }


    return $aRetorno;

}

function getDirFisEstampasFt()
{
    $tipoArqDesign = 6;
    /*$param = buscarParametroIma("dir_fis_ficha_tecnica");
    if ($param == '') {
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books/PE/FT';
    } else {
        $diretorio = $param;
    }*/
    $diretorio = getPastaRaizTpArq($tipoArqDesign,1);
    return $diretorio;
}

function desenharImgPgCompleta($imagem,$pdf)
{
    if(is_file($imagem)){
        $pdf->SetFont('times', '', 48);

        $pdf->AddPage();

        $bMargin = $pdf->getBreakMargin();

        $auto_page_break = $pdf->getAutoPageBreak();

        $pdf->SetAutoPageBreak(false, 0);

        //echo "<h1>imagem:$imagem</h1>";

        $pdf->Image($imagem, 0, 0, 210, 297, '', '', '', false, 72, '', false, false, 0);


        //$pdf->SetAutoPageBreak($auto_page_break, $bMargin);

        $pdf->SetFooterMargin(0);
        $pdf->setPageMark();
    }

    return $pdf;


}





function getDirFisEstampasSa()
{
    $tipoArqDesign = 7;
    /*$param = buscarParametroIma("dir_fis_sug_aplic");
    if ($param == '') {
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books/PE/SA';
    } else {
        $diretorio = $param;
    }*/
    $diretorio = getPastaRaizTpArq($tipoArqDesign,1);
    return $diretorio;
}


function getDirFisEstampasTr()
{
    $tipoArqDesign = 8;
    /*$param = buscarParametroIma("dir_fis_tr");
    if ($param == '') {
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books/PE/TR';
    } else {
        $diretorio = $param;
    }*/
    $diretorio = getPastaRaizTpArq($tipoArqDesign,1);
    return $diretorio;
}



function getFundoRefLisa($item,$containerParam=0)
{

    if($containerParam == 0){
        $tipo = getTipoFundoLiso();
        $dir = getDirFisLisaFundo();
    }else{
        $tipo = getTipoFundoLisoPI();
        $dir = getDirFisLisaFundoPI();
    }
    inserirLogDb('container-tipo',"$containerParam - $tipo",__FUNCTION__);
    $aArquivo = getDadosRelacItemArquivo($tipo, $item, $containerParam, '');
    if(is_array($aArquivo)){
        $arquivo = $dir.'/'.$aArquivo[0]['arquivo'];
        inserirLogDb('achou arquivo',$arquivo,__FUNCTION__);
    }else{
        $arquivo = '';
        inserirLogDb('achou arquivo','NAO',__FUNCTION__);
    }
    return $arquivo;
}
function getDirFisLisaFundo()
{
    /*$dirRefLisa = getDirFisLiso();
    if($dirRefLisa <> ''){
        $diretorio = $dirRefLisa.'/fundo';
    }*/
    $param = buscarParametroIma("dir_fis_liso_fundo");
    if ($param == '') {
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books_lisos/FUNDO';
    } else {
        $diretorio = $param;
    }
    return $diretorio;
}
function getDirFisLisaFundoPI()
{
    /*$dirRefLisa = getDirFisLiso();
    if($dirRefLisa <> ''){
        $diretorio = $dirRefLisa.'/fundo';
    }*/
    $param = buscarParametroIma("dir_fis_liso_fundo_pi");
    if ($param == '') {
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books_lisos/PI/FUNDO';
    } else {
        $diretorio = $param;
    }
    return $diretorio;
}


/*function getDirFisLisa()
{
    $param = buscarParametroIma("dir_fis_lisa");
    if ($param == '') {
        //$diretorio = '/var/www/clients/client1/web2/web/estampas/books/PE/REF';
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books_lisos/REF';
    } else {
        $diretorio = $param;
    }
    return $diretorio;
}*/



function getDirFisEstampasRefs()
{
    $param = buscarParametroIma("dir_fis_refs");
    if ($param == '') {
        $diretorio = '/var/www/clients/client1/web2/web/estampas/books/PE/REF';
    } else {
        $diretorio = $param;
    }
    return $diretorio;
}
function getTipoItem($item)
{
    $aReg = getReg(
        'espec',
        'item-ext',
        '"it-codigo"',
        "'$item'",
        'cod_tipo_item'
    );
    if(is_array($aReg)){
        $tipoItem = $aReg[0]['cod_tipo_item'];
        switch($tipoItem){
            case 1: //estampado
                $tipo = 0;
                break;
            case 2: //liso
                $tipo = 5;
                break;
        }
    }else{
        $tipo = substr($item, 2, 1);
    }

    $descTipo = '';
    switch ($tipo){
        case 0:
            $descTipo = 'estampado';
            break;
        case 5:
            $descTipo = 'liso';
            break;
        default:
            $descTipo = 'estampado';
    }
    //echo "<h1>desc.tipo item:$descTipo</h1>";
    return $descTipo;
}

function getFormaExibItem($item)
{
    //$retorno = '';
    $aReg = getReg(
        'espec',
        'item-ext',
        '"it-codigo"',
        "'$item'",
        'cod_form_exib_ref'
    );
    if(is_array($aReg)){
        $codForm = $aReg[0]['cod_form_exib_ref'];

    }else{
        $codForm = 0;
    }
    $retorno = getTipoArquivo($codForm);
    return $retorno;
}


function desenharVariantes($item, $aRefsParam1, $pdf)
{
    //echo "<h1>DesenharVariantes</h1>";
//var_dump($aRefs);echo "</br>";

    $tipo = substr($item, 2, 1);
    switch ($tipo) {

        case 0:

            $pdf = desenharVarEstamp($item, $aRefsParam1, $pdf);
            break;

        case 5:
            //echo "entrei op5";

            $pdf = desenharVarLiso($item, $aRefsParam1, $pdf);
            $pdf->SetXY(15, 18);
            break;

    }

    return $pdf;


}
function getImgItemRefLisa($item,$ref)
{
    $dir = getDirFisLiso();
    $imagem = getImgBookRef($item,$ref);
    if($imagem == ''){
        $arquivo = "{$dir}/{$ref}.jpg";
        //echo "<h1>arquivo 1 :$arquivo</h1>";
        if(file_exists($arquivo)){
            $imagem = $arquivo;
        }
    }
    if($imagem == ''){
        $imagem = "{$dir}/branco.jpg";
    }
    return $imagem;
}
function desenharVarLiso($item, $aRefs, $pdf, $container=0)
{
    $qtPorPg = getQtImgPorPgliso();
    $lArrayItens = false;
    if(is_array($item)){
        $aItens = $item;
        $item =  $aItens[0];
        $lArrayItens = true;
        inserirLogDb('item é um array?','sim',__FUNCTION__);
    }else{
        inserirLogDb('item é um array?','nao',__FUNCTION__);
    }
    $iColuna = 0;
    $iLinha  = 0;
    //echo "<h1>antes aRefs</h1>";
    //var_dump($aRefs);
    // premissa: o array do parametro deve estar em ordem de item/ref

    //$pdf->AddPage();
    //$descItem = substr(getDescrItem($item),0,30);
    //$pdf = desenharCabecalho($pdf, '', $descItem);
    //busca a imagem que terá tanto o cabeçalho e o rodapé.
    $erro = '';
    $img = getFundoRefLisa($item,$container);
    inserirLogDb('imagem retornada getFundoRefLisa',$img,__FUNCTION__);
    /* if($img <> ''){
         $pdf = desenharImgPgCompleta($img,$pdf);
     }else{
         $pdf->AddPage();
         $erro = "Fundo da página do item $item, não cadastrado.";
         $pdf->SetXY(15,13);
         $pdf->SetFont('', 'BI', 14);
         $pdf->Cell(110, 20, $erro, 0, false, 'C', 0, '', 0, false, 'M', 'M');

     }*/


    $tam = count($aRefs);

    if($tam > 41){
        $qtColunas = 7;
        $incry = 25;
        $incrx = 26;
        $w = 25;
        $h = 24;
    }
    else if($tam > 30 and $tam <= 41){
        $qtColunas = 6;
        $incry = 27;
        $incrx = 30;
        $w = 29;
        $h = 28;
    }else{
        $qtColunas = 5;
        $incry = 33;
        $incrx = 36;
        $w = 35;
        $h = 35;
    }
    inserirLogDb('qt.Colunas',$qtColunas,__FUNCTION__);
    inserirLogDb('incry',$incry,__FUNCTION__);
    inserirLogDb('incry',$incrx,__FUNCTION__);

    $itemAnt = '';
    $qtItem = 0;
    $x = getPosXIniBookLiso();
    $y = getPosYIniBookLiso();
    /* $w = 32;
     $h = 32;*/
    //echo "<h1>cheguei aqui 3</h1>";
    incrNivelCorrenteLogDb();
    for ($i = 0; $i < $tam; $i++) {
        inserirLogDb('entra no for das refs - contador:',$i,__FUNCTION__);
        $ref = $aRefs[$i];
        inserirLogDb('ref',$ref,__FUNCTION__);

        //$desenho = $aItemRef[$i]['desenho'];

        //if (($item <> $itemAnt and $itemAnt <> '') or $qtItem == $qtPorPg or $i == 0) {
        if ($qtItem == $qtPorPg or $i == 0) {
            inserirLogDb('qt. item igual a qt por pagina ou $i = 0','sim',__FUNCTION__);

            //adicionar pag e cab
            $qtItem = 0;
            if($img <> ''){
                inserirLogDb('imagem diferente de branco?','sim',__FUNCTION__);
                $pdf = desenharImgPgCompleta($img,$pdf);

            }else{
                inserirLogDb('imagem diferente de branco?','nao',__FUNCTION__);
                setErros("<h1>Pagina de Fundo nao encontrada - $item</h1>");
                if(isset([erros])){
                    inserirLogDb('ERRO',[erros],__FUNCTION__);
                }


                /*$pdf->AddPage();
                $erro = "Fundo da página do item $item, não cadastrado.";
                $pdf->SetXY(15,13);
                $pdf->SetFont('', 'BI', 14);
                $pdf->Cell(110, 20, $erro, 0, false, 'C', 0, '', 0, false, 'M', 'M');*/

            }

        }
        if($lArrayItens){
            $item = $aItens[$i];
            inserirLogDb('Item definido por posição do array:',$item,__FUNCTION__);

        }else{
            inserirLogDb('Item unico:',$item,__FUNCTION__);
        }

        $img_file = getImgItemRefLisa($item,$ref);
        inserirLogDb('imagem retornada pela funcao getImgItemRefLisa',
            $img_file,__FUNCTION__);
        //echo "<h1>$img_file</h1>";
        $qtItem++;
        /*if(file_exists($img_file)){
            inserirLogDb('Arquivo Existe?','sim',__FUNCTION__);
            $pdf->Image($img_file, $x, $y, $w, $h, 'JPG', '', '', false, 300, '', false, false, 0, 'L,M', false, false);
        }else{
            inserirLogDb('Arquivo Existe?','nao',__FUNCTION__);
        }*/
        inserirLogDb('imagem incluida na variavel PDF',$img_file,__FUNCTION__);
        inserirLogDb('posicao linha',$y,__FUNCTION__);
        inserirLogDb('posicao linha',$x,__FUNCTION__);
        $pdf->Image($img_file, $x, $y, $w, $h, 'JPG', '', '', false, 300, '', false, false, 0, 'L,M', false, false);

        $iColuna++;
        $iLinha++;

        if($iColuna == $qtColunas){
            $x = 15;
            $iColuna = 0;
            $y += $incry;
            inserirLogDb("coluna = $qtColunas  - incremento 30 no eixo y:",$y,__FUNCTION__);
            inserirLogDb('eixo x:',$x,__FUNCTION__);
        }else{
            $x += $incrx;
            inserirLogDb("coluna <> $qtColunas - incremento 33 no eixo x:",$x,__FUNCTION__);
        }
    }
    decrNivelCorrenteLogDb();
    //busca ficha técnica e já inclui uma página inteira com a ficha técnica no PDF
    //$pdf = getFichaTecnicaLiso($item,$pdf);


    // desenhar rodape
    //var_dump($pdf);
    return $pdf;


}


function getFormaBuscaArq()
{
    // opções-> diretorio ou tabela
    $paramDirFisEst = buscarParametroIma("forma_busca_arq_portal");
    if($paramDirFisEst == ''){
        $valor = 'diretorio';
    }else{
        $valor = $paramDirFisEst;
    }
    return $valor;
}

function getImgBookRef($item,$ref)
{
    $formaBuscaImg = getFormaBuscaArq();
    if($formaBuscaImg == 'diretorio'){
        $arquivo = retornarImagensItemRef($item,$ref,'fisico');
        $arquivo = $arquivo[0]['caminho'];
    }else{
        $aArquivo = getDadosRelacItemArquivo('9', $item, 0, $ref);
        //var_dump();
        if (isset($aArquivo[0]['arquivo'])) {
            $tipoItem = getTipoItem($item);
            if($tipoItem == 'estampado'){
                $dir = getDirFisEstampasRefs();
            }else{
                $dir = getDirFisLiso();
            }
            $arquivo = $aArquivo[0]['arquivo'];
            $arquivo = "$dir/$arquivo";
        } else {
            $arquivo = '';
        }
    }
    return $arquivo;
}

function desenharVarEstamp($item, $aRefsParam, $pdf)
{
    $logItemUnico = false;
    //$aTrItens = array();
    if(!is_array($item) and $item <> ''){
        $aItens  = array($item);
        //$logItemUnico = true;
    }else{
        $aItens = $item;
        //$logItemUnico = false;
    }
    // busca as TR's dos itens passados por parametro
    $aTrItens = getTrsItens($aItens);
    //echo "<h2>var dump getTrItens retorno</h2>";
    //var_dump($aTrItens);
    inserirLogDb('Trs encontradas',$aTrItens,__FUNCTION__);
    if(is_array($aTrItens)){
        $tam = count($aTrItens);
        for($i=0;$i<$tam;$i++){
            $arqComplTR = $aTrItens[$i]['arquivo'];
            $idTR       = $aTrItens[$i]['id'];
            //echo "<h2>ID = $idTR - $arqComplTR</h2>";
            //$idTR = 41738;
            inserirLogDb('Arquivo TR - ID'," $arqComplTR - $idTR",__FUNCTION__);
            $aComandos = array();
            $aRefsTRSl = getRefsTRComSaldo($idTR,$arqComplTR,$aItens,$aRefsParam);
            //var_dump($aRefsTRSl);
            $lItemComTRSemRef = $aRefsTRSl['log_item_com_tr_sem_ref'];
            $aRefsTRSaldo = $aRefsTRSl['dados'];
            inserirLogDb('referencias Tr com saldo',$aRefsTRSaldo,__FUNCTION__);
            inserirLogDb('item com TR e sem Ref ? ',   $lItemComTRSemRef,__FUNCTION__);
            if(is_array($aRefsTRSaldo)) {
                $tamx = count($aRefsTRSaldo);
            }else{
                $tamx = 0;
            }
            if($tamx > 0){
                $pdf = desenharImgPgCompleta($arqComplTR,$pdf);
                inserirLogDb('TR Desenhada',"sim - item possui referencias com saldo - Qt.Itens:$tamx",__FUNCTION__);
            }else{
                inserirLogDb('TR Desenhada','NAO - item NAO possui referencias com saldo',__FUNCTION__);
            }
            $iTempl2 = 0;
            $iReg = 0;
            $iPg = 0;
            if(is_array($aRefsTRSaldo) and $lItemComTRSemRef == false){
                $tamx = count($aRefsTRSaldo);
                $mudarTemplate = false;
                for($x=0;$x<$tamx;$x++){
                    $itemCorrente = $aRefsTRSaldo[$x]['item'];
                    $refCorrente  = $aRefsTRSaldo[$x]['ref'];
                    //echo "<h1>entrei_Refs = $refCorrente</h1>";
                    $template     = $aRefsTRSaldo[$x]['template'];
                    $arquivo      = $aRefsTRSaldo[$x]['arquivo'];
                    if($template == '' and $arquivo == ''){
                        continue;
                    }

                    $lNovaPg = false;
                    inserirLogDb('posicao - item - ref - template - arquivo',
                        "$x - $itemCorrente - $refCorrente - $template - $arquivo",
                        __FUNCTION__);
                    if($x == 0){
                        $arqDE = getArqDE($itemCorrente,$refCorrente);
                        if($arqDE <> ''){
                            $pdf = desenharImgPgCompleta($arqDE,$pdf);
                            inserirLogDb('posicao 0 - arquivo "DE" desenhado',"sim- arquivo:$arqDE",__FUNCTION__);
                        }else{
                            inserirLogDb('posicao 0 - arquivo "DE" desenhado','NAO - nao foi encontrado',__FUNCTION__);
                        }
                        //$pdf = desenharFundoRefEst($item,$pdf);
                        $iPg++;
                        inserirLogDb('Pagina incrementada para',$iPg,__FUNCTION__);
                        $arqFundo = getFundoRefEst($itemCorrente);
                        $aComandos[$iPg][] = array('acao' => 'desenhar_fundo',
                            'arquivo'=> $arqFundo,
                            'template'=>'','item'=>$itemCorrente,'ref'=>$refCorrente);
                        inserirLogDb('criado comando para desenhar fundo da pagina',
                            "sim - $arqFundo ",__FUNCTION__);
                    }
                    if($template == 2){
                        $iTempl2++;
                        inserirLogDb('template 2 incrementado -> posicao - itempl2 ',
                            "$x - $iTempl2 ",__FUNCTION__);
                    }else{
                        inserirLogDb('template 2 NAO incrementado -> posicao - itempl2 - mudarTemplate setado para true ',
                            "$x - $iTempl2 ",__FUNCTION__);
                        $mudarTemplate = true;
                    }
                    $iReg++;
                    inserirLogDb('Qt.Reg PG incrementado -> posicao - iReg ',
                        "$x - $iReg ",__FUNCTION__);
                    /*if( $iReg == 2 and $iTempl2 == 2){ //segundo registro e os dois são template 2
                        inserirLogDb('IReg e iTempl2 iguais em 2','sim - mudar Template setado como false',__FUNCTION__);
                        if($tamx % 3 <> 0){
                            inserirLogDb('qt.refs é multiplo de 3','NAO',__FUNCTION__);
                            $lNovaPg = true;
                            $iReg = 1;
                            $iTempl2 = 0;
                        }else{
                            inserirLogDb('qt.refs é multiplo de 3','SIM',__FUNCTION__);
                            $lNovaPg = false;
                        }
                        $mudarTemplate = false;
                    }else{
                        $lNovaPg = false;
                        if($tamx > 1){
                            $mudarTemplate = true;
                            inserirLogDb('iReg e iTempl2 iguais em 2',
                                'NAO - qt.imagens > 1 -> mudarTemplate setado para true e lNovaPg para false ',__FUNCTION__);
                        }else{
                            $mudarTemplate = false;
                            inserirLogDb('iReg e iTempl2 iguais em 2',
                                'NAO - qt.imagens <= 1 -> mudarTemplate setado para false e lNovaPg para false ',__FUNCTION__);
                        }
                    }*/

                    // não icrementa se for o último registro, mesmo,atendendo as condições de qts de imagens
                    if(($iReg == 3 or $iTempl2 == 2) and $tamx - 1 > $x ){
                        inserirLogDb('(qt. registros igual a 3 ou 2 templates tipo 2) e não é o último registro',
                            "sim - lNovaPg setado como true - tamx = $tamx",__FUNCTION__);
                        $lNovaPg = true;
                    }else{
                        //$lNovaPg = false;
                        inserirLogDb('(qt. registros igual a 3 ou 2 templates tipo 2) e não é o último registro',
                            'NAO',__FUNCTION__);

                    }
                    //echo "<h1>cheguei até aqui 1 </h1>";

                    $aComandos[$iPg][] = array('acao' => 'desenhar_ref','arquivo'=>$arquivo,
                        'template'=>$template,'item'=>$itemCorrente,'ref'=>$refCorrente);
                    inserirLogDb('criou comando para desenhar ref. pg->',$iPg,__FUNCTION__);

                    if($mudarTemplate and $template <> 3 ){
                        //$template = 1;
                        $aComandos = mudarTemplComando($aComandos,$iPg);
                        $iTempl2 = 0;
                    }else{
                        inserirLogDb('Nao entrou na condição de mudar template',
                            "ireg: $iReg - template: $template",__FUNCTION__);
                    }
                    if($lNovaPg){
                        $arqFundo = getFundoRefEst($itemCorrente);
                        //var_dump($arqFundo);
                        $iPg++;
                        $aComandos[$iPg][] = array('acao' => 'desenhar_fundo','arquivo'=>$arqFundo,
                            'template'=>'','item'=>$itemCorrente,'ref'=>$refCorrente);
                        inserirLogDb('condição de nova página',
                            'sim - incrementou a pagina , gerou novo comando,
                            zerou as variaveis iReg e iTempl2 e colocou a variavel 
                            mudarTemplate  igual a false',__FUNCTION__);
                        $iReg    = 0;
                        $iTempl2 = 0;
                        $mudarTemplate = false;
                    }else{
                        inserirLogDb('condição de nova página','nao',__FUNCTION__);
                    }
                    //echo "<h1>cheguei até aqui 2 </h1>";
                }
                //echo "<h1>cheguei até aqui</h1>";
                /*echo "<h1>Var dump comandos</h1>";
                var_dump($aComandos);*/
                $pdf = execComandosTR($aComandos,$pdf);
                //echo "<h1>cheguei até aqui 2 </h1>";
            }

            //echo "<h1>var dump aRefsTRSaldo</h1>";
            //var_dump($aRefsTRSaldo);
        }
    }
    else{
        $listaItens = convArrayParaLista($aItens);
        $descTipo = getDescrTipoArq(getTipoTR());
        setErros("<h1>Não Foram encontrados Arquivos do tipo $descTipo para o(s) item(ns):$listaItens</h1>");
    }
    return $pdf;
}
function mudarTemplComando($aComando,$pg)
{

    if(is_array($aComando[$pg])){
        $tam = count($aComando[$pg]);
        for($i=0;$i<$tam;$i++){
            if($aComando[$pg][$i]['acao'] == 'desenhar_ref'){
                $templateCorrente = $aComando[$pg][$i]['template'];
                $arquivoCorrente = $aComando[$pg][$i]['arquivo'];
                $itemCorrente    = $aComando[$pg][$i]['item'];
                $refCorrente     = $aComando[$pg][$i]['ref'];
                inserirLogDb('template - $arquivo - item - ref',"$templateCorrente - $arquivoCorrente - $itemCorrente - $refCorrente ",__FUNCTION__);
                if($templateCorrente == 2){
                    inserirLogDb('Template 2 mudando para o template 1',
                        'SIM - '.$arquivoCorrente,__FUNCTION__);
                    //echo "<h1>passei aqui 1</h1>";
                    //$arquivoPuro = getArquivoPuro($arquivoCorrente);
                    $arqFinal = sincrImgTempl2Cortada($arquivoCorrente,$itemCorrente,$refCorrente);
                }else{
                    inserirLogDb('Template 2 mudando para o template 1',
                        'NAO',__FUNCTION__);
                    $arqFinal = $aComando[$pg][$i]['arquivo'];
                }
                $aComando[$pg][$i]['arquivo'] = $arqFinal;
                $aComando[$pg][$i]['template'] = 1;
                inserirLogDb('mudei o template para 1-> pg - posicao - arquivo inicial - arquivo final ',
                    " $pg - $i - $arquivoCorrente - $arqFinal",__FUNCTION__);
            }
        }
    }
    return $aComando;
}
function execComandosTR($aComandos,$pdf)
{
    //inserirLogDb('array de comandos',   $aComandos,__FUNCTION__);
    //executa comandos

    $linha  = getPosYIniBookEst();
    $coluna = getPosXIniBookEst();
    /*$largura = array(180,180);
    $altura  = array(72,112);*/
    if(is_array($aComandos)){
        $tamx = count($aComandos);
        incrNivelCorrenteLogDb();
        for($x=1;$x<=$tamx;$x++){
            if(is_array($aComandos[$x])){
                $tam = count($aComandos[$x]);
                incrNivelCorrenteLogDb();
                for($i=0;$i<$tam;$i++){
                    //echo "<h1>cheg1</h1>";
                    $acao     = $aComandos[$x][$i]['acao'];
                    $arquivo  = $aComandos[$x][$i]['arquivo'];
                    $template = $aComandos[$x][$i]['template'];
                    inserirLogDb('pg - linha'," $x - $i ",__FUNCTION__);
                    inserirLogDb('acao - arquivo -template',"$acao - $arquivo - $template",__FUNCTION__);
                    switch ($template){
                        case 1:
                            $largura = getLarguraTp1MM();
                            $altura  = getAlturaTp1MM();
                            $lSaltarPg = false;
                            break;
                        case 2:
                            $largura = getLarguraTp2MM();
                            $altura  = getAlturaTp2MM();
                            $lSaltarPg = false;
                            break;
                        case 3:
                            $largura = getLarguraTp3MM();
                            $altura  = getAlturaTp3MM();
                            $coluna  = 0 ;
                            $linha   = 0;
                            $lSaltarPg = true;
                            break;
                        default:
                            $largura = getLarguraTp1MM();
                            $altura  = getAlturaTp1MM();
                            $lSaltarPg = false;

                    }
                    inserirLogDb('altura - largura',"$altura - $largura",__FUNCTION__);
                    //echo "<h1>altura: $altura - largura: $largura - template: $template</h1>";

                    //var_dump($template);
                    inserirLogDb('acao',$acao,__FUNCTION__);
                    switch($acao){
                        case 'desenhar_fundo':
                            if($arquivo <> ''){
                                $pdf = desenharImgPgCompleta($arquivo,$pdf);
                                inserirLogDb('arquivo <>  de branco - desenhei pg completa arquivo->',$arquivo,__FUNCTION__);
                            }
                            $linha = getPosYIniBookEst();
                            inserirLogDb('linha inicial setada para:',$linha,__FUNCTION__);

                            break;
                        case 'desenhar_ref':
                            //echo "<h1>Template: $template</h1>";
                            if($arquivo <> ''){
                                inserirLogDb('arquivo <>  de branco - desenhei-> imagem - altura - largura - linha -coluna ',
                                    "$arquivo - $altura - $largura - $linha - $coluna ",__FUNCTION__);
                                $pdf->Image($arquivo, $coluna, $linha, $largura,
                                    $altura, 'JPG', '', '', false,50, '', false, false, 0);
                                $linha += $altura + getEspacamentoEstMM();
                                if($lSaltarPg and $i < $tam - 1){
                                    $pdf->AddPage();
                                }

                            }else{
                                inserirLogDb('Arquivo = branco','sim',__FUNCTION__);
                            }
                            break;
                    }
                }
                decrNivelCorrenteLogDb();
            }
        }
        decrNivelCorrenteLogDb();
    }
    return $pdf;
}

function getArqDE($item,$ref)
{
    $aReg = getRegRelacItemRef($item,$ref,getTipoDE());
    if(is_array($aReg)){
        $id = $aReg[0]['relac_item_arquivo_id'];
        $retorno =  getArqRelacItemArquivo($id,1);
    }else{
        $retorno = '';
    }
    return $retorno;
}
function getRegRelacItemRef($item,$ref,$tipo,$campos='')
{
    $aReg= getReg(
        'espec',
        'relacs_item_ref',
        'it_codigo,cod_refer,cod_tipo_relac',
        "'$item','$ref',$tipo",
        'relac_item_ref_id,relac_item_arquivo_id'
    );
    return $aReg;
}

function getRefsTRComSaldo($idTR,$arquivoCompletoTR,$item,$aRefs)
{   //echo "<h1>Itens</h1>";
    //var_dump($item);
    //var_dump($aRefs);
    inserirLogDb('Variavel Item passada como parametro para funcao:',$item,__FUNCTION__);
    inserirLogDb('idTR  ',
        "$idTR ",__FUNCTION__);
    inserirLogDb('aRefs',$aRefs,__FUNCTION__);
    inserirLogDb('item',$item,__FUNCTION__);
    $aItemRefSl = array();
    $aTrComSaldo = array();
    $lAchou = false;
    $refTempl = verifExisteRefTempl($item);
    $lItemComTrSemRef = validarItemComTRSemRef($item);
    if($refTempl){
        $lItemComTrSemRef = false;
    }
    if($lItemComTrSemRef){
        inserirLogDb('Item com TR sem REF','SIM',__FUNCTION__);
        $arqPuro = getArquivoPuro($arquivoCompletoTR);
        inserirLogDb('Arquivo completo - arquivo puro',"$arquivoCompletoTR - $arqPuro",__FUNCTION__);
        $arqSemExtensao = explode('.',$arqPuro);
        $aArquivo = explode('-',$arqSemExtensao[0]);
        //var_dump($aArquivo);
        //inserirLogDb('Array arq sem extensao',$aArquivo,__FUNCTION__);
        $itemArq = $aArquivo[0];
        //echo "<h1>ItemArq=$itemArq<br></h1>";
        $refArq  = $aArquivo[1];
        //echo "<h1>ItemRefArq=$refArq<br></h1>";
        $aDadosRefsTR[0]['item'] = $itemArq;
        $aDadosRefsTR[0]['ref'] = $refArq;
    }else{
        inserirLogDb('Item com TR sem REF','NAO',__FUNCTION__);
        $aRefsTR = getRefsTR($idTR);
        //inserirLogDb('array de refers da TR',$aRefsTR,__FUNCTION__);
        if(is_array($aRefsTR)){
            $qtRefsTR = $aRefsTR['qt'];
            $aDadosRefsTR = $aRefsTR['dados'];
        }else{
            $qtRefsTR = 0;
            $aDadosRefsTR = '';
        }

    }
    $aItemRefSl = juntarItemRefSl($item, $aRefs);
    //var_dump($aItemRefSl);
    //echo "<h1>var dump itens/ref com saldo *<br></h1>";
    //echo "<h1>var dump itens/ref TR<br></h1>";
    //var_dump($aDadosRefsTR);
    if(is_array($aDadosRefsTR)){
        $tam = count($aDadosRefsTR);
        for($i=0;$i<$tam;$i++){
            $itemTR = $aDadosRefsTR[$i]['item'];
            $refTR  = $aDadosRefsTR[$i]['ref'];
            //echo "<h1>IItem $itemTR - Ref $refTR</h1>";
            if(is_array($aItemRefSl)){
                $tamx = count($aItemRefSl);
                for($x=0;$x<$tamx;$x++){
                    $itemSl = $aItemRefSl[$x]['item'];
                    $refSl = $aItemRefSl[$x]['ref'];
                    //echo "<h1>Item2 $itemSl - Ref $refSl</h1>";
                    inserirLogDb('comparacao de itens saldo com TR',
                        " $itemTR igual a  $itemSl e $refTR igual a $refSl",__FUNCTION__);
                    //echo "<h1>ItemTR = $itemTR - ItemSL = $itemSl - refTR = $refTR - refSL = $refSl - antes do if</h1>";
                    if($itemTR == $itemSl and $refTR == $refSl){
                        //echo "<h1>ItemTR = $itemTR - ItemSL = $itemSl - refTR = $refTR - refSL = $refSl</h1>";
                        if($lItemComTrSemRef == false){
                            $aArqTempl = getArqRefTempl($itemTR,$refTR);
                            if(is_array($aArqTempl)){
                                $template = $aArqTempl['template'];
                                $arquivo  = $aArqTempl['arquivo'];
                            }else{
                                $template = '';
                                $arquivo  = '';
                            }
                        }else{
                            $template = '';
                            $arquivo  = '';
                        }


                        $aTrComSaldo[] = array('item'=>$itemTR,'ref'=>$refTR,
                            'template' => $template,
                            'arquivo' => $arquivo);
                        $lAchou = true;
                        break;
                    }
                }
            }
        }
    }
    if($lAchou == false){
        $aTrComSaldo = '';
    }
    //echo 'var dump itens/ref TR com saldo FINAL<br>';
    //var_dump($aTrComSaldo);
    $aRetorno = array('log_item_com_tr_sem_ref'=>$lItemComTrSemRef,'dados'=>$aTrComSaldo);
    return $aRetorno;
}
function juntarItemRefSl($item,$aRefs)
{
    //inserirLogDb('Array referencias antes transformacao para aItemRefSl',$aRefs,__FUNCTION__);
    if(is_array($aRefs)){
        $qtReg = count($aRefs);
        /*for($i=0;$i<count($aRefs);$i++){
            inserirLogDb('TESTE - posicao',$i,__FUNCTION__);
        }*/
        inserirLogDb('montagem array aItemRefSl',"INICIO - qt.regs: $qtReg",__FUNCTION__);
        incrNivelCorrenteLogDb();
        for($i2=0;$i2<count($aRefs);$i2++){
            if(is_array($item)){
                $qtItem = count($item);
                if($qtReg == $qtItem){
                    $aItemRefSl[$i2]['item'] = $item[$i2];
                }else{
                    $aItemRefSl[$i2]['item'] = $item[0];
                }
            }else{
                $aItemRefSl[$i2]['item'] = $item;
            }
            $aItemRefSl[$i2]['ref'] = $aRefs[$i2];
            inserirLogDb('posicao - item - ref',
                $i2." - ".$aItemRefSl[$i2]['item']." - ".$aItemRefSl[$i2]['ref'] ,__FUNCTION__);
        }
        decrNivelCorrenteLogDb();
        inserirLogDb('montagem array aItemRefSl','FIM',__FUNCTION__);
    }
    //inserirLogDb('array com saldo antes agrup.TR',$aItemRefSl,__FUNCTION__);
    return $aItemRefSl;
}
function getNomeVarOrdenacaoBook()
{
    return 'ordenacao_book';

}
function getNomeVarOrdenacaoBookPi()
{
    return 'ordenacao_book_pi';

}

function getTrsItens($aItens){
    $ordenacao = getVarSessao(getNomeVarOrdenacaoBook());
    $ordenacaoPi = getVarSessao(getNomeVarOrdenacaoBookPi());
    if($ordenacao == ''){
        $ordenacao = 4;
    }
    $aTR = array();
    $lAchou = false;
    if(is_array($aItens)){
        $aItensUniq = array_unique($aItens);
        //var_dump($aItensUniq);
        inserirLogDb('item é um array?','sim',__FUNCTION__);
        if(is_array($aItensUniq)){
            $item  = $aItensUniq[0];//pega só o primeiro item
            $item2 = end($aItensUniq);
            if($item2 < $item){
                $item = $item2;
                //echo "<h1>item - $item </h1>";
            }
            inserirLogDb('primeiro item utilizado para buscar TR',$item,__FUNCTION__);
            $aTR = getTamanhoReal($item);
            $tipoItem  = getTipoItem($item);
            if($tipoItem == 'liso'){
                $ordenacao = 4;
            }
            $lAchou = true;
            /*$tam = count($aItensUniq);
            for($i=0;$i<$tam;$i++){
                $itemCorrente = $aItensUniq[$i];
                //echo "item corrente: $itemCorrente";
                //buscar tamanhos reais do item
                $aTR = getTamanhoReal($itemCorrente);
                if(is_array($aTR)){
                    //$aTrItens = array_merge($aTrItens,$aTR);
                    array_push($aTrItens,$aTR);
                }
                //var_dump($aTR);
                //echo "<br>";
            }*/
        }

    }else{
        inserirLogDb('item está em branco','sim',__FUNCTION__);
    }
    if($lAchou == false){
        $aTR = '';
    }

    $aTR = ordenarRefTR($aTR,$ordenacao,$ordenacaoPi);

    return $aTR;



}
function ordenarRefTR($aTR,$ordenacao,$ordenacaoPi)
{
    if(is_array($aTR)){
        $tam = count($aTR);
        for($i=0;$i<$tam;$i++){
            $arq = $aTR[$i]['arquivo'];
            $arqPuro = getArquivoPuro($arq);
            $arqSemExt = explode('.',$arqPuro);
            $arqItemRef = explode('_',$arqSemExt[0]);
            $arqPorTraco = explode('-',$arqItemRef[0]);
            $ref = $arqPorTraco[1]; //posicao 0 é o item
            inserirLogDb('ref passado para buscar ordem',$ref,__FUNCTION__);
            $ordemRef = getOrdemCodRefer($ref);
            inserirLogDb('Ref extraida - arquivo - ordem',"$ref - $arq - $ordemRef",__FUNCTION__);
            $aTR[$i]['ordem_ref'] = $ordemRef;
        }
        // Obtem a lista de colunas
        foreach ($aTR as $key => $row) {
            $ordemArq[$key]  = $row['ordem_ref'];
        }
        if($ordenacaoPi == 1){
            $ordenacao = 4;
        }
        if($ordenacao == 3){
            array_multisort( $ordemArq, 3, $aTR);
        }else{
            array_multisort( $ordemArq, 4, $aTR);
        }

        //inserirLogDb('array TR após reordenar',$aTR,__FUNCTION__);
    }

    return $aTR;

}
function getCompose($item, $ref)
{

    desenharCabecalho();


}

function desenharCabecalho($pdf, $item, $descItem)
{
    //$pdf = new TCPDF();
    $pdf->Image('../_lib/img/logo_ima.jpeg', 150, 22, 45, 6);
    $pdf->SetDrawColor(255, 0, 0);
    $pdf->SetLineWidth(0.1);
    $pdf->Line(195, 32, 15, 32);
    $pdf->SetXY(15, 18);
    $pdf->SetFont('', 'BI', 12);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->Cell(37, 20, 'Variantes da Estampa', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    $pdf->SetXY(1, 25);
    $pdf->SetFont('', 'BI', 20);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(145, 20, $descItem, 0, false, 'C', 0, '', 0, false, 'M', 'M');
    $pdf->SetXY(1, 36);
    $pdf->SetFont('', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(40, 10, $item, 0, false, 'C', 0, '', 0, false, 'M', 'M');
    $pdf->SetXY(1, 36);
    $pdf->SetFont('', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(327, 10, 'As cores visualizadas podem sofrer alterações.', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    return $pdf;

}

/***
 * @param $dir
 * @return array|string
 */
function getArqsDir($dir)
{   $aDir = array();
    $lAchou = false;
    $iCont = 0;
    if (is_dir($dir)) {
        //var_dump($dir);
        $sepDir = getSeparadorArquivo($dir);
        //echo "<h1>diretorio:$dir</h1>";
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                //echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
                inserirLogDb('arquivo corrente',$file,__FUNCTION__);
                inserirLogDb('substring(0,2)',substr($file,0,2),__FUNCTION__);
                $arqCompleto =$dir.$sepDir.$file;
                //echo "<h1>Arq.completo: $arqCompleto</h1>";
                if(substr($file,0,1) <> '.' and ! is_dir($arqCompleto)){
                    $aDir[$iCont] = $file;
                    $iCont++;
                    $lAchou = true;
                    inserirLogDb('arquivo desconsiderado?',"não - iCont = $iCont",__FUNCTION__);
                }else{
                    inserirLogDb('arquivo desconsiderado?','sim - começa com "." ou é um diretorio ',__FUNCTION__);
                }
            }
            closedir($dh);
        }
    }
    if($lAchou == false){
        $aDir = '';
    }

    return $aDir;
}
function sincrArqsPorTipo($tipo)
{
    $aTipo = explode(";",$tipo);
    //echo "<h1>antes var_dump aTipo</h1>";
    //var_dump($aTipo);
    inserirLogDb('tipos Passados por Parametro',$aTipo,__FUNCTION__);
    if(is_array($aTipo)){
        $tam = count($aTipo);
        incrNivelCorrenteLogDb();
        for($x=0;$x<$tam;$x++){
            $tipoCorrente = $aTipo[$x];
            inserirLogDb('Tipo Corrente',$tipoCorrente,__FUNCTION__);
            $dir = getPastaRaizTpArq($tipoCorrente,1);
            inserirLogDb("diretorio raiz tipo: $tipoCorrente",$dir,__FUNCTION__);
            $aArquivos = getArqsDir($dir);
            //$descArray = print_r($aArquivos,true);
            //inserirLogDb('Array Arquivos Diretorio',$aArquivos,__FUNCTION__);
            if(is_array($aArquivos)){
                $tam2 = count($aArquivos);
                for($i=0;$i<$tam2;$i++){
                    $arquivo = $aArquivos[$i];
                    $arquivo = str_replace('./','',$arquivo);
                    $arquivo = str_replace('._','',$arquivo);
                    if(trim($tipoCorrente) <> ''){
                        execAcaoArqDesign($arquivo,$tipoCorrente,1); // 1 - inclusao
                    }

                }
            }else{
                inserirLogDb("encontrou arquivos no diretorio $dir",'não',__FUNCTION__);
            }
            inserirLogDb('Fim - tipo de Arquivo',$tipoCorrente,__FUNCTION__);
        }
        decrNivelCorrenteLogDb();
    }
}
function setErros($msg)
{
    [erros] .= utf8_encode($msg);
}

/*function sincrFundoLiso($aArquivos)
{
    $dir  = getDirFisLisaFundo();
    $aArqs = getArqsDir($dir);
    inserirRelacItemArquivo();




}*/

function moverImagens($transacao,$dirOrigem,$dirDestino){

    $aImagens = getArqsDir("$dirOrigem/$transacao");
    //var_dump($aImagens);
    $tam = count($aImagens);
    for($i=0;$i<$tam;$i++){
        $imagens = $aImagens[$i];

        rename("$dirOrigem/$transacao/$imagens","$dirDestino/$imagens");
    }
}

function excluirImagem($arquivo,$tipoArquivo){

    $dirTipo     = getPastaRaizTpArq($tipoArquivo);
    $nomeArquivo = juntarDirArq($dirTipo,$arquivo);
    $nomeArquivo = juntarDirWebArquivo($nomeArquivo);
    //echo "<h1>nome_arquivo = $nomeArquivo</h1>";
    unlink($nomeArquivo);

}

function verifExisteRefTempl($item)
{

    $item = is_array($item) ? $item[0]:$item;
    $lAchei = false;
    $listaTipos = '16,17,18';
    $tipo     = "multi";
    $tabela   = " pub.relacs_item_arquivo ";
    $campos   = "arquivo";
    $condicao = "  it_codigo = $item and cod_tipo in($listaTipos)";
    $conexao  = "espec";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $lAchei = true;
    }
    return $lAchei;
}

?>
