<?php
//__NM____NM__FUNCTION__NM__//
function getItemRefPedWeb($itCodigoParam,$codReferParam,$containerParam,$agrup)
{ 

    $itemPedWebId = 0;
    $logInformado = 0;
    $qtPedida     = 0;
    $vlInformado  = 0;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.itens_ped_web itens, pub.peds_web ped ";
    $campos   = " item_ped_web_id, log_informado,qt_pedida,vl_informado";
    $sitsEmAberto = getSitsPedWebEmAberto();
    $condicao = "  itens.ped_web_id = ped.ped_web_id
                    and ped.num_agrup = $agrup
                    and ped.login = '".getLoginCorrente()."' and ped.ind_sit_ped_web  in ($sitsEmAberto)
                    and ped.nr_container = $containerParam
                    and itens.it_codigo = '$itCodigoParam'
                    and itens.cod_refer = '$codReferParam'";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $itemPedWebId = $aDados[0]['item_ped_web_id'] ;
        $logInformado = $aDados[0]['log_informado'] ;
        $qtPedida     = $aDados[0]['qt_pedida'];
        $vlInformado  = $aDados[0]['vl_informado'];
    }
    $aRetorno = array('id' => $itemPedWebId, 'log_informado' => $logInformado,'qt_pedida' => $qtPedida,'vl_informado' => $vlInformado);
    return $aRetorno;
}
function getItemVlTbPedWeb($itCodigoParam,$vlTb,$containerParam,$agrupPed)
{

    $vlTb = str_replace(',','.', $vlTb);
    $itemPedWebId = 0;
    $logInformado = 0;
    $qtPedida     = 0;
    $vlInformado  = 0;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.itens_ped_web itens, pub.peds_web ped ";
    $campos   = " top 1 item_ped_web_id as item_ped_web_id, log_informado,qt_pedida,vl_informado";
    $sitsEmAberto = getSitsPedWebEmAberto();
    $condicao = "  itens.ped_web_id = ped.ped_web_id
                 and ped.login = '".getLoginCorrente()."' and ped.ind_sit_ped_web in($sitsEmAberto)
                 and ped.nr_container = $containerParam
                 and ped.num_agrup = $agrupPed
                 and itens.it_codigo = '$itCodigoParam'
                 and itens.vl_unit_final = '$vlTb'";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $itemPedWebId = $aDados[0]['item_ped_web_id'] ;
        $logInformado = $aDados[0]['log_informado'] ;
        $qtPedida     = $aDados[0]['qt_pedida'];
        $vlInformado  = $aDados[0]['vl_informado'];
    }
    $aRetorno = array('id' => $itemPedWebId, 'log_informado' => $logInformado,'qt_pedida' => $qtPedida,'vl_informado' => $vlInformado);
    return $aRetorno;
}

