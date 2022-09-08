<?php
//__NM__Representante__NM__FUNCTION__NM__//
function buscarCodRep($nomeAbrevParam)
{
    $codRep = 0;
    //echo "<h1>nome abrev param : $nomeAbrevParam</h1>";
    $aRet = getDadosRepresEms2($nomeAbrevParam,'"cod-rep" as cod_rep',$campoChave='"nome-abrev"');
    if(is_array($aRet)){
        $codRep = $aRet[0]['cod_rep'];
    }
    return $codRep;
}
function getNomeAbrevRepres($codRep)
{
    $nomeAbrev = 0;
    $aRet = getDadosRepresEms2($codRep,'"nome-abrev" as nome_abrev');
    if(is_array($aRet)){
        $nomeAbrev = $aRet[0]['nome_abrev'];
    }
    return $nomeAbrev;
}
function buscarDadosRepres($codRepres,$campos='')
{
    if($campos == ''){
        $aCampos = getCpsTbSessao('ems5','representante');
        $campos = $aCampos['campos'];
    }
    $tabela   = " pub.representante ";
    $condicao = "  cdn_repres = $codRepres ";
    $aResult = retornoSimplesTb01($tabela,$campos,$condicao,'ems5');
    return $aResult;


}

function getDadosRepresEms2($cpChave,$campos='',$campoChave='"cod-rep"')
{
    if($campos == ''){
        $aCampos = getCpsTbSessao('ems2cad','repres');
        $campos = $aCampos['campos'];
    }
    $tabela   = " pub.repres ";
    $condicao = "  $campoChave = '$cpChave' ";
    $aResult = retornoSimplesTb01($tabela,$campos,$condicao,'comum');
    return $aResult;
	


}
function getDadosRepresFinanc($empresa,$repres,$campos='')
{
    $aReg = getReg('ems5','repres_financ','cod_empresa,cdn_repres',
        "'$empresa',$repres", $campos);
    return $aReg;
}

function getPercComisRepres($estab,$repres)
{
    $perc = 0;
    $aReg = getDadosRepresFinanc($estab,$repres,'val_perc_comis_repres');
    if(is_array($aReg)){
        $perc = $aReg[0]['val_perc_comis_repres'];
    }
    return $perc;
}
function getPercAumentComis()
{
    return 25 / 100;
}
function getClasseVendedor($codigo)
{
    $classe = 0;
    $aReg = getReg('espec','cm-ext-repres','"cod-rep"',
    $codigo,'classe');
    if(is_array($aReg)){
        $classe = $aReg[0]['classe'];
    }
    return $classe;
}
function getClasseVendExterno()
{
    return '3,5';

}
function getListaRepres()
{
    $classe = getClasseVendExterno();
    $lista = "";
    $aRet = getDados('multi','pub."cm-ext-repres"','"cod-rep" as cod_rep',
        "classe in($classe)","espec");
    foreach($aRet as $reg){
       $lista = util_incr_valor($lista,$reg['cod_rep'],',');
    }
    return $lista;
}
function getTipoVendedor($loginParam='') //nome abrev = login
{
    if($loginParam == ''){
        $loginParam = getVarSessao(getNomeVarLoginSessao());
    }
    //echo "<h1>login buscarCodRep: $loginParam</h1>";
   $codRep = buscarCodRep($loginParam);
   $classe = getClasseVendedor($codRep);
   //echo "<h1>codigo vendedor $codRep</h1>";
   //echo "<h1>classe vendedor $loginParam:$classe</h1>";
    //echo "<h1>classe de vendedor interno".getClasseVendInterno()."</h1>";
    $classesVendInterno = getClasseVendInterno();
    if(strstr($classesVendInterno, $classe) <> false) {
        $tipoVend = 'interno';
    }else{
        $tipoVend = 'externo';
    }

    return $tipoVend;
}
function getClasseVendInterno()
{
    return '4';
}
function getClasseGerente()
{
    return '1,2';
}
/*function getRepresGerente($codigo)
{
    $lista = '';
    $aReg = getDados('multi','"cm-hierarquia"',
        '"cod-depend" as cod',"\"cod-rep\" = $codigo ",'espec');
    foreach ($aReg as $item) {
        $repres = $item['cod'];
        $lista = util_incr_valor($lista,$repres);
    }
    return $lista;
}*/

function getTpHierarquiaGer($codRep){

    $hierarq = '';
    $aDados = getDados('multi','pub."cm-ext-repres" cm_rep','cm_rep."tp-aplic-comis" as tp_hierarq',
        "cm_rep.\"cod-rep\" = $codRep",'espec');
    //var_dump($aDados);
    if(is_array($aDados )){
        $hierarq = $aDados[0]['tp_hierarq'];
    }


    return $hierarq;
}



?>
