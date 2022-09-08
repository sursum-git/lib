<?php
//__NM__Conversão de Sql Para Progress__NM__FUNCTION__NM__//
function convSqlProgress($aTabela,$aCampos,$condicao)
{
    $tabs= '';
    $campos='';
    if(is_array($aTabela)){
        for($i=0;$i < count($aTabela);$i++){
            $tabela = $aTabela[$i];
            $alias  = str_replace("-","_", $aTabela[$i]);
            if($tabs == ''){
                $tabs = " pub.$tabela $alias ";
            }
            else{
                $tabs .= ",pub.$tabela $alias ";
            }
            if(is_array($campos)){
                for($x=0;$x < count($aTabela);$x++){

                }
            }

        }
    }
    return $sql;
}

function getCpsTb($catalogo,$tb,$logNomeTabela=false,$logMulti=true)
{
    $aRetorno       = '';
    $listaCampos    = '';
    $listaTipoCp    = '';
    $listaExtentCp  = '';

    $tabela   = "sysprogress.syscolumns_full  ";
    if($logMulti == true){
        $tabela = "$catalogo.$tabela";
        $conexao = "multi";
    }else{
        $conexao = $catalogo;
    }
    $campos   = " coltype as tipo_dado, 
                  width as tamanho, 
                  array_extent as extensao, 
                  col as campo,
                  scale as escala,
                  nullflag as log_nulo,
                  format as formato,
                  label  as label,
                  display_order as ordem,
                  owner as esquema,
                  valexp as valexp";
    $condicao = " tbl = '$tb'";
    $aResult = retornoMultReg($tabela,$campos,$condicao,$conexao);
    if(is_array($aResult))	{
        $tam = count($aResult);
        for($i=0;$i<$tam;$i++){
            $campo       = $aResult[$i]['campo'];
            $tipo        = $aResult[$i]['tipo_dado'];
            $extensao    = $aResult[$i]['extensao'];
            $esquema     = $aResult[$i]['esquema'];
            $tab         = '';
            $tabApelido  = '';
            if($extensao > 0){
                if($logNomeTabela == true){
                    $tabApelido = str_replace('-','_',$tb);
                    if($logMulti == true){
                        $tab = "$catalogo.$esquema.$tb.";

                    }
                    else{
                        $tab = "$esquema.$tb.";
                    }
                }
                for($x=1;$x <= $extensao;$x++){
                    $campo = "pro_element($tab$campo,$x,$x) as $tabApelido_$campo_$x";
                    $listaCampos = util_incr_valor($listaCampos,$campo);
                    $listaTipoCp = util_incr_valor($listaTipoCp,$tipo);
                    $listaExtentCp = util_incr_valor($listaExtentCp,$extensao);
                }


            }else{
                $listaCampos = util_incr_valor($listaCampos,$campo);
                $listaTipoCp = util_incr_valor($listaTipoCp,$tipo);
                $listaExtentCp = util_incr_valor($listaExtentCp,$extensao);
            }

        }
        $aRetorno = array('campos' => $listaCampos,'tipos' => $listaTipoCp,
            'extensoes' => $listaExtentCp);
    }
    return $aRetorno;
}

function getCpsTbSessao($catalogo,$tabela)
{
    $var = "cps_".$catalogo."_".$tabela;
    if(!isset($_SESSION[$var]) or $_SESSION[$var] == ''){
        setCpsTbSessao($catalogo,$tabela);
    }
    return $_SESSION[$var];

}


function setCpsTbSessao($catalogo,$tabela)
{
    $var = "cps_".$catalogo."_".$tabela;
    $lista = getCpsTb ($catalogo,$tabela);
    $_SESSION[$var] = $lista;

}
function convTbProgress($tabelas)
{
    //colocar logica para acrescentar pub. caso não esteja informado e para colocar aspas duplas caso tenha traço e já não tenha aspas duplas
}

function convCpProgress($tabelas)
{
    //colocar logica para  colocar aspas duplas caso tenha traço e já não tenha aspas duplas
}

function gerarFiltroPartes($cpNumerico,$cpTexto,$condicao)
{
    $listaNums = '';
    $filtro = '';
    $condicao = str_replace('  ', ' ', $condicao); // retira dois espaços
    //if (stristr($condicao, ',') <> false) {
        $aCondTermos = explode(',', $condicao);
        if (is_array($aCondTermos)) {
            $tam = count($aCondTermos);
            for ($i = 0; $i < $tam; $i++) {
                $termo = $aCondTermos[$i];
                if (is_numeric(ltrim(trim($termo)))) {
                    $termo = ltrim(trim($termo));
                    $listaNums = util_incr_valor($listaNums, $termo, ',');
                }
                else{
                    $filtroTexto = gerarFiltroTermo($cpTexto,$termo);
                    $filtro = util_incr_valor($filtro, $filtroTexto, ' or ');

                }
            }
        }
        if($listaNums <> '' and $cpNumerico <> ''){
           $listaNums   = retornarOpcoesTxt($listaNums);
           $aListaNums  = explode(',',$listaNums);
           $qt          = count($aListaNums);
           if($qt == 1){
               $incr = " $cpNumerico = $listaNums ";
           }else{
               $incr = " $cpNumerico in($listaNums)";
           }
           $filtro = util_incr_valor($filtro,$incr, " or ") ;
        }
        return $filtro;
    //}
}
function gerarFiltroTermo($campo,$termo)
{

    $termo = str_replace('  ',' ',$termo); // retira dois espaços
    $filtro = '(';
    $filtro =  util_incr_valor($filtro,"$campo like '%$termo%'"," ") ;
    $aTermo = explode(' ',$termo);
    if(is_array($aTermo)){
        $tam = count($aTermo);
        for($i=0;$i<$tam;$i++){
            $palavra = $aTermo[$i];
            if($i == 0){
                $operador = ' or ( ';
            }else{
                $operador = ' and  ';
            }
            if($palavra <> ''){
                $filtro =  util_incr_valor($filtro,"$campo like '%$palavra%'",$operador) ;
            }
        }
    }
    $filtro .= '))';
    return $filtro;
}


?>