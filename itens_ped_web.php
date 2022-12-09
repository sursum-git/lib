<?php
function desenharHtmlItemPedWeb($itemPedWebId,$codRef,$nrLote,$corteComerc,$qt,$vlUnit,$vlTotal)
{
    $descrRef           = getDescrRef($codRef);
    $descrCorteComerc   = getDescrCorteComerc($corteComerc);
    if(strtolower($nrLote) =='rd'){
        $classLote = "class='reprovado'";
    }else{
        $classLote = "class='aprovado'";
    }
    $aLinks     = array();
    $aLinks[]   = array('href'=>"../ctrl_manut_item_ped_web/ctrl_manut_item_ped_web.php?item_ped_web_id_corrente=$itemPedWebId&acao_corrente=alterar",'descricao'=>'Alterar');
    $aLinks[]   = array('href'=>"../ctrl_manut_item_ped_web/ctrl_manut_item_ped_web.php?item_ped_web_id_corrente=$itemPedWebId&acao_corrente=excluir",'descricao'=>'Excluir');
    $aLinks[]   = array('href'=>"#",'descricao'=>'Desenho');
    $links      = criarLinks($aLinks," | ",'links');
    $vlTotal    = formatarNumero($vlTotal,2);
    $retorno = <<<ITEM
<div class="container">    
	<i class="bi bi-basket2"></i> Referência: $codRef - $descrRef <br>
	<span $classLote><i class="bi bi-circle-fill"></i></span> <span class="lb" >Lote: $nrLote   </span>
	Corte Comercial: $corteComerc - $descrCorteComerc <br>
	<span class="lb">Qt.Metros: $qt - Vl.Unit: $vlUnit</span>
	<p><span class="vl"> <i class="bi bi-cash"></i> &nbsp;&nbsp;Vl.Tot.Item: $vlTotal</span></p>
	<p>$links</p>	
</div>
ITEM;
   return $retorno;

}
function excluirItemPedWeb($itemPedWebId)
{
    $cmd = " delete from itens_ped_web where id = $itemPedWebId";
    sc_exec_sql($cmd,"aux");

}
function excluirItensPedWeb($pedWebId)
{
    $cmd = " delete from itens_ped_web where ped_web_id = $pedWebId";
    sc_exec_sql($cmd,"aux");

}
function getCpsItemPedWeb()
{
    return 'id,ped_web_id,it_codigo,cod_refer,qt_pedida,dt_hr_criacao,vl_informado,nr_lote,corte_comerc';
}
function getRegItemPedWeb($itemPedWebId)
{
    return getReg('aux','itens_ped_web','id',$itemPedWebId,
        getCpsItemPedWeb()
    );
}
function verifExistItemPedWeb($itemPedWebId){
    return is_array(getRegItemPedWeb($itemPedWebId));
}
function verifExistItemPedWebPorChave($pedWebId,$itCodigo,$codRefer,$nrLote,$corteComercial)
{
    $aReg = getDados('unico','itens_ped_web',getCpsItemPedWeb(),
    " it_codigo       = '$itCodigo'           and 
              cod_refer       = '$codRefer'           and
              nr_lote         = '$nrLote'             and 
              corte_comerc    = '$corteComercial'     and
              ped_web_id      = $pedWebId
              ",
    'aux');

    return is_array($aReg);


}
function sincrItemPedweb($acaoCorrente,$pedWebId,$itemPedWebId,$itCodigo,$codRefer,$nrLote,$corteComercial,$qtPedida,$vlUnitario)
{
    $logAchou = false;
    $erro = '';

    switch(strtolower($acaoCorrente)){
        case 'incluir':
            $logAchou =   verifExistItemPedWebPorChave($pedWebId,$itCodigo,$codRefer,$nrLote,$corteComercial);
            if($logAchou){
                $erro     = "Já existe um registro para este pedido com os seguintes dados <br> 
            Item: $itCodigo - Referência: $codRefer  - Lote: $nrLote - Corte Comercial: $corteComercial   ";
            }else{
                inserirItemPedWeb($pedWebId,$itCodigo,$codRefer,$nrLote,$corteComercial,$qtPedida,$vlUnitario);
            }
            break;
        case 'alterar':
            $logAchou = verifExistItemPedWeb($itemPedWebId);
            if(!$logAchou){
                $erro = "Registro não encontrado com o id $itemPedWebId";
            }else{
                alterarItemPedWebId($itemPedWebId,$qtPedida,$vlUnitario);
            }

            break;
    }
    return $erro;
}

