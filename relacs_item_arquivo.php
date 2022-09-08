<?php
//__NM____NM__NFUNCTION__NM__//
function execAcaoArqDesign($arqParam, $tipoArqDesign='',$acao=1)
{
    inserirLogDb('parametro - Arquivo',$arqParam,__FUNCTION__);
    inserirLogDb('parametro - tipo arquivo design',$tipoArqDesign,__FUNCTION__);
    if($tipoArqDesign == ''){  // quando a origem vem de uma função que processa individualmente um arquivo  incluido na pasta.
        inserirLogDb('parametro Tipo Arquivo Design em branco?','sim',__FUNCTION__);
        $tipoArqDesign = getTipoRegPorCaminho($arqParam);
        //$arqSemDirWeb  = separarArquivoDirWeb($arquivo);
        $arqPuro        = getArquivoPuro($arqParam);

    }else{ // quando a origem vem de uma função que lê os arquivos de um diretorio
        inserirLogDb('parametro Tipo Arquivo Design em branco?','nao',__FUNCTION__);
        $arqPuro        = $arqParam;
    }
    inserirLogDb('Arquivo Puro',$arqPuro,__FUNCTION__);
    $funcao = getFuncaoTipoArq($tipoArqDesign);
    inserirLogDb('getFuncaoTipoArq->param:'.$tipoArqDesign,$funcao,__FUNCTION__);
    if($funcao <> '')
    {   inserirLogDb('funcao diferente de branco','sim',__FUNCTION__);
        $ret = call_user_func_array(array($this,$funcao),array($arqPuro,$acao));
        // call_user_func($this->$funcao,$arqPuro);

    }
    inserirLogDb('retorno função',$ret,__FUNCTION__);
    return $ret;
}
function excluirRelacItemArquivo($id)
{
    $aDados = array('dt_hr_exclusao' => 'SYSTIMESTAMP') ;
    $cmd = convertArrayEmUpdate('pub.relacs_item_arquivo',$aDados,"relac_item_arquivo_id = $id",
    '1');
    sc_exec_sql($cmd,"especw");

}

function excluirRelacsItemRef($idItemArquivo)
{
    $cmd = "delete from pub.relacs_item_ref where relac_item_arquivo_id = $idItemArquivo";
    sc_exec_sql($cmd,"especw");
}
function getTipoDE()
{
    return 15;
}
function getTipoFundoRefEst()
{
    return 11;
}
function getTipoTR()
{
    return 8;
}

function getTipoTempl2Cort()
{
    return 19;
}
function getTipoFundoLiso()
{
    return 4;
}

function getTipoFundoLisoPI()
{
    return 20;
}
function getTipoFTEst()
{
    return 6;
}
function getTipoFTLiso()
{
    return 12;
}

function getTipoCapaPIEst()
{
    return 13;
}

function getTipoCapaEst()
{
    return 5;
}

function getTipoSAEst()
{
    return 7;
}
function getTipoSAPIEst()
{
    return 14;
}
function getTipoSC()
{
    return 10;
}
function getTipoRef1()
{
    return 16;
}

function getTipoRef2()
{
    return 17;
}
function getTipoRef3()
{
    return 18;
}
function getTipoRef600(){
    return 9;
}
function getTipoRef150(){
    return 21;
}
function getTipoCartelaPi(){
    return 2;
}
function getTipoBookPi(){
    return 22;
}


function execAcaoTpArqLisoFundo($arqPuro,$acao=1)
{
   $id = execAcaoTpArqPorCodItem($arqPuro,getTipoFundoLiso(),'',0,$acao);
   return $id;
}
function execAcaoTpArqLisoFundoPI($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoFundoLisoPI(),'',0,$acao);
    return $id;
}

function execAcaoTpArqLisoFT($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoFTLiso(),'',0,$acao);
    inserirLogDb('Entrei na função do Liso FT',
        "sim - arquivo: $arqPuro acao: $acao tipo:".getTipoFTLiso(),__FUNCTION__);
    return $id;
}
function execAcaoTpArqCapaEst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoCapaEst(),'',0,$acao);
    return $id;
}
function execAcaoTpArqCapaPIEst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoCapaPIEst(),'',0,$acao);
    return $id;
}

