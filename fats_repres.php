<?php

function getRankingRepres($periodo,$numMes='',$tipoVl='fat_liq'){

    //$condMesCorrente = "mes = month(curdate())";
    if($numMes == ''){
        $numMes = date('m');
    }
    $condMes = "mes = $numMes";
    $condAnoCorrente = "ano = year(curdate())";
    switch ($periodo){
        case 'ano_corrente':
            $condicao = $condAnoCorrente;
            break;
        case 'mes_corrente':
            $condicao = " $condAnoCorrente and $condMes";

            break;
    }

    $aRet = array();
    $aDados = getDados('multi', 'PUB.fats_repres',
        'cod_rep,  
                sum(vl_desconto) as vl_desc,
                sum(vl_desconto_dev) as vl_desc_dev,
                sum(vl_devolucoes) as devol,
                sum(vl_faturamento) as vl_fat,
                sum(vl_meta) as meta'
                ,
        " $condicao  group by cod_rep",
        'espec');
    if(is_array($aDados)){
        $tam = count($aDados);
        for($i=0;$i<$tam;$i++){
            $vlFat      = $aDados[$i]['vl_fat'];
            $vlDesc     = $aDados[$i]['vl_desc'];
            $vlDevol    = $aDados[$i]['devol'];
            $vlDescDev  = $aDados[$i]['vl_desc_dev'];
            $vlMeta     = $aDados[$i]['meta'];
            switch($tipoVl){
                case 'fat_liq':
                    $aDados[$i]['valor'] = $vlFat  + $vlDesc + $vlDevol + $vlDescDev;
                    break;
                case 'perc_meta':
                    if($vlMeta <> 0){
                        $aDados[$i]['valor'] = ($vlFat  + $vlDesc + $vlDevol + $vlDescDev) / $vlMeta * 100;
                    }else{
                        $aDados[$i]['valor']  = 0;
                    }
                    break;
            }
        }
        foreach ($aDados as $key => $row) {
            $aRanking[$key]  = $row['valor'];
        }
        array_multisort($aRanking,SORT_DESC,SORT_NUMERIC,$aDados);

        $ger =  getLoginCorrente();
        $codGer = buscarCodRep($ger);
        $hierarq = getTpHierarquiaGer($codGer);
        if($hierarq == 3){
            $listaRepres = getListaRepresGer();
            $aListaRepres = explode(',',$listaRepres);
            $tamHirarq = count($aListaRepres);
        }
        //echo "<h1>Lista = $listaRepres</h1>";

        $iPosicao = 0;
        for($i=0;$i<$tam;$i++){
            $codRep     = $aDados[$i]['cod_rep'];
            $nomeAbrev = getNomeAbrevRepres($codRep);
            if($hierarq == 3){
                if(strpos($listaRepres,$nomeAbrev) === false){
                    continue;
                }
                $iPosicao++;
                $aRet[$codRep]  = "$nomeAbrev - {$iPosicao}º / $tamHirarq ";
            }else{
                $iPosicao++;
                $aRet[$codRep]  = "$nomeAbrev - {$iPosicao}º / $tam ";
            }
        }

    }
    return $aRet;
}


