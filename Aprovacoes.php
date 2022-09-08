<?php
//__NM__Aprovações de pedidos__NM__FUNCTION__NM__//

function msg_lock(){

    echo utf8_decode("Esse pedido de venda est&#225; sofrendo alguma altera&#231;&#227;o nesse momento pelo usu&#225;rio: ");
}

function avaliarPrecoPedido($listaPedidos,$situacao)
{
    $msg = '';
    try{
        sc_begin_trans("medw");
        $cmd = "update pub.\"ped-venda\" set \"cod-sit-preco\" = $situacao where \"nr-pedido\" 
		in($listaPedidos)";
        $ret= updateDireto('medw','"ped-venda"',array('"cod-sit-preco"'=>$situacao),
        "\"nr-pedido\" in($listaPedidos)");
        sc_exec_sql($cmd,"medw");
        if($ret['usuario_lock']<>''){
           $msg = msg_lock() . $ret['usuario_lock'];
               //utf8_decode("Registro está sendo usado pelo usuário: ") .$ret['usuario_lock'];
        }else{
            gerarLogs($listaPedidos,1,$situacao);
            sc_commit_trans ("medw");
        }

    }catch (Exception $e) {
        sc_rollback_trans("medw");
    }
    return $msg;
}

function gerarLogs($listaPedidos,$tipo,$situacao)
{
    $codEstab = '5'; //fixo pois tem apenas a empresa med
    $nomeAbrev='';
    $sitFinal = -1;
    $aListaPedidos = explode(',',$listaPedidos);
    if(is_array($aListaPedidos)){
        $tam = count($aListaPedidos);
        for($i=0;$i< $tam; $i++){
            $pedido = $aListaPedidos[$i];
            //echo "tamanho situacao:".strlen($situacao);

            switch($tipo){
               case 3:
                  // echo "-$situacao-".strlen($situacao);
                    if($situacao == "''"){
                        $sitFinal = 1;
                    }else{
                        $sitFinal = 2;
                    }
                    break;
                default:
                    if($situacao > 1){
                        $sitFinal = $situacao - 1;
                    }else{
                        $sitFinal = 0;
                    }

            }

            inserirHistAval($codEstab,$pedido,$tipo,$sitFinal);
            $usuarioERP = getUsuarioERP([usr_login]);
            //echo "Situação:$situacao<br>";
            if($sitFinal == 1){
                $descSituacao = "APROVADO";
            }else{
                $descSituacao = "REPROVADO";
            }
            $descTipo =  getDescTipo($tipo);
            $ocorrencia = "$descTipo $descSituacao Por $usuarioERP - AVALIAÇÃO WEB" ;
            $aRet = getRegPedVenda($pedido,'"nome-abrev" as nome_abrev' );
            if(is_array($aRet)){
                $nomeAbrev = $aRet[0]['nome_abrev'];
            }

            inserirLogPedVenda($codEstab,$pedido,$nomeAbrev,$ocorrencia,$usuarioERP);

        }
    }
}
function getDescTipo($tipo)
{
    switch($tipo){

        case 1:
            $descTipo = "Preço";
            break;
        case 2:
            $descTipo = "Frete";
            break;
        case 3:
            $descTipo = "Comissão";
            break;
        case 4:
            $descTipo = "Prioridade";
            break;
        case 5:
            $descTipo = "Tipo do Pedido";
            break;
        default:
            $descTipo = "Não Informado";
    }
    return $descTipo;
}

function avaliarComissaoPedido($listaPedidos,$situacao)
{
    //echo "<h1> sit - $situacao </h1>";
	$cmd = "update pub.\"ped-repre\" set \"cod-classificador\" = $situacao where \"nr-pedido\"
	in($listaPedidos)";
	sc_exec_sql($cmd,"medw");
    gerarLogs($listaPedidos,3,$situacao);


    /*$msg = '';
    try{
        sc_begin_trans("medw");
        $cmd = "update pub.\"ped-repre\" set \"cod-classificador\" = $situacao where \"nr-pedido\" 
	in($listaPedidos)";
        $ret= updateDireto('medw','"ped-repre"',array('"cod-classificador"'=>$situacao),
            "\"nr-pedido\" in($listaPedidos)");
        //sc_exec_sql($cmd,"medw");
        if($ret['usuario_lock']<>''){
            $msg = msg_lock() . $ret['usuario_lock'];
            //utf8_decode("Registro está sendo usado pelo usuário: ") .$ret['usuario_lock'];
        }else{
            gerarLogs($listaPedidos,3,$situacao);
            sc_commit_trans ("medw");
        }

    }catch (Exception $e) {
        sc_rollback_trans("medw");
    }
    return $msg;
}*/




}

function avaliarFretePedido($listaPedidos,$situacao)
{
	$cmd = "update pub.\"ped-venda\" set \"cod-sit-com\" = $situacao where \"nr-pedido\" 
	in($listaPedidos)";
	sc_exec_sql($cmd,"medw");
    gerarLogs($listaPedidos,2,$situacao);
}

function avaliarPrioridadePedido($listaPedidos,$situacao)
{
	$cmd = "update pub.\"ped-venda\" set \"ind-sit-desconto\" = $situacao where \"nr-pedido\" 
	in($listaPedidos)";
	sc_exec_sql($cmd,"medw");
    gerarLogs($listaPedidos,4,$situacao);
}

function avaliarTpPedidoPedido($listaPedidos,$situacao)
{
	$cmd = "update pub.\"ped-venda\" set \"ind-aprov\" = $situacao where \"nr-pedido\" 
	in($listaPedidos)";
	sc_exec_sql($cmd,"medw");
    gerarLogs($listaPedidos,5,$situacao);
}

function colorUsuarioLogAprov(){

    if({usuario} == 'mrocha'){
        sc_field_style({usuario} , "#7FFFD4", "", "", "", "");
}else if({usuario} == 'amoura'){
        sc_field_style({usuario}, "#87CEFA", "", "", "", "");
}else if({usuario} == 'edmar'){
        sc_field_style({usuario} , "CCEEFF", "", "", "", "");
}else{
        sc_field_style({usuario} , "", "", "", "", "");
}

}
function colorOcorrenciaLogAprov(){

    if({usuario} == 'mrocha'){
        sc_field_style({ocorrencia} , "#7FFFD4", "", "", "", "");
}else if({usuario} == 'amoura'){
        sc_field_style({ocorrencia}, "#87CEFA", "", "", "", "");
}else if({usuario} == 'edmar'){
        sc_field_style({ocorrencia} , "CCEEFF", "", "", "", "");
}else{
        sc_field_style({ocorrencia} , "", "", "", "", "");
}

}
/*avaliarPrioridadePedido
function calcHorarioAprov(){

    $horas = floor({hr-trans}/3600);
    $min = floor(({hr-trans} - ($horas * 3600)) / 60);
    $sec = {hr-trans} % 60 ;

    return str_pad($horas, 2, '0', STR_PAD_LEFT)
        . ":" . str_pad($min, 2, '0', STR_PAD_LEFT)
        . ":" . str_pad($sec, 2, '0', STR_PAD_LEFT);


}
*/

function getSitPedComis($pedido){


    $tipo     = "unico";
    $tabela   = " pub.\"ped-repre\" ";
    $campos   = "\"cod-classificador\" as sit_comis" ;
    $condicao = "  \"nr-pedido\" = $pedido";
    $conexao  = "med";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $sitComis = $aDados[0]['sit_comis'];

    }
    return $sitComis;


}
?>