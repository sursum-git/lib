<?php
//__NM__Pesquisa Web Nota fiscal__NM__FUNCTION__NM__//
function criarPesqNF($aWpNotaFiscal,$codWp,$banco='progress')
{
    /************************************************************
    Objetivo
    ---------------------------------------------------------
    Criar um novo registro na tabela wp_nota_fiscal
    que armazena os resultados das pesquisas de Notas Fiscais
    ---------------------------------------------------------
    aWpNotaFiscal - array com os campos e valores a serem inclusos
    na tabela.
    codWp - codigo da pesquisa atual
     *************************************************************/
    $prepostoPesqNF = '';
    if(is_array($aWpNotaFiscal) == true)
    {
        for($i = 0;$i < count($aWpNotaFiscal);$i++)
        {

            $nrNotaFis  		= $aWpNotaFiscal[$i]["nr-nota-fis"];
            $serie   			= $aWpNotaFiscal[$i]["serie"];
            $codEmitente    	= $aWpNotaFiscal[$i]["cod-emitente"];
            $noAbReppri 		= $aWpNotaFiscal[$i]["no-ab-reppri"];
            $codRep         	= $aWpNotaFiscal[$i]["cod-rep"];
            $nrPedcli			= $aWpNotaFiscal[$i]["nr-pedcli"];
            $vlTotNota	 		= $aWpNotaFiscal[$i]["vl-tot-nota"];
            $valDescontoTotal  	= $aWpNotaFiscal[$i]["val-desconto-total"];
            $vlBruto			= $aWpNotaFiscal[$i]["vl-tot-nota"] + $aWpNotaFiscal[$i]["val-desconto-total"];
            $espDocto			= $aWpNotaFiscal[$i]["esp-docto"];
            $codEstabel   		= $aWpNotaFiscal[$i]["cod-estabel"];
            $cidade				= str_replace("'","''",$aWpNotaFiscal[$i]["cidade"]) ;
            $estado				= $aWpNotaFiscal[$i]["estado"];
            $dtImplant          = $aWpNotaFiscal[$i]["dt-implant"];
            $prepostoPesqNF     = $aWpNotaFiscal[$i]["preposto"];
            $motivosDevol		= $aWpNotaFiscal[$i]["desc_devol"];
            //--echo 'desconto total:'.$valDescontoTotal ;
            if($banco == 'progress'){
                $esquema = "PUB.";
            }else{
                $esquema = "";
            }
            $cmdSql = "
                insert into ".$esquema."wp_nota_fiscal
                (nr_nota_fis,
                 serie,
                 cod_emitente,
                 no_ab_reppri,
                 cod_repres,
                 nr_pedcli,
                 vl_nota,
                 vl_desconto,
                 vl_bruto,
                 tipo_nota,
                 cod_estabel,
                 cidade,
                 estado,
                 dt_emis_nota,
                 preposto,
                 cod_wp,
				 desc_devol
                  )
                 values
                ('$nrNotaFis', 
                  '$serie',
                  $codEmitente,
                 '$noAbReppri',
                  $codRep,
                 '$nrPedcli',
                 '$vlTotNota',				 
                 '$valDescontoTotal',
				 '$vlBruto',
                 '$espDocto',
                 '$codEstabel', 
                 '$cidade',
                 '$estado',
                 '$dtImplant',
                 '$prepostoPesqNF',
                 '$codWp',
				 '$motivosDevol')" ;
            //--echo $cmdSql."</br>";
            if($banco == 'progress'){
                sc_exec_sql($cmdSql,"especw");
            }else{
                sc_exec_sql($cmdSql,"wp");
            }


            //echo "<h1>DEPOIS DO COMANDO</h1></br>";
        }
    }
}
function apagarPesqNotaFiscal($wp)
{
    $cmdSql = "delete from PUB.wp_nota_fiscal where cod_wp = '$wp'";
    //echo "criação da pesquisa:".$cmdSql;
    sc_exec_sql($cmdSql,"espec");

}
?>