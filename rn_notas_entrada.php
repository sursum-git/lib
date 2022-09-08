<?php
//__NM__Notas de Entrada__NM__FUNCTION__NM__//
function buscarTotaisRetidos($codigos,$cod_emitente,$nro_docto,$serie,$natureza)
{
  	$retorno = 0;
	$sql = 
		"SELECT sum(\"vl-imposto\") as tot_imp_ret
		FROM PUB.\"dupli-imp\" 
		WHERE 
		\"nro-docto\"            = '$nro_docto'
		and    \"serie-docto\"   ='$serie'
		and    \"cod-emitente\"  = $cod_emitente
		and   \"nat-operacao\"   = '$natureza' ";
	if($codigos != '')
	{
	   $sql .= 	" and   \"cod-retencao\" in ($codigos)";
	}	
	sc_lookup(meus_dados,$sql);
	
    if ({meus_dados} === false)
   {
      echo "Erro de acesso. Mensagem = " . {meus_dados_erro};
   }
   elseif (empty({meus_dados}))
   {
    echo "Comando select não retornou dados ";
   }
   else
   {
        $retorno =  {meus_dados[0][0]};

   }
	return $retorno;	
}
?>