function validarQtAlocada($qtSaldo,$qtAlocada,$campoFoco='')
{
    $qtCarrinho = str_replace('.','',$qtAlocada)   ;
    $qtCarrinho = str_replace(',','',$qtCarrinho)   ;

    $qtDisp     = str_replace('.','',$qtSaldo)  ;
    $qtDisp     = str_replace(',','',$qtDisp)  ;

    if($qtDisp <> 0){
        $qtDisp     = $qtDisp / 100;
    }else{
        $qtDisp = 0;
    }
    if($qtCarrinho <> 0){
        $qtCarrinho = $qtCarrinho / 100;
    }else{
        $qtCarrinho = 0;
    }

    $qtDispFormat     	  =  formatarNumero($qtDisp);
    $qtCarrinhoFormat     =  formatarNumero($qtCarrinho);
//sc_error_message("disp: $qtDisp - carrinho : $qtCarrinho" );


    if($qtCarrinho >  $qtDisp){
        if($campoFoco <> ''){
            sc_set_focus($campoFoco);
        }
        sc_ajax_javascript('setAtu', array('d'));
        sc_error_message("A Quantidade  Do Carrinho($qtCarrinhoFormat) não pode ser maior que a
         quantidade de Em Estoque/Programada($qtDispFormat)");
        $erro = true;
    }else{
        sc_ajax_javascript('setAtu', array('a'));
        $erro = false;
    }
    return $erro;

}

function sincrWpItensPed($wpParam)
{
    $aWp = getRegsEstPrecoPorWp($wpParam);
    //var_dump($aWp);
    inserirLogDb('array de registros wp',$aWp,__FUNCTION__);
    $moeda = '';
    if(is_array($aWp)){
        $tam = count($aWp);
        inserirLogDb('tamanho do array:',$tam,__FUNCTION__);
        //echo "<h1>array itens sincrWpItensPed</h1>";
        for($i=0;$i<$tam;$i++){
            $itCodigo           = $aWp[$i]['it_codigo'];
            $codRefer           = $aWp[$i]['cod_refer'];
            $nrContainer        = $aWp[$i]['nr_container'];
           //echo "<h1>it-codigo:$itCodigo - ref: $codRefer sincrWpItensPed container:$nrContainer</h1>";
            $codEstabel         = $aWp[$i]['cod_estabel'];
            $qtCarrinho         = $aWp[$i]['qt_carrinho'];
            $vlInformado        = $aWp[$i]['vl_informado'];
            //echo "<h1>valor informado:$vlInformado</h1>";
            $liquidaIma         = $aWp[$i]['liquida_ima'];
            $precoLiquidaIma    = $aWp[$i]['preco_liquida_ima'];
            $moeda              = $aWp[$i]['num_moeda'];
            $codControlePreco   = $aWp[$i]['cod_controle_preco'];
            $numIdLiquidaIma    = $aWp[$i]['num_id_liquida_ima'];
            $qtSaldoVenda       = $aWp[$i]['qt_saldo_venda'];
            $qtProgramada       = $aWp[$i]['qt_programada'];
            $tbPrecoId          = $aWp[$i]['tb_preco_id'];
            $agrupPed           = $aWp[$i]['agrup_pedido'];
            $logDivideComis     = $aWp[$i]['log_divide_comis'];
            $percRepres         = $aWp[$i]['perc_comis_repres'];
            $percVend           = $aWp[$i]['perc_comis_vend'];

            if($moeda == 1){ //real
                $vlPrecoBase = $aWp[$i]['vl_real'];
            }else{
                $vlPrecoBase = $aWp[$i]['vl_dolar'];
            }

            //$vlPreco90          = getPrecoPrazoInd(90,$vlPrecoBase);

            if($nrContainer <> 0){
                $qtSaldo = $qtProgramada;
            }else{
                $qtSaldo = $qtSaldoVenda;
            }
            //echo "<h1>Item:$itCodigo - ref:$codRefer -  Qt.carrinho:$qtCarrinho - qt.Saldo: $qtSaldo</h1>";
            if($qtCarrinho > $qtSaldo){
                $qtCarrinho = $qtSaldo;
            }
            /*if($liquidaIma <> '' and $liquidaIma <> 0 and $liquidaIma <> 1){
                $vlFinal     = $vlPreco90 * (1 - $liquidaIma / 100);
            }else{
                $vlFinal    = $vlPreco90;
            }*/
            if($precoLiquidaIma <> '' and $precoLiquidaIma <> 0 ){
                $vlFinal     = $precoLiquidaIma;
            }else{
                $vlFinal    = $vlPrecoBase;
            }


            //
            //
            inserirLogDb('item-ref-vl.final-precoliquidaima',
                "$itCodigo - $codRefer - $vlFinal -  $precoLiquidaIma",
                __FUNCTION__);
            //echo "<h1> antes sincrItemRefPedWeb ->  vl.Final($vlFinal) -   vlPreco90($vlPreco90)  - liquida ima($liquidaIma) </h1>";
            sincrItemRefPedWeb($codEstabel, $moeda,$itCodigo,$codRefer,$nrContainer,
                $qtCarrinho,$vlPrecoBase,$vlFinal,
                $codControlePreco,$numIdLiquidaIma,$liquidaIma,$qtSaldo,$precoLiquidaIma,
                $vlInformado,$logDivideComis,$percVend,$percRepres,$agrupPed,$tbPrecoId);
        }
    }
}


function getRegItemRefPedWeb($id,$campos='',$filtroCompl='')
{
    $aRetorno = getReg('espec',
        'itens_ped_web',
        'item_ped_web_id',
        $id, $campos ,$filtroCompl);
    return $aRetorno;

}

function getDadosPrecoSaldoAtuItemPedWeb($pedWebId, $itCodigo, $codRefer, $qtPedida)
{
    $aPed 	= getRegPedWeb($pedWebId,
        'log_a_vista,dias_cond_pagto_esp,nr_container,cod_moeda,tb_preco_id,
        num_agrup,log_divide_comissao,perc_comis_vend,perc_comis_repres');
    $vlInformadoPed = 0;
    $logAchou 		= 0;
    $msg            = '';
    $logDivideComissao = 0;
    $percComisVend     = 0;
    $percComisRepres   = 0;
    if(is_array($aPed)){
        inserirLogDb('array pedido',$aPed,__FUNCTION__);
        //echo "<h1>entrei no pedido</h1>";
        $logAVista				= $aPed[0]['log_a_vista'];
        $diasCondPagtoEsp 		= $aPed[0]['dias_cond_pagto_esp'];
        //echo "<h1>dias cond.pagto:$diasCondPagtoEsp</h1>";
        $nrContainer			= $aPed[0]['nr_container'];
        $moeda					= $aPed[0]['cod_moeda'];
        $tbPrecoId				= $aPed[0]['tb_preco_id'];
        $numAgrup               = $aPed[0]['num_agrup'];
        $logDivideComissaoPed   = $aPed[0]['log_divide_comissao'];
        $percComisVendPed       = $aPed[0]['perc_comis_vend'];
        $percComisRepresPed     = $aPed[0]['perc_comis_repres'];



        //echo "vl.Informado: $vlInformadoPed";
        if($logAVista == 0 and $diasCondPagtoEsp == ''){
            $msg1 = 'A Condição de Pagamento do pedido ainda não foi definida';
            $msg    = util_incr_valor($msg,$msg1,"</br");
        }
        $aSaldoPreco = getPrecoSaldoItemRefCondPagtoContainer($itCodigo,$codRefer,$diasCondPagtoEsp,$nrContainer,$tbPrecoId);
        inserirLogDb('item -ref - diascondpagto -nrcontainer - tbpreco',"$itCodigo - $codRefer - $diasCondPagtoEsp - $nrContainer - $tbPrecoId",__FUNCTION__);
        //var_dump($aSaldoPreco);
        if(is_array($aSaldoPreco)){
            $logDivideComissao = $aSaldoPreco['log_divide_comis'];
            $logDivideComissao = tratarNumero($logDivideComissao);
            $percComisVend     = $aSaldoPreco['perc_comis_vend'];
            $percComisVend     = tratarNumero($percComisVend);
            $percComisRepres   = $aSaldoPreco['perc_comis_repres'];
            $percComisRepres   = tratarNumero($percComisRepres);
            inserirLogDb('moeda - container - tb.preco',"$moeda - $nrContainer -  $tbPrecoId ",__FUNCTION__);
            $numAgrupPreco = getAgrupPedido($moeda,$nrContainer,$tbPrecoId,$logDivideComissao,$percComisVend,$percComisRepres);
            inserirLogDb('Agrup Ped  - Agrup Item',"$numAgrup - $numAgrupPreco",__FUNCTION__);
            if($numAgrup <> 0 ){ //se o agrupamento for diferente de zero quer dizer que não é um pedido manual sem itens

                //verifica se existe preço informado para o item corrente no pedido
                $aItemPed = getPriItemPedWeb($itCodigo,$nrContainer,$numAgrup);
                $idItem = $aItemPed['id'];
                //echo "container:$nrContainer<br>";
                //var_dump($aItemPed);
                if($idItem <> 0){
                    $vlInformadoPed = $aItemPed['vl_informado'];
                }else{
                    $vlInformadoPed = 0;
                }

                if($numAgrup <> $numAgrupPreco ){
                    $msg1 = "As Condições do Preço atual deste Item/Referência não são compatíveis com as do pedido de venda. 
                    É necessário criar outro pedido ou encontrar um pedido em digitação que atenda as condições do preço.</br>
                    Diferenças Encontradas:";
                    $msg2 = getMsgAnaliseDif('Divisão de Comissão','Pedido:','Preço Item/Ref:',
                        getVlLogico($logDivideComissaoPed) ,getVlLogico($logDivideComissao));
                    $msg1 = util_incr_valor($msg1,$msg2,"<br>",true);
                    $msg2 = getMsgAnaliseDif('% Comis. Vendedor','Pedido:','Preço Item/Ref:',
                        formatarNumero($percComisVendPed),
                        formatarNumero($percComisVend) );
                    $msg1 = util_incr_valor($msg1,$msg2,"<br>",true);
                    $msg2 = getMsgAnaliseDif('% Comis. Repres.','Pedido:','Preço Item/Ref:',
                        formatarNumero($percComisRepresPed) ,
                        formatarNumero($percComisRepres));
                    $msg1 = util_incr_valor($msg1,$msg2,"<br>",true);
                    $msg = util_incr_valor($msg,$msg1,"</br>",true);
                }
            }

            //echo "<h1>achei o preco do item ref</h1>";

            if(isset($aSaldoPreco['qt_saldo'])){
                $qtSaldo = $aSaldoPreco['qt_saldo'];
            }else{
                $qtSaldo = 0;
            }

            if($moeda  <> 1 and $moeda <> 'real'){ // <> de real
                $cp = 'vl_unit_dolar';
            }else{
                $cp = 'vl_unit_tabela';
            }


            if(isset($aSaldoPreco[$cp])){
                $precoTb = round($aSaldoPreco[$cp],2);
            }else{
                $precoTb = 0;
            }
            if(isset($aSaldoPreco['preco_liquida_ima'])){
                $precoOutlet = round($aSaldoPreco['preco_liquida_ima'],2);
            }else{
                $precoOutlet = 0;
            }
            if(isset($aSaldoPreco['vl_unit_final'])){
                $vlInformado = round($aSaldoPreco['vl_unit_final'],2);
            }else{
                $vlInformado = 0;
            }

            if(isset($aSaldoPreco['cod_controle_preco'])){
                $codControlePreco = $aSaldoPreco['cod_controle_preco'];
            }else{
                $codControlePreco = 0;
            }

            if(isset($aSaldoPreco['num_id_liquida_ima'])){
                $numLiquidaIma	= $aSaldoPreco['num_id_liquida_ima'];
            }else{
                $numLiquidaIma = 0;
            }


            if(isset($aSaldoPreco['vl_unit_final'])){
                $vlUnitFinal	= round($aSaldoPreco['vl_unit_final'],2);
            }else{
                $vlUnitFinal	= 0;
            }
            $logAchou = 1;
            //var_dump($aSaldoPreco);
        }
        else{
            //echo "<h1>NAO achei o preco do item ref</h1>";
            $qtSaldo            = 0;
            $precoTb            = 0;
            $precoOutlet        = 0;
            $vlInformado        = 0;
            $vlUnitFinal        = 0;
            $codControlePreco   = 0;
            $numLiquidaIma	    = 0;
            $msg1               = 'Item/Referencia sem Saldo';
            $msg                = util_incr_valor($msg,$msg1,"</br");
            $logDivideComissao = 0;
            $percComisVend     = 0;
            $percComisRepres   = 0;


        }
        if($qtSaldo < 0){
            $qtSaldo = 0;
        }

    }else{
        $qtSaldo            = 0;
        $precoTb            = 0;
        $precoOutlet        = 0;
        $vlInformado        = 0;
        $moeda		        = 1;
        $nrContainer        = 0;
        $diasCondPagtoEsp   = '0';
        $msg1               = 'Dados do Pedido Inconsistentes';
        $msg                = util_incr_valor($msg,$msg1,"</br");

    }
    //echo "<br>valor inf.ped:$vlInformadoPed";
    /*if($vlInformadoPed > 0 ){
        $vlInformado = $vlInformadoPed;
    }*/
    //echo "<h1>qt.saldo: $qtSaldo</h1>";
    $vlTotal	   = $qtPedida * $vlInformado;


    /*echo "<h1>campo qt.saldo: {qt_saldo} - preco tb.: {preco_tb} - preco outlet: {preco_outlet} </h1>";*/
    //echo "<h1>cheguei até aqui</h1>";


    /**/

    if($qtSaldo < 0 or $qtSaldo == ''){
        //echo "entrei na condicao do qtsaldo<br>";
        $logAchou = 0;
    }

    //echo "qtsaldo : $qtSaldo<br>";

    if($logAchou == 0 ){
        $msg1   = "Item/Referência sem quantidade Disponivel.";
        $msg    = util_incr_valor($msg,$msg1,"</br");
    }
    if($precoTb == 0){
        $msg1 = "Não existe Preço válido cadastrado para o item nos parametros(origem, moeda, tb.preço) informados no pedido ";
        $msg    = util_incr_valor($msg,$msg1,"</br");
    }
    $aRet = array('moeda'       => $moeda,
        'nr_container'          => $nrContainer,
        'vl_unit_final'         => $vlUnitFinal,
        'cod_controle_preco'    => $codControlePreco,
        'num_liquida_ima'       => $numLiquidaIma,
        'qt_saldo'              => $qtSaldo,
        'preco_tb'              => $precoTb,
        'preco_outlet'          => $precoOutlet,
        'dias_cond_pagto_esp'   => $diasCondPagtoEsp,
        'erro'                  => $msg,
        'log_achou'             => $logAchou,
        'tb_preco_id'           => $tbPrecoId,
        'vl_informado'          => $vlInformado,
        'vl_informado_agrup'    => $vlInformadoPed,
        'vl_total'              => $vlTotal,
        'log_divide_comissao'   => $logDivideComissao,
        'perc_comis_vend'       => $percComisVend,
        'perc_comis_repres'     => $percComisRepres,
        'num_agrup_preco'       => $numAgrupPreco,
        'num_agrup_ped'         => $numAgrup);

    return $aRet;
}




function getPriItemPedWeb($itCodigoParam,$containerParam,$agrupPed)
{
    $itemPedWebId = 0;
    $logInformado = 0;
    $qtPedida     = 0;
    $vlInformado  = 0;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.itens_ped_web itens, pub.peds_web ped ";
    $campos   = " top 1 cod_refer, item_ped_web_id, log_informado,qt_pedida,vl_informado";
    $sitsEmAberto = getSitsPedWebEmAberto();
    $condicao = "  itens.ped_web_id = ped.ped_web_id
                 and ped.num_agrup = $agrupPed
                 and ped.login = '".getLoginCorrente()."' and ped.ind_sit_ped_web in($sitsEmAberto)
                 and ped.nr_container = $containerParam
                 and itens.it_codigo = '$itCodigoParam'";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $itemPedWebId = $aDados[0]['item_ped_web_id'] ;
        $logInformado = $aDados[0]['log_informado'] ;
        $qtPedida     = $aDados[0]['qt_pedida'];
        $vlInformado  = $aDados[0]['vl_informado'];
    }

    $aRetorno = array('id' => $itemPedWebId, 'log_informado' => $logInformado,'qt_pedida' => $qtPedida,'vl_informado' => $vlInformado);
    inserirLogDb('array retorno',$aRetorno,__FUNCTION__);
    return $aRetorno;
}



function criarItemRefPedWeb($codEstabel,$moeda,$itCodigoParam,$codReferParam,$containerParam,
                            $qtPedida,$vlUnitTabela,$vlUnitFinal,
                            $codControlePreco,$numIdLiquidaIma,
                            $liquidaIma,$qtSaldo,$precoLiquidaIma,
                            $vlInformado=0,$logDivideComis,$percVend,$percRepres,$agrupPed,$tbPrecoId)
{
    if($vlInformado <> 0){
        $logInformado = 1;
    }else{
        $logInformado = 0;
    }
    inserirLogDb('item-ref',"$itCodigoParam - $codReferParam",__FUNCTION__);
    //echo "<h1>it-codigo:$itCodigoParam - cod.refer: $codReferParam -  sincrWpItensPed - sincrItemRefPedWeb - criarItemRefPedWeb</h1>";
    $pedWebId = sincrPedWeb(getLoginCorrente(),$containerParam,$moeda,$codEstabel,  $logDivideComis,$percVend  ,$percRepres,$agrupPed,$tbPrecoId);
    //pegar cond.pagto
    $aReg = getRegPedWeb($pedWebId,'dias_cond_pagto_esp');
    $diasCondPagto = $aReg[0]['dias_cond_pagto_esp'];
    $vlUnitTabela = getPrecoPrazoInd($diasCondPagto,$vlUnitTabela);

    //$pedWebId = 0;
    $liquidaIma = tratarNumero($liquidaIma);
    if($liquidaIma == 0){
        $vlUnitFinal = getPrecoPrazoInd($diasCondPagto,$vlUnitFinal);
    }
    $codControlePreco = tratarNumero($codControlePreco);
    $precoLiquidaIma  = tratarNumero($precoLiquidaIma);
    $cmd = "insert into pub.itens_ped_web(
            item_ped_web_id,
            ped_web_id,
            it_codigo,
            cod_refer,
            qt_pedida,
            vl_unit_tabela,
            vl_unit_final,
			vl_informado,
            cod_controle_preco,
            num_id_liquida_ima,
            liquida_ima,
            dt_hr_criacao,
            qt_saldo,
            preco_liquida_ima,
            log_informado)
            values(
            pub.seq_item_ped_web.NEXTVAL,
            $pedWebId,
            '$itCodigoParam',
            '$codReferParam',
            $qtPedida,
            '$vlUnitTabela',
            '$vlUnitFinal',
			'$vlInformado',
            '$codControlePreco',
            '$numIdLiquidaIma',
             $liquidaIma,
            sysdate(),
            '$qtSaldo',
            '$precoLiquidaIma',
            $logInformado)";
    inserirLogDb('comando de criação do registro no bd',$cmd,__FUNCTION__);
    $conEspec =conectarBase('especw');
    $result = execAcaoPDO($cmd,$conEspec,'cmd');
   if($result['erro']<> ''){
       echo "<h1>".$result['erro']."</h1>";
       inserirLogDb('erro comando',$result['erro'],__FUNCTION__);
   }
    //sc_exec_sql($cmd,"especw");
    $id = buscarVlSequenciaEspec('seq_item_ped_web','itens_ped_web');
    inserirLogDb('ID registro criado',$id,__FUNCTION__);
    return $id;

}

