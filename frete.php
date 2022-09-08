<?php
//__NM____NM__FUNCTION__NM__//
function getUfsCifParcial() 
{
    $estados = 'TO,MT,MS,AC,AP,AM,PA,RO,RR';
    return $estados;

}
function getUfsSudeste()
{
    $estados = "MG,SP,RJ,ES";
    return $estados;
}


function calcTipoFrete($nrPedido,$aTotais='',$indPedido=1 ,$tbPreco=1)
{
    $vlTotalPedido   = 0 ;
    $uf              = '';
    $erro = '';
    $tipoFrete = 0;
    switch($indPedido){
        case 1: // pedido web
            $aReg = getRegPedWeb($nrPedido);
            if(is_array($aReg)){
                $clienteId       = $aReg[0]['cliente_id'];
                $clienteTriangId = $aReg[0]['cliente_triang_id'];
                $container       = $aReg[0]['nr_container'];
                $diasCondPagto   = $aReg[0]['dias_cond_pagto_esp'];
                $moeda           = $aReg[0]['cod_moeda'];
                $tbPreco         = $aReg[0]['tb_preco_id'];
                if($aTotais == ''){
                    $aTotais = valorizarItensRefPedWeb($nrPedido,$container,$diasCondPagto,$tbPreco,$moeda);
                }
                //var_dump($aTotais);

                $vlTotalPedido = $aTotais['total'];
                //$logPrecoInformado = $aTotais['log_informado'];
                $logAbaixoTb = $aTotais['log_abaixo_tb'];
                $prazoMedio = $aTotais['prazo_medio'];
                if($clienteTriangId <> 0){
                    $cliente = $clienteTriangId;
                }else{
                    $cliente = $clienteId;
                }
                $uf = getUfCliente($cliente);
            }
            break;
        case 2: // pedido PE do ERP
            /*$aRegPedPE = getRegPedVenda($nrPedido);
            if(is_array($aRegPedPE)){
                $clienteId        = $aRegPedPE[0]['cod_emitente'];
                $nomeAbrevCliTri  = $aRegPedPE[0]['nome_abrev_tri'];
                if($nomeAbrevCliTri <> ''){
                    $aCliente  = getRegClientePorNomeAbrev($nomeAbrevCliTri,'"cod-emitente"');
                    if(is_array($aCliente)){
                        $clienteTriangId  = $aCliente[0]['cod_emitente'];
                    }else{
                        $clienteTriangId = 0;
                    }
                }else{
                    $clienteTriangId = 0;
                }
                $container       = 0;
                $diasCondPagto   = ''; // fazer a regra utilizada para o preço
                if($clienteTriangId <> 0){
                    $cliente = $clienteTriangId;
                }else{
                    $cliente = $clienteId;
                }
                $uf = getUfCliente($cliente);

                $vlTotalPedido = calcularTotaisPedVenda('5');
                $logPrecoInformado = 0; //criar função que encontre pelo menos um item informado;
            }*/
            break;
    }
    if($uf == ''){
        $erro = '<h1>Estado do Cliente em branco.</h1> ';
        $tipoFrete = 0; //nao calculado
    }
    if($vlTotalPedido == ''){
        $erro .= '<h1>Pedido Com Valor Total Zerado.</h1>';
        $tipoFrete = 0; //nao calculado
    }
    if($erro == ''){
        /*implementa  regra do frete CIF
          até 1499 reais ou 300 dolares FOB
          de 1500 a 2499 não sendo preço informado e SUDESTE (CIF) SENAO (FOB)
          de 2500 reais ou 600 dolares para cima CIF exceto para os estados
          que tem redespacho. Para estes casos fica CIF Parcial
        */
        $vlMinFreteCif = getVlMinFreteCIF($moeda);
        $vlMinFreteCifSudeste = getVlMinFreteCIFSudeste($moeda);
        //echo "<h1>valor minimo  CIF: $vlMinFreteCif</h1>";
        //echo "<h1>valor minimo  CIF Sudeste: $vlMinFreteCifSudeste</h1>";
        //verifica se o estado do cliente está no sudeste
        $ufsSudeste = getUfsSudeste();
        //echo "<h1>ufs sudeste: $ufsSudeste</h1>";
        if(strstr($ufsSudeste,$uf) <> false and $tbPreco == 1){
            $logSudeste = true;
        }else{
            $logSudeste = false;
        }
        //echo "<h1>sudeste?".getVlLogico($logSudeste)."</h1>";
        if($vlTotalPedido < $vlMinFreteCifSudeste){
            //echo "<h1>valor total do pedido menor que o valor minimo sudeste - FRETE FOB</h1>";
            //echo "<h1>menor de 1500 reais</h1>";
            $tipoFrete = 3; // FOB
        }else{
            if($vlTotalPedido >= $vlMinFreteCif){
                //echo "<h1>valor total do pedido MAIOR ou  IGUAL que o valor minimo GERAL - FRETE CIF</h1>";
                //echo "<h1>igual ou maior que 2500</h1>";
                $tipoFrete = 1; //CIF
            }else{
                //echo "<h1>valor total do pedido MAIOR que o valor minimo sudeste e menor que o minimo GERAL</h1>";
                if($logAbaixoTb == true or $prazoMedio > 90 ){
                    $tipoFrete = 3; //FOB
                    //echo "<h1>prazo medio maior de 90 ou preço abaixo da tabela</h1>";
                }else{
                    //echo "<h1>prazo medio menor de 90 e preço igual ou acima da tabela</h1>";
                    if($logSudeste == true){ //sudeste + maior igual a 1500 e menor que 2500 + prazo menor igual a 90 + sem preco inf
                        //echo "<h1>SUDESTE</h1>";
                        $tipoFrete = 1; //CIF
                    }else{ // nao é do sudeste e é menor que 2500
                        //echo "<h1>NAO SUDESTE</h1>";
                        $tipoFrete = 3; //FOB
                    }
                }
            }
        }
        $cifParcial = verifUfCifParcial($uf);
        if($tbPreco == 1){
            if($tipoFrete == 1 and $cifParcial == true){ //CIF
                //echo "<h1>CIF Parcial devido a localização </h1>";
                $tipoFrete = 2; //CIF Parcial
            }
        }else{ // tabela RUBI
            $ufsSudeste = getUfsSudeste();
            if(strstr($ufsSudeste,$uf)  == false ){ //não é sudeste
                if($tipoFrete == 1){
                    $tipoFrete = 2;
                }
            }
        }
    }
    $aRetorno = array('tipo_frete' => $tipoFrete, 'erro' => $erro);
    return $aRetorno;
}

function verifUfCifParcial($uf)
{
    $ufs = getUfsCifParcial();
    if(strstr($ufs,$uf) <> false){
        $log = true;
    }else{
        $log = false;
    }
    return $log;
}
function atualizarTipoFrete($pedWebId,$aTotais='',$tbPreco=1)
{
    $aTipoFrete = calcTipoFrete($pedWebId,$aTotais,1,$tbPreco);
    $tipoFrete = $aTipoFrete['tipo_frete'];
    setTipoFrete($pedWebId,$tipoFrete);
    return $tipoFrete;
}

function getTipoFrete($tipo)
{
	$desc = '';
    switch ($tipo){
        case 1:
            $desc = "CIF";
            break;
        case 2:
            $desc = "CIF Parcial";
            break;
        case 3:
            $desc = "FOB";
            break;
    }
    return $desc;
}
?>