function execAcaoTpArqFTEst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoFTEst(),'',0,$acao);
    return $id;
}
function execAcaoTpArqFundoRefEst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoFundoRefEst(),'',0,$acao);
    return $id;
}

function execAcaoTpArqSAEst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoSAEst(),'',0,$acao);
    return $id;
}
function execAcaoTpArqSAPIEst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoSAPIEst(),'',0,$acao);
    return $id;
}

function execAcaoTpArqTREst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItemRef($arqPuro,getTipoTR(),2,$acao);
    return $id;
}
function execAcaoTpArqDEEst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItemRef($arqPuro,getTipoDE(),2,$acao);
    return $id;
}
function execAcaoTpArqSCPIEst($arqPuro,$acao=1)
{
    //$id = execAcaoTpArqPorCodItemRef($arqPuro,10,2,$acao);
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoSC(),'',0,$acao);
    return $id;
}

function execAcaoTpArqCartelaPi($arqPuro,$acao=1){
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoCartelaPi(),'',0,$acao);
    return $id;
}
function execAcaoTpArqBookPi($arqPuro,$acao=1){
    $id = execAcaoTpArqPorCodItem($arqPuro,getTipoBookPi(),'',0,$acao);
    return $id;
}
function execAcaoTpArqRef600Est($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItemRef($arqPuro,getTipoRef600(),1,$acao);
    return $id;
}
function execAcaoTpArqRef150Est($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItemRef($arqPuro,getTipoRef150(),1,$acao);
    return $id;
}

function execAcaoTpArqRefTp1Est($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItemRef($arqPuro,getTipoRef1(),1,$acao);
    return $id;
}
function execAcaoTpArqRefTp2Est($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItemRef($arqPuro,getTipoRef2(),1,$acao);
    return $id;
}
function execAcaoTpArqRefTp3Est($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItemRef($arqPuro,getTipoRef3(),1,$acao);
    return $id;
}

function inserirTpArqRefTp2CortEst($arqPuro,$acao=1)
{
    $id = execAcaoTpArqPorCodItemRef($arqPuro,getTipoTempl2Cort(),1,$acao);
    return $id;
}

