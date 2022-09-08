<?php
//__NM__Pedido de Venda__NM__FUNCTION__NM__//
function buscarPedidosPE($filtroEmp='',$filtroComum='',$filtroEsp='',$filtroContainerPE='', $empresa)
{

    $agora = date("Y_m_d_G_i_s");
    //$fp = fopen("c:\\xampp\\htdocs\\vendermais\\".$agora."_".[usr_login]."_".$empresa."_buscarPedidosPE.html","a");
    $html = "";
    $aPedidos    = array();
    $lAchou      = false;
    $origem      = '';
    $nrContainer = '';
    $vlLiquido   = 0 ;
    $vlDesconto  = 0 ;
    $naoAprovar = "";
    $iCont = 0;
    $aPedConsider = array();
    //PUB.\"ped-venda\".\"vl-liq-ped\",
    $sql = "select pedido.\"cod-estabel\", 
                   pedido.\"nr-pedcli\" , 
                   pedido.\"dt-implant\",
                   pedido.\"cod-emitente\",
                   pedido.\"nome-abrev\", 
                   pedido.\"no-ab-reppri\",                  
                   pedido.\"cod-sit-ped\",
                   pedido.\"cod-sit-aval\",
				   pedido.\"cod-sit-com\",
                   pedido.\"tp-pedido\",                   
                   pedido.\"vl-liq-abe\",
                   pedido.\"val-desconto-total\",
                   pedido.completo,
				   pedido.\"cod-priori\",
				   pedido.\"mo-codigo\",
				   pedido.\"cod-sit-preco\",
                   ped_rep.\"nome-ab-rep\" as rep_nome,
                   ped_rep.\"perc-comis\"
                   from PUB.\"ped-venda\" pedido inner join PUB.\"ped-repre\" ped_rep on 
			       ped_rep.\"nr-pedido\" = pedido.\"nr-pedido\" 
                   where rep_nome <> 'fulano'
                   

                    " ;

    // "acrescenta os filtros passados como parametro";
    if(strstr($sql,'where') <> false){
        $operador = " and ";
    }else{
        $operador = " where ";
    }
    $sql  = util_incr_valor($sql, $filtroEmp.$filtroComum,$operador);
    //--echo "sql-$empresa:".$sql."</br>";
    //
    switch($empresa)
    {
        case "IMA":
            //
            sc_select(ped , $sql, "ima");
            //echo "Buscando Dados IMA...</br>";
            break;
        case "MED":
            //
            sc_select(ped , $sql, "med");
            //echo "Buscando Dados MED...</br>";
            break;
    }
    if ({ped} === false)
     {
         echo "Erro de acesso. Banco $empresa desconectado!";
         $html .= "<h1> CONSEGUI acesso a base de dados</h1>";
     }
     else
     {
         $html .= "<h1>CONSEGUI acesso a base de dados</h1>";
         while (!$ped->EOF)
         {
             //"retorna dados da tabela ped-venda-ext <br>";
             $aPedVendaExt = retornarDadosPedVendaExt($ped->fields[0],$ped->fields[1],$filtroEsp);
             // verifica se o pedido foi achado com base na chave e nos filtros passados
             if(is_array($aPedVendaExt) || $filtroEsp == '')
             {
                 if($aPedVendaExt != '')
                 {
                     @$prepPE     = $aPedVendaExt[0]["preposto"];
                     $origem      = $aPedVendaExt[0]["origem"];
                     $nrContainer = $aPedVendaExt[0]["nr-container"];
                     $naoAprovar  = $aPedVendaExt[0]["l-nao-aprovar"];
                 }
                 else
                 {
                     @$prepPE 	 = '' ;
                     $origem   	 = '' ;
                     $nrContainer = '' ;
                     $naoAprovar  = '' ;
                 }

                 if($ped->fields[12] == 0) // pedido incompleto
                 {
                     $aTotaisIncompleto =  calcularTotPedIncompleto($ped->fields[0], $ped->fields[1],$ped->fields[4],$ped->fields[13]);
                     $vlLiquido  = $aTotaisIncompleto[0]["liquido"] ;
                     $vlDesconto = $aTotaisIncompleto[0]["desconto"];
                 }

                 else
                 {
                     $vlLiquido  = $ped->fields[10];
                     $vlDesconto = $ped->fields[11];

                 }

                 if($ped->fields[6] == 3 /*faturado*/ or $ped->fields[6] == 6 /*cancelado*/ ){
                     $atotaisFaturados =calcularTotPedFatur($ped->fields[0],
                         $ped->fields[1],
                         $ped->fields[4],
                         $ped->fields[13]);
                     $vlLiquido  = $atotaisFaturados[0]["liquido"] ;
                     $vlDesconto = $atotaisFaturados[0]["desconto"];

                 }

                 $chave = $ped->fields[0]."_".$ped->fields[1];

                 /*if(isset($aPedConsider[$chave])){
                     inserirLogDb('entrei na chave',$chave,__FUNCTION__);
                     $iContAnt = $iCont - 1 ;
                     $aPedidos[$iContAnt]['nome_abrev_repres_2'] = $ped->fields[16];
                     $aPedidos[$iContAnt ]['perc_repres_2']       = $ped->fields[17];
                     continue;
                 }*/
                 inserirLogDb('icont - chave',"$iCont - $chave",__FUNCTION__);

                 //Guarda os dados no array de Pedidos para juntar com o PI posteriormente
                 $aPedidos[] = array(
                     "cod-estabel"        => $ped->fields[0],
                     "nr-pedcli"          => $ped->fields[1],
                     "dt-implant"         => sc_date_conv($ped->fields[2],"aaaa-mm-dd","mm/dd/aaaa"),
                     "cod-emitente"       => $ped->fields[3],
                     "nome-abrev"         => $ped->fields[4],
                     "no-ab-reppri"       => $ped->fields[5],
                     "cod-sit-ped"        => $ped->fields[6],
                     "cod-sit-aval"       => $ped->fields[7],
                     "cod-sit-preco"      => $ped->fields[15],
                     "tp-pedido"          => strtoupper($ped->fields[9]),
                     "vl-liq-ped"         => $vlLiquido,
                     "val-desconto-total" => $vlDesconto,
                     "nr-container"       => $nrContainer,
                     "preposto"           => @$prepPE,
                     "origem"             => $origem,
                     "completo"			  => $ped->fields[12],
                     "cod-priori"		  => $ped->fields[13],
                     "mo-codigo"		  => $ped->fields[14],
                     "l-nao-aprovar"      => $naoAprovar,
                     "nome_abrev_repres"  => $ped->fields[16],
                     "perc_repres"		  => $ped->fields[17]
                     /* ,
                      "cod-sit-com"		  => $ped->fields[8]*/
                 );
                 $lAchou = true;
                 //$aPedConsider[$chave] = '';
                 //$iCont++;
             }
             $ped->MoveNext();

         }
         $ped->Close();
     }
	//
	//$html .= "</br>primeiro pedido:".$aPedidos[0]["nr-pedcli"]." - estab:".$aPedidos[0]["cod-estabel"];
	//$escreve = fwrite($fp, $html);
	//fclose($fp);
    if($lAchou == false){
        $aPedidos = '';
    }
	return $aPedidos ;
}

