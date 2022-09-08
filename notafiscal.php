<?php
//__NM__Nota fiscal__NM__FUNCTION__NM__//


function getDadosNf($codEstabel,$nrPedido){   

    $nrNf     = 0;
    $tipo     = "unico";
    $tabela   = " pub.\"nota-fiscal\" ";
    $campos   = "\"nr-nota-fis\" as nr_nf";
    $condicao = "  \"cod-estabel\" = $codEstabel and \"nr-pedcli\" = $nrPedido";	
    $conexao  = "med";
	$aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $nrNf = $aDados[0]['nr_nf'];

    }
    return $nrNf;		
	
}	
	
function getCondEntreEmpresas($origem,$tipo,$valor)
{
    $entreEmpresas = '';
    switch ($origem){
        case 'venda':
            $condMed = " AND nf.\"cod-emitente\" <> 1 
                    and nf.\"cod-estabel\" = '5'";
            $condIma  = " AND nf.\"cod-emitente\" <> 10535 
                    and nf.\"cod-estabel\" = '1'";
            break;
        case 'devolucao':
            $condMed = " AND dev.\"cod-emitente\" <> 1 ";
            $condIma  = " AND nf.\"cod-emitente\" <> 10535 ";
            break;
    }

    switch ($tipo)
    {
        case 'base':
            switch($valor){
                case 'med':
                    $entreEmpresas = $condMed ;
                    break;
                case 'ima':
                    $entreEmpresas = $condIma ;
                    break;
            }
            break;
        case 'empresa':
        case 'MED':
            $entreEmpresas = $condMed;
            break;
        case 'IMA':
            $entreEmpresas = $condIma;
            break;
            break;

    }
    return $entreEmpresas;


}

function getTotVendNfMes($base="med",$filtroMes='',$logFiltroTipoUsuario=true, $logPorRepres=1)
{
    $retorno = 0;
    $aDatasMes = retonarDatasMesCorrente();
    $dtIni     = $aDatasMes[0]['dtIni'];
    $dtFim     = $aDatasMes[0]['dtFim'];
    $filtro = " and nf.\"dt-emis-nota\" >= '$dtIni' and nf.\"dt-emis-nota\" <= '$dtFim' ";
    if($filtroMes <> ''){
        $filtro .= $filtroMes;
    }
    if($logPorRepres == 1){
        $retorno = getTotVendNfRepres($base,$filtro,$logFiltroTipoUsuario);
    }else{
        $retorno = getTotVendNf($base,$filtro,$logFiltroTipoUsuario);
    }
    return $retorno;
}

function getTotDevNfRepres($base='med',$filtro,$logFiltroTipoUsuario=true)
{
    $dev         = array();
    $desc        = array();
    $vlDevTot    = 0;
    $vlDescTot   = 0;
    $total       = 0;
    $condLista = '';
    $tabela   = "  pub.\"devol-cli\" relacto_venda ,PUB.\"docum-est\" dev,pub.\"nota-fiscal\" venda,
    pub.\"ped-venda\" ped,PUB.\"natur-oper\" nat, pub.\"ped-repre\" ped_rep   ";
    $campos   = " sum(relacto_venda.\"vl-devol\") as totdev, 
round(sum(relacto_venda.\"vl-devol\" * (((100/(1 - ped.\"val-pct-desconto-total\"/100)) -100) /100))|2) as totdesc,
  ped_rep.\"nome-ab-rep\" as nome_abrev";
    $condicao = ' relacto_venda."nat-operacao"  = nat."nat-operacao"
            and nat."tipo-compra"               = 3 and nat."tp-rec-desp" = 1
            and relacto_venda."serie-docto"     = dev."serie-docto"
            and relacto_venda."nro-docto"       = dev."nro-docto"
            and relacto_venda."cod-emitente"    = dev."cod-emitente"
            and relacto_venda."nat-operacao"    = dev."nat-operacao"
            and venda."cod-estabel"             = relacto_venda."cod-estabel"
            and venda."nr-nota-fis"             = relacto_venda."nr-nota-fis"
            and venda."serie"                   = relacto_venda."serie"
            and ped."nr-pedido"                 = venda."nr-pedcli" 
            and ped_rep."nr-pedido"             = ped."nr-pedido"';
    
    if($logFiltroTipoUsuario == true) {
        $condicaoTpUsuario = aplicarFiltroTipoUsuarioPV('devol-cli','venda' );
        $listaNfs = getListaDevNfFiltroPedRepre($condicao.$filtro.$condicaoTpUsuario);
        if($listaNfs == ''){
            $listaNfs = "''";
            $condLista = " and \"nr-nota-fis\" in ($listaNfs)";
        }
    }
    $condicao .=  $filtro. $condLista;
    //echo "<h1>condição:$condicao</h1>";
    $entreEmpresas = getCondEntreEmpresas('devolucao','base',$base);
    $condicao .= $entreEmpresas;
    $condicao .= " and venda.\"cod-estabel\" = '5' group by nome_abrev";

    $aRet = retornoMultReg($tabela,$campos,$condicao,$base);

    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $nomeRep = $aRet[$i]['nome_abrev'];
            $vlDev   = $aRet[$i]['totdev'];
            $vlDesc  = $aRet[$i]['totdesc'];
            $codRep = buscarCodRep($nomeRep);
            $dev[$codRep]  = $vlDev ;
            $desc[$codRep] = $vlDesc ;
            $vlDevTot += $vlDev;
            $vlDescTot += $vlDesc;
        }
        $total = $vlDevTot + $vlDescTot;
    }
    $retorno = array('dev' => $dev, 'desc' => $desc ,
        'total' => $total,'total_dev'=> $vlDevTot,
        'total_desc' => $vlDescTot) ;
    return $retorno;


}