function execAcaoTpArqPorCodItemRef($arqPuro, $tipo, $tpInsRef,$acao=1)
{
    $itemCorr = '';
    $aId       = array();
    $lAchou    = false;
    //echo "arquivo puro: $arqPuro<br>";
    $aArquivoSemExtensao = explode('.',$arqPuro);
    inserirLogDb('array arquivo separando extensão',$aArquivoSemExtensao,__FUNCTION__);
    //echo "arquivo puro sem extensao <br>";
    //var_dump($aArquivoSemExtensao);
    $aArqPorItem = explode('_',$aArquivoSemExtensao[0]);
    inserirLogDb('array arquivo separando Item',$aArqPorItem,__FUNCTION__);
    //echo "arquivo puro sem extensao por item<br>";
    //var_dump($aArqPorItem);
    $aItemRef = array();

    if(is_array($aArqPorItem)){
        $tamx = count($aArqPorItem);
         //no caso de dois itens - sincroniza a relação dos itens
        if($tamx == 2){
            for($x=0;$x<$tamx;$x++) {
                $aItens = explode('-', $aArqPorItem[$x]);
                if($x == 0){
                    $itCodigo01 =  $aItens[0];
                }
                if($x == 1){
                    $itCodigo02 =  $aItens[0];
                }
            }
            if($itCodigo01 <> '' and $itCodigo02 <> ''){
                sincrRelacsItem($itCodigo01,$itCodigo02);
            }
        }
        for($x=0;$x<$tamx;$x++){
            //echo "item:".$aArqPorItem[$x]."<br>";
            $aArquivo = explode('-',$aArqPorItem[$x]);
            inserirLogDb('array de arquivo e referencia',$aArquivo,__FUNCTION__);
            if(is_array($aArquivo)){
                $tam = count($aArquivo);
                inserirLogDb('qt.array',$tam,__FUNCTION__);
                $listaClass = '';
                for($i=0;$i<$tam;$i++){
                    switch ($i){
                        case 0:
                            $itemCorr = $aArquivo[$i];
                            inserirLogDb('posicao 0 - item corrente',$itemCorr,__FUNCTION__);
                            break;
                        case 1:
                            $ref = $aArquivo[$i];
                            if(strlen($ref) == 2){
                                $ref = "0".$ref;
                            }
                            $aItemRef[] = array('item' => $itemCorr,'ref'=> $ref);
                            inserirLogDb('posicao maior que zero - array atual',$aItemRef,__FUNCTION__);
                            break;
                        default:
                            /*classificações que ocorrem nas referencias de template 1, 2 e 3 */
                            if($tipo == getTipoRef1() or
                               $tipo == getTipoRef2() or
                               $tipo == getTipoRef3()){
                               $listaClass = util_incr_valor($listaClass,$aArquivo[$i],','
                                   ,true) ;
                            }
                            if($tipo == getTipoDE() or  $tipo == getTipoTR()){
                                inserirLogDb('Item corrente(TR/DE):',$itemCorr,__FUNCTION__);
                                $aItemRef[] = array('item' => $itemCorr,'ref'=>$aArquivo[$i]);
                            }
                    }
                }
            }
            /* a principio a classificação ficará desvinculada de um arquivo.
            caso seja necessário a vinculação pode ser feita futuramente*/
            if($listaClass <> ''){
                excluirClassItemRef('*',$itemCorr,$ref);
                sincrClassItemRef($listaClass,$itemCorr,$ref,$acao);
            }

            $lRelac = false;
            $lArquivoChave = false;
            $codRefer = '';
            //echo "<h1>item: $item</h1>";
            //var_dump($aRef);
            switch ($tpInsRef){
                case 1: //
                    if(isset($aItemRef[0]['ref'])){
                        $codRefer = $aItemRef[0]['ref'];
                        inserirLogDb('Tipo de Inserção ref:',"$tpInsRef - codrefer -> $codRefer",__FUNCTION__);
                    }
                    $lRelac = false;
                    $lArquivoChave = false;
                    break;
                case 2:
                    $codRefer = '';
                    $lRelac = true;
                    $lArquivoChave = true;
                    inserirLogDb('Tipo de Inserção ref:',"$tpInsRef - codrefer -> $codRefer",__FUNCTION__);
                    break;
            }
            if($itemCorr <> ''){
                inserirLogDb('item antes de relacionar com o arquivo',$itemCorr,__FUNCTION__);
                //echo "<h1>item diferente de branco</h1>";

                $id = sincrRelacItemArquivo($arqPuro,$itemCorr,$codRefer,0,$tipo,$acao,true,$lArquivoChave);
                $aId[] = $id;
                $itemRelac = getItemRelac($itemCorr);
                inserirLogDb('item relacionado(getitemrelac)',$itemRelac,__FUNCTION__);
                if($tamx == 1) {
                    inserirLogDb('entrei na condicao de ter so um item?','SIM',__FUNCTION__);

                    inserirLogDb('item corrente - item relacionado - '," $itemCorr - $itemRelac -",__FUNCTION__);
                    if(substr($itemRelac,0,1)== '2'){
                        $lContainer = true;
                    }
                    if($itemRelac <> '' and $lContainer == false){
                        $idRelac = sincrRelacItemArquivo($arqPuro,$itemRelac ,$codRefer,0,$tipo,$acao,true,$lArquivoChave);
                        $aId[] = $idRelac;
                    }

                }else{
                    inserirLogDb('entrei na condição de ter só um item?','NAO',__FUNCTION__);
                }
                $lAchou = true;
            }
        }



        /*echo "<h1>var dump aId</h1>";
        var_dump($aId);
        echo "<h1>var dump aItemRef</h1>";
        var_dump($aItemRef);*/

        if($lRelac == true){
            inserirLogDb('Tipo de Registro com Relacao item x ref','SIM',__FUNCTION__);
            /*Logica errada
             * if($tamx == 1){ //arquivos que tem apenas um item informado
                $aItemRef = criarRelacItemRefItemRelac($itemCorr,$aItemRef);
                inserirLogDb('desenho com apenas um item no nome?',
                    "SIM item princ:$itemCorr",__FUNCTION__);

            }else{
                inserirLogDb('desenho com apenas um item no nome?','NAO',__FUNCTION__);
            }*/
            if(is_array($aId)){
                $tamx = count($aId);
                inserirLogDb('aId é array','sim',__FUNCTION__);
                inserirLogDb('array relac item ref',$aItemRef,__FUNCTION__);
                for($x=0;$x< $tamx; $x++){
                    if(is_array($aItemRef)){
                        $tam2 = count($aItemRef);
                        for($i=0;$i<$tam2;$i++){
                            $itemCorrente = $aItemRef[$i]['item'];
                            if($x == 1){ //segunda relação item arquivo inclusa
                                $itemCorrente = $itemRelac;
                            }
                            $refCorrente = $aItemRef[$i]['ref'];
                            //echo "<h1>item: $itemCorrente - ref: $refCorrente - id -".$aId[$x]."</h1>";

                            sincrRelacsItemRef($itemCorrente,$refCorrente,
                                $tipo,$aId[$x],$acao);
                        }
                    }
                }
            }else{
                inserirLogDb('aId é array','nao',__FUNCTION__);
            }

        }else{
            inserirLogDb('Tipo de Registro com Relacao item x ref','NAO',__FUNCTION__);

        }

    }
    if($lAchou == false){
        $aId = 0;
    }
    if(is_array($aId)){
        if(count($aId) == 1){
            $aId = $aId[0];
        }
    }
    //echo "<h1>final funcao inserirTpArqPorCodItemRef </h1>";
    return $aId;
}


