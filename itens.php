<?php
//__NM__Itens__NM__FUNCTION__NM__//
function buscarDadosItem($cod_item)
{
    $retorno = array();
    $lAchou = false;
    $sql = "select \"desc-item\", un, narrativa, \"peso-liquido\"  
                from pub.item where \"it-codigo\" =  '$cod_item' WITH (NOLOCK)";
    //echo $sql."<br>";
    sc_lookup(meus_dados, $sql,"med");
    if ({meus_dados} === false)
		{
            echo "Erro de acesso. Mensagem = " . {meus_dados_erro};
		}
		elseif (empty({meus_dados}))
		{
            //echo "Comando select não retornou dados ";
        }
		else

		{

            $desc_item    = {meus_dados[0][0]};
			$un_item	  = {meus_dados[0][1]};
			$narrativa    = {meus_dados[0][2]};
			$pesoLiquido  = {meus_dados[0][3]};
			$retorno[] = array("desc-item" => $desc_item, "un" => $un_item,
                'narrativa' => $narrativa,
                "peso_liquido" => $pesoLiquido);
			$lAchou = true;

		}
		if($lAchou == false){
		    $retorno = '';
        }
		return $retorno;
}
function buscarGramaturaItem($cod_item)
{
    $retorno = '';
    $sql = "select gramatura from pub.\"item-ext\" where \"it-codigo\" = '$cod_item'  WITH (NOLOCK)";
    //echo $sql."<br>";
    sc_lookup(meus_dados, $sql, "espec");
    if ({meus_dados} === false){
    echo "Erro de acesso. Mensagem = buscarGramaturaItem -  " . {meus_dados_erro};
    }
	elseif (empty({meus_dados})){
    //echo "Comando select não retornou dados ";
    }
	else{
        $retorno = {meus_dados[0][0]};
	}
	return $retorno;
}

/**tratarFiltroItem
 * 1- verifica se existe uma virgula e considera que se sim o usuario
 * quer pesquisar pelo termo existente antes de cada virgula
 * 2- Verifica se foi informado apenas um numero e se sim joga o filtro
 * para o campo Item
 * @param $item
 */
function tratarFiltroItem($item)
{
    $listaItem = '';
    $filtro = '';
    if(stristr($item,',') <> false){
        $aItem = explode(',',$item);
        if(is_array($aItem)){
            $tam = count($aItem);
            for($i=0;$i<$tam;$i++){
                $termo = $aItem[$i];
                if(is_numeric(ltrim(trim($termo))) ){
                    $termo = ltrim(trim($termo));
                    $listaItem = util_incr_valor($listaItem,$termo,',');
                }else{
                    $filtro = gerarFiltroPartes('item.\"desc-item\"');
                }
            }
        }

    }else{

    }
}


function getDescrItem($itCod,$complemento='')
{
    //$gram                = buscarGramaturaItem($itCod);
    $aDescItem		     = buscarDadosItem($itCod);
    $gram = '';
    //var_dump($aDescItem);
    if(is_array($aDescItem)){
        //echo "<h1>1</h1>";
        $gram = buscarGramaturaItem($itCod);
        //$pesoLiquidoFormat = formatarNumero($aDescItem[0]['peso_liquido']) ;
        $descItem			= $aDescItem[0]['desc-item']. "- UM:".$aDescItem[0]['un']." - ". $gram." G/M  ";
        $descItem = util_incr_valor($descItem,$complemento," ");
        //echo "<h1>2</h1>";
    }else{
        $descItem			= '';
    }

    return $descItem;

}