function getDadosFatsRepresClientes($logMes=1,$periodo,$represIni='000000',$represFim='999999',$mes){
    $listaClientes = 0;
    $listaReps = 0;
    $loginCorrente =  getLoginCorrente();
    $codRepLogin = buscarCodRep($loginCorrente);
    $codGer = $codRepLogin;
    $tipoUsuario = getTipoUsuario($loginCorrente);
    $hierarq = getTpHierarquiaGer($codGer);
    if($hierarq == 3){
        $listaReps = getListaRepresGer('cod');
    }
    if($logMes == 2){
        $condMesCorrente = "mes = $mes - 1";
    }else{
        $condMesCorrente = "mes = $mes";
    }
    $condAnoCorrente = "ano = year(curdate())";
    switch ($periodo){
        case 'ano_corrente':
            if($tipoUsuario <> 2){
                if($hierarq == 3 and $represIni <> $represFim){
                    $condicao = "$condAnoCorrente and cod_rep in($listaReps)" ;
                }else{
                    $condicao = "$condAnoCorrente and cod_rep >= $represIni and cod_rep <= $represFim" ;
                }
            }else{
                $condicao = "$condAnoCorrente and cod_rep = $codRepLogin" ;
            }

            break;
        case 'mes_corrente':
            if($tipoUsuario <> 2){
                if($hierarq == 3 and $represIni <> $represFim){
                    $condicao = " $condAnoCorrente and $condMesCorrente and cod_rep in($listaReps) ";
                }else{
                    $condicao = " $condAnoCorrente and $condMesCorrente and cod_rep >= $represIni and cod_rep <= $represFim ";
                }
            }else{
                $condicao = " $condAnoCorrente and $condMesCorrente and cod_rep = $codRepLogin ";
            }


            break;
    }
    $aDados = getDados('multi','PUB.fats_repres_clientes','
    cod_emitente as cliente,    
    sum(qt_dev),
    sum(qt_fat),   
    sum(vl_desconto),
    sum(vl_desconto_dev),
    sum(vl_devolucoes) as devol,
    sum(vl_faturamento) as fat,
    sum(vl_meta)',"$condicao  group by cliente
    order by sum(vl_faturamento) desc",'espec');

    $qtCli = 0;
    //var_dump($aDados);
    if(is_array($aDados)){
        foreach($aDados as $dados){
            $cliente = $dados['cliente'];
            //$dev     = $dados['devol'];
            $fat     = $dados['fat'];
            //echo "<h1>dados = $cliente - $dev</h1>";
            if($fat > 0){
                if($cliente <> 0){
                    $listaClientes = util_incr_valor($listaClientes,$cliente,',');
                }
            }

        }
        //echo "<h1>Lista = $listaClientes</h1>";
        $aCliente = explode(',',$listaClientes);
        $qtCli = count($aCliente);
        //echo "<h1>qt = $qtCli</h1>";
    }


    return $qtCli;

}

function getQtAnoFatsRepresClientes($represIni='000000',$represFim='999999'){
    $listaClientes = 0;
    $listaReps = 0;
    $condAnoCorrente = "ano = ". date('Y');
    $loginCorrente =  getLoginCorrente();
    $codRepLogin = buscarCodRep($loginCorrente);
    $codGer = $codRepLogin;
    $tipoUsuario = getTipoUsuario($loginCorrente);
    $hierarq = getTpHierarquiaGer($codGer);
    $mesCur = date('m');

        for($i=0;$i<$mesCur;$i++){
            $meses = $mesCur - $i;
            $condMesCorrente = "mes = $meses";
            //echo "<h1>cond = $condMesCorrente</h1>";
            if($tipoUsuario <> 2){
                if($hierarq == 3 and $represIni <> $represFim){
                    $listaReps = getListaRepresGer('cod');
                    $condicao = " $condAnoCorrente and $condMesCorrente and cod_rep in($listaReps) ";
                }else{
                    $condicao = " $condAnoCorrente and $condMesCorrente and cod_rep >= $represIni and cod_rep <= $represFim ";
                }
            }else{
                $condicao = " $condAnoCorrente and $condMesCorrente and cod_rep = $codRepLogin ";
            }

            $aDados = getDados('multi','PUB.fats_repres_clientes','
            cod_emitente as cliente,    
            sum(qt_dev),
            sum(qt_fat),   
            sum(vl_desconto),
            sum(vl_desconto_dev),
            sum(vl_devolucoes),
            sum(vl_faturamento) as fat,
            sum(vl_meta)',"$condicao  group by cliente
            order by sum(vl_faturamento) desc",'espec');

            //$qtCli = 0;
            if(is_array($aDados)){
                foreach($aDados as $dados){
                    $cliente = $dados['cliente'];
                    //echo "<h1>qtClis = $cliente / rep = $represIni</h1>";
                    $fat     = $dados['fat'];
                    if($fat > 0){
                        if($cliente <> 0){
                            $listaClientes = util_incr_valor($listaClientes,$cliente,',');
                        }
                    }
                    $aCliente = explode(',',$listaClientes);
                    $qtCli = count($aCliente);
                }


            }
            //echo "<h1>qtClis = $qtCli / rep = $represIni</h1>";

        }

    return $qtCli;
}

function getQtNfsFatsRepres($tipo='mes_corrente',$codRepIni,$codRepFim,$listaRep=0,$mesQtNfs){

    $qtNfs = 0;
    $codRepLogin = buscarCodRep([usr_login]);
    $codRepGer = $codRepLogin;
    $tipoUsuario = getTipoUsuario([usr_login]);
    $hierarq = getTpHierarquiaGer($codRepGer);
    $mes = date('m');
    $anoQtNfs = date('Y');
    if($tipoUsuario <> 2){
        if($hierarq == 3 and $codRepIni <> $codRepFim){
            if($tipo == 'mes_corrente'){
                $condicao = "mes >= $mesQtNfs and mes <= $mesQtNfs and ano = $anoQtNfs and cod_rep in ($listaRep) ";
            }else{
                $condicao = "mes >= 01 and mes <= $mes and ano = $anoQtNfs and cod_rep in ($listaRep) ";
            }

        }else{
            if($tipo == 'mes_corrente'){
                $condicao = "mes >= $mesQtNfs and mes <= $mesQtNfs and ano = $anoQtNfs and cod_rep >= $codRepIni and cod_rep <= $codRepFim ";
            }else{
                $condicao = "mes >= 01 and mes <= $mes and ano = $anoQtNfs and cod_rep >= $codRepIni and cod_rep <= $codRepFim ";
            }
        }
    }else{
        if($tipo == 'mes_corrente'){
            $condicao = "mes >= $mesQtNfs and mes <= $mesQtNfs and ano = $anoQtNfs and cod_rep = $codRepLogin ";
        }else{
            $condicao = "mes >= 01 and mes <= $mes and ano = $anoQtNfs and cod_rep = $codRepLogin ";
        }
    }

    $aDados = getDados('multi','PUB.fats_repres','qt_nf',$condicao,'espec');
    //var_dump($aDados);
    if(is_array($aDados)){

            foreach($aDados as $aQtNfs){
                $qtNfsMes = $aQtNfs['qt_nf'];
                $qtNfs = $qtNfs + $qtNfsMes;
            }

    }

    return $qtNfs;

}

function getDadosFatsRepres($tipo='mes_corrente',$campo='',$codRepIni,$codRepFim,$listaRep=0,$mes){

    $vlTot = 0;
    $codRepLogin = buscarCodRep([usr_login]);
    $codRepGer = $codRepLogin;
    $tipoUsuario = getTipoUsuario([usr_login]);
    $hierarq = getTpHierarquiaGer($codRepGer);
    $mesCor = date('m');
    $ano = date('Y');
    if($tipoUsuario <> 2){
        if($hierarq == 3 and $codRepIni <> $codRepFim){
            if($tipo == 'mes_corrente'){
                $condicao = "mes >= $mes and mes <= $mes and ano = $ano and cod_rep in ($listaRep) ";
            }else{
                $condicao = "mes >= 01 and mes <= $mesCor and ano = $ano and cod_rep in ($listaRep) ";
            }

        }else{
            if($tipo == 'mes_corrente'){
                $condicao = "mes >= $mes and mes <= $mes and ano = $ano and cod_rep >= $codRepIni and cod_rep <= $codRepFim ";
            }else{
                $condicao = "mes >= 01 and mes <= $mesCor and ano = $ano and cod_rep >= $codRepIni and cod_rep <= $codRepFim ";
            }
        }
    }else{
        if($tipo == 'mes_corrente'){
            $condicao = "mes >= $mes and mes <= $mes and ano = $ano and cod_rep = $codRepLogin ";
        }else{
            $condicao = "mes >= 01 and mes <= $mesCor and ano = $ano and cod_rep = $codRepLogin ";
        }
    }



    $aDados = getDados('multi','PUB.fats_repres',$campo,$condicao,'espec');
    //var_dump($aDados);
    if(is_array($aDados)){

        foreach($aDados as $reg){
            $vlMes  = $reg[$campo];
            $vlTot += $vlMes;
        }

    }
    $vlTot = str_replace('-','',$vlTot);
    return $vlTot;

}

function ultAtuFatsRepres(){

    $sql = 'select dt_hr_ini from pub.transacoes
where transacao_id in (select max(transacao_id) from pub.fats_repres)';

    $aRegs = getRegsSqlLivre($sql,'dt_hr_ini','espec');
    $dtHrTrans = $aRegs[0]['dt_hr_ini'];
    $dtHrTrans = substr($dtHrTrans,'0','19');
    $dtHrTrans = date_create($dtHrTrans);
    $dtHrTrans = date_format($dtHrTrans,'d-m-Y H:i:s');
    return $dtHrTrans;



}

function getItensMaisVendidos($codRepIni,$codRepFim){

    $sql = "SELECT    
    top 10
    it_codigo as item,      
    sum(vl_desconto) as vl_desc,
    sum(vl_desconto_dev) as vl_desc_dev,
    sum(vl_devolucoes) as vl_dev, 
    sum(vl_faturamento) as fat
FROM
    PUB.fats_repres_clientes_prod tb01    
    where ano = year(curdate()) and cod_rep >= $codRepIni and cod_rep <= $codRepFim
    group by it_codigo     
    order by sum(vl_faturamento) desc
    ";

    $aRegs = getRegsSqlLivre($sql,'item,vl_desc,vl_desc_dev,vl_dev,fat','espec');
    $itensFats = '';
    foreach($aRegs as $itensFat){
        $item      = $itensFat['item'];
        $vlDesc    = $itensFat['vl_desc'];
        $vlDecDev  = $itensFat['vl_desc_dev'];
        $vlDev     = $itensFat['vl_dev'];
        $devol     = str_replace('-','',$vlDev);
        $vlFat     = $itensFat['fat'];
        $vlTotFat  = $vlFat + $vlDesc - ($devol + $vlDecDev);
        $vlTotFat  = formatarPreco('real',$vlTotFat);
        $dadosItem = $item." - ".$vlTotFat;
        //echo "<h1>$item - $vlDesc - $vlDecDev - $vlDev - $vlFat - $vlTotFat - $dadosItem</h1>";
        $itensFats = util_incr_valor($itensFats,$dadosItem,' / ');
        //echo "<h1>dados = $itensFats</h1>";
    }
    //echo "<h1>dados = $itensFats</h1>";
    return $dadosItem;

}


