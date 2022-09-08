<?php
//__NM____NM__FUNCTION__NM__//
/**
 * Created by PhpStorm.
 * User: sursum_corda
 * Date: 21/12/2018
 * Time: 04:09
 */

function getPrazoMedioCP($condPagto)
{
    $prazoMedio = 90;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.\"cond-pagto\" ";
    $campos   = " \"qtd-dias-prazo-medio\" as prazo_medio ";
    $condicao = "  \"cod-cond-pag\" = $condPagto ";
    $conexao  = "comum";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $prazoMedio = $aDados[0]['prazo_medio'];

    }
    return $prazoMedio;
}
function getPrazoMedioInf($dias,$dtRef='',$formato='aaaa-dd-mm')
{
    $total = 0;
    $prazoMedio = 90;
    if($dias <> '' and $dias <> 0 ){
        $aDias = explode(',',$dias);
        $tam   = count($aDias);
        //echo "<h1>$dias</h1>";
        for($i=0;$i<$tam;$i++){
            //echo "<h1>".$aDias[$i]."</h1>";
            if(strstr($aDias[$i],'/') <>  false){
                $qtDias = getDiasDataPagto($dtRef,$aDias[$i],$formato);
            }else{
                $qtDias = $aDias[$i];
            }
            $total += $qtDias;
        }
        $prazoMedio = $total / $tam;
    }
    if($dias == 0 ){
        $prazoMedio = 0;
    }
    return $prazoMedio;

}
function buscarIndice($empresa='5')
{
    $aEmpresa = getDadosEmpresa($empresa);
    $conexao  = $aEmpresa['conexao'];

    $aRet = getIndiceFinancSessao($empresa);
    if(is_array($aRet)){
        $aIndice = $aRet ;
        $lAchou = true;
    }else{
        $aIndice = array();
        $lAchou = false;
        $aReg = getReg($conexao,'"tab-finan"',
            '1',
            '1',
            'top 1 "tab-dia-fin" as tab_dia_fin,
                    "tab-ind-fin" as tab_ind_fin '
        );
        if(is_array($aReg)){
            $aDias = explode(';',$aReg[0]['tab_dia_fin']);
            $aInd  = explode(';',$aReg[0]['tab_ind_fin']);
            for($i=0;$i < count($aDias);$i++)
            {
                $aIndice[] = array('dias' => $aDias[$i], 'indice' => $aInd[$i]);
                $lAchou = true;
                if($aDias[$i] == '90')
                    break;
            }
            setIndiceFinancSessao($aIndice,$empresa);

        }else{
            $aIndice= '';
            $lAchou = false;
        }

    }
    if($lAchou == false){
        $aIndice = '';
    }
    return $aIndice;

}
function getIndFinancPrazo($listaDias,$empresa='5')
{
    $aIndice = buscarIndice($empresa);
    $prazoMedio = getPrazoMedioInf($listaDias);
    $indice = 0;
    if($prazoMedio <= 30 and $prazoMedio > 1) {
        $indice = 1;
    }
    if($prazoMedio > 30 and $prazoMedio <= 60){
        $indice = 2;
    }
    if($prazoMedio > 60){
        $indice = 3;
    }
    if(is_array($aIndice)){
        $vlInd = $aIndice[$indice]['indice'];
    }else{
        $vlInd = 0;
    }
    return $vlInd;
}
function getDiasDataPagto($dataRef,$dataPagto,$formato1='dd/mm/aaaa',$formato2='dd/mm/aaaa')
{
    //echo "<h1>$dataRef - $dataPagto - $formato1 - $formato2</h1>";
    $retorno = sc_date_dif($dataRef, $formato2,$dataPagto, $formato1);
    //echo "retorno:$retorno";
    return $retorno;

}
function getDescCondPagto($codCondPagto)
{
	$desc      = '';
    $descricao = "ESPECIAL";
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.\"cond-pagto\" ";
    $campos   = " descricao,\"qtd-dias-prazo-medio\" as qtd_dias_prazo_medio ";
    $condicao = "  \"cod-cond-pag\" = $codCondPagto ";
    $conexao  = "comum";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $desc = $aDados[0]['descricao'];
        $dias      = $aDados[0]['qtd_dias_prazo_medio'];
        $descricao = "$codCondPagto - $desc( média $dias dias )  ";
    }
    return $descricao;
}

function limparGlobCondPagto()
{
    [gl_log_a_vista] = '';
    [gl_dias_cond_pagto] = '';

}
function verifGlobCondPagto()
{
    $log = false;
    if([gl_log_a_vista] <> '' or  [gl_dias_cond_pagto] <> ''  ){
        $log = true;
    }
    return $log;
}
function getDiasParcela($qtParcelas)
{
    $dias = '';
    switch($qtParcelas){
        case 1:
            $dias = '30' ;
            break;
        case 3:
            $dias = '30,60,90' ;
            break;
        case 5:
            $dias = '30,60,90,120,150' ;
            break;
    }
    return $dias;
}

function getNomeVarIndFinan()
{
    return 'indice_finan';
}
function setIndiceFinancSessao($aIndice,$empresaParam=5)
{
    $aIndSessao[$empresaParam]= $aIndice;
    setVarSessao(getNomeVarIndFinan(),$aIndSessao);

}
function getIndiceFinancSessao($empresa=5)
{
    $aInd = getVarSessao(getNomeVarIndFinan());
    if($aInd <> ''){
        if(isset($aInd[$empresa]) and is_array($aInd[$empresa])){
          $aRet =  $aInd[$empresa];
        }
    }else{
        $aRet = '';
    }
    return $aRet;
}
?>