function inserirRelacsItem($item1,$item2)
{
    $arqTransacaoCorrente = getArqTransacaoImg();
    $arrayInsert = array('it_codigo'=>$item1,'it_codigo_02' =>$item2, 'arq_transacao_img_id'=> $arqTransacaoCorrente);
    $cmd = convertArrayEmInsert('pub.relacs_item',$arrayInsert);
    sc_exec_sql($cmd,"especw");
}
function sincrRelacsItem($item1,$item2)
{
    $lAchou = getRelacItem($item1,$item2);
    if($lAchou == false){
        inserirRelacsItem($item1,$item2);
    }
}
function getRelacItem($item,$item2)
{
    $lAchou = false;
    $aReg = retornoSimplesTb01(
        'pub.relacs_item',
        'it_codigo,it_codigo_02',
        "(it_codigo ='$item' and it_codigo_02 = '$item2') or 
                 (it_codigo ='$item2' and it_codigo_02 = '$item') ",
        'espec'
    );
   if(is_array($aReg)){
       $lAchou = true;
   }
   return $lAchou;
}
function getItemRelac($item)
{
    $itemRelac = '';
    $aReg = retornoSimplesTb01(
        'pub.relacs_item',
        'it_codigo,it_codigo_02',
        "it_codigo ='$item' or it_codigo_02 = '$item'",
        'espec'
    );
    if(is_array($aReg)){
        inserirLogDb('relação de itens encontrada',"sim - item: $item possui relação com outro item",__FUNCTION__);
        $aReg = $aReg[0];
        inserirLogDb('valor do campo it_codigo',"|".$aReg['it_codigo']."|",__FUNCTION__);
        inserirLogDb('valor do parametro item',"|$item|",__FUNCTION__);
        if($aReg['it_codigo'] == $item){
            $itemRelac = $aReg['it_codigo_02'];
            inserirLogDb('item passado o parametro igual ao conteudo do campo it_codigo',
                "sim - item relacto = $itemRelac",__FUNCTION__);
        }else{
            $itemRelac = $aReg['it_codigo'];
            inserirLogDb('item passado o parametro igual ao conteudo do campo it_codigo',
                "nao - item relacto = $itemRelac",__FUNCTION__);
        }
    }else{
        inserirLogDb('relação de itens encontrada','não',__FUNCTION__);
    }
    return $itemRelac;

}



function inserirRelacsItemRef($item,$ref,$tipoRelac,$idArquivo)
{
    $arqtransacaoCorrente = getArqTransacaoImg();
    $arrayInsert = array('relac_item_ref_id' => 'pub.seq_relac_item_ref.NEXTVAL',
        'it_codigo'=> $item,
        'cod_refer'=> $ref,
        'cod_tipo_relac' =>$tipoRelac,
        'relac_item_arquivo_id' => $idArquivo,
        'arq_transacao_img_id'=> $arqtransacaoCorrente);
    $cmd = convertArrayEmInsert('pub.relacs_item_ref',$arrayInsert,'1');
    sc_exec_sql($cmd,"especw");
}
function sincrRelacsItemRef($item,$ref,$tipo,$idArquivo,$acao)
{
    $idItemArquivo = getRelacItemRef($item,$ref,$tipo,$idArquivo);
    if($idItemArquivo == 0 and $acao == 1){
        inserirRelacsItemRef($item,$ref,$tipo,$idArquivo);
    }
    if($idItemArquivo <> 0 and $acao == 2 ){
        excluirRelacsItemRef($idItemArquivo);
    }
}
function getRelacItemRef($item,$ref,$tipo,$idArquivo)
{
    $idItemArquivo = 0;
    $aReg = retornoSimplesTb01(
        'pub.relacs_item_ref',
        'relac_item_arquivo_id',
        "(it_codigo = '$item' and cod_refer ='$ref' and cod_tipo_relac = '$tipo' 
                  and relac_item_arquivo_id = $idArquivo ) ",
        'espec'
    );
    if(is_array($aReg)){
        $idItemArquivo = $aReg[0]['relac_item_arquivo_id'];
    }
    return $idItemArquivo;
}
function getListaItensPorTipoDesign($tipoDesign)
{
    $lista = '';
    $aReg = getDados('multi',
    'pub."item-ext"',
    '"it-codigo" as it_codigo',
    " cod_tipo_item  = $tipoDesign");
    if(is_array($aReg)){
        foreach($aReg as $reg){
            $item = $reg['it_codigo'];
            $lista = util_incr_valor($lista,"'$item'");
        }
    }
    return $lista;
}

function getDadosItemExt($item,$campos){
    $aDados = getDados('multi','PUB."item-ext"',$campos,"\"it-codigo\" = $item",'espec');
    return $aDados;

}




?>