/**
 * @param $item
 * @param $aItemRef
 * Verifica se o item passado por parametro tem relação com outro item.
 * Se sim, duplica os registros do array, substituindo o item passado por parametro pelo item relacionado
 * nos registros duplicados.

function criarRelacItemRefItemRelac($item, $aItemRef)
{
    inserirLogDb('array de itens refs a ser duplicado',$aItemRef,__FUNCTION__);
    $itemRelac = getItemRelac($item);
    if($itemRelac <> ''){
        inserirLogDb('item relacionado diferente de branco?','SIM',__FUNCTION__);
        if(is_array($aItemRef)){
            $tam = count($aItemRef);
            for($i=0;$i<$tam;$i++){
                $aItemRef[] = array('item' => $itemRelac,'ref'=>$aItemRef[$i]['ref'] );
            }
        }
    }else{
        inserirLogDb('item relacionado diferente de branco?','NAO',__FUNCTION__);
    }

    inserirLogDb('array de itens refs após duplicacao',$aItemRef,__FUNCTION__);
    return $aItemRef;
}
 * */
/*function convTipoArqTipoRelac($tipo)
{
    $tipoRelac = 0;
    switch($tipo){
        case 8:
            $tipoRelac = 1;
        break;
        case 10;
            $tipoRelac = 3;
            break;
        case 14:
            $tipoRelac = 2;
            break;
    }
    return $tipoRelac;
}*/

