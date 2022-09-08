<?php
function getBdsConsulta($filtroNomeTb='')
{
    if($filtroNomeTb <> ''){
        $cond = " where SCHEMA_NAME $filtroNomeTb  ";
    }else{
        $cond = "";
    }
    $sql ="SELECT SCHEMA_NAME as bd
  FROM INFORMATION_SCHEMA.SCHEMATA  $cond";
    $aReg = getRegsSqlLivre($sql,'bd','geral');
    return $aReg;
}
function verExistBdUsuario($idUsuario)
{
    $lExiste = false;
    $nomeDb =getNomeBd($idUsuario);
    $sql ="SELECT SCHEMA_NAME as bd
  FROM INFORMATION_SCHEMA.SCHEMATA
 WHERE SCHEMA_NAME = '$nomeDb'";
    $aReg = getRegsSqlLivre($sql,'bd','geral');
    if(is_array($aReg)){
        $lExiste = true;
    }
    return $lExiste;
}

function getNomeBd($idUsuario)
{
    $nomeDb = "bd_{$idUsuario}";
    return $nomeDb;
}
function getNomeBdUsuarioCorrente()
{
    $ret = getNomeBd(getVarSessao('id_usuario'));
    if($ret == ''){
        $ret = '0';
    }
    return $ret;
}
function sincrBdConsulta($idUsuario)
{
    $lExist = verExistBdUsuario($idUsuario);
    $nomeDb = getNomeBd($idUsuario);
    if(! $lExist){
        criarBd($nomeDb);
    }
    trocarDbDin($nomeDb);
    return $lExist;
}
function criarBd($nomeBanco)
{
    $cmd = "create database $nomeBanco";
    sc_exec_sql($cmd,"geral");
}
function trocarDbDin($nomeBanco)
{
    $array = getDadosConexao($nomeBanco);
    sc_connection_edit('dinamico',$array);
}

function getDadosConexao($nomeBanco)
{
    $senha = credBdConsultas();
    return
    array('server'=>'192.168.0.170',
        'user'=>'root',
        'password'=>$senha,
        'database'=>$nomeBanco);
}
function executarComandoBds($comando,$numDb)
{
    if($numDb <> 0){
        $nomeBanco = "bd_$numDb";
        $con = conectarBaseMysql($nomeBanco);
        $ret = execAcaoPDO($comando,$con,$acao='cmd');

    }else{
        $aBd = getBdsConsulta("like 'bd_%' ");
        foreach ($aBd as $reg) {
            $banco = $reg['bd'];
            $con = conectarBaseMysql($banco);
            $ret = execAcaoPDO($comando,$con,$acao='cmd');
        }
    }
    return $ret;

}

function limparTbsBancos($nomeBanco='')
{
    $cmd= "drop table wp;drop table wp_estoque_preco_000";
    if($nomeBanco <> ''){
        $con = conectarBaseMysql($nomeBanco);
        $ret = execAcaoPDO($cmd,$con,$acao='cmd');
        //var_dump($ret);
    }else{
        $aBd = getBdsConsulta();
        foreach ($aBd as $reg) {
            $banco = $reg['bd'];
            if(substr($banco,0,3) == 'bd_'){
                $con = conectarBaseMysql($banco);
                $lExiste = verExistTbConsPorBanco($banco,'wp');
                if($lExiste){
                    $ret = execAcaoPDO($cmd,$con,$acao='cmd');
                    echo "<h1>Apagou tabelas modelos do banco $banco</h1>";
                }

            }
        }
    }

}

?>