function atualizarItemRefPedWeb($logInformado,$itemPedWebId,
                                $qtPedida,$vlUnitTabela,$vlUnitFinal,
                                $codControlePreco,$numIdLiquidaIma,$liquidaIma,$qtSaldo,$vlInformado,
                                $logAtuVlInf,$precoLiquidaIma)
{


    //echo "<h1> valor tabela: $vlUnitTabela</h1>";
    //echo "<h1>atualizar vl.inf:$logAtuVlInf - vl.informado: $vlInformado</h1>";
    $aRegItemPedWeb = getRegItemRefPedWeb($itemPedWebId,'ped_web_id');
    $pedWebId = $aRegItemPedWeb[0]['ped_web_id'];
    $aReg = getRegPedWeb($pedWebId,'dias_cond_pagto_esp');
    $diasCondPagto = $aReg[0]['dias_cond_pagto_esp'];

    //obs $vlUnitTabela = getPrecoPrazoInd($diasCondPagto,$vlUnitTabela);


    if($logInformado == 1){
        inserirLogDb('Preço Informado?','SIM',__FUNCTION__);
        if($logAtuVlInf == true){
            inserirLogDb('Atualiza Preço Informado?','SIM',__FUNCTION__);
            $cmdVlInf = ",vl_informado = '$vlInformado', log_informado = 1";
        }else{
            inserirLogDb('Atualiza Preço Informado?','NAO',__FUNCTION__);
            $cmdVlInf = '';
        }
    }else{
        inserirLogDb('Preço Informado?','NAO',__FUNCTION__);
        if($vlInformado == 0){
            $cmdVlInf = ",vl_informado = '$vlUnitFinal'";
        }else{
            $cmdVlInf =",vl_informado = '$vlInformado' ";
        }

    }

    $codControlePreco   = tratarNumero($codControlePreco);
    $qtSaldo            = tratarNumero($qtSaldo);
    $liquidaIma         = tratarNumero($liquidaIma);
    $precoLiquidaIma    = tratarNumero($precoLiquidaIma);

    /*obs if($liquidaIma == 0){
        $vlUnitFinal= getPrecoPrazoInd($diasCondPagto,$vlUnitFinal);
    }*/


    $update = " qt_pedida = $qtPedida, vl_unit_tabela = $vlUnitTabela,
            vl_unit_final = $vlUnitFinal, cod_controle_preco = $codControlePreco,
            num_id_liquida_ima = '$numIdLiquidaIma', liquida_ima=$liquidaIma, preco_liquida_ima = '$precoLiquidaIma',
            qt_saldo = '$qtSaldo', dt_hr_atualiz = sysdate() $cmdVlInf";

    $cmd = "update pub.itens_ped_web set $update where item_ped_web_id = $itemPedWebId ";
    inserirLogDb('comando atualização',$cmd,__FUNCTION__);
    sc_exec_sql($cmd,"especw");

}