function sincrRelacItemArquivo($arqPuro,$itCodigo,$codRefer,$container,$tipo,
                               $acao,$logSubstituir=false,$logArquivoChave=false)
{
    $id = 0;
    $campoId = 'relac_item_arquivo_id';
    if($logArquivoChave){
        $arqParam = $arqPuro;
    }else{
        $arqParam = '';
    }
    $aReg = getRegRelacItemArquivo($itCodigo,$codRefer,$container,$tipo,
        $campoId,$arqParam);
    if(is_array($aReg)){
        inserirLogDb('achou relacao item arquivo?','sim',__FUNCTION__);
        $id = $aReg[0][$campoId];
        if($logSubstituir){
            $aUpdate = array(
                'arquivo' => $arqPuro,
                'dt_hr_alteracao'  => 'SYSTIMESTAMP'
            );
            atuRelacItemArquivo($id,$aUpdate,2);
        }
        if($acao == 2){
            inserirLogDb('acao de excluir','SIM',__FUNCTION__);
            excluirRelacItemArquivo($id);
            excluirRelacsItemRef($id);

        }
        else{
            inserirLogDb('acao de excluir','NAO',__FUNCTION__);
        }
    }else{
        if($acao == 1){
            inserirLogDb('acao','1 - incluir',__FUNCTION__);
            inserirLogDb('achou relacao item arquivo?','nao',__FUNCTION__);
            $id = inserirRelacItemArquivo(
                $itCodigo,
                $codRefer,
                $container,
                $tipo,
                $arqPuro,
                1,
                0
            );
        }else{
            inserirLogDb('acao',"$acao - NAO entrou na inclusao",__FUNCTION__);
        }

    }
    return $id;
}
function execAcaoTpArqPorCodItem($arqPuro, $tipo, $codRefer='', $container=0, $acao= 1,$logSubstituir=false)
{
    /***************'**************************************
     * Premissa
     * O nome do arquivo deve conter o codigo do item,
     * podendo informar no nome do arquivo
     * mais de um item separado por "-"(traço)
     * a terceira posição é reservada para o container caso exista
     * ***************************************************/
    $aArquivo = explode('.',$arqPuro);
    $item = $aArquivo[0];
    $id = 0;
    $qtItem = 0;
    $listaId ='';
    $aItem = explode('-',$item);
    $descArray = print_r($aItem,true) ;
    inserirLogDb('array - nome arquivo:',$descArray,__FUNCTION__);
    if(is_array($aItem)){
        $tam = count($aItem);
        inserirLogDb('Tamanho aItem(qt.itens)->',$tam,__FUNCTION__);
        $qtItem = $tam;
        switch ($tam){
            case 1:
                $qtItem = 1;
                $container = 0;
                inserirLogDb('tamanho 1','qtitem = 1 e container = 0',__FUNCTION__);
                break;
            case 2:
                $container = $aItem[1];
                inserirLogDb('tamanho 2 - container assumiu o valor da posicao 1  do array->',$aItem[1],__FUNCTION__);
                $lContainer = verifContainer($container);
                if($lContainer == false){
                    inserirLogDb('container nao encontrado','setado container como 0 e qtItem como 2',__FUNCTION__);
                    $container = 0;
                    $qtItem = 2;
                }
                else{
                    inserirLogDb('container encontrado','setado qtItem como 1',__FUNCTION__);
                    $qtItem = 1;
                }
                break;
            case 3:
                $container = $aItem[2];
                //inserirLogDb('arquivo tem 3 posicoes',"sim - container:$container",__FUNCTION__);
                $qtItem = 2;
                inserirLogDb('tamanho 3 -setado qtItem igual 2 e container assumiu a posição 2 do array->'
                    ,$container,__FUNCTION__);
                break;
        }
        inserirLogDb('qt.posicoes',"$qtItem - container param:$container",__FUNCTION__);
        for($i=0;$i< $qtItem;$i++){
            inserirLogDb('posicao do array de itens',$i,__FUNCTION__);
            $itCodigo = isset($aItem[$i]) ? trim($aItem[$i]) : '';
            if($itCodigo == ''){
                continue;
            }
            inserirLogDb('item corrente',$itCodigo,__FUNCTION__);
            if($i == 0){
                $itCodigoPai = $itCodigo;
                inserirLogDb('item pai - posicao 0:',$itCodigoPai,__FUNCTION__);
            }
            $id = sincrRelacItemArquivo($arqPuro,$itCodigo,$codRefer,$container,$tipo,$acao,true);
            $listaId = util_incr_valor($listaId,$id);
            if($i > 0){ // no caso de ter dois itens informados no nome do arquivo inclui o relacionamento dos itens
                inserirLogDb('item pai -- itcodigo',"$itCodigoPai - $itCodigo",__FUNCTION__);
                sincrRelacsItem($itCodigoPai,$itCodigo);
            }
        }
    }
    inserirLogDb('retorno',$listaId,__FUNCTION__);
    return $listaId;
}