function getTotDevNf($base='med',$filtro,$logFiltroTipoUsuario=true)
{
    $dev         = array();
    $desc        = array();
    $vlDevTot    = 0;
    $vlDescTot   = 0;
    $total       = 0;
    $condLista = '';
    $tabela   = "  pub.\"devol-cli\" relacto_venda ,PUB.\"docum-est\" dev,pub.\"nota-fiscal\" venda,pub.\"ped-venda\" ped,PUB.\"natur-oper\"  nat  ";
    $campos   = " sum(relacto_venda.\"vl-devol\") as totdev, 
        round(sum(relacto_venda.\"vl-devol\" * (((100/(1 - ped.\"val-pct-desconto-total\"/100)) -100) /100))|2) as totdesc,
          venda.\"cod-rep\" as cod_rep";
    $condicao = "  relacto_venda.\"nat-operacao\" = nat.\"nat-operacao\"
        and nat.\"tipo-compra\" = 3 and nat.\"tp-rec-desp\" = 1
        and relacto_venda.\"serie-docto\" = dev.\"serie-docto\"
        and relacto_venda.\"nro-docto\"  = dev.\"nro-docto\"
        and relacto_venda.\"cod-emitente\" = dev.\"cod-emitente\"
        and relacto_venda.\"nat-operacao\" = dev.\"nat-operacao\"
        and venda.\"cod-estabel\" = relacto_venda.\"cod-estabel\"
        and venda.\"nr-nota-fis\" = relacto_venda.\"nr-nota-fis\"
        and venda.\"serie\"       = relacto_venda.\"serie\"
        and  ped.\"nr-pedido\" = venda.\"nr-pedcli\"    ";


    if($logFiltroTipoUsuario == true) {
        $condicaoTpUsuario = aplicarFiltroTipoUsuarioPV('devol-cli','venda' );
        $listaNfs = getListaDevNfFiltroPedRepre($condicao.$filtro.$condicaoTpUsuario);
        if($listaNfs == ''){
            $listaNfs = "''";
            $condLista = " and dev.\"nro-docto\" in ($listaNfs)";
        }

    }
    $condicao .=  $filtro.$condLista;
    $entreEmpresas = getCondEntreEmpresas('devolucao','base',$base);
    $condicao .= $entreEmpresas;
    $condicao .= "group by venda.\"cod-rep\"";

    $aRet = retornoMultReg($tabela,$campos,$condicao,$base);
    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $codRep = $aRet[$i]['cod_rep'];
            $vlDev   = $aRet[$i]['totdev'];
            $vlDesc = $aRet[$i]['totdesc'];
            $dev[$codRep]  = $vlDev ;
            $desc[$codRep] = $vlDesc ;
            $vlDevTot += $vlDev;
            $vlDescTot += $vlDesc;
        }
        $total = $vlDevTot + $vlDescTot;
    }
    $retorno = array('dev' => $dev, 'desc' => $desc ,
        'total' => $total,'total_dev'=> $vlDevTot,
        'total_desc' => $vlDescTot) ;
    return $retorno;


}