function sincrItemRefPedWeb($codEstabel,$moeda,$itCodigoParam,$codReferParam,$containerParam,
                            $qtPedida,$vlUnitTabela,$vlUnitFinal,
                            $codControlePreco,$numIdLiquidaIma,
                            $liquidaIma,$qtSaldo,$precoLiquidaIma,$vlInformado=0,
                            $logDivideComis,$percVend,$percRepres,$agrupPed,$tbPrecoId)
{
    //echo "<h1>it-codigo:$itCodigoParam -Moed: $moeda - cod.refer: $codReferParam -  sincrWpItensPed - sincrItemRefPedWeb </h1>";
    $aItem = getItemRefPedWeb($itCodigoParam,$codReferParam,$containerParam,$agrupPed);
    inserirLogDb('Array retorno - itens de pedidos web em digitação  ',
        $aItem,__FUNCTION__);
    $itemPedWebId = $aItem['id'];
    $logInformado = $aItem['log_informado'];
    //echo "<h1>ItemPedwebid:$itemPedWebId - qtPedida: $qtPedida</h1>";
    if($itemPedWebId == 0){
        inserirLogDb('Item do Pedido de venda  web encontrado?','NAO',__FUNCTION__);
        //echo "<h1>entrei na condicao de zero</h1>";
        if($qtPedida > 0){
            inserirLogDb('Quantidade Pedida Maior que zero',$qtPedida,__FUNCTION__);
            //echo "<h1>quantidade maior que zero</h1>";
            $itemPedWebId =  criarItemRefPedWeb($codEstabel,$moeda,$itCodigoParam,$codReferParam,$containerParam,
                $qtPedida,$vlUnitTabela,$vlUnitFinal,$codControlePreco,$numIdLiquidaIma,$liquidaIma,
                $qtSaldo,$precoLiquidaIma,$vlInformado,$logDivideComis,$percVend,$percRepres,
                $agrupPed,$tbPrecoId);
        }else{
            inserirLogDb('Quantidade Pedida IGUAL que zero',$qtPedida,__FUNCTION__);
        }

    }else{
        inserirLogDb('Item do Pedido de venda web encontrado?','SIM',__FUNCTION__);
        if($qtPedida == 0){
            inserirLogDb('Quantidade Pedida IGUAL a zero',$qtPedida,__FUNCTION__);
            //echo "<h1>apagar qtPedida</h1>";
            //apagarItemRefPedWeb($itemPedWebId);
        }else{
            inserirLogDb('Quantidade Pedida Maior que zero - vou atualizar o item do pedido',$qtPedida,__FUNCTION__);
            //echo "<h1>quantidade maior que zero</h1>";
            atualizarItemRefPedWeb($logInformado,$itemPedWebId,$qtPedida,$vlUnitTabela,
                $vlUnitFinal,$codControlePreco,$numIdLiquidaIma,$liquidaIma,$qtSaldo,
                $vlInformado,true,$precoLiquidaIma);
        }
    }
    inserirLogDb('Item Ped Web ID',$itemPedWebId,__FUNCTION__);
    return $itemPedWebId;
}

function apagarItemRefPedWeb($itemPedWebId)
{
    $cmd = " delete from pub.itens_ped_web where item_ped_web_id = $itemPedWebId";
    sc_exec_sql($cmd,"especw");
}

function getItensRefPedWeb($pedWebId)
{
    $tipo     = "multi"; // unico ou multi
    $tabela   = " pub.itens_ped_web ";
    $campos   = " it_codigo,cod_refer,qt_pedida,vl_informado,log_informado,
	cod_controle_preco,num_id_liquida_ima,liquida_ima,vl_unit_tabela,vl_unit_final,item_ped_web_id";
    $condicao = "  ped_web_id = $pedWebId ";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    return $aDados;
}

function getItensRefPedWebCompleto($pedWebId,$item='')
{

    /*$aDados = '';
    $lAchou = 0;*/
    $tipo     = "multi"; // unico ou multi
    $tabela   = " espec.pub.itens_ped_web itens_ped_web ";
    $campos   = " it_codigo,cod_refer,qt_pedida,vl_informado,log_informado,
	cod_controle_preco,num_id_liquida_ima,liquida_ima,vl_unit_tabela,vl_unit_final,item_ped_web_id,
	item.\"desc-item\" as desc_item, item.un as un,peds_web.nr_container as nr_container, 
	peds_web.dias_cond_pagto_esp as dias_cond_pagto_esp,qt_saldo ";
    $condicao = "  peds_web.ped_web_id = $pedWebId ";
    if($item <> ''){
        $condicao = util_incr_valor($condicao," it_codigo = '$item' "," AND ");
    }
    $conexao  = "multi";
    $join = ' inner join med.pub.item item on itens_ped_web.it_codigo = item."it-codigo" 
              inner join espec.pub.peds_web peds_web on peds_web.ped_web_id = itens_ped_web.ped_web_id ';
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao,$join,1);
    /*if(is_array($aDados)){
        $tam = count($aDados);
        for($i=0;$i< $tam; $i++){
            $item = $aDados[$i]['it_codigo'];
            $ref  = $aDados[$i]['cod_refer'];
            $diasCondPagtoEsp = $aDados[$i]['dias_cond_pagto_esp'];
            $containerId = $aDados[$i]['nr_container'];
            $aPrecoSaldo = getPrecoSaldoItemRefCondPagtoContainer($item,$ref,$diasCondPagtoEsp,$containerId);
            $aDados[$i]['qt_saldo'] =

        }
    }*/
    return $aDados;
}
function getListaIdItensPedWeb($pedWebId,$item,$qtPedida)
{
    $listaId = '';
    $aRet = getItensRefPedWebCompleto($pedWebId,$item);
    foreach($aRet as $reg){
        $id = $reg['item_ped_web_id'];
        $listaId = util_incr_valor($listaId,$id);
    }
    $listaId = tratarNumero($listaId);
    return $listaId;
}
function atuQtItensPedWeb($pedWebId,$item,$qtPedida)
{
    $lista = getListaIdItensPedWeb($pedWebId,$item,$qtPedida);
    $array = array('qt_pedida'=>$qtPedida);
    $cmd = convertArrayEmUpdate('itens_ped_web',$array,"item_ped_web_id in($lista)");
    sc_exec_sql($cmd,"especw");


}