function buscarPedidosPI($filtroComum , $filtroPI, $desconsiderarPI)
{
    $aPedidos = array();
    $lAchou = false;


    $sql = " select PUB.\"pp-ped-venda\".\"cod-estabel\", 
                   PUB.\"pp-ped-venda\".\"nr-pedcli\" , 
                   PUB.\"pp-ped-venda\".\"dt-implant\",
                   PUB.\"pp-ped-venda\".\"cod-emitente\",
                   PUB.\"pp-ped-venda\".\"nome-abrev\",
                   PUB.\"pp-ped-venda\".\"no-ab-reppri\",                  
                   PUB.\"pp-ped-venda\".\"cod-sit-ped\",
                   '0',
				   '0',
                   PUB.\"pp-ped-venda\".\"tp-pedido\",
                   PUB.\"pp-ped-venda\".\"des-pct-desconto-inform\",
                   PUB.\"pp-ped-venda\".\"nr-container\",
                   PUB.\"pp-ped-venda\".preposto,
                   PUB.\"pp-ped-venda\".origem 
              from PUB.\"pp-ped-venda\",PUB.\"pp-container\"            
                        
            ";

    // adiciona os filtros passados como parametro
    $pedidosPEPI = ''; //" and \"nr-pedcli\" not in ($desconsiderarPI)";
    $joinPPContainer = " and \"pp-container\".situacao = '1' ";
    $sql .= $filtroComum.$filtroPI.$pedidosPEPI.$joinPPContainer ;
    //echo "sql pi:".$sql;
    //--echo "Buscando Dados PI...</br>";
    sc_select(pedidosPI, $sql,"espec");
    while (!$pedidosPI->EOF)
    {
        // busca o total do pedido
        $totPed = retornarTotPedPI($pedidosPI->fields[1], $pedidosPI->fields[4]);

        //Guarda os dados no array de Pedidos para juntar com o PE
        $aPedidos[] = array(
            "cod-estabel"        => $pedidosPI->fields[0],
            "nr-pedcli"          => $pedidosPI->fields[1],
            "dt-implant"         => $pedidosPI->fields[2],
            "cod-emitente"       => $pedidosPI->fields[3],
            "nome-abrev"         => $pedidosPI->fields[4],
            "no-ab-reppri"       => $pedidosPI->fields[5],
            "cod-sit-ped"        => $pedidosPI->fields[6],
            "cod-sit-aval"       => $pedidosPI->fields[7],
            "cod-sit-preco"      => $pedidosPI->fields[15],
            "tp-pedido"          => strtoupper($pedidosPI->fields[9]),
            "vl-liq-ped"         => $totPed * ( 1 - $pedidosPI->fields[10] / 100),
            "val-desconto-total" => $totPed * $pedidosPI->fields[10] / 100 ,
            "nr-container"       => $pedidosPI->fields[11],
            "preposto"           => $pedidosPI->fields[12],
            "origem"             => $pedidosPI->fields[13]
        );
        $lAchou = true;
        $pedidosPI->MoveNext();
    }
    $pedidosPI->Close();
    if($lAchou == false){
        $aPedidos = '';
    }
    return $aPedidos;
}

