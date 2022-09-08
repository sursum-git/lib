<?php
function inserirClassItemRef($class,$item,$ref)
{
    $arqTransacaoCorrente = getArqTransacaoImg();
   // echo "<h1>Transação = $arqTransacaoCorrente</h1>";
    if($class <> ''){
        $aClass = explode(',',$class);
        if(is_array($aClass)){
            $tam = count($aClass);
            for($i=0;$i<$tam;$i++){
                $classCorr = $aClass[$i];
                $arrayInsert = array('class_item_ref_id' => 'pub.seq_class_item_ref.NEXTVAL',
                    'it_codigo'=> $item,
                    'cod_refer'=> $ref,
                    'class_design_id'=>$classCorr,
                    'arq_transacao_img_id' => $arqTransacaoCorrente
                );
                $cmd = convertArrayEmInsert('pub.class_item_ref',$arrayInsert,'1');
                sc_exec_sql($cmd,"especw");
            }
        }
    }
}

function excluirClassItemRef($class,$item,$ref)
{
    if($class <> '' and $class <> '*'){
        $aClass = explode(',',$class);
        if(is_array($aClass)){
            $tam = count($aClass);
            for($i=0;$i<$tam;$i++){
                $classCorr = $aClass[$i];
                $cmd = "delete from pub.class_item_ref where it_codigo = '$item' 
                        and cod_refer = '$ref' and class_design_id = $classCorr  ";

            }
        }
    }else{
        if($class == '*'){
            $cmd = "delete from pub.class_item_ref where it_codigo = '$item' 
                        and cod_refer = '$ref'   ";

        }
    }
    sc_exec_sql($cmd,"especw");
}
function sincrClassItemRef($class,$item,$ref,$acao)
{
    switch ($acao){
        case 1:
            inserirClassItemRef($class,$item,$ref);
            break;
        case 2:
            excluirClassItemRef($class,$item,$ref);
            break;
    }
}
function renomearArqs($logSimular=false)
{
    $aItemRefClass = getArqsTemplates();
    $aResult = array();
    if(is_array($aItemRefClass)){
        $tam = count($aItemRefClass);
        //var_dump($aItemRefClass);
        for($i=0;$i<$tam;$i++){
            $itCodigo = $aItemRefClass[$i]['item'];
            $codRefer = $aItemRefClass[$i]['ref'];
            $class    = $aItemRefClass[$i]['lista_class'];
            $aRegItemArquivo = getDadosRelacItemArquivo(getTipoRef1().",".getTipoRef2().",".getTipoRef3(),
                $itCodigo,0,$codRefer);

            if(is_array($aRegItemArquivo)){
                  $arquivo = $aRegItemArquivo[0]['arquivo'];
                  $tipo = $aRegItemArquivo[0]['cod_tipo'];
                  $dirTipo = getPastaRaizTpArq($tipo,true);
                  $arqCompleto = juntarDirArq($dirTipo,$arquivo);
                  $arqCompleto = str_replace("//","/",$arqCompleto);
                  $logAchouArq = is_file($arqCompleto);
                  $aArquivoCompleto = explode('.',$arqCompleto);
                  $arqSemExtensao = $aArquivoCompleto[0];
                  $arqSemExtensao = getArquivoPuro($arqSemExtensao);
                  if($logAchouArq){
                      $novoNomeArquivo = $arqSemExtensao."-".$class.".jpg";
                      if(strlen($arqSemExtensao) == 10 or strlen($arqSemExtensao) == 11){
                          if($logSimular == false){
                              rename($arqCompleto,$novoNomeArquivo);
                              $descSimulacao = "não";
                          }else{
                              $descSimulacao = "sim";
                          }
                          $aResult[] = array('item'=> $itCodigo,'ref'=> $codRefer,
                              'resultado'=>"Arquivo renomeado de  $arqCompleto para $novoNomeArquivo - simulação?:$descSimulacao");
                      }else{
                          $aResult[] = array('item'=> $itCodigo,'ref'=> $codRefer,
                              'resultado'=>"Arquivo $arqCompleto desconsiderado por não ter o tamanho correto (10 caracteres sem extensão)");
                      }
                  }else{
                      $aResult[] = array('item'=> $itCodigo,'ref'=>$codRefer,'resultado'=>"arquivo $arqCompleto não encontrado no sistema de arquivos");
                  }
            }else{
                $aResult[] = array('item'=> $itCodigo,'ref'=>$codRefer,'resultado'=>'arquivo não encontrado no bd');
            }
        }
    }
    return $aResult;
}
function getArqsTemplates()
{
    $itemAnterior = '';
    $refAnterior = '';
    $listaClass = '';
    $aItemRefClass = array();
    $logCrieiReg = false;
    $aDados = getDados('multi','pub.class_item_ref',
        'it_codigo,cod_refer,class_design_id',
        '1=1 order by it_codigo, cod_refer',
    'espec');
    if(is_array($aDados)){
        $tam = count($aDados);
        for($i=0;$i<$tam;$i++){
            $itCodigo   = $aDados[$i]['it_codigo'];
            $codRefer   = $aDados[$i]['cod_refer'];
            $classId    = $aDados[$i]['class_design_id'];
            //echo "<h4>posicao -> $i - item:$itCodigo - ref: $codRefer - class: $classId</h4> ";
            if( ($codRefer <> $refAnterior and $refAnterior <> '')
            or($codRefer == $refAnterior and $itCodigo <> $itemAnterior)){
                //echo "<h5>entrei na condição de troca de item anterior-item-refant($itemAnterior-$itCodigo-$refAnterior), lista classificações-> $listaClass</h5>";
                $aItemRefClass[] = array('item'=> $itemAnterior, 'ref'=>$refAnterior,
                     'lista_class' => $listaClass);
                $logCrieiReg = true;
                $listaClass = '';
            }else{
                //echo "<h5>NAO entrei na condição de troca de item-refant($itCodigo-$refAnterior), lista classificações-> $listaClass</h5>";
            }
            $listaClass    = util_incr_valor($listaClass,$classId,'-',true);
            //echo "<h5>lista classificações após incremento-> $listaClass</h5>";
            $itemAnterior = $itCodigo;
            $refAnterior  = $codRefer;

        }
        if($logCrieiReg == false){
            $aItemRefClass[] = array('item'=>$itCodigo,'ref'=> $refAnterior,
                'lista_class'=>$listaClass);
        }
    }

    return $aItemRefClass;

}

function verificarClassItemRef($tipoArquivo,$arqTransacaoId,$nomeArquivo){

    $msg = '';
    $temClassif = verifTpArqTemClassif($tipoArquivo);
    if($temClassif){
        $aReg = getDados('unico',
            'PUB.class_item_ref',
            'count(*) as qt',
            "arq_transacao_img_id = $arqTransacaoId and it_codigo <> '' and cod_refer <> ''",
            'espec');
        if(is_array($aReg)){
            $qt = $aReg[0]['qt'];
            //echo "<h1>$qt</h1>";
        }else{
            $qt = 0;
        }
        $aNomeArquivo = explode('-',$nomeArquivo);
        $contador = count($aNomeArquivo);
        $contador -= 2;
        //echo "<h1>contador:$contador  - qt: $qt</h1>";
        if($qt <> $contador){
            $msg = "Quantidade incorreta";
        }
    }else{
        $msg = 'N/A';
    }

    return $msg;

}
?>
