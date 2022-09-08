<?php
function buscarTotSaldo($item,$estab)
{
    $saldo = 0;
    $sql = " select sum(\"qtidade-atu\") from pub.\"saldo-estoq\" 
			where \"it-codigo\" = '$item' and \"cod-estabel\" = '$estab'
			and \"cod-depos\" = 'ARM'";
    switch($estab)
    {
        case "1":
            //
            sc_lookup(saldoestoq , $sql, "ima");
            //echo "Buscando Dados IMA...</br>";
            break;
        case "5":
            //
            sc_lookup(saldoestoq , $sql, "med");
            //echo "Buscando Dados MED...</br>";
            break;
    }
    if ({saldoestoq} === false){
    echo "Erro de Acessso função buscarTotSaldo . Mensagem=". {saldoestoq_erro} ;
	}
	elseif (empty({saldoestoq})){
    echo "Sem retorno";
}
	else{
    $saldo =  {saldoestoq[0][0]};
	}
	if($saldo == ''){
        $saldo = 0;
    }
	return $saldo;
}

/**
 * @param $empresaParam
 * @param $filtro
 * @param string $item(pode ser um item ou uma lista de itens separad
 * @param bool $logBuscarPreco
 * @return array|string
 */
function buscarSaldoEstoque($empresaParam,
                            $filtro,
                            $logBuscarPreco=true,
                            $nivel=1, //1-item 2- item /ref
                            $logBuscarCarrinho=true,
                            $logBuscarDadosItens=true,
                            $listaRefs='',
                            $tbPreco=1)
{
    //echo "<h1> Refs=$listaRefs</h1>";
    //variaveis iniciais
    $aSaldoEstoq = array();
    $lAchou = false;
    $saldoestoq  = '';
    $itemAnt     = '';
    $refAnt      = '';
    $gram  = '';
    $descItem = '';

    inserirLogDb('parametros - empresa',$empresaParam,__FUNCTION__);
    inserirLogDb('parametros - filtro',$filtro,__FUNCTION__);
    inserirLogDb('parametros - buscar preço?',getVlLogico($logBuscarPreco),__FUNCTION__);
    inserirLogDb('parametros - nivel',$nivel,__FUNCTION__);
    inserirLogDb('parametros - buscar Carrinho?',getVlLogico($logBuscarCarrinho),__FUNCTION__);
    inserirLogDb('parametros - buscar dados itens?',getVlLogico($logBuscarDadosItens),__FUNCTION__);
    inserirLogDb('parametros - lista refers',$listaRefs,__FUNCTION__);
    inserirLogDb('parametros - tb.preço',$tbPreco,__FUNCTION__);


    $listarefs = inserirAspasEmLista($listaRefs);

    if($listaRefs <> ''){
        $filtro2 = " and  \"cod-refer\" in ($listaRefs)";
    }else{
        $filtro2 = '';
    }
    $logConsiderarDepositoFechado = getConsiderarDepositoFechado();
    if($logConsiderarDepositoFechado == 1){
        $estabDeposFechado = getEstabDepositoFechado();
        $filtroEstabDeposFechado = " or  saldo.\"cod-estabel\" = '$estabDeposFechado' ";

        $codDeposDeposFechado = getCodDeposDepositoFechado();
        $filtroCodDeposFechado = " or  saldo.\"cod-depos\" = '$codDeposDeposFechado' ";

    }else{
        $filtroEstabDeposFechado = '';
        $filtroCodDeposFechado = '';
    }
    if($nivel == 1){ // 1- sumariza por item 2- por item / referencia
        $cpCodRefer = '';
        $cpQtidadeAtu = "sum(saldo.\"qtidade-atu\") as qtidade_atu ";
        $groupBy      = " group by saldo.\"it-codigo\"";
    }else{
        $cpCodRefer     = ', saldo."cod-refer" as cod_refer';
        $cpQtidadeAtu   = "saldo.\"qtidade-atu\" as qtidade_atu ";
        $groupBy        = "";

        if($logConsiderarDepositoFechado == 1){

            $cpQtidadeAtu = "sum(saldo.\"qtidade-atu\") as qtidade_atu ";
            $groupBy      = " group by saldo.\"it-codigo\", saldo.\"cod-refer\"";
        }

    }
    $aRegEmpresa = getDadosEmpresa($empresaParam);
    $conexao = $aRegEmpresa['conexao'];
    $aRegEst = getDados('multi','pub."saldo-estoq" saldo',
        "$cpQtidadeAtu ,                    
                   saldo.\"it-codigo\" as it_codigo $cpCodRefer",
        " (saldo.\"cod-estabel\" = '$empresaParam' $filtroEstabDeposFechado ) 
                   and  saldo.\"qtidade-atu\" > 0 
				   and saldo.\"cod-refer\" = lote
				   and (saldo.\"cod-depos\" = 'arm' $filtroCodDeposFechado ) 
				   $filtro $filtro2 $groupBy ",$conexao);
    if(is_array($aRegEst)) {
        foreach ($aRegEst as $reg)
        {
            //$codEstabel         = $reg['cod_estabel'];
            $itCod              = $reg['it_codigo'];
            //echo "<h1>ITEM: $itCod</h1>";
            if($logBuscarDadosItens == true){
                $descItem = getDescrItem($itCod);
                //echo "<h1>passei aqui</h1>";
            }else {
                $descItem = '';
            }
            if($descItem <> '') {
                $descItem = "$itCod - $descItem ";
            }
            $idPreco           = 0;
            if($nivel == 1){
                $codRefer = '';
                $filtro       = " and \"it-codigo\" = '$itCod' ";
                $ordemRef     = 0;

            }else{
                $codRefer           = $reg['cod_refer'];
                $filtro       = " and \"it-codigo\" = '$itCod' and \"cod-refer\" = '$codRefer' ";
                //inserirLogDb('ref passa para buscar ordenacao',$codRefer,__FUNCTION__);
                $ordemRef       = getOrdemCodRefer($codRefer);
            }
            inserirLogDb('Ref.Corrente',$codRefer,__FUNCTION__);
            if($logBuscarCarrinho == true) {
                $qtCarrinhoGeral         = getQtCarrinho($itCod,$codRefer,0);
                $qtCarrinhoLoginCorrente = getQtCarrinhoLoginCorrente($itCod,$codRefer,0);
            }else {

                $qtCarrinhoGeral         = 0;
                $qtCarrinhoLoginCorrente = 0;
            }
            inserirLogDb('quantidade carrinho geral',$qtCarrinhoGeral,__FUNCTION__);
            inserirLogDb('quantidade carrinho login corrente',$qtCarrinhoLoginCorrente,__FUNCTION__);


            //$qtCarrinho = $qtCarrinhoLoginCorrente;

            $qtSaldoEstoque     = $reg['qtidade_atu'];
            inserirLogDb('quantidade atual estoque(qtidade_atu) ',$qtSaldoEstoque,__FUNCTION__);

            $filtroEsp    = " and \"nr-container\" = 0";
            $filtro      .= $filtro2;
            $qtPedida     = buscarQtPedida($empresaParam,$filtro);
            inserirLogDb('quantidade Pedida',$qtPedida,__FUNCTION__);
            //echo "<h1>$qtPedida</h1> ";
            $qtFaturAbert = buscarQtNFSemAtuEst($empresaParam,$filtro); //comentado em 18/08/2015 a pedido do Angelo e descomentado a pedido do Toninho em 14/08/2017
            inserirLogDb('quantidade Fatur.em Aberto',$qtFaturAbert,__FUNCTION__);
            //echo "<h1>$qtFaturAbert</h1> ";
            $qtPedida     += $qtFaturAbert;
            $qtNaoIntegrado  = getQtNaoIntegrada($itCod,$codRefer,'0');
            inserirLogDb('quantidade não integrada',$qtNaoIntegrado,__FUNCTION__);
            //tratando valores numericos
            $qtSaldoEstoque = tratarNumero($qtSaldoEstoque);
            $qtPedida = tratarNumero($qtPedida);
            $qtCarrinhoGeral = tratarNumero($qtCarrinhoGeral);
            $qtNaoIntegrado = tratarNumero($qtNaoIntegrado);
            //echo "<h1>Saldo=$qtSaldoEstoque - QtPedida=$qtPedida - qtCarrinho=$qtCarrinhoGeral - Nao Integrado=$qtNaoIntegrado - </h1> ";

            $qtSaldoVenda = $qtSaldoEstoque -  $qtPedida - $qtCarrinhoGeral - $qtNaoIntegrado; //+ $qtCarrinhoLoginCorrente;
            //echo "<h1>$qtSaldoVenda</h1> ";
            inserirLogDb('$qtSaldoVenda = $qtSaldoEstoque -  $qtPedida(já somada qtfaturabert) - $qtCarrinhoGeral - $qtNaoIntegrado'," $qtSaldoVenda = $qtSaldoEstoque -  $qtPedida - $qtCarrinhoGeral - $qtNaoIntegrado; ",__FUNCTION__);
            /*if($qtSaldoVenda < 0){
                $qtSaldoVenda = 0;
            }*/
            //echo "<h1>qt.saldo estoq.:$qtSaldoEstoque - qt.pedida: $qtPedida - qt.carrinho:$qtCarrinho</h1>";
            //echo "<h1>$qtSaldoVenda</h1> ";
            if($logBuscarPreco == true){
                // echo "<h1>vou busca o preco do item $itCod e referencia $codRefer</h1> ";
                $aPreco      = buscarPreco($empresaParam,'4',$itCod,$codRefer,0,true,$tbPreco);
            }else{
                $aPreco = '';
            }
            if(is_array($aPreco)){
                $aPreco = $aPreco[0];
                $precoReal         = $aPreco['vl_real'];
                $precoDolar        = $aPreco['vl_dolar'];
                $idPreco           = $aPreco['id'] ;
                $logDivideComis    = $aPreco['log_divide_comis'];
                $percComisVend     = $aPreco['perc_comis_vend'];
                $percComisRepres   = $aPreco['perc_comis_repres'];
                $idLiq             = $aPreco['id_liq'];
                $precoLiquidaIma   = $aPreco['preco_liquida_ima'];
                $origemLiqIma       = $aPreco['origem_liquida_ima'];

            }else{
                $precoReal         = 0;
                $precoDolar        = 0;
                $idPreco           = 0 ;
                $logDivideComis    = 0;
                $percComisVend     = 0;
                $percComisRepres   = 0;
                $idLiq             = 0;
                $precoLiquidaIma   = 0;
                $origemLiqIma      = 0;
            }
            //echo "<h1>descrição item completa:$descItemCompleta</h1>";
            $aSaldoEstoq[] = array(
                "cod_estabel"          => $empresaParam,
                "it_codigo"            => $itCod,
                "desc_item"            => $descItem,
                //"desc_preco"           => $descPreco,
                "cod_refer"            => $codRefer,
                "qt_saldo"	            => $qtSaldoEstoque,
                "qt_pedido"	        => $qtPedida ,
                //"preco_prazo01"	    => $precoVista,
                //"preco_prazo02"	    => $preco30Dias,
                //"preco_prazo03"	    => $preco60Dias,
                //"preco_prazo04"	    => $preco90Dias,
                "nr_container"         => 0,
                "qt_disp"              => $qtSaldoEstoque,
                "qt_carrinho_geral"    => $qtCarrinhoGeral,
                "qt_carrinho"          => $qtCarrinhoLoginCorrente,
                "qt_saldo_venda"       => $qtSaldoVenda,
                "dt_prev_chegada"      => '',
                "moeda"                => 'real',
                "id_preco"             => $idPreco,
                "ordem_ref"            => $ordemRef,
                "preco_real"           => $precoReal,
                "preco_dolar"          => $precoDolar,
                "log_divide_comis"     => $logDivideComis,
                "perc_comis_vend"      => $percComisVend,
                "perc_comis_repres"    => $percComisRepres,
                "preco_liquida_ima"    => $precoLiquidaIma,
                "id_liq"               => $idLiq,
                "origem_liquida_ima"   => $origemLiqIma);
            $lAchou = true;
        }
    }
    if($lAchou == false){
        $aSaldoEstoq = '';
    }
    return $aSaldoEstoq  ;
}

function buscarQtNFSemAtuEst($empresa,$filtro)
{
    $qtNF = 0;
    $sql = "select sum(to_number(pro_element(PUB.\"it-nota-fisc\".\"qt-faturada\",1,1))) 
			from PUB.\"it-nota-fisc\", pub.\"nota-fiscal\"
			where  PUB.\"nota-fiscal\".\"dt-cancela\" is null
            and    PUB.\"nota-fiscal\".\"dt-confirma\" is null
            and pub.\"nota-fiscal\".\"cod-estabel\" = '$empresa'
            and pub.\"nota-fiscal\".\"cod-estabel\" =  pub.\"it-nota-fisc\".\"cod-estabel\"   
			and pub.\"nota-fiscal\".\"serie\"  =  pub.\"it-nota-fisc\".\"serie\"
            and pub.\"nota-fiscal\".\"nr-nota-fis\"  =  pub.\"it-nota-fisc\".\"nr-nota-fis\"
 			AND PUB.\"nota-fiscal\".\"nat-operacao\" not in
 			( select \"nat-vinculada\" from pub.\"natur-oper\"
 			  where \"nat-vinculada\" <> '') 			
			".$filtro." WITH (NOLOCK)";
    //echo $sql;
    switch($empresa)
    {
        case '1':
            sc_lookup(nfqt, $sql,"ima");
            break;
        case '5':
            sc_lookup(nfqt, $sql,"med");
            break;

    }

    if ({nfqt} === false)
    {
        echo "Erro de acesso. Mensagem = " . {nfqt_erro};
    }
    elseif (empty({nfqt}))
    {
        $qtNF = 0;
    }
    else
    {
        $qtNF = {nfqt[0][0]};
	}

	return $qtNF;



}
function buscarQtPedida($empresa,$filtro)
{
    $qtPedido = 0;
    $sql = "select sum(PUB.\"ped-item\".\"qt-pedida\") 
			from PUB.\"ped-item\", pub.\"ped-venda\"
			where  PUB.\"ped-item\".\"cod-sit-item\" in (1,4)
            and pub.\"ped-venda\".\"nome-abrev\" =  pub.\"ped-item\".\"nome-abrev\"   
			and pub.\"ped-venda\".\"nr-pedcli\"  =  pub.\"ped-item\".\"nr-pedcli\" 
 			and
			(pub.\"ped-venda\".\"tp-pedido\" = 'PE' 
                       
             )
 			
			".$filtro." WITH (NOLOCK)";
    /*
     * o trecho abaixo foi retirado da variavel $sql acima
     * or (pub."ped-venda"."tp-pedido" = 'pi'
                           and pub."ped-venda"."dt-entrega" <= sysdate()
                           )
     *
     * */
    switch($empresa)
    {
        case '1':
            sc_lookup(ped, $sql,"ima");
            break;
        case '5':
            sc_lookup(ped, $sql,"med");
            break;

    }

    if ({ped} === false)
    {
        echo "Erro de acesso. Mensagem = " . {ped_erro};
    }

    elseif (empty({ped}))
    {
        $qtPedido = 0;
    }
    else
    {
        $qtPedido = {ped[0][0]};
	}

	return $qtPedido;

}
function buscarSaldoPI($empresa , $item , $referencia)
{

    $qtSaldo = 0;
    $sql = "select sum(\"qt-pedida\") from PUB.\"pp-container\", PUB.\"pp-it-container\"
            where PUB.\"pp-container\".\"nr-container\"       = PUB.\"pp-it-container\".\"nr-container\"
            and   PUB.\"pp-container\".\"situacao\"           = 1
            and   PUB.\"pp-container\".\"cod-estabel\"        = '$empresa'
		    and   PUB.\"pp-it-container\".\"it-comprado\"     = '$item'
            and   PUB.\"pp-it-container\".\"ref-comprada\"    = '$referencia'
            WITH (NOLOCK)   ";
    sc_lookup(saldopi, $sql,"espec");

    if ({saldopi} === false)
    {
        echo "Erro de acesso. Mensagem = " . {saldopi_erro};
    }
    elseif (empty({saldopi}))
    {
        $qtSaldo = 0;
    }
    else
    {
        $qtSaldo = {saldopi[0][0]};
	}

	return $qtSaldo;

}


function buscarRestricaoContainerRepres($codRepres)
{    //echo "<h1>codRepres:".$codRepres."</h1>";
    $listaContainer = '';
    $sql = "
              select  \"nr-container\" from pub.\"pp-container\"
			  where  exclusivo = 1 WITH (NOLOCK)" ;
    //echo $sql;
    sc_select(restricao , $sql, "espec");
    while (!$restricao->EOF)
    {
        //echo "</br>".$restricao->fields[0];
        $container = $restricao->fields[0];
        //echo $container;
        if($container == '')
            $container = '0';
        if($codRepres == '')
            $codRepres = 0;

        $sql = "select nr_container from PUB.pp_container_permissao where 
                nr_container = " . $container . " and cod_repres = $codRepres
                 WITH (NOLOCK) ";
        //echo "</br>".$sql;
        //echo "</br>".$container;
        sc_lookup(permissao_repres, $sql,"espec");

        if(empty({permissao_repres}) == true && $codRepres != 0)
		{


            if($listaContainer == '')
                $listaContainer = $restricao->fields[0];
            else
                $listaContainer .= ",".$restricao->fields[0];
        }

		$restricao->MoveNext();
	}
    $restricao->Close();
    return $listaContainer;
}
function buscarQtAlocada($empresa,$filtro)
{
    $qtAlocada = 0;
    $sql = "select sum(PUB.\"ped-item\".\"qt-alocada\"),sum(PUB.\"ped-item\".\"qt-log-aloc\")  
			from PUB.\"ped-item\", pub.\"ped-venda\"
			where  PUB.\"ped-item\".\"cod-sit-item\" not in (1,4)
            and pub.\"ped-venda\".\"nome-abrev\" =  pub.\"ped-item\".\"nome-abrev\"   
			and pub.\"ped-venda\".\"nr-pedcli\"  =  pub.\"ped-item\".\"nr-pedcli\" 
 			and 
			(pub.\"ped-venda\".\"tp-pedido\" = 'PE' 
                       or (pub.\"ped-venda\".\"tp-pedido\" = 'pi' 
                           and pub.\"ped-venda\".\"dt-entrega\" <= sysdate() 
                           )
             )
 			
			".$filtro." WITH (NOLOCK) ";
    switch($empresa)
    {
        case '1':
            sc_lookup(ped, $sql,"ima");
            break;
        case '5':
            sc_lookup(ped, $sql,"med");
            break;

    }

    if ({ped} === false)
    {
        echo "Erro de acesso. Mensagem = " . {ped_erro};
    }
    elseif (empty({ped}))
    {
        $qtAlocada = 0;
    }
    else
    {
        $qtAlocada = {ped[0][0]}."|".{ped[0][1]};
	}

	return $qtAlocada;

}

function buscarSaldoEstoq($empresa,$item,$refer)
{
    $sql = "select top 1 qt-alocada, qt-aloc-ped from pub.\"saldo-estoq\" where \"cod-depos\" = 'arm' 
    WITH (NOLOCK) ";
    switch($empresa)
    {
        case '1':
            sc_lookup(ped, $sql,"ima");
            break;
        case '5':
            sc_lookup(ped, $sql,"med");
            break;

    }

    if ({ped} === false)
    {
        echo "Erro de acesso. Mensagem = " . {ped_erro};
    }
    elseif (empty({ped}))
    {
        $qtAlocada = 0;
    }
    else
    {
        $qtAlocada = {ped[0][0]}."|".{ped[0][1]};
	}

	return $qtAlocada;

}

function buscarBloqueioGerencia($usuario)
{
    /*5 - gerencia de vendas*/
    $log_gerencia = 0;
    $listaContainer = '';
    $aRetGerencia = retornoSimplesTb01('pub.usuarios_grupos',
        'cod_grupo',
        " login_usuario = '$usuario' and cod_grupo = 5","espec");
    if(is_array($aRetGerencia)){
        //echo 'entrei na gerencia';
        $aRetRestricao = retornoMultReg('pub."pp-container"','"nr-container"',
            " situacao = 1 and \"log-1\" = 1 ","espec");
        if(is_array($aRetRestricao)){
            for($i=0; $i < count($aRetRestricao);$i++){
                //echo 'entrei na lista de container';
                $container = $aRetRestricao[$i]['"nr-container"'];
                if($listaContainer == ''){
                    $listaContainer =   $container;
                }
                else{
                    $listaContainer .=  ','.$container;
                }
            }
        }
    }
    else{
        //echo 'não entrei na gerencia';
    }

    return $listaContainer;
}

function getListaItensFiltroEstoqUnico($familiaItem, $itemParam, $un, $gramaturaIni, $gramaturaFim,
                                       $pesoLiquidoIni, $pesoLiquidoFim, $tbPrecoFiltrarComSaldo='',$empresa='5')
{
    $tabItem                = 'item';
    $tabItemExt             = 'item_ext';
    $filtroItem             = '';
    $listaItens             = '';
    $logFiltro              = false;
    if($familiaItem <> ''){
        $logFiltro = true;
        $filtroItem = util_incr_valor($filtroItem,"$tabItem.\"fm-codigo\" = $familiaItem",'' );
    }
    if($un <> ''){
        $logFiltro = true;
        $filtroItem = util_incr_valor($filtroItem,"$tabItem.\"un\" = '$un'",' and ' );
    }
    if($pesoLiquidoIni > 0 or $pesoLiquidoFim < 100){
        $logFiltro = true;
        $incremento = " $tabItem.\"peso-liquido\" >= $pesoLiquidoIni and $tabItem.\"peso-liquido\" <= $pesoLiquidoFim ";
        $filtroItem = util_incr_valor($filtroItem,$incremento,' and ' );
    }
    if($itemParam <> ''){
        $logFiltro = true;
        $filtro = gerarFiltroPartes("$tabItem.\"it-codigo\"","$tabItem.\"desc-item\"",$itemParam);
        $filtroItem = util_incr_valor($filtroItem,$filtro," and ");
    }
    // echo "<h1>gramatura ini: $gramaturaIni - gramatura fim: $gramaturaFim </h1>";
    if($gramaturaIni > 0 or $gramaturaFim < 999){
        // echo "<h1> ENTREI gramatura ini: $gramaturaIni - gramatura fim: $gramaturaFim </h1>";
        $logFiltro = true;
        $incremento = " {$tabItemExt}.gramatura >= $gramaturaIni and  {$tabItemExt}.gramatura <= $gramaturaFim ";
        $filtroItem = util_incr_valor($filtroItem,$incremento,' AND ' );
    }
    $tipo     = "multi"; // unico ou multi
    $tabela   = " med.pub.item item, espec.pub.\"item-ext\" item_ext ";
    $campos   = " item.\"it-codigo\" as it_codigo ";
    if($filtroItem <> ''){
        $filtroItem = " and $filtroItem ";
    }
    $condicao = "  item.\"ge-codigo\" >= 50
                   and item.\"ge-codigo\" <= 60 and item.\"it-codigo\" = item_ext.\"it-codigo\"
                   $filtroItem ";
    $conexao  = "multi";
    $aItens = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    inserirLogDb('array retorno filtros de props. item',$aItens,__FUNCTION__);
    if(is_array($aItens)){
        $tam = count($aItens);
        for($i=0;$i<$tam;$i++){
            $itCodigo   = $aItens[$i]['it_codigo'];
            inserirLogDb('item corrente',$itCodigo,__FUNCTION__);
            if($tbPrecoFiltrarComSaldo <> ''){
                inserirLogDb('tabela a filtrar  preco',$tbPrecoFiltrarComSaldo,__FUNCTION__);
                $lExiste = getExistPrecoItemRefTb($tbPrecoFiltrarComSaldo,$itCodigo,$empresa);
            }else{
                inserirLogDb('nao filtrou tabela','sim',__FUNCTION__);
                $lExiste = 1;
            }
            if($lExiste == 1){
                $listaItens = util_incr_valor($listaItens,"'$itCodigo'");
            }
        }
    }
    $aRetorno  = array('lista'=>$listaItens,'log_filtro' => $logFiltro );
    return $aRetorno;
}

/**getFiltroEstoqUnicoItem
 * Esta função deve ser utilizada com as seguintes aplicações
 * ctrl_filtro_estoq_unificao e cons_item_estoq_unificado
 * @param $familiaItem
 * @param $itemParam
 * @param $codRefer
 * @param $opcoesSaldo
 * @param $un
 * @param $gramatura
 * @param $pesoLiquido
 * @return array
 */
function getFiltroEstoqUnicoItem($familiaItem, $itemParam, $codRefer, $opcoesSaldo, $un, $gramaturaIni,
                                 $gramaturaFim, $pesoLiquidoIni, $pesoLiquidoFim, $logSoLiquidaIma,
                                 $qtMinKg, $qtMinMT,$tbPrecoFiltrarComSaldo='',$empresa='5')
{

    $qtMin = $qtMinKg + $qtMinMT;

    $logPEComSaldo      = false;
    $filtro             = '';
    $filtroFinal        = '';
    $filtroItem         = '';
    $filtroItemExt      = '';
    $filtroSubSelect    = '';
    $logItem            = false;
    $logItemExt         = false;

    /*a premissa é que a query principal da consulta que utiliza o filtro tenha os alias iguais as
     tabelas baixo*/

    $tabEstoque     = 'saldo_estoque';
    $tabProg        = 'item_container';
    $tabProm        = 'promocao';
    $tabContainer   = 'container';
    /*********************************************************************************************/
    $aEstoquePE     = '';
    $aEstoquePI     = '';
    $aLiquidaIma    = array() ;
    $lAchouLiqIma   = false;
    $filtroCodRefer = '';
    $filtroEstoque  = '';
    $filtroProgVenda = '';
    $filtroProm     = '';
    $listaItensPE   = '';
    $listaItensPI   = '';
    $logVazio       = false;
    $aListaItens    = getListaItensFiltroEstoqUnico($familiaItem, $itemParam, $un, $gramaturaIni, $gramaturaFim,
        $pesoLiquidoIni, $pesoLiquidoFim,$tbPrecoFiltrarComSaldo,$empresa);
    //echo "<h1>lista itens filtro</h1><pre>";
    //var_dump($aListaItens);
    $listaItens = $aListaItens['lista'];
    $logFiltro  = $aListaItens['log_filtro'];

    if($logFiltro == true and $listaItens == ''){
        $logVazio = true;
    }
    if($logVazio == false){
        if ($listaItens <> '') {
            $filtroEstoque = " saldo.\"it-codigo\" in ($listaItens) ";
        }
        if ($codRefer <> '') {
            if (stristr($codRefer, ',') <> false) { // se encontrar virgula entende que deve buscar como in
                $codRefer = retornarOpcoesTxt($codRefer);
                $filtroCodRefer = "\"cod-refer\" in ($codRefer)";
                $codReferIn     =  $codRefer ;

            } else {
                $filtroCodRefer = "\"cod-refer\" = '$codRefer'";
                $codReferIn     = "'$codRefer'";
            }
        }else{
            $codReferIn = '';
        }
        if ($filtroCodRefer <> '') {
            $filtroEstoque = util_incr_valor($filtroEstoque, $filtroCodRefer, " AND ");
        }

        if ($filtroEstoque <> '') {
            $filtroEstoque = " AND $filtroEstoque ";
        }
        // echo "<h1>opcões saldo -> $opcoesSaldo</h1>";
        if ($opcoesSaldo <> '') {
            //criar logica para chamar uma função especifica para cada saldo em estoque selecionado
            $aOpcoes = explode(',', $opcoesSaldo);
            if (is_array($aOpcoes)) {
                $qt = count($aOpcoes);
                //echo "<h1>opcoes:</h1>";
                //var_dump($aOpcoes);
                for ($j = 0; $j < $qt; $j++) {
                    $opcao = $aOpcoes[$j];
                    //echo "<h2>opção Corrente:$opcao</h2>";
                    switch ($opcao) {
                        case '1': //estoque
                            if($logSoLiquidaIma == true){
                                $filtroLiqIma = getFiltroLiquidaIma('saldo');
                            }else{
                                $filtroLiqIma = '';
                            }
                            //echo "<h1>filtro liquida: $filtroLiqIma</h1>";
                            if($filtroLiqIma <> '') {
                                $filtroLiqIma = " AND $filtroLiqIma";
                                $filtroEstoque = util_incr_valor($filtroEstoque,$filtroLiqIma,' ');
                                //echo "<h1>FiltroLiqui = $filtroLiqIma / FiltroEstoque = $filtroEstoque</h1>";
                            }
                            if($qtMin > 0){
                                $nivel = 2;
                            }else{
                                $nivel = 1;
                            }
                            $aEstoquePE = buscarSaldoEstoque('5', $filtroEstoque,false,
                                $nivel,true,false,$codReferIn);
                            $listaItensPE = '';
                            /*echo "array estoque <br>";
                            var_dump($aEstoquePE);*/
                            $aEstoquePE = filtrarPorQt($aEstoquePE,$qtMinKg,$qtMinMT,'qt_saldo_venda');
                            //echo "<br>array estoque após filtro de quantidade<br>";
                            //var_dump($aEstoquePE);
                            if (is_array($aEstoquePE)) {
                                $tam = count($aEstoquePE);
                                for ($i = 0; $i < $tam; $i++) {
                                    $itCodigo = $aEstoquePE[$i]['it_codigo'];
                                    $listaItensPE = util_incr_valor($listaItensPE,$itCodigo );
                                    /*$qtLiquidaIma = getQtLiquidaIma($itCodigo,'',$qtMinKg,$qtMinMT);
                                    if($qtLiquidaIma <> 0){
                                      $aLiquidaIma[]  = array('item' => $itCodigo,'qt' => $qtLiquidaIma) ;
                                      $lAchouLiqIma = true;
                                    }*/
                                }
                            }
                            //echo "<h1>lista PE: $listaItensPE </h1>";
                            break;
                        case '2': //prog.venda

                            if($qtMin > 0){
                                $nivel = 'referencia';
                            }else{
                                $nivel = 'item';
                            }
                            $aEstoquePI = getQtsPI($nivel, $listaItens, $codRefer, '',
                                //" and ($tabProg.\"qt-pedida\"  * $tabProg.\"perc-dsp-venda\" / 100) - $tabProg.\"qt-vendida\" > 0"
                                " and ($tabProg.\"qt-pedida\"  * $tabProg.\"perc-dsp-venda\" / 100) > 0"

                            );
                            $aEstoquePI = filtrarPorQt($aEstoquePI,$qtMinKg,$qtMinMT,'qt_saldo_com_carrinho');
                            //var_dump($aEstoquePI);
                            $listaItensPI = '';
                            if (is_array($aEstoquePI)) {
                                $tam = count($aEstoquePI);
                                for ($i = 0; $i < $tam; $i++) {
                                    $itCodigo    = $aEstoquePI[$i]['it_codigo'];
                                    $listaItensPI = util_incr_valor($listaItensPI, $itCodigo);
                                }
                            }
                            //echo "<h1>lista PI: $listaItensPI </h1>";
                            break;
                    }
                }
            }
        }
        //echo "<h1>Itens PE: $listaItensPE</h1>";
        if($listaItensPE <> ''){
            $aListaItensPE = explode(',', $listaItensPE);
        }else{
            $aListaItensPE = array();
        }
        if($listaItensPI <> ''){
            $aListaItensPI = explode(',', $listaItensPI);
        }else{
            $aListaItensPI = array();
        }
        $aFinal = array_merge($aListaItensPE, $aListaItensPI);
        $aFinal = array_unique($aFinal);
        $listaItensFinal = implode(',', $aFinal);
    }else{
        $listaItensFinal = '';
    }

    if($listaItensFinal <> ''){
        $listaItensFinal = str_replace(',',"','",$listaItensFinal);
        $listaItensFinal = "'$listaItensFinal'";
    }
    //echo " <h3>lista itens final: $listaItensFinal</h3>";

    if($lAchouLiqIma == false){
        $aLiquidaIma = '';
    }

    $aRetorno = array('lista_itens' => $listaItensFinal, 'estoque_pe' =>$aEstoquePE ,
        'estoque_pi' => $aEstoquePI, 'liquida_ima'=> $aLiquidaIma );
    //echo "<pre>";
    //var_dump($aRetorno);
    return $aRetorno ;
}

/**
 * @param $nivel (item - valores sumarizados por item (referencia caso informada), container - valores sumarizados por
 *item (referencia caso informado) e por container
 * @param $item
 * @param string $ref_param
 * @param string $container_param
 * @return string|array
 */
function getQtsPI($nivel, $item_param='', $ref_param='', $container_param='',
                  $filtro_param='',$logBuscarPreco=false,$logConsiderarVendas=1)
{
    $aRetorno        = array();
    $lAchou = false;
    $camposContainer = '';
    $moeda = '';
    if($item_param <> ''){
        if(strstr($item_param,',') <> false){
            $condItem = "and item_container.\"it-codigo\" in ($item_param)";
        }else{
            if(strstr($item_param,"'") == false){
                $item_param = "'$item_param'";
            }
            $condItem = "and item_container.\"it-codigo\" = $item_param";
        }
    }else{
        $condItem = '';
    }
    //echo "<h1>condItem:$condItem</h1>";

    if(  getTipoUsuarioCorrente() <> '' and getLoginCorrente() <> ''
        and isset([codRepIni]) ){
        $condRestrPI = getCondContainersRestrPI();
    }
    $agrup = '';
    if($ref_param <> ''){
        if(strstr($ref_param,',') <> false) {
            $condRef = " and item_container.\"cod-refer\" in ($ref_param) ";
        }else{
            $condRef = " and item_container.\"cod-refer\" = '$ref_param'";
        }
    }else{
        $condRef = '';
    }
    if($container_param <> ''){
        $condContainer = " and container.\"nr-container\" = $container_param ";
    }else{
        $condContainer = '';
    }

    if($logConsiderarVendas == 1){
        $cpVenda = 'item_container."qt-vendida"';
    }else{
        $cpVenda = '0';
    }
    $camposQts   = "   sum(item_container.\"qt-pedida\") as qt_pedida,
     sum(item_container.\"qt-vendida\") as qt_vendida,
     sum(item_container.\"qt-pedida\" * item_container.\"perc-dsp-venda\" / 100) as qt_disp,
     sum((item_container.\"qt-pedida\"  * item_container.\"perc-dsp-venda\" / 100)
      - $cpVenda ) as qt_saldo";
    $ordemRef = '';
    switch ($nivel){
        //retorna apenas um registro sumarizado
        case 'item':
            $agrup = ' group by item_container."it-codigo" ';
            $campos = " item_container.\"it-codigo\" as it_codigo,  $camposQts";
            break;
        case 'container':
            $agrup = ' group by container."nr-container", container."dt-prev-chegada" ';
            $campos = "container.\"nr-container\" as nr_container, container.\"dt-prev-chegada\" as dt_prev_chegada,
                      $camposQts";
            break;
        case 'referencia':
            $agrup = '';
            $campos = " container.\"nr-container\" as nr_container, container.\"dt-prev-chegada\" as dt_prev_chegada,
                       item_container.\"it-codigo\" as it_codigo, item_container.\"cod-refer\" as cod_refer,
                       item_container.\"qt-pedida\" as qt_pedida,
                       item_container.\"qt-vendida\" as qt_vendida,
                       (item_container.\"qt-pedida\" * item_container.\"perc-dsp-venda\" / 100) as qt_disp,
                       (item_container.\"qt-pedida\"  * item_container.\"perc-dsp-venda\" / 100
                       - $cpVenda ) as qt_saldo " ;
            break;
    }

    $tabela   = " pub.\"pp-container\" container, pub.\"pp-it-container\" item_container  ";

    $condicao = " container.\"nr-container\" = item_container.\"nr-container\"  
                 and container.situacao = 1 $condItem $condRef $condRestrPI $condContainer 
                 $filtro_param $agrup";
    $aRet = getDados("multi",$tabela,$campos,$condicao,'espec');
    //var_dump($aRet);
    //echo '<br>';
    if(is_array($aRet)){
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $qtPedida   =  $aRet[$i]['qt_pedida'];
            $qtDisp     =  $aRet[$i]['qt_disp'];
            $qtVendida  =  $aRet[$i]['qt_vendida'];
            $qtSaldo    =  $aRet[$i]['qt_saldo'];
            switch ($nivel)
            {
                case 'container': //neste nivel o itCodigo e o codRefer devem ser passados por parametro

                    $nrContainer   = $aRet[$i]['nr_container'];
                    $dtChegadaPrev = $aRet[$i]['dt_prev_chegada'];
                    $dtChegadaPrev = getQuinzenaMesAnoData($dtChegadaPrev);
                    if($dtChegadaPrev == '') {
                        $dtChegadaPrev = "$nrContainer(Não Informada)";
                    }
                    //echo "<h1>entrei no nivel do container-> $dtChegadaPrev</h1>";
                    $itCodigo        = $item_param;
                    $codRefer        = $ref_param;
                    //inserirLogDb('nivel item - codrefer passada para ordenação->',$codRefer,__FUNCTION__);
                    $ordemRef       = $codRefer <> '' ? getOrdemCodRefer($codRefer):'';
                    $qtCarrinhoGeral = getQtCarrinho($itCodigo,$codRefer,$nrContainer);
                    $qtNaoIntegrado  = getQtNaoIntegrada($itCodigo,$codRefer,$nrContainer);
                    $qtCarrinhoLoginCorrente = getQtCarrinhoLoginCorrente($itCodigo,$codRefer,$nrContainer);

                    break;
                case 'item':
                    $itCodigo      = $aRet[$i]['it_codigo'];
                    $nrContainer   = '';
                    $dtChegadaPrev = '';
                    $codRefer      = '';
                    $ordemRef       = $codRefer <> '' ? getOrdemCodRefer($codRefer):'';
                    //inserirLogDb('nivel container - codrefer passada para ordenação->',$codRefer,__FUNCTION__);

                    $qtCarrinhoGeral = getQtCarrinho($itCodigo,$codRefer,'*');
                    $qtNaoIntegrado  = getQtNaoIntegrada($itCodigo,$codRefer,'*');
                    $qtCarrinhoLoginCorrente = getQtCarrinhoLoginCorrente($itCodigo,$codRefer,'*');
                    break;
                case 'referencia':
                    $nrContainer   = $aRet[$i]['nr_container'];
                    $dtChegadaPrev = $aRet[$i]['dt_prev_chegada'];
                    $dtChegadaPrev = getQuinzenaMesAnoData($dtChegadaPrev);
                    $itCodigo      = $aRet[$i]['it_codigo'];
                    $codRefer      = $aRet[$i]['cod_refer'];
                    // inserirLogDb('nivel ref - codrefer passada para ordenação->',$codRefer,__FUNCTION__);
                    $ordemRef       = $codRefer <> '' ? getOrdemCodRefer($codRefer):'';
                    $qtCarrinhoGeral = getQtCarrinho($itCodigo,$codRefer,$nrContainer);
                    $qtNaoIntegrado  = getQtNaoIntegrada($itCodigo,$codRefer,$nrContainer);
                    $qtCarrinhoLoginCorrente = getQtCarrinhoLoginCorrente($itCodigo,$codRefer,$nrContainer);
                    break;
            }
            $qtCarrinho         = $qtCarrinhoLoginCorrente;
            $qtSaldo            = tratarNumero($qtSaldo);
            $qtCarrinhoGeral    = tratarNumero($qtCarrinhoGeral);
            $qtNaoIntegrado     = tratarNumero($qtNaoIntegrado);

            $qtSaldoComCarrinho = $qtSaldo - $qtCarrinhoGeral - $qtNaoIntegrado; // + $qtCarrinhoLoginCorrente;
            //echo "<h1>qtcarrinho: $qtCarrinho | qtsaldocomcarrinho = qt.saldo: $qtSaldo - qtcarrinhoGeral: $qtCarrinhoGeral -  qtnaointegrado: $qtNaoIntegrado</h1>";
            $descItem      = getDescrItem($itCodigo);
            if($descItem <> ''){
                $descItem = "$itCodigo - $descItem";
            }
            //echo '<h1>cheguei ate aqui 2</h1>';
            if($logBuscarPreco == true){
                $aPreco = buscarPreco('5',2,$itCodigo,$codRefer,$nrContainer);
                //echo "<h1>array preco PI</h1>";
                //var_dump($aPreco);
                $precoReal     = $aPreco[0]['vl_real'];
                $precoDolar    = $aPreco[0]['vl_dolar'];
                $idPreco    = $aPreco[0]['id'];
            } else{
                $precoReal     = 0;
                $precoDolar    = 0;
                $idPreco    = 0;
                $descPreco  = '';
            }
            //echo '<h1>cheguei ate aqui 3 </h1>';
            //echo "<h1>moeda dentro do getQtsPi: $moeda</h1>";
            $aRetorno[]= array(
                'cod_estabel' => '5',
                'desc_item' => $descItem,
                //'desc_preco' => $descPreco,
                'qt_pedida' => $qtPedida,
                'qt_disp' => $qtDisp,
                'qt_vendida' => $qtVendida,
                'qt_saldo' => $qtSaldo,
                'nr_container' => $nrContainer,
                'dt_prev_chegada' => $dtChegadaPrev,
                'it_codigo' => $itCodigo,
                'cod_refer' => $codRefer,
                'qt_carrinho' => $qtCarrinho,
                'qt_carrinho_geral' => $qtCarrinhoGeral,
                'qt_saldo_com_carrinho' => $qtSaldoComCarrinho,
                'id_preco' => $idPreco,
                'ordem_ref'=> $ordemRef,
                'preco_real' => $precoReal,
                'preco_dolar' => $precoDolar

            );
            $lAchou = true;

        }
        /*$qtPedida   = converterNumero($qtPedida);
        $qtDisp     = converterNumero($qtDisp);
        $qtVendida  = converterNumero($qtVendida);
        $qtSaldo    = converterNumero($qtSaldo);*/
    }
    if($lAchou == false){
        $aRetorno = '';
    }
    return $aRetorno;
}
function getQtsEPriChegPI($item, $ref='')
{
    $aRetorno    = array();
    $lAchou      = false;
    $qtTotPedida = 0;
    $qtTotDisp   = 0;
    $qtTotVend   = 0;
    $qtTotSaldo  = 0;
    $dtPriCheg   = '';
    $aQts = getQtsPI('container',$item,$ref);
    if(is_array($aQts)){
        $dtPriCheg = $aQts[0]['dt_prev_chegada'];
        $tam = count($aQts);
        for($i=0;$i<$tam;$i++){
            $qtTotPedida += $aQts[$i]['qt_pedida'];
            $qtTotDisp   += $aQts[$i]['qt_disp'];
            $qtTotVend   += $aQts[$i]['qt_vendida'];
            $qtTotSaldo  += $aQts[$i]['qt_saldo'];

        }
        /*$qtTotPedida = converterNumero($qtTotPedida);
        $qtTotDisp   = converterNumero($qtTotDisp);
        $qtTotVend   = converterNumero($qtTotVend);
        $qtTotSaldo  = converterNumero($qtTotSaldo);*/
        $aRetorno = array('qt_pedida' => $qtTotPedida,
            'qt_disp' => $qtTotDisp,
            'qt_vendida' => $qtTotVend,
            'qt_saldo' => $qtTotSaldo,
            'dt_prev_prim_chegada' => $dtPriCheg);
        $lAchou = true;
    }
    if($lAchou == false){
        $aRetorno = '';
    }
    return $aRetorno;
}
function getPriContainerCheg($item,$ref='')
{
    if($ref <> ''){
        $condRef = " and item.\"cod-refer\" = '$ref'";
    }else{
        $condRef = " ";
    }
    $container = 0;
    $aRet = getDados('unico',
        'pub."pp-container" container, pub."pp-it-container" item ',
        ' top 1 container."nr-container" as nr_container',
        " container.situacao = 1 and
                  container.\"nr-container\" = item.\"nr-container\" and  item.\"it-codigo\" = '$item' $condRef ");
    if(is_array($aRet)){
        $container = $aRet[0]['nr_container'];
    }


    return $container;

}

function getContainersCheg($item,$ref='')
{
    $container = array();
    if ($ref <> '') {
        $condRef = " and item.\"cod-refer\" = '$ref'";
    } else {
        $condRef = " ";
    }
    $aRet = getDados('multi',
        'PUB."pp-container" container, PUB."pp-it-container" item ',
        ' container."nr-container" as nr_container',
        "  
                     item.\"it-codigo\" = '$item' $condRef 
                   and container.\"nr-container\" = item.\"nr-container\"
                   and container.situacao = 1  order by container.\"dt-prev-chegada\" ",'espec');
    if (is_array($aRet)) {
        foreach ($aRet as $reg) {
            $container[] = $reg['nr_container'];
        }
        return $container;
    }
}

function getCondContainersRestrPI()
{
    $condicao = '';
    switch(getTipoUsuarioCorrente())
    {
        case getNumTpUsuarioAdmVendas():
        case getNumTpUsuarioAdm():
            $containerRestritos = buscarBloqueioGerencia(getLoginCorrente());
            break;
        default:
            $containerRestritos = buscarRestricaoContainerRepres([codRepIni]);
    }
    if($containerRestritos != ''){
        $condicao = " and container.\"nr-container\" not in(".$containerRestritos.")";
    }
    return $condicao;

}




/*function getPrecoLiquidaIma($item,$ref,$preco)
{
    $aLiquida = getPercLiquidaIma($item,$ref);
    $perc = $aLiquida['perc'];
    $id   = $aLiquida['id'];
    if($perc <> 0){
        $preco *=  1 + $perc /100;
    }
    $aRetorno = array('preco' => $preco, 'id' => $id);
    return $aRetorno;


}*/


/*function getFiltroLiquidaIma($tb,$estab=5)
{
    $cond = '';
    $listaItem = '';
    $listaRefer = '';
    $tabela   = " espec.pub.\"liquida-ima\"  promocao ";
    $campos   = " \"it-codigo\" as item, \"cod-refer\" as refer, \"cod-estabel\" as estab  ";
    $condicao = "  promocao.\"dt-inicio\" <= curdate()
    and (promocao.\"dt-final\" >= curdate() or promocao.\"dt-final\" is null)  ";
    $aRet = getDados('multi',$tabela,$campos,$condicao,'multi');
    if(is_array($aRet)){
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $refer = $aRet[$i]['refer'];
            $item  = $aRet[$i]['item'];
            $listaItem = util_incr_valor($listaItem,"'$item'",',');
            $listaRefer = util_incr_valor($listaRefer,"'$refer'",',');
            $incremento = " ( $tb.\"it-codigo\" = '$item' and $tb.\"cod-refer\" = '$refer') " ;
            $cond = util_incr_valor($cond,$incremento," OR ");
        }
    }
    if($cond <> ''){
        $cond = " ($cond) ";
    }
    return $cond;
}*/

function getQtLiquidaIma($itCod,$codRefer='',$qtMinkg=0,$qtMinMT=0,$estab=5)
{
    $qt   = 0;
    $aRet = getRefsItemComOutlet($itCod,$codRefer,$estab);
    $listaRefer = $aRet['lista'];
    $lTodasRefs = $aRet['log_todas_refers'];

    if($listaRefer <> '' or $lTodasRefs){
        $filtro = " and saldo.\"it-codigo\" = '$itCod' ";
    }else{
        $filtro = '';
    }
    if($filtro <> ''){
        $aSaldoEstoq = buscarSaldoEstoque($estab, //fixo, pois só existe uma empresa atualmente
            $filtro,
            false,
            1,
            true,
            false,
            $listaRefer);
        if(is_array($aSaldoEstoq)){
            $tam = count($aSaldoEstoq);
            for($i=0;$i<$tam;$i++){
                $itCorrente = $aSaldoEstoq[$i]['it_codigo'] ;
                $codRefer   = $aSaldoEstoq[$i]['cod_refer'];
                $qtSaldoVenda = $aSaldoEstoq[$i]['qt_saldo_venda'];
                $aUn  = buscarDadosItem($itCorrente);
                $un   = $aUn[0]['un'];
                switch (strtolower($un)) {
                    case 'm':
                    case 'mt':
                        $qtMin = $qtMinMT;
                        break;
                    case 'kg':
                        $qtMin = $qtMinkg;
                        break;
                }
                //echo "<h1>$qtSaldoVenda</h1>";
                //logDb('item | ref | qt'," $itCorrente | $codRefer | $qtSaldoVenda ",__FUNCTION__);
                if($qtSaldoVenda >= $qtMin ){
                    $qt += $qtSaldoVenda;
                }

                //echo "<h1>$qt</h1>";
            }
        }
    }else{
        $qt = 0;
    }

    return $qt;
}

/**
 * @param $aSaldoEstoq(array retornado pela função buscarSaldoEstoque)
 */

/*function sumarizarEstPorItem($aSaldoEstoq)
{
   $qtTotDisp = 0;
   $qtTotLiquidaIma = 0;
   $item = '';
   $itemAnt = '';
   $aSaldo = '';
   if(is_array($aSaldoEstoq)){
       $tam = count($aSaldoEstoq);
       for($i=0;$i<$tam;$i++){
            if($itemAnt <> '' and $item <> $itemAnt){
                $aSaldo['qt_disp'] = $qtTotDisp;
                $aSaldo['qt_liquida_ima'] = $qtTotLiquidaIma;
                $qtTotDisp = 0;
                $qtTotLiquidaIma = 0;
            }
            $item           = $aSaldoEstoq[$i]['it_codigo'];
            $qtDisp         = $aSaldoEstoq[$i]['qt_disp'];
            $qtLiquidaIma   = $aSaldoEstoq[$i]['qt_liquida_ima'];

            $qtTotDisp += $qtDisp;
            $qtTotLiquidaIma += $qtLiquidaIma;

            $itemAnt    = $item;
       }
   }
   return $aSaldo;
}*/

function getQtPorSitPed($sitPed, $itCodigo, $codRef='', $container='',$login='')
{

    $condSit = '';
    if($sitPed <> ''){
        if(strstr($sitPed,',') <> false){
            $condSit = " and ind_sit_ped_web in($sitPed)";
        }else{
            $condSit = " and ind_sit_ped_web = $sitPed ";
        }
    }

    if($login <> ''){
        $condLogin = " and login = '$login'";
    }else{
        $condLogin = '';
    }
    //$logCond = false;
    $condCompl = '';
    if($codRef <> ''){
        if(strstr($codRef,',') <> false){
            $condCompl = " and cod_refer in($codRef) ";
        }else{
            if(strstr($codRef,chr(39)) == false){
                $codRef = "'$codRef'";
            }
            $condCompl = " and cod_refer = $codRef";
        }
        //$logCond = true;
    }
    $container = strval($container);
    if(($container <> '' and $container <> '0') or $container == '*'){
        if($container == '*'){
            $condCompl = util_incr_valor($condCompl," AND nr_container <> 0 ",' ')  ;
        }else{
            $condCompl = util_incr_valor($condCompl," AND nr_container = $container ",' ')  ;
        }
    }else{
        $condCompl = util_incr_valor($condCompl," AND nr_container = 0 ",' ')  ;
    }
    /*
    if($logCond == true){
        $campo   = " qt_pedida ";
    }else{
        $campo   = " sum(qt_pedida) as qt_pedida ";
    }*/
    $campo   = " sum(qt_pedida) as qt_pedida ";
    $qt = 0;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.itens_ped_web itens , pub.peds_web ped ";

    //$condicao = " it_codigo = '$itCodigo' $condCompl   and  ind_sit_ped_web  <=2 ";
    //echo "<h1>condCompl: $condCompl </h1>";
    $condicao = " it_codigo = '$itCodigo' $condCompl  $condLogin  $condSit 
    and ped.ped_web_id = itens.ped_web_id ";
    //echo "<h1> Condição = $condicao </h1>";
    $aSaldo = getDados($tipo,$tabela,$campo,$condicao,'espec');
    if(is_array($aSaldo)){
        $qt =  $aSaldo[0]['qt_pedida'];
    }
    $qt = tratarNumero($qt);
    //echo "<h1> QT = $qt </h1>";
    return $qt;
}






/**
 * @param $item
 * @param string $ref
 * @param string $container(se informar '*' busca todos containers que não estão zerados
 * @return int
 */

function getSitsQtCarrinho()
{
    return '1,8,9';
    //1- em digitação 8- pend.aprov.ger. 9- solict.alteracao
}
function getQtCarrinho($itCodigo, $refParam='', $container='',$login='')
{
    //echo "<h1>ref.param: $refParam / Container = $container</h1>";
    $qt = getQtPorSitPed(getSitsQtCarrinho(),$itCodigo,$refParam,$container,$login);
    return $qt;
}

function getQtCarrinhoLoginCorrente($itCodigo, $refParam='', $container='')
{
    $qt = getQtCarrinho($itCodigo,$refParam,$container,getLoginCorrente());
    return $qt;
}

function getSitsQtNaoIntegrada(){
    return '2,5';
    //2 - efetivada 5- rejeitado
}
function getQtNaoIntegrada($itCodigo, $refParam='', $container='')
{

    $qt = getQtPorSitPed(getSitsQtNaoIntegrada(),$itCodigo,$refParam,$container,'');
    return $qt;
}

function getPrecoItemRefWp($filtroParam)
{
    $tb= '';
    $moeda = '';
    $aReg = getRegItemEstoqueWp(0,'nr_container,preco_prazo01,preco_prazo02,
    preco_prazo03,preco_prazo04,moeda,preco_liquida_ima',$filtroParam);

    if(is_array($aReg)){
        $tb .='<table class="table table-striped">
				<tr><td>Origem</td><td>À Vista</td><td>30 Dias</td><td>60 Dias</td><td>90 Dias</td><td>Moeda</td><tr>';
        $tam = count($aReg);
        for($i=0;$i<$tam;$i++){

            $origem     = $aReg[$i]['nr_container'];
            if($origem == 0){
                $origem = "Estoque";
            }

            $precoVistaBase = $aReg[$i]['preco_prazo01'];
            $preco30Base    = $aReg[$i]['preco_prazo02'];
            $preco60Base    = $aReg[$i]['preco_prazo03'];
            $preco90Base = $aReg[$i]['preco_prazo04'];
            $precoVista = formatarNumero(round($aReg[$i]['preco_prazo01'],2));
            $preco30    = formatarNumero(round($aReg[$i]['preco_prazo02'],2));
            $preco60    = formatarNumero(round($aReg[$i]['preco_prazo03'],2));
            $preco90    = formatarNumero(round($aReg[$i]['preco_prazo04'],2));

            $precoLiquidaIma = $aReg[$i]['preco_liquida_ima'];
            $moeda      = $aReg[$i]['moeda'];
            if($moeda == 1 or $moeda == 'real'){
                $moeda = "R$";
            }else{
                $moeda = "US$";
            }

            $tb .= "<tr><td>$origem</td><td>$precoVista</td><td>$preco30</td><td>$preco60</td>
                  <td>$preco90</td><td>$moeda</td></tr>";
            if($precoLiquidaIma > 0){
                $fator = $precoLiquidaIma / $preco90Base;
                $precoVistaLI = $precoVistaBase * $fator;
                $preco30LI    = $preco30Base    * $fator;
                $preco60LI    = $preco60Base    * $fator;
                $preco90LI    = $preco90Base    * $fator;
                $precoVistaLI   = formatarNumero(round($precoVistaLI,2),2);
                $preco30LI    = formatarNumero(round($preco30LI,2),2);
                $preco60LI    = formatarNumero(round($preco60LI,2),2);
                $preco90LI    = formatarNumero(round($preco90LI,2),2);
                $tb .= "<tr><td>Outlet</td><td>$precoVistaLI</td><td>$preco30LI</td><td>$preco60LI</td>
                  <td>$preco90LI</td><td>$moeda</td></tr>";
            }

        }
        $tb .="</table>";
    }
    $aRetorno = array('array' => $aReg,'tabela' => $tb);
    return $aRetorno;
}

function getPrecosSaldoItemRef($item,$ref,$container='',$tabPreco=1)
{
    $aTabPreco  = array();
    $lAchou = false;
    $perc		= 0;
    $idLiquida  = 0;
    $moeda      = '';
    $precoLiquidaIma    = 0;
    $logDivideComissao  = 0;
    $percComisVend      = 0;
    $percComisRepres    = 0;
    $listaRefs ='';
    if($container ==0 or $container==''){
        //preços do estoque
        $filtro = " and saldo.\"it-codigo\" = '$item' and saldo.\"cod-refer\"= '$ref' ";
        $listaRefs    = util_incr_valor($listaRefs,"'$ref'");
        //echo "<h1>Refs=$listaRefs</h1>";
        $aPrecoEstoque = buscarSaldoEstoque('5', $filtro,
            true,
            2, //1-item 2- item /ref
            true,
            false,$listaRefs,$tabPreco);
        inserirLogDb('array retorno buscarSaldoEstoque' ,$aPrecoEstoque,__FUNCTION__);
        //comentado, pois, a função buscarSaldoEstoque já está trazendo os dados
        //$aPrecoLiquidaIma = getPrecoLiquidaIma($item,$ref);



        if(is_array($aPrecoEstoque)){
            $tam = count($aPrecoEstoque);
            for($i=0;$i<$tam;$i++){
                //echo "<h1>qt.saldo venda:".$aPrecoEstoque[$i]['qt_saldo_venda']."</h1>";
                $aTabPreco[] = array('origem'=>'estoque',
                    'preco_real'    => $aPrecoEstoque[$i]['preco_real'],
                    'preco_dolar'   => $aPrecoEstoque[$i]['preco_dolar'],
                    'id_preco'  	=> $aPrecoEstoque[$i]['id_preco'],
                    'perc_liquida_ima' 	=> 0,
                    'id_liquida_ima'   	=> $aPrecoEstoque[$i]['id_liq'],
                    'qt_saldo'			=> $aPrecoEstoque[$i]['qt_saldo_venda'],
                    'preco_liquida_ima' => $aPrecoEstoque[$i]['preco_liquida_ima'],
                    'log_divide_comis'  => $aPrecoEstoque[$i]['log_divide_comis'],
                    'perc_comis_vend'   => $aPrecoEstoque[$i]['perc_comis_vend'],
                    'perc_comis_repres' => $aPrecoEstoque[$i]['perc_comis_repres']
                );
                inserirLogDb("resultado final array PE ",$aTabPreco,__FUNCTION__);
                $lAchou = true;
            }
        }
    }
    if($container <> 0 and $container <> ''){
        //buscar preços de container
        //echo "<h1>entrei no preço do container</h1>";
        $moeda = "R$";
        $aPrecoPi = getQtsPI('referencia', $item, $ref, $container ,'',true);
        if(is_array($aPrecoPi)){
            $tam = count($aPrecoPi);
            for($i=0;$i<$tam;$i++){
                /*if($aPrecoPi[$i]['moeda']){
                    $moeda = $aPrecoPi[$i]['moeda'];
                }else{
                    $moeda = "R$";
                }
                if($moeda == '' or $moeda == 0){
                    $moeda = "R$";
                }
                if($moeda == 'dolar'){
                    $moeda = "US$";
                }*/
                $aTabPreco[] = array('origem'=> $aPrecoPi[$i]['nr_container'],
                    'preco_real'        => $aPrecoPi[$i]['preco_real'],
                    'preco_dolar'       => $aPrecoPi[$i]['preco_dolar'],
                    'id_preco' 		    => $aPrecoPi[$i]['id_preco'],
                    'perc_liquida_ima'  => $perc,
                    'id_liquida_ima'    => $idLiquida,
                    'qt_saldo'		    => $aPrecoPi[$i]['qt_saldo_com_carrinho'],
                    'preco_liquida_ima' => $precoLiquidaIma,
                    'log_divide_comis'  => 0,
                    'perc_comis_vend'   => 0,
                    'perc_comis_repres' => 0
                );
                $lAchou = true;
                inserirLogDb("resultado final array  PI ",$aTabPreco,__FUNCTION__);
            }
        }
    }
    $tb = '';
    if($lAchou == false){
        $aTabPreco = '';
    }
    if(is_array($aTabPreco)){
        $tb .='<table class="table table-striped">
            <tr><td>Origem</td><td>À Vista</td><td>30 Dias</td><td>60 Dias</td><td>90 Dias</td><td>Moeda</td><tr>';

        $tam = count($aTabPreco);
        for($i=0;$i<$tam;$i++){
            $origem     = $aTabPreco[$i]['origem'];
            /*$precoVista = formatarNumero(round($aTabPreco[$i]['preco_vista'],2));
            $preco30    = formatarNumero(round($aTabPreco[$i]['preco_30'],2));
            $preco60    = formatarNumero(round($aTabPreco[$i]['preco_60'],2));
            $preco90    = formatarNumero(round($aTabPreco[$i]['preco_90'],2));*/

            $vlReal  = $aTabPreco[$i]['vl_real'];
            $vlDolar = $aTabPreco[$i]['vl_dolar'];

            if($vlReal > 0){
                $moeda = "R$";
                $linha = desenharLinhaTbPreco($origem,$vlReal,$moeda);
                $tb.=$linha;
            }
            if($vlDolar > 0){
                $moeda = "US$";
                $linha = desenharLinhaTbPreco($origem,$vlDolar,$moeda);
                $tb.=$linha;
            }
        }
        $tb .="</table>";
    }

    $aRetorno = array('array' => $aTabPreco,'tabela' => $tb);
    return $aRetorno;

}
function desenharLinhaTbPreco($origem,$vl,$moeda)
{
    $precoVista = formatarNumero(round(getPrecoPrazoInd(1,$vl),2));
    $preco30    = formatarNumero(round(getPrecoPrazoInd(30,$vl),2));
    $preco60    = formatarNumero(round(getPrecoPrazoInd(60,$vl),2));
    $preco90    = formatarNumero(round(getPrecoPrazoInd(90,$vl),2));

    $linha = "<tr><td>$origem</td><td>$precoVista</td><td>$preco30</td><td>$preco60</td>
                  <td>$preco90</td><td>$moeda</td></tr>";
    return $linha;
}


function ajustarEstoqueItemPedWeb($itemPedWebId,$item,$ref,$container,$qtPedido)
{
    $dif = 0;
    $aSaldo  = getPrecosSaldoItemRef($item,$ref,$container);
    $qtSaldo = $aSaldo['array']['qt_saldo'];
    if($qtSaldo  == '' ){
        $qtSaldo = 0;
    }
    if($qtSaldo - $qtPedido < 0){
        $cmd = " update itens_ped_web set qt_pedida = '$qtSaldo' 
                where item_ped_web_id = $itemPedWebId  ";
        sc_exec_sql($cmd,"especw");
        $dif = $qtSaldo - $qtPedido;
    }
    return $dif;
}

function ajustarEstoquePedWeb($pedWeb)
{
    $aPedido = getRegPedWeb($pedWeb,'nr_container');
    $container = $aPedido[0]['nr_container'];
    $aItens = getItensRefPedWeb();
    //var_dump($aItens);
    if(is_array($aItens)){
        $tam    = count($aItens);
        for($i=0;$i< $tam;$i++){
            $itCodigo = $aItens[$i]['it_codigo'];
            $codRefer = $aItens[$i]['cod_refer'];
            $qtPedida = $aItens[$i]['qt_pedida'];

        }
    }

}


function filtrarPorQtRef($aSaldo,$qtMinKg,$qtMinMt,$colunaSaldo)
{
    //echo "<h1>tadeu15</h1>";
    $aSaldoRetorno = array();
    if(is_array($aSaldo)){
        $tam = count($aSaldo);
        for($i=0;$i < $tam;$i++){

            $item       = $aSaldo[$i]['it_codigo'];
            $qt         = $aSaldo[$i][$colunaSaldo];
            $aUn        = buscarDadosItem($item);
            $un         = $aUn[0]['un'];

            switch(strtolower($un)){
                case 'm':
                case 'mt':
                    $qtMin = $qtMinMt;
                    break;
                case 'kg':
                    $qtMin = $qtMinKg;
                    break;
            }
            //echo "<h1>tadeu20</h1>";
            $qt    = tratarNumero($qt);
            $qtMin = tratarNumero($qtMin);
            if($qt < $qtMin and $qtMin > 0){
                unset($aSaldo[$i]);
            }
            //echo "<h1>tadeu30</h1>";
        }
    }
    if(is_array($aSaldo)){
        $i = 0;
        foreach($aSaldo as $reg){
            $aSaldoRetorno[$i] = $reg;
            $i++;
        }
    }
    //echo "<h1>tadeu40</h1>";
    return $aSaldoRetorno;
}
function filtrarPorQt($aSaldo,$qtMinKg,$qtMinMt,$colunaSaldo)
{
    $itemAnt = '';
    $qtTotal = 0;
    //$qt = 0;
    $aSaldoFilt = array();
    if (is_array($aSaldo)) {
        $tam = count($aSaldo);
        //var_dump($aSaldo);
        for ($i = 0; $i < $tam; $i++) {
            $item = $aSaldo[$i]['it_codigo'];
            $ref  = $aSaldo[$i]['cod_refer'];
            $qt   = $aSaldo[$i][$colunaSaldo];
            //echo "<h1>qt: $qt </h1>";
            $aUn  = buscarDadosItem($item);
            $un   = $aUn[0]['un'];


            if ($item <> $itemAnt and $itemAnt <> '') {
                //echo "<h1>Item: $item - qt.total: $qtTotal </h1>";
                $aSaldoFilt[$itemAnt]['qt'] = $qtTotal;
                $qtTotal = 0;
                $maiorPreco = 0;
            }
            switch (strtolower($un)) {
                case 'm':
                case 'mt':
                    $qtMin = $qtMinMt;
                    break;
                case 'kg':
                    $qtMin = $qtMinKg;
                    break;
            }
            if ($qt >= $qtMin) {
                $qtTotal += $qt;
            }
            //echo "<h1>Item anterior:$itemAnt -  Item: $item - ref: $ref - qt: $qt </h1>";

            if ($tam - 1 == $i and !isset($aSaldoFilt[$item])) {
                //echo "<h1>UNICO - Item: $item - qt.total: $qtTotal </h1>";
                $aSaldoFilt[$item]['qt'] = $qtTotal;
            }

            $itemAnt = $item;
        }
    }
    $aSaldo = unique_multidim_array($aSaldo, 'it_codigo');
    /*echo "array saldo filt<br>";
    var_dump($aSaldoFilt);
    echo "array saldo<br>";
    var_dump($aSaldo);*/
    if (is_array($aSaldo)) {
        $tam = count($aSaldo);
        foreach($aSaldo as $key => $reg) {
            $item = $reg['it_codigo'];
            if (isset($aSaldoFilt[$item])) {
                if($aSaldoFilt[$item]['qt'] <= 0 and $qtMin > 0 ){
                    unset($aSaldo[$key]);
                }else{
                    $aSaldo[$key][$colunaSaldo] = $aSaldoFilt[$item]['qt'];
                }
                /* $reg['cod_refer'] = '';
                 $reg['ordem_ref'] = '';*/
            }else{
                unset($aSaldo[$key]);
            }
        }
    }
    $aSaldo = array_values($aSaldo);
    //echo "<br>array saldo após ajuste<br>";
    //var_dump($aSaldo);
    return $aSaldo;
}
/* comentada, pois a Ana Flávia definiu que o preço a ser mostrado deve ser do proximo container a chegar.
function getMaiorPrecoContainer($item)
{
    $maiorPreco = 0;
    $aRegs = getContainersAbertoItem($item);
    //var_dump($aRegs);
    if(is_array($aRegs)){
        $tam = count($aRegs);
        for($i=0;$i<$tam;$i++){
            $container = $aRegs[$i]['nr_container'];
            $aPreco = buscarPreco('5',2,$gitem,'',$container);
            $preco = $aPreco[0]['preco_90'];
            //echo "<h1>$preco</h1>";
            if($preco > $maiorPreco){
                $maiorPreco = $preco;
            }
        }
    }
    return $maiorPreco;
}
*/



function getPriRefComSlEstPorItem($item)
{
    $codRefer = '';
    $aReg = getReg("med",'saldo-estoq','"it-codigo"',
        "'$item'",
        ' top 1 "cod-refer" as cod_refer',
        '"qtidade-atu" > 0 ');
    if(is_array($aReg)){
        $codRefer = $aReg[0]['cod_refer'];
    }
    return $codRefer;
}
function getSaldoItemRef($item,$ref,$container=0,$logConsideraCarrinho=1)
{
    $qtSaldo = 0;
    if($container == 0 or $container ==''){
        $filtro = " and saldo.\"it-codigo\" = '$item' and 
                       saldo.\"cod-refer\"= '$ref' ";
        $aSaldo = buscarSaldoEstoque('5', $filtro,
            false,
            2, //1-item 2- item /ref
            true,
            $logBuscarDadosItens=false);
    }else{
        $aSaldo = getQtsPI('referencia',$item,$ref,$container,'',false);
    }
    if(is_array($aSaldo)){
        if($container == 0 or $container == '' ){
            $qtSaldo =   $aSaldo[0]['qt_saldo_venda'];
        }else{
            $qtSaldo = $aSaldo[0]['qt_saldo_com_carrinho'];
        }
        if($logConsideraCarrinho == 0){
            $qtSaldo += $aSaldo[0]['qt_carrinho'];
        }
    }

    if($qtSaldo < 0){
        $qtSaldo = 0;
    }
    return $qtSaldo;


}
?>