function getQtItensRefPedWeb($pedWebId)
{
    $qt = 0;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.itens_ped_web ";
    $campos   = "  count(item_ped_web_id) as qt";
    $condicao = "  ped_web_id = $pedWebId ";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $qt = $aDados[0]['qt'];
    }
    $qt =tratarNumero($qt);
    return $qt;
}



function valorizarItensRefPedWeb($pedWebId,$container,$diasCondPagto,$tbPreco=1,$moeda='real')
{
    $aDados                 = getItensRefPedWeb($pedWebId);
    $qtMetro                = 0;
    $qtKg                   = 0;
    $qtSemValor             = 0;
    $totGeral               = 0;
    $lMetaComis             = 1;
    $logPrecoInformado      = false;
    $prazoMedio             = 90;
    $logOutlet              = 0;
    $logAbaixoLiquidaIma    = 0;
    $logAbaixoTb            = false;

    inserirLogDb('array retorno getItensRefPedWeb',$aDados,__FUNCTION__);
    if(is_array($aDados)){
        $tam = count($aDados);
        for($i=0;$i<$tam;$i++){
            $item           = $aDados[$i]['it_codigo'];
            $ref            = $aDados[$i]['cod_refer'];
            $qtPedida       = $aDados[$i]['qt_pedida'];
            $vlUnitFinal    = $aDados[$i]['vl_unit_final'];
            $vlUnitTabela   = $aDados[$i]['vl_unit_tabela'];
            $logPrecoInformado   = $aDados[$i]['log_informado'];
            $vlInformado         = $aDados[$i]['vl_informado'];

            //verifica se existe algum preço informado
            if($logPrecoInformado == false){
                if($logPrecoInformado == true and $vlInformado < $vlUnitFinal){
                    $logPrecoInformado = true;
                    inserirLogDb('Valor informado?','sim',__FUNCTION__);
                }else{
                    inserirLogDb('Valor informado?','nao',__FUNCTION__);
                }
            }
            $aItem = buscarDadosItem($item);
            inserirLogDb("array dados item $item",$aItem,__FUNCTION__);
            if(is_array($aItem)){
                $unidade = strtoupper($aItem[0]['un']) ;
            }else{
                $unidade = '' ;
            }

            switch($unidade){
                case 'M':
                    $qtMetro += $qtPedida;
                    break;
                case 'KG':
                    $qtKg    += $qtPedida;
                    break;
            }
            //echo"<h1>Passei aqui</h1>";
            $prazoMedio = getPrazoMedioInf($diasCondPagto);
            inserirLogDb('Prazo médio - dias cond.pagto',
                "$prazoMedio - $diasCondPagto",__FUNCTION__);
            if($prazoMedio > 90){
                inserirLogDb('Prazo médio maior de 90?','sim',__FUNCTION__);
                $lMetaComis = 0;
            }else{
                inserirLogDb('Prazo médio maior de 90?','nao',__FUNCTION__);
            }
            //echo "<h1>prazo medio: $prazoMedio</h1>";
            inserirLogDb('container',$container,__FUNCTION__);
            if($container == 0){
                //echo "<h2>Tabela: $tbPreco</h2>";
                $aPreco = buscarPreco('5',4,$item,$ref,$container,true,$tbPreco);
            }else{
                $aPreco = buscarPreco('5',2,$item,$ref,$container, false,$tbPreco);
            }
            inserirLogDb('array Preco',$aPreco,__FUNCTION__);
            /*echo "<h1>array preco</h1>";
            var_dump($aPreco);*/
            $precoLiquidaIma = round($aPreco[0]['preco_liquida_ima'],2);
            inserirLogDb('Preço Outlet',$precoLiquidaIma,__FUNCTION__);
            if($moeda == 1 or $moeda == 'real'){
                $precoBase = $aPreco[0]['vl_real'];
            }else{
                $precoBase = $aPreco[0]['vl_dolar'];
            }
            $preco90    = round(getPrecoPrazoInd(90,$precoBase),2);
            inserirLogDb('Preco90 - PrecoBase - Moeda',"$preco90 - $precoBase - $moeda",__FUNCTION__);
            inserirLogDb('vl informado - vl tabela ',"$vlInformado - $vlUnitTabela",__FUNCTION__);


            if($precoLiquidaIma > 0){
                $fator = $precoLiquidaIma / $preco90;
                //$lMetaComis = 0;
                if($logOutlet <> 1){
                    $logOutlet = 1;
                }
            }else{
                $fator = 1;
            }

            inserirLogDb('Fator outlet',$fator,__FUNCTION__);
            $vlUnit = round(getPrecoPrazoInd($prazoMedio,$precoBase) * $fator,2);

            if($vlInformado < $vlUnit){
                inserirLogDb('Valor informado menor que valor unit. final','sim - meta de comissão não alcançada',__FUNCTION__);
                $lMetaComis = 0;
                $logAbaixoTb = true;

            }else{
                inserirLogDb('Valor informado menor que valor unit. final','nao',__FUNCTION__);
            }

            inserirLogDb('Valor Informado - valor unit tab.',"$vlInformado - $vlUnit",__FUNCTION__);
            /*if($vlInformado < $vlUnit){
                inserirLogDb('Valor informado menor que valor unit. final','sim - meta de comissão não alcançada',__FUNCTION__);
                $lMetaComis = 0;
            }else{
                inserirLogDb('Valor informado menor que valor unit. final','nao',__FUNCTION__);
            }*/

            inserirLogDb('vlunit - prazomedio - precobase - fator',
                "$vlUnit - $prazoMedio - $precoBase - $fator",__FUNCTION__);

            if($vlUnit <> $vlInformado and $vlInformado <> 0){
                $vlUnit = $vlInformado;
                inserirLogDb('Valor unitário assume o valor informado?','sim',__FUNCTION__);
            }else{
                inserirLogDb('Valor unitário assume o valor informado?','nao',__FUNCTION__);
            }

            if($precoLiquidaIma > $vlUnit and $logAbaixoLiquidaIma <> 1){
                $logAbaixoLiquidaIma = 1;
            }else{
                $logAbaixoLiquidaIma = 0;
            }
            inserirLogDb('Preço Abaixo outlet?',$logAbaixoLiquidaIma,__FUNCTION__);
            //echo "<h1>valor unitário antes da comparação com valor informado ($vlUnit)</h1>";

            $totItem = round($vlUnit * $qtPedida,2);

            if($totItem == 0){
                $qtSemValor++;
            }
            $totGeral +=$totItem;
            inserirLogDb('Total Item',$totItem,__FUNCTION__);
            inserirLogDb('Total Geral',$totGeral,__FUNCTION__);
        }
    }
    $vlMin = getVlMinFreteCIF($moeda);
    if($totGeral < $vlMin){
        $lMetaComis = 0;
        inserirLogDb("Total Geral menor que vl min-> $vlMin?",'sim',__FUNCTION__);
    }else{
        inserirLogDb("Total Geral menor que vl min-> $vlMin?",'nao',__FUNCTION__);
    }
    $aRetorno = array('total'=> round($totGeral,2),
        'qt_sem_preco' => $qtSemValor,
        'qt_metro' => $qtMetro,
        'qt_kg' => $qtKg,
        'log_informado' => $logPrecoInformado,
        'prazo_medio' => $prazoMedio,
        'log_meta_comis' => $lMetaComis,
        'log_abaixo_outlet'=> $logAbaixoLiquidaIma,
        'log_outlet'=>$logOutlet,
        'log_abaixo_tb'=>$logAbaixoTb);
    //var_dump($aRetorno);
    return $aRetorno;
}