function getTotVendNf($base="med",$filtro,$logFiltroTipoUsuario=true)
{
    //$retorno    = array();
    $nf         = array();
    $desc       = array();
    $vlNfTot    = 0;
    $vlDescTot  = 0;
    $vlTotal      = 0;
    $condLista  = '';
    $tabela     = " pub.\"nota-fiscal\" nf , PUB.\"natur-oper\" nat ,
                  pub.\"ped-venda\" pedido ";
    $campos   = " sum(nf.\"vl-tot-nota\") as tot, 
                     sum(nf.\"vl-tot-nota\" * (((100/(1 - pedido.\"val-pct-desconto-total\"/100)) -100) /100)) as totdesc,
                     nf.\"cod-rep\" as cod_rep";
    $condicao = " nf.\"dt-cancela\" is null 
                 and nf.\"nat-operacao\" = nat.\"nat-operacao\" 
                 and nat.\"tp-rec-desp\" = 1 and nat.\"tipo-compra\" <> 3                 
                 and nf.\"nr-pedcli\"   = pedido.\"nr-pedido\"                
                  " ;



    if($logFiltroTipoUsuario == true){
        $condicaoTpUsuario= aplicarFiltroTipoUsuarioPV('nota-fiscal','nf');
        $listaNfs = getListaNfFiltroPedRepre($condicao.$filtro.$condicaoTpUsuario);

        if($listaNfs == ''){
            $listaNfs = "''";
        }
        $condLista = " and \"nr-nota-fis\" in ($listaNfs)";
    }
    $condicao .=  $filtro. $condLista;
    //echo "<h1>condição:$condicao</h1>";

    $entreEmpresas = getCondEntreEmpresas('venda','base',$base);
    $condicao .= $entreEmpresas;
    $condicao .= "group by nf.\"cod-rep\"";
    //$condicao .= "group by ped.\"cod-priori\", nf.\"nr-nota-fis\"";

    $aRet = retornoMultReg($tabela,$campos,$condicao,$base);
    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $codRep = $aRet[$i]['cod_rep'];
            $vlNf   = $aRet[$i]['tot'];
            $vlDesc = $aRet[$i]['totdesc'];
            if($vlNf > 0){
                $nf[$codRep]  = $vlNf ;
            }
            if($vlDesc > 0){
                $desc[$codRep] = $vlDesc ;
            }
            $vlNfTot += $vlNf;
            $vlDescTot += $vlDesc;
        }
    }
    $vlTotal = $vlNfTot + $vlDescTot;
    $retorno = array('nf' => $nf, 'desc' => $desc ,
        'total' => $vlTotal,'total_nf'=> $vlNfTot,
        'total_desc' => $vlDescTot) ;
    return $retorno;
}

function getTotVendNfRepres($base="med",$filtro,$logFiltroTipoUsuario=true)
{
    //$retorno    = array();
    inserirLogDb('Inicio Func','sim',__FUNCTION__);
    $nf         = array();
    $desc       = array();
    $vlNfTot    = 0;
    $vlDescTot  = 0;
    $total      = 0;
    $condLista  = '';
    $tabela     = " pub.\"nota-fiscal\" nf , PUB.\"natur-oper\" nat ,
                  pub.\"ped-venda\" pedido , pub.\"ped-repre\" ped_rep ";
    $campos   = " sum(nf.\"vl-tot-nota\") as tot, 
                     sum(nf.\"vl-tot-nota\" * (((100/(1 - pedido.\"val-pct-desconto-total\"/100)) -100) /100)) as totdesc,
                     ped_rep.\"nome-ab-rep\" as nome_abrev";
    $condicao = " nf.\"dt-cancela\" is null 
                 and nf.\"nat-operacao\" = nat.\"nat-operacao\" 
                 and nat.\"tp-rec-desp\" = 1 and nat.\"tipo-compra\" <> 3                 
                 and nf.\"nr-pedcli\"   = pedido.\"nr-pedido\"
                 and ped_rep.\"nr-pedido\" = pedido.\"nr-pedido\"
                 and ped_rep.\"nome-ab-rep\" <> 'fulano'                
                  " ;


    if($logFiltroTipoUsuario == true){
        inserirLogDb('filtroPorUsuario','sim',__FUNCTION__);
        $condicaoTpUsuario= aplicarFiltroTipoUsuarioPV('nota-fiscal','nf');
        $listaNfs = getListaNfFiltroPedRepre($condicao.$filtro.$condicaoTpUsuario);
        if($listaNfs == ''){
            $listaNfs = "''";
            $condLista = " and \"nr-nota-fis\" in ($listaNfs)";
            inserirLogDb('lista preenchida?','nao',__FUNCTION__);
        }else {
            inserirLogDb('lista preenchida?',"sim - $listaNfs",__FUNCTION__);
        }
    }else{
        inserirLogDb('filtroPorUsuario','nao',__FUNCTION__);
    }
    $condicao .=  $filtro. $condLista;
    //echo "<h1>condição:$condicao</h1>";
    $entreEmpresas = getCondEntreEmpresas('venda','base',$base);
    $condicao .= $entreEmpresas;
    $condicao .= "group by nome_abrev";
    //$condicao .= "group by ped.\"cod-priori\", nf.\"nr-nota-fis\"";
    $aRet = retornoMultReg($tabela,$campos,$condicao,$base);
    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $nomeRep = $aRet[$i]['nome_abrev'];
            $vlNf    = $aRet[$i]['tot'];
            $vlDesc  = $aRet[$i]['totdesc'];
            inserirLogDb('i - nomerep - vlnf - vldesc',"$i - $nomeRep - $vlNf - $vlDesc",__FUNCTION__);
            $codRep = buscarCodRep($nomeRep);
            if($vlNf > 0){
                $nf[$codRep]  = $vlNf ;
            }
            if($vlDesc > 0){
                $desc[$codRep] = $vlDesc ;
            }
            $vlNfTot += $vlNf;
            $vlDescTot += $vlDesc;
            inserirLogDb('total parcial vlnftot - vldesctot',"$vlNfTot - $vlDescTot",__FUNCTION__);
        }
    }
    $total = $vlNfTot + $vlDescTot;
    //echo "Tot = <h1>$total</h1>";
    $retorno = array('nf' => $nf, 'desc' => $desc ,
        'total' => $total,'total_nf'=> $vlNfTot,
        'total_desc' => $vlDescTot) ;
    //var_dump($retorno);
    return $retorno;
}



