<?php
//__NM____NM__FUNCTION__NM__//
	
function retornarLegenda()
{
	$imagem = '../_lib/img/legenda.jpg';
	$imagem = "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src='$imagem' alt='legenda' height='100px'  >";
		
	return $imagem;
}

function retornarListaLegenda()
{
	$retorno = 'A,B,C,D,E,F,G,H,I,J,K,L';	
	return $retorno;	
}

?>