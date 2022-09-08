<?php
function inserirLogPedVenda($codEstab, $nrPedido,$nomeAbrev,$ocorrencia,$usuarioERP='')
{
    $agora = date('H:i:s');
    $aAgora = explode(':',$agora);
    $seg = $aAgora[0] * 60 * 60 + $aAgora[1] * 60 + $aAgora[2];
    if($usuarioERP == ''){
        $usuarioERP = getUsuarioERP([usr_login]);
    }
    $cmd = "insert into pub.\"his-ped-venda-ext\"(\"cod-estabel\",\"nr-pedcli\",\"nome-abrev\",\"dt-trans\",\"hr-trans\",usuario,ocorrencia)
            values('$codEstab','$nrPedido','$nomeAbrev',curDate(),$seg,'$usuarioERP','$ocorrencia')";
    sc_exec_sql($cmd,"especw");
}

function buscarDadosLogPed($codEstab,$pedido){

    $aDados = getDados('multi',"PUB.\"his-ped-venda-ext\" as ped",'ocorrencia, usuario',
        "ped.\"cod-estabel\" = '$codEstab' and ped.\"nr-pedcli\" = $pedido",'espec');
    return $aDados;

}
function retornarOcorCanc($aDados){
    $tam = count($aDados);
    for($i=0;$i<$tam;$i++){
        $ocorrencia = $aDados[$i]['ocorrencia'];
        $ocorCanc = strripos($ocorrencia, "Pedido Cancelado");
        if($ocorCanc === 0) {
            return $ocorrencia;
        }

    }
}

function verificaRegAvalComis($pedido){
    $aDados = buscarDadosLogPed(5,$pedido);
    //var_dump($aDados);
    $logAchou = false;
    foreach ($aDados as $dados){

        $reg = $dados['ocorrencia'];
        $avalComis = strpos($reg,'Comissão APROVADO');

        if($avalComis !== false){
            $logAchou = true;
        }


    }
    return $logAchou;

}


?>
