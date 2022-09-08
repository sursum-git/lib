<?php
//__NM__Condição de Pagamento Especial do Pedido__NM__FUNCTION__NM__//
function retornarCondPed($nrPedido,$condPagto,$tipoCondPed="PE")
{
    $retorno = '';
    if($condPagto == 0)
    {
        switch($tipoCondPed)
        {
            case "PE":
                $sql = "select \"nr-dias-venc\",\"data-pagto\" from PUB.\"cond-ped\" where \"nr-pedido\" = $nrPedido ";
                sc_select(condped , $sql, "med");
                break;
            case "PI":
                $sql = "select \"nr-dias-venc\", \"data-pagto\" from PUB.\"pp-cond-ped\" where \"nr-pedido\" = $nrPedido ";
                sc_select(condped , $sql, "espec");
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
               $nrDiasVencto = $condped->fields[0];
               $dataVencto   = $condped->fields[1];
               if($dataVencto <> ''){
                   $hoje = date('Y-m-d');
                   //$incr =  sc_date_conv($dataVencto,"aaaa-mm-dd","dd/mm/aaaa");
                   $incr = sc_date_dif($dataVencto,"aaaa-mm-dd",$hoje,"aaaa-mm-dd");
               }else{
                   $incr =  $nrDiasVencto;
               }
               $retorno = util_incr_valor($retorno,$incr);
               $condped->MoveNext();
           }
           $retorno = "Especial(".$retorno.")";
       }
	 }
    else
    {
        $sql = "select \"descricao\" from PUB.\"cond-pagto\" where \"cod-cond-pag\" = $condPagto ";
        sc_lookup(condpagto, $sql,"comum");
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