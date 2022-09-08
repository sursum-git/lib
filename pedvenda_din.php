<?php
//__NM__Pedido Venda Conexão Dinamica__NM__FUNCTION__NM__//
function buscarPedidosPE($filtroEmp='',$filtroComum='',$filtroEsp='',$filtroContainerPE='', $empresa)
{
	 
	 $agora = date("Y_m_d_G_i_s");
	 $conexaoagora = date("YmdGis");
	
	 //$fp = fopen("c:\\xampp\\htdocs\\vendermais\\".$agora."_".[usr_login]."_".$empresa."_buscarPedidosPE.html","a");
	 $html = "";
	 $aPedidos    = '';
	 $origem      = '';
	 $nrContainer = '';
	 $vlLiquido   = 0 ;
	 $vlDesconto  = 0 ; 
	 $prepPE      = '';
	 $naoAprovar = "";
	//PUB.\"ped-venda\".\"vl-liq-ped\",
	 $sql = "select PUB.\"ped-venda\".\"cod-estabel\", 
                   PUB.\"ped-venda\".\"nr-pedcli\" , 
                   PUB.\"ped-venda\".\"dt-implant\",
                   PUB.\"ped-venda\".\"cod-emitente\",
                   PUB.\"ped-venda\".\"nome-abrev\", 
                   PUB.\"ped-venda\".\"no-ab-reppri\",                  
                   PUB.\"ped-venda\".\"cod-sit-ped\",
                   PUB.\"ped-venda\".\"cod-sit-aval\",
				   PUB.\"ped-venda\".\"cod-sit-com\",
                   PUB.\"ped-venda\".\"tp-pedido\",                   
                   PUB.\"ped-venda\".\"vl-liq-abe\",
                   PUB.\"ped-venda\".\"val-desconto-total\",
                   PUB.\"ped-venda\".completo,
				   PUB.\"ped-venda\".\"cod-priori\",
				   PUB.\"ped-venda\".\"mo-codigo\"
			       from PUB.\"ped-venda\" 
                    " ;
	
	 // acrescenta os filtros passados como parametro 
     $sql  .= $filtroComum.$filtroEmp;	
	 //--echo "sql-$empresa:".$sql."</br>";
	//
	 switch($empresa)
     {
		 case "IMA":
	//	 
		 sc_select( $conexaoagora , $sql, "ems206imaPRO");
		    //echo "Buscando Dados IMA...</br>";
		 break;
		 case "MED":
		//
		 sc_select($conexaoagora , $sql, "ems206medPRO");	
		   //echo "Buscando Dados MED...</br>";
		 break;
	 }     
	 if ($$conexaoagora === false)
     {
         echo "Erro de acesso. Banco $empresa desconectado!";
		 $html .= "<h1> CONSEGUI acesso a base de dados</h1>";
     }
     else
     {
	    $html .= "<h1>CONSEGUI acesso a base de dados</h1>";
	    while (!$$conexaoagora->EOF)  
	    {  
			  // retorna dados da tabela ped-venda-ext
			  $aPedVendaExt = retornarDadosPedVendaExt($$conexaoagora->fields[0],$$conexaoagora->fields[1],$filtroEsp);
			  // verifica se o pedido foi achado com base na chave e nos filtros passados
			  if($aPedVendaExt != '' || $filtroEsp == '')
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
				  if($conexaoagora->fields[12] == 0) // pedido incompleto
				  {
					$aTotaisIncompleto =  calcularTotPedIncompleto($$conexaoagora->fields[0], $$conexaoagora->fields[1],$$conexaoagora->fields[4],$conexaoagora->fields[13]);
					$vlLiquido  = $aTotaisIncompleto[0]["liquido"] ;
					$vlDesconto = $aTotaisIncompleto[0]["desconto"];
				  }
				  else
				  {
					$vlLiquido  = $$conexaoagora->fields[10];
					$vlDesconto = $$conexaoagora->fields[11];  
					  
				  }
				  
			     //Guarda os dados no array de Pedidos para juntar com o PI posteriormente				
		         $aPedidos[] = array(
				                 "cod-estabel"        => $$conexaoagora->fields[0], 
			       				 "nr-pedcli"          => $$conexaoagora->fields[1], 
					   		     "dt-implant"         => sc_date_conv($ped->fields[2],"aaaa-mm-dd","mm/dd/aaaa"),
				                 "cod-emitente"       => $$conexaoagora->fields[3],
								 "nome-abrev"         => $$conexaoagora->fields[4],
								 "no-ab-reppri"       => $$conexaoagora->fields[5],
								 "cod-sit-ped"        => $$conexaoagora->fields[6],
								 "cod-sit-aval"       => $$conexaoagora->fields[7],
								 "cod-sit-preco"      => $$conexaoagora->fields[8],
								 "tp-pedido"          => strtoupper($$conexaoagora->fields[9]),
                                 "vl-liq-ped"         => $vlLiquido,
                                 "val-desconto-total" => $vlDesconto,
				                 "nr-container"       => $nrContainer,
				                 "preposto"           => @$prepPE,
				                 "origem"             => $origem,
					             "completo"			  => $$conexaoagora->fields[12],
					             "cod-priori"		  => $$conexaoagora->fields[13],
					             "mo-codigo"		  => $$conexaoagora->fields[14],
					 			 "l-nao-aprovar"	  => $naoAprovar
								); 
                    
	         }	    
			$ped->MoveNext();		  
        }
		$ped->Close();
     }	
	return $aPedidos ;
}
	