function alterarItemPedWebId($itemPedWebId, $qtPedida, $vlUnitario)
{
    $aUpdate = array('qt_pedida'=>$qtPedida,'vl_informado'=>$vlUnitario);
    $cmd = convertArrayEmUpdate('itens_ped_web',$aUpdate," id = $itemPedWebId",'',false);
    sc_exec_sql($cmd,"aux");
}

function inserirItemPedWeb($pedWebId, $itCodigo, $codRefer, $nrLote, $corteComercial, $qtPedida, $vlUnitario)
{
    $aInsert = array('ped_web_id'=>$pedWebId,
        'it_codigo'=>$itCodigo,
        'cod_refer'=>$codRefer,
        'nr_lote'=>$nrLote,
        'corte_comerc'=>$corteComercial,
        'qt_pedida'=>$qtPedida,
        'vl_informado'=>$vlUnitario,
        'dt_hr_criacao'=>'now()');
    $cmd = convertArrayEmInsert('itens_ped_web',$aInsert,'8',false);
    sc_exec_sql($cmd,"aux");
}
function validarQtPorCorteComercELote($corteComerc,$nrLote,$qt)
{
    $msgErro = '';
    $aCorte = getRegCorteComerc($corteComerc,
        '"compr-min" as compr_min,
        "compr-med" as compr_med,
        "tp-embalag" as tp_embalag ');
    if(is_array($aCorte)){
        $comprMin   = getVlIndiceArrayDireto($aCorte[0], 'compr_min',0);
        $comprMed   = getVlIndiceArrayDireto($aCorte[0], 'compr_med',0);
        $tpEmbalag  = getVlIndiceArrayDireto($aCorte[0], 'tp_embalag',0);
        //echo "<h2>qt.pedida: $qt - qt. Minima: $comprMin</h2>";
        if($qt < $comprMin){
            $msgErro = util_incr_valor($msgErro,"<h3>A Quantidade informada($qt), menor que a quantidade minima aceita 
para o corte $corteComerc($comprMin)<h3>",' ');

        }
        if(strtolower($nrLote) == 'rp'){
            if($tpEmbalag == '1' or $tpEmbalag == '2'){
                if($qt % $comprMed <> 0){
                    $msgErro = util_incr_valor($msgErro,"<h3>A Quantidade informada($qt), não é multipla da 
 quantidade do corte comercial $corteComerc(multiplo: $comprMed)<h3>",' ');

                }
            }

        }
    }
    return $msgErro;

}

function getItensPedWebSemValor($pedWebId)
{
    $aReg = getReg('aux','itens_ped_web','ped_web_id',
    " vl_informado = 0");
    return is_array($aReg);

}
function getItensPedWeb($pedWebId)
{
     $aReg = getDados('multi',
         'itens_ped_web',
         getCpsItemPedWeb(),
     " ped_web_id = $pedWebId  ",'aux');
     //id,ped_web_id,it_codigo,cod_refer,qt_pedida,dt_hr_criacao,vl_informado,nr_lote,corte_comerc
     return $aReg;

}

function getItensPedWebSemQt($pedWebId)
{
    $aReg = getReg('aux','itens_ped_web','ped_web_id',
        " qt_pedida = 0");
    return is_array($aReg);

}
function atuVlUnitItemPedWeb($itCodigo,$pedWebId,$vlUnit)
{
    $vlUnit = tratarNumero($vlUnit);
    $cmd = "update itens_ped_web set vl_informado = '$vlUnit'  
            where it_codigo = '$itCodigo' and ped_web_id = $pedWebId ";
    sc_exec_sql($cmd,"aux");
}
?>
