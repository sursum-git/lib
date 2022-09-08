<?php
//__NM__Condição Especial para pedidos com a função sc_connect_new__NM__FUNCTION__NM__//
function retornarCondPed($nrPedido,$condPagto,$tipoCondPed="PE",$empresa)
{
  	$retorno = '';
	if($condPagto == 0)
	{	
	   switch($tipoCondPed)
	   {
		   case "PE":
		   $sql = "select \"nr-dias-venc\" from PUB.\"cond-ped\" where \"nr-pedido\" = $nrPedido ";
	       //sc_select(condped , $sql, "ems206medPRO");
		   switch($empresa)
           {
		     case "IMA":
		      sc_select(condped , $sql, "ems2IMA");
		      //echo "Conectando o banco Ima - Buscando Faturamento</br>";
		     break;
		     case "MED":
		      sc_select(condped , $sql, "ems2MED");	
		      //echo "Conectando o banco Med - Buscando Faturamento</br>";
		     break;
	       } 		   
		   
		   break;
		   case "PI":
		   $sql = "select \"nr-dias-venc\" from PUB.\"pp-cond-ped\" where \"nr-pedido\" = $nrPedido ";
	       sc_select(condped , $sql, "ems206espPRO");
		   break;		   
		}	
	   //echo "sql condped:".$sql;
	   if ({condped} === false)
       {
          echo "Erro de acesso ou sintaxe na função retornardiasCondPed!";
       }
       else
       {
	     while (!$condped->EOF)  
	     {  
			if($retorno == '')
			   $retorno = $condped->fields[0];
			else
			   $retorno .= "/".$condped->fields[0]; 
			
			$condped->MoveNext();			
		  }	
		  $retorno = "Especial(".$retorno.")"; 
	   }
	 }
	 else
	 {  		
		$sql = "select \"descricao\" from PUB.\"cond-pagto\" where \"cod-cond-pag\" = $condPagto ";
		sc_lookup(condpagto, $sql,"ems206comPRO");
	    if ({condpagto} === false)
        { 
           echo "Erro de acesso. Mensagem = " . {condpagto_erro};
        }
        elseif (empty({condpagto}))
        {
          $retorno = '';
        }
        else
       {
          $retorno = {condpagto[0][0]};
	   }	 
		 
	 }	 
	 return $retorno;
	
}

?>