function atualizarTotsPedidoCabec($vlPedido,$vlItemAnt,$vlItemPos,$qtMetros,$qtKg,$qtAnt,$qtPos,$un)
{
    $log = '';
    $un = strtoupper($un);
    //tratar formato dos itens
    $vlItemAnt = desformatarValor($vlItemAnt);
    $vlItemPos = desformatarValor($vlItemPos);
    $qtAnt     = desformatarValor($qtAnt);
    $qtPos     = desformatarValor($qtPos);

    switch($un){
        case 'M':
            $qtMetros = $qtMetros - $qtAnt + $qtPos;
            $log.= "<h3>metros: $qtMetros - $qtAnt + $qtPos</h3>";
            break;
        case 'KG':
            $qtMetros = $qtKg - $qtAnt + $qtPos;
            $log.= "<h3>kg: $qtKg - $qtAnt + $qtPos</h3>";
            break;

    }
    $vlPedido = $vlPedido - $vlItemAnt + $vlItemPos;
    $log.= "<h3>$vlPedido - $vlItemAnt + $vlItemPos</h3>";
    /*sc_master_value('qt_metros', sc_format_num($qtMetros, '.', ',', 2, 'S', '1', ''));
    sc_master_value('qt_kg', sc_format_num($qtKg, '.', ',', 2, 'S', '1', ''));
    sc_master_value('vl_total_pedido', sc_format_num($vlPedido, '.', ',', 2, 'S', '1', ''));*/
    //criarLogArquivo('log_atualizarTotsPedidoCabec',$log);
    sc_master_value('qt_metros', formatarNumero($qtMetros) );
    sc_master_value('qt_kg', formatarNumero($qtKg));
    sc_master_value('vl_total_pedido', formatarNumero($vlPedido));

    $aRetorno = array('vl_pedido' => $vlPedido,'qt_metros' => $qtMetros,'qt_kg' => $qtKg );
    return $aRetorno;
}
function existPrecoInfPedWeb($pedWebId)
{
    $log	  = false;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.itens_ped_web ";
    $campos   = " top 1 item_ped_web_id";
    $condicao = " itens.ped_web_id = $pedWebId
                  and log_informado = 1";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $log = true;
    }
    return $log;
}

function atuPrecosPedWeb($pedWebId,$logAtuVlInf=false,$aVlsInf = '',$qtPedidaParam=0)
{
    //$logAtualizado  = 0;
    $aPedWeb 		= getRegPedWeb($pedWebId);
    $containerId 	= $aPedWeb[0]['nr_container'];
    $codMoeda       = $aPedWeb[0]['cod_moeda'];
    //echo "<h1>$codMoeda</h1>";
    $tbPreco        = $aPedWeb[0]['tb_preco_id'];
    $codEstab       = $aPedWeb[0]['cod_estabel'];
    $erroQt         = '';
    //$condPagto		= $aPedWeb[0]['cond_pagto_id'];
    $diasCondPagtoEsp = $aPedWeb[0]['dias_cond_pagto_esp'];
    $diasCondPagtoEsp = tratarNumero($diasCondPagtoEsp);
    $aItens 		= getItensRefPedWeb($pedWebId);
    if(is_array($aItens)){
        $tam = count($aItens);
        inserirLogDb('Dias Cond Pagto:',$diasCondPagtoEsp,__FUNCTION__);
        inserirLogDb('qt Itens',$tam,__FUNCTION__);
        incrNivelCorrenteLogDb();
        for($i=0;$i<$tam;$i++){
            //it_codigo,cod_refer,qt_pedida,vl_informado,item_ped_web_id,log_informado
            $item			= $aItens[$i]['it_codigo'];
            $ref			= $aItens[$i]['cod_refer'];
            inserirLogDb("i - item - ref ","$i - $item - $ref ",__FUNCTION__);
            $logInformado	= $aItens[$i]['log_informado'];
            $qtPedida 		= $aItens[$i]['qt_pedida'];
            $itemPedWebId 	= $aItens[$i]['item_ped_web_id'];
            $vlInformado  	= $aItens[$i]['vl_informado'];
            //$vlUnitFinal  	= $aItens[$i]['vl_unit_final'];

            incrNivelCorrenteLogDb();
            $aPrecoSaldo 	= getPrecoSaldoItemRefCondPagtoContainer($item,$ref,
                $diasCondPagtoEsp,$containerId,$tbPreco);
            decrNivelCorrenteLogDb();
            //echo "<h1>Array do resultado getPrecoSaldoItemRefCondPagtoContainer</h1>";
            inserirLogDb('array aPrecoSaldo',$aPrecoSaldo,__FUNCTION__);
            //var_dump($aPrecoSaldo);
            $vlUnitTabela	= $aPrecoSaldo['vl_unit_tabela'];
            $vlUnitFinal	= round($aPrecoSaldo['vl_unit_final'],2);
            $vlUnitFinalChave = str_replace('.','_',$vlUnitFinal);
            $aVlUnitFinalChave = explode('_',$vlUnitFinalChave);
            $vlUnitFinal_01 = $aVlUnitFinalChave[0];
            $vlUnitFinal_02 = $aVlUnitFinalChave[1];
            $repZero        = 2 - strlen($vlUnitFinal_02);
            if( $repZero > 0 ){
                $vlUnitFinal_02 .= str_repeat('0',$repZero) ;
            }
            $vlUnitFinalChave = $vlUnitFinal_01."_".$vlUnitFinal_02;

            //echo "<h1>log_atu_vl_inf:$logAtuVlInf  - vl.informado: $vlInformado </h1>";
            //echo "<h1>array de valores informados a alterar dentro da função atuPrecosPedWeb</h1>";
            //var_dump($aVlsInf);
            if($logAtuVlInf == true){
                $vlInformado = $vlUnitFinal	;
                $logInformado = 1;
            }
            /*var_dump($aVlsInf);
            echo "<h1>item:$item - vl unit final chave: $vlUnitFinalChave</h1>";*/
            if($aVlsInf <> ''){
                //echo "<h1>item:$item - vlUnit:$vlUnitFinalChave</h1>";
                //echo "<h1>cheguei ate aqui:".$aVlsInf[$item][$vlUnitFinalChave]."</h1>";
                //var_dump($aVlsInf);
                if(isset($aVlsInf[$item][$vlUnitFinalChave])){
                    //echo "<h1>cheguei ate aqui dentro ao array</h1>";
                    $vlInformado = $aVlsInf[$item][$vlUnitFinalChave];
                    //echo "<h1>vl.informado:$vlInformado</h1>";
                    $logAtuVlInf = true;
                }else{
                    $logAtuVlInf = false;
                }
            }else{
                $logAtuVlInf = false;
            }

            if(is_array($aPrecoSaldo) ){
                $codControlePreco	=	$aPrecoSaldo['cod_controle_preco'];
                $numIdLiquidaIma	=	$aPrecoSaldo['num_id_liquida_ima'];
                $liquidaIma			=	$aPrecoSaldo['perc_liquida_ima'];
                $qtSaldo			=	$aPrecoSaldo['qt_saldo'];
                $precoLiquidaIma    =   $aPrecoSaldo['preco_liquida_ima'];
                //echo "<h1>qt = $qtSaldo</h1>";

            }else{
                $codControlePreco	=	0;
                $numIdLiquidaIma	=	0;
                $liquidaIma			=	0;
                $qtSaldo			=	0;
                $precoLiquidaIma    =   0;

            }
            $qtPedidaOriginal = $qtPedida;
            if($qtPedidaParam > 0){ // caso seja passado o parametro qtPedidaParam a qtPedida é sobreposta
                $qtPedida = $qtPedidaParam;
            }

            //calculo do saldo para validacao
            $qtSaldo += $qtPedidaOriginal;
            if($qtSaldo < 0){
                $qtSaldo = 0;
                //echo "<h1>Passei aqui no menor que 0</h1>";
            }
            inserirLogDb('qt.pedida - qt.saldo',"$qtPedida - $qtSaldo",__FUNCTION__);
            //validacao qt.pedida
            if($qtPedida > $qtSaldo){
                inserirLogDb('qt.pedida maior que saldo?','sim',__FUNCTION__);
                $qtPedida = $qtSaldo;
                $qtSaldoFormat = formatarNumero($qtSaldo);
                $incr = "O Item: $item - Ref: $ref tem saldo de $qtSaldoFormat 
                e foi assumida esta quantidade de saldo";
                $erroQt = util_incr_valor($erroQt,$incr,"</br>");
            }else{
                inserirLogDb('qt.pedida maior que saldo?','nao',__FUNCTION__);
            }

            //echo "<h1>vl.informado: $vlInformado</h1>";
            atualizarItemRefPedWeb($logInformado,$itemPedWebId,
                $qtPedida,$vlUnitTabela,$vlUnitFinal,
                $codControlePreco,$numIdLiquidaIma,$liquidaIma,
                $qtSaldo,$vlInformado,$logAtuVlInf,$precoLiquidaIma);
            //$logAtualizado = 1;
        }
        decrNivelCorrenteLogDb();
    }
    //$logAtualizado;
    inserirLogDb('Erro',$erroQt,__FUNCTION__);
    return $erroQt;
}