function atuRelacItemArquivo($id,$aUpdate,$cpSemAspas ='')
{

    $cmd = convertArrayEmUpdate(
        'pub.relacs_item_arquivo',
        $aUpdate,
        "relac_item_arquivo_id = $id",
       $cpSemAspas);
    return $cmd;

}

function excluirArqPortipo($listaTipos)
{
    $aTipos = explode(';',$listaTipos);

    if(is_array($aTipos)){
        $tam = count($aTipos);
        for($i=0;$i<$tam;$i++){
            $tipo = $aTipos[$i];
            $aIds = getRelacsItemArquivoPorTipo($tipo);
            $cmd = "delete from pub.relacs_item_arquivo where cod_tipo = $tipo ";
            sc_exec_sql($cmd,"especw");

        }
    }
    if(is_array($aIds)){
        $tam = count($aIds);
        for($i=0;$i<$tam;$i++){
              $id = $aIds[$i]['relac_item_arquivo_id'];
              $cmd = "delete from pub.relacs_item_ref where relac_item_arquivo_id = $id  ";
              sc_exec_sql($cmd,"especw");
        }
    }



}
function getRelacsItemArquivoPorTipo($tipo)
{
    $aDados = getDados('multi','pub.relacs_item_arquivo',
                       'relac_item_arquivo_id',"cod_tipo = $tipo",'espec');

    return $aDados;

}


/*function getRelacItemArqPorArqCompleto($arquivo)
{
    $dirArquivo = getDiretorioArquivo($arquivo);
    $tipoArquivo = getTpArqPastaRaiz($dirArquivo);
}*/
function getRegRelacItemArquivo($itCodigo, $codRefer, $container, $codTipo, $campos='', $arquivoParam='')
{
    if($arquivoParam){
        $condArq = " and arquivo ='$arquivoParam' ";
    }else{
        $condArq = '';
    }
    $aReg= getReg(
        'espec',
        'relacs_item_arquivo',
    'it_codigo,cod_refer,container_id,cod_tipo',
    "'$itCodigo','$codRefer',$container,$codTipo",$campos,
    "dt_hr_exclusao is null $condArq ");
    return $aReg;
}
function getArqRelacItemArquivo($id,$logArqCompleto=0)
{
    $arquivo = '';
    $aReg = getRegRelacItemArquivoPorID($id,'arquivo');
    if(is_array($aReg)){
       $arquivo = $aReg[0]['arquivo'];
       if($logArqCompleto){
           $tipoArqDesign = getTipoDE();
           $dir = getPastaRaizTpArq($tipoArqDesign,1);
           $arquivo =  juntarDirArq($dir,$arquivo);
       }
    }
    return $arquivo;
}
function getRegRelacItemArquivoPorID($id,$campos)
{
    $aReg= getReg(
        'espec',
        'relacs_item_arquivo',
        'relac_item_arquivo_id',
        "$id",$campos,
        "dt_hr_exclusao is null");
    return $aReg;

}
function inserirRelacItemArquivo($itCodigo,$codRefer,$container,$codTipo,$arquivo,$codTipoBusca,$qtPalavras)
{
    $arqTransacaoCorrente = getArqTransacaoImg();
    //echo "<h1>Item = $itCodigo / Container = $container</h1>";
    $aDados = array('relac_item_arquivo_id' => 'pub.seq_relac_item_arquivo.NEXTVAL',
                    'it_codigo'        => $itCodigo,
                    'cod_refer'        => $codRefer,
                    'container_id'     => $container,
                    'cod_tipo'         => $codTipo,
                     'arquivo'         => $arquivo,
                    'cod_tipo_busca'   => $codTipoBusca,
                    'qt_palavras'      => $qtPalavras,
                    'dt_hr_inclusao'   => 'SYSTIMESTAMP',
                    'arq_transacao_img_id' => $arqTransacaoCorrente);
    //var_dump($aDados);
    $cmd= convertArrayEmInsert('pub.relacs_item_arquivo',$aDados,
        '1,9');
    sc_exec_sql($cmd,"especw");
    $id = buscarVlSequenciaEspec('seq_relac_item_Arquivo','relacs_item_arquivo');
    inserirLogDb('Id Incluido',$id,__FUNCTION__);
    return $id;
}