/*function getTotDevNfRepres($base='med',$filtro,$logFiltroTipoUsuario=true)
{
    $dev         = array();
    $desc        = array();
    $vlDevTot    = 0;
    $vlDescTot   = 0;
    $total       = 0;
    $condLista = '';
    $tabela   = "  pub.\"devol-cli\" relacto_venda ,PUB.\"docum-est\" dev,pub.\"nota-fiscal\" venda,
    pub.\"ped-venda\" ped,PUB.\"natur-oper\" nat, pub.\"ped-repre\" ped_rep   ";
    $campos   = " sum(relacto_venda.\"vl-devol\") as totdev, 
round(sum(relacto_venda.\"vl-devol\" * (((100/(1 - ped.\"val-pct-desconto-total\"/100)) -100) /100))|2) as totdesc,
  ped_rep.\"nome-ab-rep\" as nome_abrev";
    $condicao = ' relacto_venda."nat-operacao"  = nat."nat-operacao"
            and nat."tipo-compra"               = 3 and nat."tp-rec-desp" = 1
            and relacto_venda."serie-docto"     = dev."serie-docto"
            and relacto_venda."nro-docto"       = dev."nro-docto"
            and relacto_venda."cod-emitente"    = dev."cod-emitente"
            and relacto_venda."nat-operacao"    = dev."nat-operacao"
            and venda."cod-estabel"             = relacto_venda."cod-estabel"
            and venda."nr-nota-fis"             = relacto_venda."nr-nota-fis"
            and venda."serie"                   = relacto_venda."serie"
            and ped."nr-pedido"                 = venda."nr-pedcli" 
            and ped_rep."nr-pedido"             = ped."nr-pedido"';

    if($logFiltroTipoUsuario == true) {
        $condicaoTpUsuario = aplicarFiltroTipoUsuarioPV('devol-cli','venda' );
        $listaNfs = getListaDevNfFiltroPedRepre($condicao.$filtro.$condicaoTpUsuario);
        if($listaNfs == ''){
            $listaNfs = "''";
            $condLista = " and \"nr-nota-fis\" in ($listaNfs)";
        }
    }
    $condicao .=  $filtro. $condLista;
    //echo "<h1>condição:$condicao</h1>";
    $entreEmpresas = getCondEntreEmpresas('devolucao','base',$base);
    $condicao .= $entreEmpresas;
    $condicao .= "group by nome_abrev";


    $aRet = retornoMultReg($tabela,$campos,$condicao,$base);
    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $nomeRep = $aRet[$i]['nome_abrev'];
            $vlDev   = $aRet[$i]['totdev'];
            $vlDesc  = $aRet[$i]['totdesc'];
            $codRep = buscarCodRep($nomeRep);
            $dev[$codRep]  = $vlDev ;
            $desc[$codRep] = $vlDesc ;
            $vlDevTot += $vlDev;
            $vlDescTot += $vlDesc;
        }
        $total = $vlDevTot + $vlDescTot;
    }
    $retorno = array('dev' => $dev, 'desc' => $desc ,
        'total' => $total,'total_dev'=> $vlDevTot,
        'total_desc' => $vlDescTot) ;

    return $retorno;


}*/
function getTotDevNfMes($base='med',$filtroMes='',$logFiltroTipoUsuario=true,$logPorRepres=1)
{
    $aDatasMes = retonarDatasMesCorrente();
    $dtIni     = $aDatasMes[0]['dtIni'];
    $dtFim     = $aDatasMes[0]['dtFim'];
    $filtro = " and relacto_venda.\"dt-devol\" >= '$dtIni' and relacto_venda.\"dt-devol\" <= '$dtFim' ";
    if($filtroMes <> ''){
        $filtro .= $filtroMes;
    }
    if($logPorRepres == 1){
        $retorno = getTotDevNfRepres($base,$filtro,$logFiltroTipoUsuario);
    }else{
        $retorno = getTotDevNf($base,$filtro,$logFiltroTipoUsuario);
    }

    return $retorno;
}


