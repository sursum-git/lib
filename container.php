<?php
//__NM____NM__FUNCTION__NM__//

function getDadosContainer($container,$campos='')
{

    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.\"pp-container\" ";
    $condicao = "  \"nr-container\" = $container ";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    return $aDados;

}

function getDtPrevContainer($container)
{
    $dtPrevChegada = 'Não Informado';
    $aRet = getDadosContainer($container,'"dt-prev-chegada" as dt_prev_chegada');
    if(is_array($aRet)){
        $dtPrevChegada = $aRet[0]['dt_prev_chegada'];
        $dtPrevChegada = sc_date($dtPrevChegada, "yyyy-mm-dd", "+", 7, 0, 0);
        $dtPrevChegada = getQuinzenaMesAnoData($dtPrevChegada);
    }
    return $dtPrevChegada;
}
function vericaContainerExclusivo($container){

    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.\"pp-container\" ";
    $condicao = "  \"nr-container\" = $container ";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,'exclusivo',$condicao,$conexao);
    $exclusiv = $aDados[0]['exclusivo'];
    return $exclusiv;

}
function sincrExclusividadeContainer($acao,$containers,$codReps=''){

    switch ($acao){
        case 'alterar':
            $cmd = " update pub.\"pp-container\" set exclusivo = 1 
                     where \"nr-container\" = $containers";
            sc_exec_sql($cmd,"especw");
            break;

        case 'incluir':
            $cmd = "insert into PUB.pp_container_permissao (nr_container,cod_repres)
                    values($containers,$codReps)";
            sc_exec_sql($cmd,"especw");
            break;
    }

}
function getRegContainerPermissao($container,$codRep){
    $tipo     = "multi"; // unico ou multi
    $tabela   = " pub.pp_container_permissao";
    $condicao = "  nr_container = $container and cod_repres = $codRep ";
    $conexao  = "espec";
    $aDados = getDados($tipo,$tabela,'nr_container,cod_repres',$condicao,$conexao);
    return $aDados;
}
function sincrPermisContainersRep($listCont,$listReps){

    $aListaContainer = explode(',',$listCont);
    $reps = explode(';',$listReps);
    foreach($aListaContainer as $containers){
        $contExclusivo = vericaContainerExclusivo($containers);
        if($contExclusivo == 0){
            sincrExclusividadeContainer('alterar',$containers);
        }
        foreach ($reps as $codRepres){

            $aReg = getRegContainerPermissao($containers,$codRepres);
            if($aReg == ''){
                sincrExclusividadeContainer('incluir',$containers,$codRepres);
            }

        }
    }
}


?>