function verificarRelacsItArq($tipoArquivo,$arqTransacaoId,$nomeArquivo){

    $msg = '';
    $aReg = getDados('unico', 'PUB.relacs_item_arquivo','count(arquivo) as qt',
        "arq_transacao_img_id = $arqTransacaoId 
                and dt_hr_exclusao is null and it_codigo <> ''",'espec');
    $separador = '_';
    if(is_array($aReg)){
        $qt = $aReg[0]['qt'];
        if(strstr($nomeArquivo,"_") <> false){
            $separador = "_";
        }else{
            if(strstr($nomeArquivo,"-") <> false){
                $separador = "-";

            }
        }
        $aNomeArquivo = explode($separador,$nomeArquivo);
        if(is_array($aNomeArquivo) and $tipoArquivo > 2){ // o tipo um é 1-PDF BOOK e 2- PDF Cartela e tem regras que não se Aplicam
            $contador = count($aNomeArquivo);
            $qt2 = count($aNomeArquivo);
            // tratamento para descontar o container no numero de registros
            $lTpArqPi = verifTpArqPi($tipoArquivo);
            if($lTpArqPi){
                for($i=0;$i < $qt2;$i++){
                    if($i > 0){
                        $lContainer = verifContainer($aNomeArquivo[$i]);
                        if($lContainer){
                            $contador--;
                            //echo "<h1>Entrei aqui - $contador </h1>";
                        }
                        //echo "<h1>Nome arquivo = {$aNomeArquivo[$i]}</h1>";
                    }
                }
            }
            //var_dump($aNomeArquivo);
            $logItemUnico = verifTpArqItemUnico($tipoArquivo);
            if($logItemUnico == false)
            {
                if($qt <> $contador){
                    $msg = "Quantidade incorreta";
                }
            }else{ //item unico
                if($qt <> 1){
                    $msg = "Quantidade incorreta";

                }
            }
        }
    }
    return $msg;

}
function extrairItensNomeArquivo($nomeArquivo,$separador1,$separador2='')
{   $aItem = array();
    $aNomeArquivoSemExtensao = explode('.',$nomeArquivo);
    $nomeArquivo = $aNomeArquivoSemExtensao[0];
    $aNomeArquivo = explode($separador1, $nomeArquivo);
    if(is_array($aNomeArquivo)){
        foreach($aNomeArquivo as $reg){
            if($separador2 <> ''){
                $aSubNome = explode($separador2,$reg);
                $aItem[] = $aSubNome[0];
            }else{
                $aItem[] = $reg;
            }
        }
    }
    return $aItem;

}
function verificarRelacsItem($tipo, $nomeArquivo)
{
    $aItem  = '';
    $msg    = '';
     switch($tipo){
         case  1:
         case  4:
         case  5:
         case  6:
         case  7:
             $aItem = extrairItensNomeArquivo($nomeArquivo,'-');
             break;
         case 8:
         case 15:
             $aItem = extrairItensNomeArquivo($nomeArquivo,'_','-');
             break;
     }


     if(is_array($aItem) and  count($aItem) == 2){
          $it1 = $aItem[0];
          $it2 = $aItem[1];
          $aReg = getDados('unico', 'PUB.relacs_item', 'it_codigo as it, it_codigo_02 as it2',
              "it_codigo = $it1 and it_codigo_02 = $it2", 'espec');
          if(!is_array($aReg)){
              $msg ='não encontrado';
          }
      }
     $verifItemClassif = verifTpArqTemClassif($tipo);
     if($verifItemClassif){
         $msg = "N/A";
     }


    return $msg;

}
function verifContainer($parte)
{
    $parte = str_replace('.jpg','',$parte);
    $lContainer = false;
    $aItem = buscarDadosItem($parte);
    if($aItem == ''){ // não achou item
        $aContainer = getDadosContainer($parte,'"nr-container"');
        if($aContainer == ''){ //não achou o container
            if(substr($parte,0,1)== '2'){
                $lContainer = true;
            }
        }else{
            $lContainer = true;
        }
    }
    return $lContainer;
}
?>
