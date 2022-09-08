<?php
//__NM____NM__FUNCTION__NM__//
function existMetaAnoMes($mes,$ano)
{
  $tabela   = " metas_repres ";
  $campos   = " meta_repres_id ";
  $condicao = "  mes = $mes e ano = $ano ";
  $aResult = retornoSimplesTb01($tabela,$campos,$condicao,"espec");
  if(is_array($aResult)){
     $retorno = true;
  }else{
     $retorno = false;
  }
  return $retorno;
}

function getIdAnoMesRepres($mes,$ano,$repres)
{
    $tabela   = " pub.metas_repres ";
    $campos   = " meta_repres_id ";
    $condicao = "  mes = $mes and ano = $ano and cod_repres = $repres ";
    $aResult = retornoSimplesTb01($tabela,$campos,$condicao,"espec");
    if(is_array($aResult)){
        $retorno = $aResult[0]['meta_repres_id'];
    }else{
        $retorno = 0;
    }
    return $retorno;
}

function getDadosMetaRepres($id,$campos='',$cond='')
{
    $condicao = '';
    if($campos == ''){
        $aCampos = getCpsTbSessao('espec','metas_repres');
        $campos = $aCampos['campos'];
    }
    $tabela   = " pub.metas_repres ";
    if($id <> 0){
        $condicao = "  meta_repres_id = $id ";
    }

    if($cond <> ''){
        $condicao .= $cond;
    }
    $aResult = retornoSimplesTb01($tabela,$campos,$condicao,"espec");
    return $aResult;

}

function getMetaPerfilMesCorrente()
{
    $anoAtual = getParteDtCorrente('ano');
    $mesAtual = getParteDtCorrente('mes');
    $meta     = 0;
    $tabela   = " pub.metas_repres ";
    $campos   = " sum(vl_meta) as vl_meta ";
    $condicao = "  ano = $anoAtual and mes = $mesAtual and cod_repres >= [codRepIni] and cod_repres <= [codRepFim] ";
    $aRet = retornoMultReg($tabela,$campos,$condicao);
    //var_dump($aRet);
    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $meta += $aRet[$i]['vl_meta'];
        }
    }
    return $meta;

}

function getUltMesMetaRepres()
{

    $aResult = getDadosMetaRepres(0,'ano,mes',
        "meta_repres_id in (select top 1 meta_repres_id from pub.metas_repres mr2
         order by  to_char(ano) + '_' + to_char(mes) desc)");
    return $aResult;
}

function getlistaIdRepresMetaAnoMes($ano,$mes)
{
    $tabela   = " pub.metas_repres ";
    $campos   = " cod_repres ";
    $condicao = "  ano = $ano and mes = $mes ";
    $lista = '';
    $aResult = retornoMultReg($tabela,$campos,$condicao,"espec");
    if(is_array($aResult))	{
        $tam = count($aResult);
        for($i=0;$i<$tam;$i++){
            $codRepres = $aResult[$i]['cod_repres'];
            $lista = util_incr_valor($lista,$codRepres);
        }
    }
    return $lista;

}

function inserirMetaRepres($ano,$mes,$repres,$meta,$tipo='')
{
    $cp = 'vl_meta';
    if($tipo == ''){
        $cp = 'vl_meta';
    }else{
        $cp = 'vl_meta_quinz';
    }
    $cmd = "insert into pub.metas_repres(meta_repres_id,ano,mes,cod_repres,$cp)
            values(pub.seq_meta_repres.NEXTVAL,$ano,$mes,$repres,'$meta')";
    sc_exec_sql($cmd,"especw");
    $ultSeq = buscarVlSequenciaEspec('seq_meta_repres','metas_repres');

    return $ultSeq;

}

function atualizarMetaRepres($id,$meta,$tipo)
{
    $cp = 'vl_meta';
    if($tipo == ''){
        $cp = 'vl_meta';
    }else{
        $cp = 'vl_meta_quinz';
    }
    $cmd = "update pub.metas_repres set $cp ='$meta' where 
            meta_repres_id = $id";
    sc_exec_sql($cmd,"especw");
}