function buscarNF($empresa,$filtro,$filtroEsp,$filtroUsuario='')
{

    $aNF = array();
    $lAchou = false;
    $prepostoNF = "";
    $aDadosPedExt = "";
    $entreEmpresas = "";
    $sql = "
	        select \"nr-nota-fis\" , 
			       \"serie\", 				   
				   \"cod-emitente\",
				   \"no-ab-reppri\",
				   \"cod-rep\",
                   \"nr-pedcli\",
				   \"vl-tot-nota\",
				   \"val-desconto-total\",
				   \"esp-docto\",
				   \"cod-estabel\",
				   cidade,
				   estado,
                   \"dt-emis-nota\"
			       from PUB.\"nota-fiscal\" nf , PUB.\"natur-oper\" nat
				where nf.\"idi-sit-nf-eletro\" not in (4) and nf.\"dt-cancela\" is null"  ;
    $joinNaturOper = " and nf.\"nat-operacao\" = nat.\"nat-operacao\" 
                       and \"tp-rec-desp\" = 1 and \"tipo-compra\" <> 3";
    $sql .= $filtro.$joinNaturOper;
    $lista = getListaNfFiltroPedRepre($filtro.$filtroUsuario);

    $sql.=" and nf.\"nr-nota-fis\" in ($lista)";
    //echo "sql buscarNF:".$sql."</br>";
    // entre empresas foi colocado em uma função separada para se reutilizado em outras funções
    $entreEmpresas = getCondEntreEmpresas('venda','empresa',$empresa);
    switch($empresa){
        case "IMA":
            //$entreEmpresas = " AND \"cod-emitente\" <> 10535 and \"cod-estabel\" = '1'";
            $sql .= $entreEmpresas;
            sc_select(nf , $sql, "ima");
            //echo "Conectando o banco Ima - Buscando Faturamento</br>";
            break;
        case "MED":
            //$entreEmpresas = " AND \"cod-emitente\" <> 1 and \"cod-estabel\" = '5'";
            $sql .= $entreEmpresas;
            sc_select(nf , $sql, "med");
            //echo "Conectando o banco Med - Buscando Faturamento</br>";
            break;
    }
    if ({nf} === false){ echo "Erro de acesso ou Sintaxe - função buscarNF";}
     else
     {
         while (!$nf->EOF)
         {
             //--echo "nota-fiscal:".$nf->fields[0]."</br>";
             $aDadosPedExt = retornarDadosPedVendaExt($nf->fields[9],$nf->fields[5],$filtroEsp);
             if($aDadosPedExt != ""){
                 @$prepostoNF       = $aDadosPedExt[0]["preposto"];

             }
             else{
                 @$prepostoNF      = '';
             }
             if($aDadosPedExt != "" || $filtroEsp == '')	{
                 //--echo"</br>serie".$nf->fields[1];
                 $serie = $nf->fields[1];
                 $descontoTotal = calcularDescontoNF($nf->fields[9],$serie,$nf->fields[0]);
                 //echo "<h1>depois desconto NF</h1>";
             }

             $aNF[]  = array(
                 "nr-nota-fis"         	=> $nf->fields[0] 	,
                 "serie" 			   	=> $nf->fields[1] 	,
                 "cod-emitente"			=> $nf->fields[2]	,
                 "no-ab-reppri"			=> $nf->fields[3]	,
                 "cod-rep"				=> $nf->fields[4]	,
                 "nr-pedcli"			=> $nf->fields[5]	,
                 "vl-tot-nota"			=> $nf->fields[6]	,
                 "val-desconto-total"	=> $descontoTotal	,
                 "esp-docto"			=> $nf->fields[8]	,
                 "cod-estabel"			=> $nf->fields[9]	,
                 "cidade"				=> $nf->fields[10]	,
                 "estado"				=> $nf->fields[11]	,
                 "dt-implant"			=> sc_date_conv($nf->fields[12],
                     "aaaa-mm-dd","mm/dd/aaaa"),
                 "preposto"				=> @$prepostoNF,
                 "desc_devol"			=> ''
             );
             $lAchou = true;
             $nf->MoveNext();
         }
         $nf->Close();
     }
     if($lAchou == false){
         $aNF = '';
     }
     return $aNF;
}
function buscarDevolucoes($empresa,$filtro,$filtroEsp,$filtroNfOri,$filtroUsuario='')
{
    /*
      Objetivo: buscar as notas fiscais de devolução de vendas, sejam proprias ou de clientes e retornar em um array
    */
    $aNF = array();
    $lAchou = false;
    $aNFVendaDevol = "";
    $prepostoNF = "";

    $sql = "
	        select \"nro-docto\" , 
			       \"serie-docto\", 				   
				   \"cod-emitente\",                  
				   \"tot-valor\",				  
				   \"esp-docto\",
				   \"cod-estabel\",
				   cidade,
				   uf,
                   \"dt-trans\",
				   PUB.\"docum-est\".\"nat-operacao\"
			       from PUB.\"docum-est\" docum, PUB.\"natur-oper\" nat
	       "  ;
    $joinNaturOper = " and  docum.\"nat-operacao\" = nat.\"nat-operacao\"
                       and \"tipo-compra\" = 3 and \"tp-rec-desp\" = 1
                     ";
    $sql .= $filtro.$joinNaturOper;

    //--echo "sql buscar devolucoes:".$sql;
    switch($empresa)
    {
        case "IMA":
            $entreEmpresas = " AND \"cod-emitente\" <> 10535 and \"cod-estabel\" = '1'";
            $sql .= $entreEmpresas;
            sc_select(nf , $sql, "ima");
            //echo "Conectando o banco Ima - Buscando Devoluções</br>";
            break;
        case "MED":
            $entreEmpresas = " AND \"cod-emitente\" <> 1 and \"cod-estabel\" = '5'";
            $sql .= $entreEmpresas;
            sc_select(nf , $sql, "med");
            //echo "Conectando o banco Med -  - Buscando Devoluções</br>";
            break;
    }
    if ({nf} === false)
     {
         echo "Erro de acesso ou sintaxe - função buscarDevolucoes!";
     }
     else
     {
         while (!$nf->EOF)
         {
             //busca os itens que foram devolvidos, assim como, os motivos de devolução.
             $descDevol = buscarMotivosDevolucao($nf->fields[5],$nf->fields[1],$nf->fields[0]);

             //--echo 'chamada Nvendadevol-estab:'.$nf->fields[5].' - emitente:'.$nf->fields[2].' -  NF:'.$nf->fields[0].' - serie'.$nf->fields[1].' - nat oper:'.$nf->fields[9].' - filtro'.$filtroEsp;
             //busca a nota fiscal de venda que foi devolvida
             $aNFVendaDevol = buscarNFVendaDevol($nf->fields[5], $nf->fields[2],$nf->fields[0],$nf->fields[1],
                 $nf->fields[9],$filtroEsp,$filtroNfOri,$filtroUsuario);

             if($aNFVendaDevol != "")
             {
                 $noAbReppri 	= $aNFVendaDevol[0]["noAbReppri"];
                 $codRep			= $aNFVendaDevol[0]["codRep"];
                 $nrPedcli		= $aNFVendaDevol[0]["nrPedcli"];
                 $prepostoNF      = $aNFVendaDevol[0]["preposto"];
                 $codPriori      = $aNFVendaDevol[0]["codPriori"];
                 $desconto       = $nf->fields[3] * -1 /(1 - (20 - $codPriori) % 10 / 10 )  *  ((20 - $codPriori) % 10) /10;
                 //echo "valor:".$nf->fields[3]." - prioridade:".$codPriori;
                 $aNF[]  = array(
                     "nr-nota-fis"         	=> $nf->fields[0] 	,
                     "serie" 			   	=> $nf->fields[1] 	,
                     "cod-emitente"			=> $nf->fields[2]	,
                     "no-ab-reppri"			=> $noAbReppri,
                     "cod-rep"				=> $codRep,
                     "nr-pedcli"			=> $nrPedcli	,
                     "vl-tot-nota"			=> $nf->fields[3]	* -1,
                     "val-desconto-total"	=> $desconto	,
                     "esp-docto"			=> $nf->fields[4]	,
                     "cod-estabel"			=> $nf->fields[5]	,
                     "cidade"				=> $nf->fields[6]	,
                     "estado"				=> $nf->fields[7]	,
                     "dt-implant"			=> sc_date_conv($nf->fields[8],"aaaa-mm-dd","mm/dd/aaaa"),
                     "preposto"				=> $prepostoNF,
                     "desc_devol"			=> $descDevol
                 );
                 $lAchou = true;
             }
             $nf->MoveNext();
         }
         $nf->Close();
     }
     if($lAchou == false){
         $aNF = '';
     }
     return $aNF;
}
function buscarNFVendaDevol($codEstabel, $codEmitente,$nroDocto,$serieDocto,$natOperacao,$filtroEsp,$filtroNfOri,$filtroUsuario='')
{
    /*
       Objetivo: Buscar os dados da nota fiscal de venda que originou a devolução, assim como os dados do preposto da tabela ped-venda-ext
    */
    $aDadosPedExt = '';
    $aRetorno = array();
    $lAchou = false;
    $prepostoDevol = '';
    $sql = " select top 1 nf.\"nr-pedcli\", nf.\"cod-rep\" ,             
          nf.\"no-ab-reppri\", ped.\"cod-priori\", nf.\"vl-tot-nota\"		    
            from PUB.\"devol-cli\" devol 
            inner join PUB.\"nota-fiscal\" nf
            on nf.\"nr-nota-fis\"           = devol.\"nr-nota-fis\"
			and nf.\"serie\"                = devol.\"serie\"
			and nf.\"cod-emitente\"         = devol.\"cod-emitente\"
            inner join  PUB.\"ped-venda\" ped      
            on nf.\"nr-pedcli\"             = ped.\"nr-pedcli\"
            and nf.\"nome-ab-cli\"          = ped.\"nome-abrev\"     
            inner join pub.\"ped-repre\" ped_rep
            on ped.\"nr-pedido\" = ped_rep.\"nr-pedido\"
            where devol.\"cod-estabel\"     = '$codEstabel' 
            and devol.\"cod-emitente\"      = $codEmitente 
            and devol.\"nro-docto\"         = '$nroDocto'	  
            and devol.\"serie-docto\"       = '$serieDocto'
            and devol.\"nat-operacao\"      = '$natOperacao'			
		";
    $sql .= $filtroNfOri.$filtroUsuario;
    //--echo '</br>sql nota fiscal devolucao'.$sql.'</br>';
    switch($codEstabel)
    {
        case '1':
            sc_lookup(devolCli, $sql, "ima" );
            break;
        case '5':
            sc_lookup(devolCli, $sql, "med" );
            break;
    }
    if ({devolCli} != false)
	{
        $nrPedcli     = {devolCli[0][0]};
		$codRep       = {devolCli[0][1]};
		$noAbReppri   = {devolCli[0][2]};
		$codPriori    = {devolCli[0][3]};
		$vlTotNotaOri = {devolCli[0][4]};
		//--echo '</br>estab:'.$codEstabel." Pedido:".$nrPedcli." filtro:".$filtroEsp;
		if($nrPedcli == '')
            $nrPedcli = 0;
		$aDadosPedExt = retornarDadosPedVendaExt($codEstabel,$nrPedcli,$filtroEsp);

		if($aDadosPedExt != '')
            $prepostoDevol = $aDadosPedExt[0]["preposto"];
        else
            $prepostoDevol = '';

		if($aDadosPedExt != '' || strstr($filtroEsp,'preposto') == false)
        {
            $aRetorno[]   = array("nrPedcli"  		=> $nrPedcli,
                "codRep"     		=> $codRep,
                "noAbReppri" 		=> $noAbReppri,
                "preposto"   		=> $prepostoDevol,
                "codPriori"  		=> $codPriori,
                "vlTotNotaOri" 	=> $vlTotNotaOri
            );
            $lAchou = true;
        }
	}
	else
	{
        //echo "Erro de acesso ou sintaxe  ou retorno vazio - função buscarNFVendaDevol</br>sql:".$sql;

    }
	if($lAchou == false){
	    $aRetorno = '';
    }
	return $aRetorno;
}
function buscarDevolItemNF($codEstabel,$serie,$nrNotaFis,$item,$referencia)
{
    $aRetorno = array();
    $lAchou = false;
    $sql = "select sum(\"qt-devolvida\"), sum(\"vl-devol\")  from PUB.\"devol-cli\"
			where  \"cod-estabel\" = '$codEstabel'
            and     serie = '$serie'
            and     \"nr-nota-fis\" = '$nrNotaFis'
            and     \"it-codigo\"   = '$item'
            and     \"cod-refer\"  = '$referencia'";

    switch($codEstabel)
    {
        case '1':
            sc_lookup(devolitem, $sql, "ima" );
            break;
        case '5':
            sc_lookup(devolitem, $sql, "med" );
            break;
    }
    if ({devolitem} != false)
   {
       $aRetorno[] = array("qt-devolucao" => {devolitem[0][0]},"vl-devol" => {devolitem[0][1]});
       $lAchou = true;
   }
    if($lAchou == false){
        $aRetorno = '';
    }
    return $aRetorno;

}
function calcularDescontoNF($codEstabel,$serie,$nrNotaFis)
{
    //echo 'serie - desconto'.$serie;
    $totalDesconto = 0;
    $descNota = 0;
    $sql = " select substr(PUB.\"it-nota-fisc\".\"char-2\",1500,10)
            from PUB.\"it-nota-fisc\"
			where \"nr-nota-fis\" = '$nrNotaFis'
			and \"serie\" = '$serie'
			and \"cod-estabel\" = '$codEstabel' ";
    //--echo 'sql desconto:'.$sql.'</br>';
    switch($codEstabel)
    {
        case "1":
            sc_select(nfdesc , $sql, "ima");
            //echo "Conectando o banco ima - Calculo Desconto Nota Fiscal</br>";
            break;
        case "5":
            sc_select(nfdesc , $sql, "med");
            //echo "Conectando o banco Med - Calculo Desconto Nota Fiscal</br>";
            break;
    }
    if ({nfdesc} === false)
     {
         echo "Erro de acesso ou sintaxe - função calcularDescontoNF!";
     }
     else
     {
         while (!$nfdesc->EOF)
         {
             $descNota = converterDecimal($nfdesc->fields[0]);
             if(is_numeric($descNota) or is_float($descNota)){
                 $totalDesconto += $descNota;
             }

             $nfdesc->MoveNext();
         }
         $nfdesc->Close();
     }
	//--echo "total desconto:".$totalDesconto.'</br>';
    return $totalDesconto;
}
function calcularTotaisFatur($empresa,$filtro)
{
    $aTotais = array();
    $lAchou = false;
    $valor = 0;
    $quant = 0;
    $sql = "select sum(\"vl-tot-nota\"), count(\"nr-nota-fis\") from
			PUB.\"nota-fiscal\" nf ";
    $sql .= $filtro;
    //echo "sql calcularTotaisFatur:".$sql;
    switch($empresa)
    {
        case '1':
            sc_lookup(total, $sql,"ima");
            break;
        case '5':
            sc_lookup(total, $sql,"med");
            break;
    }
    if ({total} === false)
    {
        echo "Erro de acesso  - calcularTotaisFatur. Mensagem = " . {total_erro} ;
    }
    elseif (empty({total}))
    {
        $valor = 0;
        $quant = 0;

        $aTotais[] = array("valor" => $valor, "qte" => $quant);
        $lAchou = true;

    }
    else
    {
        $valor = {total[0][0]} ;
		$quant = {total[0][1]};
		$aTotais[] = array("valor" => $valor , "qte" => $quant);
		$lAchou = true;
	}
    if($lAchou == false){
        $aTotais = '';
    }
	return $aTotais;
}
function calcularTotaisDevol($empresa,$filtro)
{
    $valor = 0;
    $quant = 0;
    $aTotais = array();
    $lAchou = false;
    $sql = "
	        select sum(\"vl-devol\"), count(PUB.\"docum-est\".\"cod-estabel\")				  
				  
			       from PUB.\"docum-est\" docum, PUB.\"natur-oper\" nat, PUB.\"devol-cli\" devol, PUB.\"nota-fiscal\" nf
	       "  ;
    $joinNaturOper = " and  docum.\"nat-operacao\" = nat.\"nat-operacao\"
                       and \"tipo-compra\" = 3 and \"tp-rec-desp\" = 1
                     ";
    $joinDevolCli  = " 
						and   docum.\"nro-docto\"        = devol.\"nro-docto\"
						and   docum.\"serie-docto\"      = devol.\"serie-docto\"
						and   docum.\"cod-estabel\"      = devol.\"cod-estabel\"
						and   docum.\"nat-operacao\"     = devol.\"nat-operacao\"
						and   docum.\"cod-emitente\"     = devol.\"cod-emitente\"
					 ";
    $joinNFVenda   = "
						and   nf.\"nr-nota-fis\"      = devol.\"nr-nota-fis\"
						and   nf.\"serie\"            = devol.\"serie\"
                        and   nf.\"cod-estabel\"      = devol.\"cod-estabel\"						
						and   nf.\"cod-emitente\"     = devol.\"cod-emitente\"
";



    $sql .= $filtro.$joinNaturOper.$joinDevolCli.$joinNFVenda;
    //--echo "sql calcularTotaisDevol:".$sql;

    switch($empresa)
    {
        case '1':
            sc_lookup(total, $sql,"ima");
        break;
        case '5':
            sc_lookup(total, $sql,"med");
        break;
    }
    if ({total} === false)
    {
       echo "Erro de acesso  - calcularTotaisDevol. Mensagem = " . {total_erro} ;
    }
    elseif (empty({total}))
    {
        $valor = 0;
        $quant = 0;

        $aTotais[] = array("valor" => $valor, "qte" => $quant);
        $lAchou = true;

    }
    else
    {
        $valor = {total[0][0]}  ;
        $quant = {total[0][1]};
        $aTotais[] = array("valor" => $valor , "qte" => $quant);
        $lAchou = true;
    }
    if($lAchou == false){
        $aTotais = '';
    }
    return $aTotais;

}
function buscarMotivosDevolucao($cod_estab,$serieParam,$documento)
{
    $descDevol = '';
    $sql = "select distinct pub.\"devol-cli\".\"codigo-rejei\", pub.\"cod-rejeicao\".descricao
			from pub.\"devol-cli\", pub.\"cod-rejeicao\" where \"nro-docto\" = '$documento'
			and \"serie-docto\" = '$serieParam' and \"cod-estabel\" = '$cod_estab'
			and  pub.\"cod-rejeicao\".\"codigo-rejei\" = pub.\"devol-cli\".\"codigo-rejei\" " ;
    switch($cod_estab){
        case '1':
            sc_select(itensdevol, $sql,"ima");
            break;
        case '5':
            sc_select(itensdevol, $sql,"med");
            break;
    }
    if ({itensdevol} === false){
    echo "Erro de Acesso.Função buscarMotivosDevolucao Mensagem=". {itensdevol_erro};
	}
	else{
    //echo "itens encontrados";
        while (!$itensdevol->EOF){
            /*$itCodigo    = $itensdevol->fields[0];
            $codRefer    = $itensdevol->fields[1];
            $nroDocto    = $itensdevol->fields[2];
            $serieDocto  = $itensdevol->fields[3];*/
            $codRejeicao = $itensdevol->fields[0];
            $descRejeicao = $itensdevol->fields[1];
            $descDevol  .= "$codRejeicao - $descRejeicao|" ;
            $itensdevol->MoveNext();
        }
        $itensdevol->Close();
   }
   return substr($descDevol,0,200);
}
/*function calculcarDescAplicNF($priori)
{
   IMPORTANTE:  Esta forma de calculo foi comentada, pois, não funciona visto que nem sempre o valor do
   desconto é exato, pois, é calculado por item, dando uma diferença referente adiantamento

    $perc = calcularPercDescPedido($priori);
    $prop = 1- $perc;
    $result = ((100/ $prop) - 100) / 100;
    return $result;

}*/

function getUltsNotaTriang($clienteParam)
{
   $logTriang = 0;
   $aReg = getDados('unico','pub."nota-fiscal" nf, pub."natur-oper" nat',
            'top 3 "dt-emis-nota"',
             "nf.\"nat-operacao\" = nat.\"nat-operacao\"
                        and   nat.\"log-oper-triang\" = 1
                        and \"cod-emitente\" = $clienteParam order by \"dt-emis-nota\" desc ",
       "med");
   if(is_array($aReg)){
       $logTriang = 1;
   }
   return $logTriang;
}


?>