function buscarPedidosPI($filtroComum , $filtroPI, $desconsiderarPI)
{
	$aPedidos = '';
	
	
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
	 sc_select(pedidosPI, $sql,"ems206espPRO");
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
								 "cod-sit-preco"      => $pedidosPI->fields[8],
								 "tp-pedido"          => strtoupper($pedidosPI->fields[9]),
                                 "vl-liq-ped"         => $totPed * ( 1 - $pedidosPI->fields[10] / 100),
                                 "val-desconto-total" => $totPed * $pedidosPI->fields[10] / 100 ,
			                     "nr-container"       => $pedidosPI->fields[11],
			                     "preposto"           => $pedidosPI->fields[12],
			                     "origem"             => $pedidosPI->fields[13]
								); 
           $pedidosPI->MoveNext();	
	   }
	   $pedidosPI->Close();   
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
	sc_lookup(pedidosPIItem, $sql,"ems206espPRO");
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
	  
	sc_lookup(container, $sql,"ems206espPRO");
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
function retornarDadosPedVendaExt($codEstabel,$nrPedido,$filtroEsp)
{
	 /*************************************
     Objetivo: retornar os dados dos campos
     customizados da tabela ped-venda que fica
     na tabela ped-venda-ext
     ************************************/ 
	$prepostoExt = "";
	$origem = "";
	$naoAprovar = "";
	if($nrPedido ==" ")
	{
	   $nrPedido = 0;		
	}
	$sql = "select preposto, origem, \"nr-container\",\"l-nao-aprovar\" from PUB.\"ped-venda-ext\" 
                    where \"nr-pedido\" = '$nrPedido' and \"cod-estabel\" = '$codEstabel' ";
	$sql.= $filtroEsp;
	//--echo "sql RetonarDadosPedVendaExt:".$sql."</br>";
	$aPedVendaExt = '';  
	sc_lookup(pedvendaext, $sql,"ems206espPRO");
	if ({pedvendaext} === false)
    { 
       echo "Erro de acesso ESPEC - retornarDadosPedVendaExt. Mensagem = " . {pedvendaext_erro} ;
    }
    elseif (empty({pedvendaext}))
    {
        $aPedVendaExt = '';
    }
    else
    {
		@$prepostoExt   = {pedvendaext[0][0]};
		$origem         = {pedvendaext[0][1]};
		$nrContainer    = {pedvendaext[0][2]};		
		$naoAprovar     = {pedvendaext[0][3]};
        $aPedVendaExt[] = array("preposto" => @$prepostoExt,"origem" => $origem ,"nr-container" => $nrContainer, 
							    "l-nao-aprovar"=>$naoAprovar);
		
	}
	return $aPedVendaExt;
}