function sincrMetaRepres($ano,$mes,$repres,$meta,$tipo='')
{

    $id = getIdAnoMesRepres($mes,$ano,$repres);
    if($id == 0){
        $id = inserirMetaRepres($ano,$mes,$repres,$meta,$tipo);
        $acao = "Inclusão";
    }else{
        atualizarMetaRepres($id,$meta,$tipo);
        $acao = "Alteração";
    }
    return array('id' =>$id, 'acao' => $acao);
}
function getMetaCorrentePorListaRepres($listaCodRep,$mes='')
{
    $anoAtual = getParteDtCorrente('ano');
    $mesAtual = getParteDtCorrente('mes');
    $filtro = "and ano = $anoAtual and mes = $mesAtual";
    if($mes <> ''){
        $filtro = "and ano = $anoAtual and mes = $mes";
    }
    $retorno = getMetaPorListaRepres($listaCodRep,$filtro);
    return $retorno;

}
function getMetaPorListaRepres($listaCodRep,$filtro='')
{

    $aMeta = array();
    $aMetaQunz = array();
    $vlMetaTotal = 0;
    $vlMetaTotalQuinz = 0;
    $vlMetaCorrente = 0;
    $vlMetaQuinzCorrente = 0;
    $vlMetaGeral = 0;
    $vlMetaQuinzGeral = 0;
    //$aVendedores = getVendedores();
    //var_Dump($aVendedores);
    //$listaCodRep = $aVendedores['lista_completa'];
    if($listaCodRep == ''){
        $listaCodRep = 0;
    }
    $tabela   = " pub.metas_repres ";
    $campos   = " vl_meta, cod_repres, vl_meta_quinz ";
    $condicao = "  cod_repres in($listaCodRep) ";
    $condicao.=$filtro;
    //echo "<h1>cond = $condicao </h1>";
     $aRet = retornoMultReg($tabela,$campos,$condicao);
    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $codRepres      = $aRet[$i]['cod_repres'];
            $vlMeta         = $aRet[$i]['vl_meta'];
            $vlMetaQuinz    = $aRet[$i]['vl_meta_quinz'];
            //$aMeta[]   = array('cod_repres' => $codRepres, 'vl_meta' =>$vlMeta);
            $aMeta[$codRepres]     = $vlMeta;
            $aMetaQunz[$codRepres] = $vlMetaQuinz;
            if($codRepres == [codRepIni]){
                $vlMetaCorrente      = $vlMeta;
                $vlMetaQuinzCorrente = $vlMetaQuinz;
            }else{
                $vlMetaTotal      += $vlMeta;
                $vlMetaTotalQuinz += $vlMetaQuinz;
            }
        }
        $vlMetaGeral = $vlMetaCorrente + $vlMetaTotal;
        $vlMetaQuinzGeral = $vlMetaQuinzCorrente + $vlMetaTotalQuinz;
    }
    //echo "<h1>cheguei aqui sem erro</h1>";
    return array( 'vl_meta_corrente' => $vlMetaCorrente,'meta_vend' => $aMeta,
                  'vl_total_sem_corrente' => $vlMetaTotal,
                  'vl_total' => $vlMetaGeral,
                  'vl_meta_quinz_corrente' => $vlMetaQuinzCorrente,'meta_vend_quinz' => $aMetaQunz,
                  'vl_total_quinz_sem_corrente' => $vlMetaTotalQuinz,
                  'vl_quinz_total' => $vlMetaQuinzGeral);
}

function getMetaMesCorrente($mes='',$tpMeta='mensal')
{
    $anoAtual = getParteDtCorrente('ano');
    $mesAtual = getParteDtCorrente('mes');
    $meta     = 0;
    $tabela   = " pub.metas_repres ";
    if($tpMeta == 'quinzenal'){
        $campos   = " sum(vl_meta_quinz) as vl_meta ";
    }else{
        $campos   = " sum(vl_meta) as vl_meta ";
    }

    $condicao = "  ano = $anoAtual and mes = $mesAtual ";
    if($mes <> ''){
        $condicao = "  ano = $anoAtual and mes = $mes ";
    }
    $aRet = retornoMultReg($tabela,$campos,$condicao);
    //var_dump($aRet);
    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $meta += $aRet[$i]['vl_meta'];
        }
    }
    return $meta;

}

function getMetaQuinzenalCor($codrep){
    $ano = date('Y');
    $mes = date('m');
    $aReg = getReg('espec','metas_repres','cod_repres',$codrep,
                'vl_meta_quinz',"ano = $ano and mes = $mes");

    if(is_array($aReg)){
        $metaQuinz = $aReg[0]['vl_meta_quinz'];
    }else{
        $metaQuinz = 0;
    }

    return $metaQuinz;


}





?>