function retornarTotPedPI($nrPedCli, $nomeAbrev)
{   /*************************************
Objetivo: retornar o total do valor
do pedido, desconsiderando os itens
cancelados.
 ************************************/
    $sql = "
            select sum(\"vl-preuni\" * \"qt-pedida\") as totped
            from PUB.\"pp-ped-item\" 
            where \"nr-pedcli\" = '$nrPedCli'
            and   \"nome-abrev\" = '$nomeAbrev' 
            and   \"sit-item\" = 1 
          ";
    //echo "sql retornarTotaPedPI:".$sql."</br>";
    $totPed = 0;
    sc_lookup(pedidosPIItem, $sql,"espec");
    if ({pedidosPIItem} === false)
    {
        echo "Erro de acesso. Mensagem = " . {pedidosPIItem_erro};
    }
    elseif (empty({pedidosPIItem}))
    {
        $totPed = 0;
    }
    else
    {
        $totPed = {pedidosPIItem[0][0]};
	}
	return $totPed;
}
function retornarContainerPE($nrPedcli, $nomeAbrev,$condicao)
{
    /*************************************
    Objetivo: retornar o container caso
    exista um pedido de venda PI para os
    parametros passados.
     ************************************/
    $container = '';
    $sql = "select \"nr-container\" from PUB.\"pp-ped-venda\" 
                    where \"nr-pedcli\" = '$nrPedcli' and \"nome-abrev\" = '$nomeAbrev' ";
    $sql .= $condicao;
    //--echo "sql-retornarContainerPE".$sql."</br>" ;

    sc_lookup(container, $sql,"espec");
    if ({container} === false)
    {
        echo "Erro de acesso. Mensagem = " . {container_erro};
    }
    elseif (empty({container}))
    {
        $container = '';
    }
    else
    {
        $container = {container[0][0]};
	}
	return $container;
}
function retornarDadosPedVendaExt($codEstabel,$nrPedido,$filtroEsp='')
{
    /*************************************
    Objetivo: retornar os dados dos campos
    customizados da tabela ped-venda que fica
    na tabela ped-venda-ext
     ************************************/
    $prepostoExt = "";
    $origem = "";
    $naoAprovar = "";
    $aPedVendaExt = array();
    //echo "<h1>$nrPedido</h1>";
    if($nrPedido == "")
    {
        $nrPedido = 0;
    }
    //echo "<h1>antes do erro - 2.9</h1>";
    $aReg = getReg('espec','ped-venda-ext','"cod-estabel","nr-pedido"',
        "'$codEstabel','$nrPedido'",
        'preposto, origem,"nr-container","l-nao-aprovar","tp-frete","tp-pagto",tb_preco_id',$filtroEsp);
	
    if($aReg <> ''){
		$prepostoExt    = $aReg[0]['preposto'];
		$origem         = $aReg[0]['origem'];
		$nrContainer    = $aReg[0]['"nr-container"'];
		$naoAprovar     = $aReg[0]['"l-nao-aprovar"'];
		$tpFrete        = $aReg[0]['"tp-frete"'];
		$tbPreco        = $aReg[0]['tb_preco_id'];
		$aPedVendaExt[] = array("preposto" => $prepostoExt,"origem" => $origem ,"nr-container" => $nrContainer,
            "l-nao-aprovar"=>$naoAprovar,'tp-frete' => $tpFrete, "tb_preco_id" =>$tbPreco);
	}else{
		$aPedVendaExt = '';
	}
	return $aPedVendaExt;
    //return $aReg;
}
function getDescTpFrete($tpFrete){
    switch($tpFrete){
        case 1:
            $desc ='CIF' ;
            break;
        case 2:
            $desc ='CIF PARCIAL' ;
            break;
        case 1:
            $desc ='FOB' ;
            break;

    }
}

function calcularTotaisPedVenda($empresa,$filtro)
{
    $aTotais = array();
    $sql   = "select sum(\"vl-tot-ped\"), count(\"nr-pedcli\"),sum(\"val-desconto-total\") from PUB.\"ped-venda\" ";
    $sql   =str_replace("''","'''",$sql);
    $sql  .= $filtro;
    //echo "sql calculartotaispedvenda:".$sql."</br>";
    switch($empresa)
    {
        case '1':
            sc_lookup(total, $sql,"ima");
            break;
        case '5':
            sc_lookup(total, $sql,"med");
            break;
    }
    if ({total} === false)
    {
        echo "Erro de acesso  - calcularTotaisPedVenda. Mensagem = " . {total_erro} ;
    }
    elseif (empty({total}))
    {
        $valor = 0;
        //$valor = number_format($valor, 2, ',', '.');
        $quant = 0;
        //$quant = number_format($quant, 2, ',', '.');
        $aTotais[] = array("valor" => $valor, "qte" => $quant);
    }
    else
    {
        $valor = {total[0][0]} + {total[0][2]} ;
		//$valor = number_format($valor, 2, ',', '.');
		$quant = {total[0][1]};
		//$quant = number_format($quant, 2, ',', '.');

		$aTotais[] = array("valor" => $valor , "qte" => $quant);
	}
	return $aTotais;
}
function calcularTotPedIncompleto($empresa, $nrPedcli,$nomeAbrev,$codPriori)
{
    $totalBruto    = 0;
    $totalLiquido  = 0;
    $totalDesconto = 0;
    $aTotais = array();
    $nomeAbrev = str_replace("'","''",$nomeAbrev);
    $sql = "
 			select sum(\"vl-preuni\" *  \"qt-pedida\"  * ( mod ( 20 - $codPriori , 10 )  /10 ) ) as desconto,
                    sum(\"vl-preuni\" *  \"qt-pedida\") as bruto
           from PUB.\"ped-item\" 
           where \"nr-pedcli\"   = '$nrPedcli'
           and   \"nome-abrev\" = '$nomeAbrev'
           and \"cod-sit-item\" <> 6
           ";
    //--echo "sql calcularTotPedIncompleto:".$sql;
    switch($empresa)
    {
        case '1':
            sc_lookup(total, $sql,"ima");
            break;
        case '5':
            sc_lookup(total, $sql,"med");
            break;

    }
    if ({total} === false)
    {
        echo "Erro de acesso ou sintaxe. Mensagem = " . {total_erro} ;

    }
    elseif (empty({total}))
    {
        $totalBruto 	= 0;
        $totalDesconto  = 0;
        $totalLiquido	= 0;

    }
    else
    {
        $totalBruto 	= {total[0][1]};
		$totalDesconto  = {total[0][0]};
		$totalLiquido	= $totalBruto - $totalDesconto;


	}
	//echo "totais incompleto - pedido:".$nrPedcli." vl-liquido:".$totalLiquido.':valor desc:'.$totalDesconto.'</br>';
	$aTotais[] = array("bruto" => $totalBruto,
        "desconto" => $totalDesconto,
        "liquido" => $totalLiquido);
	return $aTotais;


}

function calcularTotPedFatur($empresa, $nrPedcli,$nomeAbrev,$codPriori)
{
    $totalBruto    = 0;
    $totalLiquido  = 0;
    $totalDesconto = 0;
    $aTotais = array();
    $nomeAbrev = str_replace("'","''",$nomeAbrev);
    $sql = "
 			select sum(\"vl-preori\" *  \"qt-pedida\"  * ( mod ( 20 - $codPriori , 10 )  /10 ) ) as desconto,
                    sum(\"vl-preori\" *  \"qt-pedida\") as bruto
           from PUB.\"ped-item\" 
           where \"nr-pedcli\"   = '$nrPedcli'
           and   \"nome-abrev\" = '$nomeAbrev'

           ";
    //echo "sql calcularTotPedIncompleto:".$sql;
    switch($empresa)
    {
        case '1':
            sc_lookup(total, $sql,"ima");
            break;
        case '5':
            sc_lookup(total, $sql,"med");
            break;
    }
    if ({total} === false)
    {
        echo "Erro de acesso ou sintaxe. Mensagem = " . {total_erro} ;
    }
    elseif (empty({total}))
    {
        $totalBruto 	= 0;
        $totalDesconto  = 0;
        $totalLiquido	= 0;
    }
    else
    {
        $totalBruto 	= {total[0][1]};
		$totalDesconto  = {total[0][0]};
		$totalLiquido	= $totalBruto - $totalDesconto;

	}
	//--echo "totais incompleto - pedido:".$nrPedcli." vl-liquido:".$totalLiquido.':valor desc:'.$totalDesconto.'</br>';
	$aTotais[] = array("bruto" => $totalBruto,
        "desconto" => $totalDesconto,
        "liquido" => $totalLiquido);
	return $aTotais;


}

function buscarPedidosContainer($empresa,$nrContainer)
{
    $inPedidos = '';
    $sql = "select \"nr-pedido\" from pub.\"ped-venda-ext\"
			where \"nr-container\" = $nrContainer
			and \"cod-estabel\" = '$empresa'
		   "	;
    //echo 'sql - buscarPe3didosContainer:'.$sql;
    sc_select(pedidos, $sql,"espec");

    while (!$pedidos->EOF)
    {
        if($inPedidos == '')
            $inPedidos = $pedidos->fields[0];
        else
            $inPedidos .= ','.$pedidos->fields[0];

        $pedidos->MoveNext();
    }
    $pedidos->Close();
    return $inPedidos;
}

function calcularTotalPedPI($empresa,$inNrPedidos,$item,$referencia)
{
    $total = 0;
    if($inNrPedidos == '')
        $inNrPedidos = 0;
    //echo "<h1>".$iNrPedidos."</h1>";
    $sql =
        "
        select sum(pub.\"ped-item\".\"qt-pedida\")
		from pub.\"ped-venda\" , pub.\"ped-item\"
	    where pub.\"ped-venda\".\"nr-pedido\" in ($inNrPedidos)
        and   pub.\"ped-item\".\"nr-pedcli\"    =  pub.\"ped-venda\".\"nr-pedcli\"
		and   pub.\"ped-item\".\"nome-abrev\"   =  pub.\"ped-venda\".\"nome-abrev\"
		and   pub.\"ped-item\".\"cod-sit-item\" <> 6   		
        and   pub.\"ped-item\".\"it-codigo\"   = '$item'
        and   pub.\"ped-item\".\"cod-refer\"   = '$referencia'
	   ";
    //and   pub.\"ped-item\".\"cod-sit-item\" not in (3,6)
    //pub.\"ped-venda\".\"cod-sit-ped\" not in(3,6)        and

    switch($empresa)
    {
        case '1':
            sc_lookup(totped, $sql,"ima");
            break;
        case '5':
            sc_lookup(totped, $sql,"med");
            break;
    }
    if ({totped} === false)
    {
        echo "Erro de acesso. Mensagem = " . {totped_erro};
    }
    elseif (empty({totped}))
    {
        $total = 0;
    }
    else
    {
        $total = {totped[0][0]};
	}
	return $total;
}
function buscarTotalPedPIItem($empresa,$nrContainer,$item,$referencia)
{
    $inPedidos = buscarPedidosContainer($empresa,$nrContainer);
    $total	   = calcularTotalPedPI($empresa,$inPedidos,$item,$referencia);
    return $total;
}
function calcularPercDescPedido($codPrioridade)
{
    $perc = 20 - $codPrioridade;
    $retorno = ($perc % 10)/ 10;
    return $retorno;
}
function getRegPedVenda($nrPedido,$campos='',$filtroCompl='')
{
    $aReg = getReg('med','ped-venda','"nr-pedido"',
                    $nrPedido,$campos,$filtroCompl);
    return $aReg;
}

function getCampoPedVendaExt($codEstabel,$nrPedido,$campo){


    $cp = '';
    $tipo     = "unico";
    $tabela   = " pub.\"ped-venda-ext\" ";
    //$campos   = "\"l-nao-aprovar\" as nao_aprovar" ;
    $condicao = "  \"cod-estabel\" = $codEstabel and \"nr-pedido\" = $nrPedido";
    $conexao  = "espec";
    $aDados  = getDados($tipo,$tabela,$campo,$condicao,$conexao);
    if(is_array($aDados)){
        $cp = $aDados[0][$campo];

    }
    return $cp;
}
function getDadosPedVendaExt($codEstabel,$nrPedido){

    $naoAprovar = "";
    $tipo     = "unico";
    $tabela   = " pub.\"ped-venda-ext\" ";
    $campos   = "\"l-nao-aprovar\" as nao_aprovar";
    $condicao = "  \"cod-estabel\" = $codEstabel and \"nr-pedcli\" = $nrPedido";
    $conexao  = "espec";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $$naoAprovar = $aDados[0]['nao_aprovar'];

    }
    return $naoAprovar;

}

function getPedPercComis($nrPedido,$nomeRep){

    $percComis  = 0;
    $tipo     = "unico";
    $tabela   = " pub.\"ped-repre\" ";
    $campos   = "\"perc-comis\" as perc_comis" ;
    $condicao = "\"nr-pedido\" = $nrPedido and \"nome-ab-rep\" = '$nomeRep'";
    $conexao  = "med";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $percComis = $aDados[0]['perc_comis'];
        //var_dump($aDados);
    }
    return $percComis;
}

function getListaPedidosFiltroExt($filtroEsp,$filtroComum){

    $lista = '';
    $aDados = getDados('multi','pub."ped-venda-ext" ped_ext','ped_ext."nr-pedido" as pedido',
        " 1=1 $filtroEsp and $filtroComum",'multi',
        " inner join med.pub.\"ped-venda\" pedido on pedido.\"nr-pedido\" = ped_ext.\"nr-pedido\" 
               inner join med.pub.\"ped-repre\" ped_rep on  pedido.\"nr-pedido\" = ped_rep.\"nr-pedido\" ");
    //var_dump($aDados);
    if(is_array($aDados)){
        foreach($aDados as $reg){
            $lista = util_incr_valor($lista,$reg['pedido']);
            //echo "<h2>Passei Aqui!!!</h2>";

        }
    }

    return $lista;
}

function getListaPedidosFiltroPedRepre($filtro){

    $lista = '';
    $aDados = getDados('multi','pub."ped-repre" ped_rep','distinct ped_rep."nr-pedido" as pedido',
        "$filtro",'med',
        " inner join pub.\"ped-venda\" pedido on pedido.\"nr-pedido\" = ped_rep.\"nr-pedido\"");
    var_dump($aDados);
    inserirLogDb('filtro comum',$filtro   ,__FUNCTION__);
    inserirLogDb('array de retorno',$aDados,__FUNCTION__);
    if(is_array($aDados)){
        foreach($aDados as $reg){
            $lista = util_incr_valor($lista,$reg['pedido']);
            echo "<h2>Passei Aqui!!!</h2>";
        }
    }

    return $lista;
}


function getListaNfFiltroPedRepre($filtroComum){

    $lista = '';
    if(substr($filtroComum,0,4) == ' and'){
        $filtroComum = substr($filtroComum,5,strlen($filtroComum)) ;
    }

    $aDados = getDados('multi','pub."ped-repre" ped_rep',
        'distinct nf."nr-nota-fis" as nf',
        "$filtroComum",'med',
        " inner join pub.\"ped-venda\" pedido on pedido.\"nr-pedido\" = ped_rep.\"nr-pedido\"
                inner join pub.\"nota-fiscal\" nf on 
                pedido.\"nome-abrev\" = nf.\"nome-ab-cli\"
                and pedido.\"nr-pedcli\" = nf.\"nr-pedcli\" inner join pub.\"natur-oper\" nat on
                nf.\"nat-operacao\" = nat.\"nat-operacao\" 
                 and nat.\"tp-rec-desp\" = 1 and nat.\"tipo-compra\" <> 3");
    //var_dump($aDados);
    if(is_array($aDados)){
        foreach($aDados as $reg){
            $lista = util_incr_valor($lista,"'".$reg['nf']."'");
            //echo "<h2>Passei Aqui!!!</h2>";
        }
    }
    if($lista == ''){
        $lista ="'0'";
    }


    return $lista;
}

function getListaDevNfFiltroPedRepre($filtroComum){
    //echo "<h2>filtro = $filtroComum</h2>";
    $lista = '';
    if(substr($filtroComum,0,4) == ' and'){
        $filtroComum = substr($filtroComum,5,strlen($filtroComum)) ;
    }

    $aDados = getDados('multi','pub."ped-repre" ped_rep','distinct venda."nr-nota-fis" as venda',
        "$filtroComum",'med',
        " inner join pub.\"ped-venda\" ped on ped.\"nr-pedido\" = ped_rep.\"nr-pedido\"
                inner join pub.\"nota-fiscal\" venda on 
                ped.\"nome-abrev\" = venda.\"nome-ab-cli\"
                and ped.\"nr-pedcli\" = venda.\"nr-pedcli\" inner join pub.\"natur-oper\" nat on
                venda.\"nat-operacao\" = nat.\"nat-operacao\" inner join pub.\"devol-cli\" relacto_venda on
                venda.\"nat-operacao\" =  relacto_venda.\"nat-operacao\" inner join PUB.\"docum-est\" dev on 
                relacto_venda.\"serie-docto\"     = dev.\"serie-docto\" and
                 relacto_venda.\"nro-docto\"       = dev.\"nro-docto\"
                 and nat.\"tp-rec-desp\" = 1 and nat.\"tipo-compra\" <> 3");
    //var_dump($aDados);
    if(is_array($aDados)){
        foreach($aDados as $reg){
            $lista = util_incr_valor($lista,"'".$reg['nf']."'");
            //echo "<h2>Passei Aqui!!!</h2>";
        }
    }
    if($lista == ''){
        $lista ="'0'";
    }


    return $lista;
}

function getDadosPedRepre($pedido){

    $aRet = array();
    $lAchou = false;
    $aDados = getDados('multi','pub."ped-repre"','"nr-pedido","nome-ab-rep","perc-comis"',
        "pub.\"ped-repre\".\"nr-pedido\" = $pedido and pub.\"ped-repre\".\"nome-ab-rep\" <> 'fulano'",'med');
    //var_dump($aDados);
    if(is_array($aDados)){
        foreach($aDados as $reg){
            $lAchou =true;
            $ped       = $reg['"nr-pedido"'];
            $nomeRep   = $reg['"nome-ab-rep"'];
            $percComis = $reg['"perc-comis"'];
            $aRet[] = array(

                'rep' =>  $nomeRep,
                'comis' => $percComis,
                'ped' => $ped

            );
        }
    }
    if($lAchou == false){
        $aRet = '';
    }

    return $aRet;
}


/*inner join pub."ped-repre" ped_repre
on pedido."nr-pedido" = ped_repre."nr-pedido"
where ped_repre."nome-ab-rep" <> 'fulano'*/

function getVlsPedVenda($sitPed,$completo,$estab,$nrPedido,$nomeAbrev,$priori,$vlLiqAbe,$valDescTot){


    $vlLiquido  = $vlLiqAbe;
    $vlDesconto = $valDescTot;

    if($completo == 0) // pedido incompleto
    {
        $aTotaisIncompleto =  calcularTotPedIncompleto($estab, $nrPedido,$nomeAbrev,$priori);
        $vlLiquido  = $aTotaisIncompleto[0]["liquido"] ;
        $vlDesconto = $aTotaisIncompleto[0]["desconto"];
    }

    if($sitPed == 3 /*faturado*/ or $sitPed == 6 /*cancelado*/ ){
        $atotaisFaturados =calcularTotPedFatur($estab,$nrPedido,
            $nomeAbrev,
            $priori);
        $vlLiquido  = $atotaisFaturados[0]["liquido"] ;
        $vlDesconto = $atotaisFaturados[0]["desconto"];

    }

    return array('vl_liq' => $vlLiquido, 'vl_desc'=> $vlDesconto);



}

function getTabPrecoPedVendaExt($codEstabel,$nrPedido){

    $tbPreco  = "";
    $tipo     = "unico";
    $tabela   = " pub.\"ped-venda-ext\" ";
    $campos   = "tb_preco_id";
    $condicao = "  \"cod-estabel\" = $codEstabel and \"nr-pedcli\" = $nrPedido";
    $conexao  = "espec";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $tbPreco = $aDados[0]['tb_preco'];

    }
    return $tbPreco;

}
function getDadosPedVenda($nrPedido,$campos='')
{
    $tbPreco  = "";
    $tipo     = "unico";
    $tabela   = " pub.\"ped-venda\" ";
    $condicao = "   \"nr-pedido\" = $nrPedido";
    $conexao  = "med";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    return $aDados;
}
function getCondPagtoPedVenda($nrPedido)
{
    $cond = '';
    $aReg = getDadosPedVenda($nrPedido,'"cod-cond-pag" as cond');
    if(is_array($aReg)){
        $cond = $aReg[0]['cond'];
    }
    return $cond;
}
function getTpPagtoPedVendaExt($nrPedido)
{
    $tpPagto  = "";
    $tipo     = "unico";
    $tabela   = " pub.\"ped-venda-ext\" ";
    $campos   = "\"tp-pagto\" as tp_pagto" ;
    $condicao = " \"nr-pedido\" = $nrPedido";
    $conexao  = "espec";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $tpPagto = $aDados[0]['tp_pagto'];

    }
    return $tpPagto;
}

?>