function calcularTotaisPedVenda2($empresa,$filtro)
{
	$conexaoagora = date("YmdGis");
	$aTotais = ''; 
	$sql   = "select sum(\"vl-tot-ped\"), count(\"nr-pedcli\"),sum(\"val-desconto-total\") from PUB.\"ped-venda\" ";
	$sql   =str_replace("''","'''",$sql);
	$sql  .= $filtro;
	
	switch($empresa)
	{
		case '1':
			sc_lookup($conexaoagora, $sql,"ems206imaPRO");		
		break;
		case '5':
			sc_lookup($conexaoagora, $sql,"ems206medPRO");
		break;
	}
	if ($$conexaoagora === false)
    { 
       echo "Erro de acesso  - calcularTotaisPedVenda. "  ;
    }
    elseif (empty($$conexaoagora))
    {
		$valor = 0;
		//$valor = number_format($valor, 2, ',', '.');
		$quant = 0;
		//$quant = number_format($quant, 2, ',', '.');
        $aTotais[] = array("valor" => $valor, "qte" => $quant);		
    }
    else
    {
		$valor = $$conexaoagora[0][0] + $$conexaoagora[0][2] ;		
		//$valor = number_format($valor, 2, ',', '.');		
		$quant = $$conexaoagora[0][1];			
		//$quant = number_format($quant, 2, ',', '.');
		
		$aTotais[] = array("valor" => $valor , "qte" => $quant);
	} 	
	return $aTotais;	
}
function calcularTotPedIncompleto($empresa, $nrPedcli,$nomeAbrev,$codPriori)
{
	$conexaoagora = date("YmdGis");
	$totalBruto    = 0;
	$totalLiquido  = 0;
	$totalDesconto = 0;
	$aTotais = "";
	$nomeAbrev = str_replace("'","''",$nomeAbrev);
	$sql = "
 			select sum(\"vl-preuni\" *  \"qt-pedida\"  * ( mod ( 20 - $codPriori , 10 )  /10 ) ) as desconto,
                    sum(\"vl-preuni\" *  \"qt-pedida\") as bruto
           from PUB.\"ped-item\" 
           where \"nr-pedcli\"   = '$nrPedcli'
           and   \"nome-abrev\" = '$nomeAbrev'

           ";
	
	switch($empresa)
	{
		case '1':
			sc_lookup($conexaoagora, $sql,"ems206imaPRO");		
		break;
		case '5':
			sc_lookup($conexaoagora, $sql,"ems206medPRO");
		break;
	}	
	if ($$conexaoagora === false)
    { 
       echo "Erro de acesso ou sintaxe." ;
    }
    elseif (empty($$conexaoagora))
    {
		$totalBruto 	= 0;
		$totalDesconto  = 0;
		$totalLiquido	= 0;		        
    }
    else
    {
		$totalBruto 	= $$conexaoagora[0][1];
		$totalDesconto  = $$conexaoagora[0][0];
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
	sc_select(pedidos, $sql,"ems206espPRO");		
	
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
    $conexaoagora = date("YmdGis");
	$total = 0;
	if($inNrPedidos == '')	
	   $inNrPedidos = 0;
	//echo "<h1>".$iNrPedidos."</h1>";
	$sql = 
	   "
        select sum(pub.\"ped-item\".\"qt-pedida\")
		from pub.\"ped-venda\" , pub.\"ped-item\"
	    where pub.\"ped-venda\".\"cod-sit-ped\" not in(3,6)
        and   pub.\"ped-venda\".\"nr-pedido\" in ($inNrPedidos)
        and   pub.\"ped-item\".\"nr-pedcli\"    =  pub.\"ped-venda\".\"nr-pedcli\"
		and   pub.\"ped-item\".\"nome-abrev\"   =  pub.\"ped-venda\".\"nome-abrev\"
		and   pub.\"ped-item\".\"cod-sit-item\" not in (3,6)   
        and   pub.\"ped-item\".\"it-codigo\"   = '$item'
        and   pub.\"ped-item\".\"cod-refer\"   = '$referencia'
	   ";		
	switch($empresa)
	{
		case '1':
			sc_lookup($conexaoagora, $sql,"ems206imaPRO");		
		break;
		case '5':
			sc_lookup($conexaoagora, $sql,"ems206medPRO");
		break;
	}
	if ($$conexaoagora === false)
    { 
       echo "Erro de acesso.";
    }
    elseif (empty($$conexaoagora))
    {
        $total = 0;
    }
    else
    {
        $total = $$conexaoagora[0][0];
	}	
	return $total;
}	
function buscarTotalPedPIItem($empresa,$nrContainer,$item,$referencia)
{
		$inPedidos = buscarPedidosContainer($empresa,$nrContainer);
		$total	   = calcularTotalPedPI($empresa,$inPedidos,$item,$referencia);
		return $total;
}	
?>