function atualizarVlInfItensPreco($pedWebId, $aVlsInf,$qtPedida=0)
{
    $erro = atuPrecosPedWeb($pedWebId,false,$aVlsInf,$qtPedida );
    return $erro;
}

function atualizarVlInformado($pedWebId,$itCodigo, $valor, $vlInf)
{
    $cmd = "update pub.itens_ped_web set vl_informado = '$vlInf'
            where ped_web_id = $pedWebId and it_codigo = '$itCodigo' and vl_unit_final = '$valor'";
    sc_exec_sql($cmd,"especw");
}

function atuPrecoQtItemPedweb($itemPedWebId,$vlinformado,$qtPedida)
{
    $cmd = "update pub.itens_ped_web set vl_informado = '$vlinformado', qt_pedida = '$qtPedida'
            where item_ped_web_id = $itemPedWebId";
    sc_exec_sql($cmd,"especw");

}
function excluirItensPedWeb($listaIds)
{
    $cmd = "delete from pub.itens_ped_web where item_ped_web_id in($listaIds)";
    sc_exec_sql($cmd,"especw");


}

function getNumCartaoCredito()
{
    return 4;
}
function calcPercComisPedWeb($pedWebId, $aVls='')
{

    /*
     valores iniciais
    $qtTotMetros     =  0 ;
    $qtTotQuilos     =  0 ;
    $vlTotal         =  0;
    $percFinal       =  0;
    */

    $aComis          = array();
    $aPed            = getRegPedWeb($pedWebId);
    $percCalcVendPrinc = 0;
    inserirLogDb('array peds_web',$aPed,__FUNCTION__);
    inserirLogDb('valor array passado por parametro',$aVls,__FUNCTION__);
    if(is_array($aPed)){
        $aPed = $aPed[0];
        $formaPagto         = $aPed['cod_forma_pagto'];
        $containerPedido    = $aPed['nr_container'];
        $diasCondPagto      = $aPed['dias_cond_pagto_esp'];
        $codVendPed         = $aPed['repres_id'];
        $estab              = $aPed['cod_estabel'];
        $tbPreco            = $aPed['tb_preco_id'];
        $percVendPed        = $aPed['perc_comis_vend'];
        $percRepresPed      = $aPed['perc_comis_repres'];
        $divideComis        = $aPed['log_divide_comissao'];
        $clienteId          = $aPed['cliente_id'];
        $codMoeda           = $aPed['cod_moeda'];
    }else{
       $containerPedido     = 0;
        $diasCondPagto      = '';
        $codVendPed         = 0;
        $estab              = '';
        $tbPreco            = 0;
        $percVendPed        = 0;
        $percRepresPed      = 0;
        $divideComis        = 0;
        $clienteId          = 0;
        $formaPagto         = 0;
    }

    //valorizar variaveis totais do resumo
    if($aVls == ''){
        $aVls = valorizarItensRefPedWeb($pedWebId,$containerPedido,$diasCondPagto,$tbPreco,
            $codMoeda);
    }
    inserirLogDb('array retorno valorizarItensRefPedweb',$aVls,__FUNCTION__);
    //$tipoFrete = atualizarTipoFrete([ped_web_corrente],$aVls);

    $empresa        =  substr($estab,0,1).'00';
    $percComis      = getPercComisRepres($empresa,$codVendPed);
    $nomeAbrevVend  = getNomeAbrevRepres($codVendPed);

    $aOutroVend             = verifCliOutroRepres($clienteId);
    $lOutroVend             = $aOutroVend['log_outro_vend'];
    $nomeAbrevOutroRepres   = $aOutroVend['nome_repres_cli'];
    $codOutroRepres         = buscarCodRep($nomeAbrevOutroRepres);
    $percComisOutroRepres   = getPercComisRepres($empresa,$codOutroRepres);
    $aTbPreco               = getRegTbPreco($tbPreco);
    inserirLogDb("array tabela de preço: $tbPreco",$aTbPreco,__FUNCTION__);

    // dados tabela de preço que reduzem comissão ou geram bonus
    if(is_array($aTbPreco)){
        $percReducComis         = $aTbPreco[0]['perc_reduc_comis'];
        $logCalcBonus           = $aTbPreco[0]['log_calc_bonus'];
    }else{
        $percReducComis = 0;
        $logCalcBonus   = 0;
    }
    $fatorReduc             = 1 - $percReducComis / 100;
    $bonus                  = getHabilitarBonusRepres();
    inserirLogDb('Percentual de Redução de Comissão da Tabela de Preços',$percReducComis,__FUNCTION__);
    inserirLogDb('Fator de Redução',$fatorReduc,__FUNCTION__);
    //valores totais dos itens e informações sobre metas, outlet e prazo medio
    if(is_array($aVls)){
        $logMetaComis    = $aVls['log_meta_comis'];
        $logOutlet       = $aVls['log_outlet'];
        $prazoMedio      = $aVls['prazo_medio'];
        $logAbaixoOutlet = $aVls['log_abaixo_outlet'];
        if($prazoMedio <= 90 and $logAbaixoOutlet == 0){
            $logComisOutletOk = 1;
        }else{
            $logComisOutletOk = 0;
        }

        //echo "<h1>meta: $logMetaComis</h1>";

        inserirLogDb('requisitos para meta OK?', getVlLogico($logMetaComis) ,__FUNCTION__);
        inserirLogDb('Bonus ativado?', getVlLogico($bonus) ,__FUNCTION__);
        inserirLogDb('tabela de preço calcula bonus?', getVlLogico($logCalcBonus) ,__FUNCTION__);
        inserirLogDb('Dentro das condições da meta?',getVlLogico($logMetaComis),__FUNCTION__);
        inserirLogDb('Dentro das condições de Outlet?',getVlLogico($logComisOutletOk),__FUNCTION__);
        inserirLogDb('Prazo Médio', $prazoMedio ,__FUNCTION__);
        inserirLogDb('Itens com preço abaixo do outlet?',getVlLogico($logAbaixoOutlet),__FUNCTION__);
        inserirLogDb('Percentual de comissão padrão do vendedor corrente',$percComis,__FUNCTION__);
        inserirLogDb('Nome Abrev Vendedor Corrente',$nomeAbrevVend,__FUNCTION__);
        inserirLogDb('Código Vendedor Corrente',$codVendPed,__FUNCTION__);
        inserirLogDb('Percentual de comissão padrão do repres2 ',$percComisOutroRepres,__FUNCTION__);
        inserirLogDb('Nome Abrev do repres2 ',$nomeAbrevOutroRepres,__FUNCTION__);
        inserirLogDb('Código do repres2 ',$codOutroRepres,__FUNCTION__);
        inserirLogDb('Percentual de comissão do vendedor vindo da campanha',$percVendPed,__FUNCTION__);
        inserirLogDb('Percentual de comissão do repres vindo da campanha',$percRepresPed,__FUNCTION__);
        /*
         * acrescimo de condicao de que o tipo de pagamento 3(cartão de crédito) não em bonus de comissão
         * conforme e-mail 19/01/2022 enviado pela Ana Flávia as 17:24
        */
        if($logMetaComis == 1 and $bonus == 1 and $logCalcBonus == 1
            and $formaPagto <> getNumCartaoCredito()
         ){
            $percAument = 1 + getPercAumentComis();
        }else{
            $percAument = 1;
        }
        inserirLogDb('Fator de Aumento',$percAument ,__FUNCTION__);
        $aComis[0]['nome_abrev'] = $nomeAbrevVend;
        $aComis[0]['cod_rep']    = $codVendPed;
        if($logOutlet){
            inserirLogDb('Entrei na condição de ser outlet?','Sim',__FUNCTION__);
            $tipoVendedor = getTipoVendedor(); //quando nao passa parametro pega o login corrente
            inserirLogDb('Tipo Vendedor',$tipoVendedor,__FUNCTION__);
            if($tipoVendedor == 'interno'){
                if($lOutroVend == 1){
                    inserirLogDb('entrei na condição de ter outro vendedor','sim',__FUNCTION__);
                    if($divideComis == 1){
                        inserirLogDb('entrei na condição de dividir comissão','sim',__FUNCTION__);
                        if($logComisOutletOk == 1){
                            inserirLogDb('entrei na condição de outlet ok','sim',__FUNCTION__);
                            if($percRepresPed == 0){
                                $aComis[0]['perc_comis'] =  $percComisOutroRepres / 2;
                                $percAument = 1;
                            }else{
                                $aComis[0]['perc_comis'] = $percRepresPed / 2;
                            }

                            $percCalcVendPrinc = $aComis[0]['perc_comis'] * 2;
                            inserirLogDb('Perc. Comissão vend. corrente',$aComis[0]['perc_comis'],__FUNCTION__);

                        }else{
                            inserirLogDb('entrei na condição de outlet ok','NAO',__FUNCTION__);
                            inserirLogDb('perc comis outro repres',$percComisOutroRepres,__FUNCTION__);
                            $aComis[0]['perc_comis'] = $percComisOutroRepres / 2;
                            $percCalcVendPrinc = $aComis[0]['perc_comis'] * 2;
                            inserirLogDb('Perc. Comissão vend. corrente',$aComis[0]['perc_comis'],__FUNCTION__);
                        }
                        $aComis[1]['nome_abrev']    = $nomeAbrevOutroRepres;
                        $aComis[1]['cod_rep']       = $codOutroRepres;
                        $aComis[1]['perc_comis']    = $aComis[0]['perc_comis'];
                    }else{
                        /*
                        * esta situação não pode ocorrer, pois, se tem dois vendedores é porque vai dividir
                         * comissão
                        */
                    }
                }else{
                    inserirLogDb('entrei na condição de ter outro vendedor','NAO',__FUNCTION__);
                    $aComis[0]['perc_comis'] = $percVendPed == 0 ? $percComis : $percVendPed ;
                    $percCalcVendPrinc = $aComis[0]['perc_comis'];
                    inserirLogDb('Percentual Comissão vendedor',$percCalcVendPrinc,__FUNCTION__);

                }
            }else{ //representante
                inserirLogDb('entrei na condição de ser vendedor interno','NAO',__FUNCTION__);
                $aComis[0]['perc_comis'] = $percRepresPed == 0 ? $percComis : $percRepresPed ;
                $percCalcVendPrinc = $aComis[0]['perc_comis'];
            }
            $aComis[0]['perc_comis'] = $aComis[0]['perc_comis'] * $fatorReduc * $percAument;
            if(isset($aComis[1]['perc_comis'])){
                $aComis[1]['perc_comis'] = $aComis[1]['perc_comis'] * $fatorReduc;
                inserirLogDb('Percentual final comis repres2',$aComis[1]['perc_comis'],__FUNCTION__);
            }
        }else{ //nao é outlet
            inserirLogDb('Entrei na condição de ser outlet?','NAO',__FUNCTION__);
            $aComis[0]['perc_comis'] = $percComis;
            $perComisAntesFator = $aComis[0]['perc_comis'];
            $aComis[0]['perc_comis'] = $aComis[0]['perc_comis'] * $fatorReduc * $percAument;
            $percCalcVendPrinc = $aComis[0]['perc_comis'];
            inserirLogDb('perc_comis = 1 * fator * percAument',
                $aComis[0]['perc_comis']." = $perComisAntesFator * $fatorReduc * $percAument",__FUNCTION__);
            //inserirLogDb('','',__FUNCTION__);
        }

    }else{
        $aComis[0]['perc_comis']    = 0;
        $aComis[0]['cod_rep']       = 0;
        $aComis[0]['nome_abrev']    = '';
        $percCalcVendPrinc          = 0;

    }
    // tratamento agenciador
    if(getTodosClienteCorrente() == 1){
        inserirLogDb('Todos Clientes','Sim',__FUNCTION__);
        $percPartComis = getPercPartComisCorrente();
        $aComis[0]['nome_abrev']    = $nomeAbrevVend;
        $aComis[0]['cod_rep']       = $codVendPed;
        $aComis[0]['perc_comis']    = $percCalcVendPrinc * (1 - $percPartComis / 100 );

        $aComis[1]['nome_abrev']    = $nomeAbrevOutroRepres;
        $aComis[1]['cod_rep']       = $codOutroRepres;
        $aComis[1]['perc_comis']    = $percCalcVendPrinc * ($percPartComis / 100 );

    }else{
        $percPartComis = 0;
        inserirLogDb('Todos clientes','Não',__FUNCTION__);
    }

    inserirLogDb('Array final de comissões',$aComis,__FUNCTION__);
    return $aComis;
}
function calcPercComisPedWebApi($pedWebId,$codPrograma,$transacao=0,$logAtualiza=1)
{
    $usuarioERP = getUsuarioERP(getUsuarioCorrente());
    if($usuarioERP == ''){
        $codUsuario = getUsuarioCorrente();
    }else{
        $codUsuario = $usuarioERP;
    }
    $campos = "chave,valor";
    $aRetorno = getDadosApis('pdp/v1/calcComissao',
        'GET',
        array('ped_web_id'=>$pedWebId,
            'id_transacao'=>$transacao,
            'cod_usuario' => $codUsuario,
            'log_atualiza'=>$logAtualiza,
            'cod_programa'=>$codPrograma),
        $campos);
    return $aRetorno;

}
function verificPermItemPedWeb($divideComis,$percComisRep,$percComisVend,$codControlePreco)
{
    $logPermissao = 0;
    $aPreco = getRegControlePrecoPorId($codControlePreco);
    if(is_array($aPreco)){
        $campanhaId = $aPreco[0]['campanha_id'];
    }else{
        $campanhaId = 0;
    }
    $aCampanha =  getRegCampanha($campanhaId);
    if(is_array($aCampanha)){
        $divideComisCamp = $aCampanha[0]['log_dividir_comis'];
        $percRepresCamp  = $aCampanha[0]['perc_repres'];
        $percVendCamp    = $aCampanha[0]['perc_vendedor'];
    }else{
        $divideComisCamp = 0;
        $percRepresCamp  = 0;
        $percVendCamp    = 0;

    }
    if($divideComis == $divideComisCamp
        and $percComisVend == $percVendCamp
        and $percComisRep == $percVendCamp){
        $logPermissao = 1;
    }
    return $logPermissao;

}


function atuItemPedWebPorArray($id,$aDados)
{
    $cmd = convertArrayEmUpdate('itens_ped_web',$aDados,"item_ped_web_id = $id");
    sc_exec_sql($cmd,"especw");


}
function setDtHrCriacaoItemPedweb($itemPedWebId,$dtHr)
{
    $aDados = array('dt_hr_criacao'=> $dtHr);
    atuItemPedWebPorArray($itemPedWebId,$aDados);

}
function existItemRefPed($pedWebId,$itCodigo,$codRefer)
{
    $log = false;
    $aReg = getDados('unico','pub.itens_ped_web',
    'item_ped_web_id',
    " it_codigo = '$itCodigo' and cod_refer = '$codRefer'
      and ped_web_id = $pedWebId");
    if(is_array($aReg)){
        $log = true;
    }
    return $log;
}

?>
