<?php
//__NM__Pesquisas do Pedido de Venda__NM__FUNCTION__NM__//
function criarPesqPedVenda($aWpPedVenda,$codWp)
{
    /************************************************************
    Objetivo
    ---------------------------------------------------------
    Criar um novo registro na tabela wp_ped_venda
    que armazena os resultados das pesquisas de pedidos
    permitindo a junção das tabelas de pedidos PI e PE
    ---------------------------------------------------------
    aWpPedVenda - array com os campos e valores a serem inclusos
    na tabela.
    codWp - codigo da pesquisa atual
     *************************************************************/
    $prepostoWp = '';

    if(is_array($aWpPedVenda) == true and ! is_null($aWpPedVenda))
    {
        $qt = count($aWpPedVenda);
        for($i = 0;$i < $qt;$i++)
        {

            $codEstabel  = $aWpPedVenda[$i]["cod-estabel"];
            if ($codEstabel == 0){
                continue;
            }
            $nomeAbrev   = str_replace("'","''",$aWpPedVenda[$i]["nome-abrev"]);
            if($aWpPedVenda[$i]["nr-container"] == '')
            {
                $nrContainer = 0;
            }
            else
            {
                $nrContainer = $aWpPedVenda[$i]["nr-container"];
            }

            inserirLogDb('Linha',$i,__FUNCTION__);
            $nrPedCli    	  = $aWpPedVenda[$i]["nr-pedcli"];
            $dtImplant   	  = $aWpPedVenda[$i]["dt-implant"];
            $codSitPed   	  = $aWpPedVenda[$i]["cod-sit-ped"];
            $tipoPedido  	  = $aWpPedVenda[$i]["tp-pedido"];
            $noAbReppri  	  = $aWpPedVenda[$i]["no-ab-reppri"];
            $codSitAval  	  = $aWpPedVenda[$i]["cod-sit-aval"];
            $codSitPreco 	  = $aWpPedVenda[$i]["cod-sit-preco"];
            $vlLiqPed    	  = $aWpPedVenda[$i]["vl-liq-ped"];
            $vlDesconto  	  = $aWpPedVenda[$i]["val-desconto-total"] ;
            $vlBruto          = $aWpPedVenda[$i]["vl-liq-ped"] + $aWpPedVenda[$i]["val-desconto-total"];
            $codEmitente      = $aWpPedVenda[$i]["cod-emitente"];
            $prepostoWp       = $aWpPedVenda[$i]["preposto"];
            $completo         = $aWpPedVenda[$i]["completo"];
            $codPrior         = $aWpPedVenda[$i]["cod-priori"];
            $moCodigo         = $aWpPedVenda[$i]["mo-codigo"];
            $naoAprovar       = $aWpPedVenda[$i]["l-nao-aprovar"];
            //$nomeRep          = $aWpPedVenda[$i]["nome_abrev_repres"];
            //$percComis        = $aWpPedVenda[$i]["perc_repres"];

            //var_dump($aWpPedVenda[$i]);
            if($aWpPedVenda[$i]["origem"] == '')
            {
                $origem           = 0;
            }
            else
            {

                $origem = $aWpPedVenda[$i]["origem"];
            }
            if($vlDesconto == '')
            {
                $vlDesconto   = 0;
            }


            $percComis = tratarNumero($percComis);
            $array = array(
                "cod_estabel"       =>$codEstabel,
                "nr_container"      =>$nrContainer,
                "nome_abrev"        =>$nomeAbrev,
                "nr_pedcli"         =>$nrPedCli,
                "dt_implant"        =>$dtImplant,
                "cod_sit_ped"       =>$codSitPed,
                "tp_pedido"         =>$tipoPedido,
                "no_ab_reppri"      =>$noAbReppri,
                "cidade"            =>'',
                "estado"            =>'',
                "cidade_cif"        =>'',
                "cod_sit_aval"      =>$codSitAval,
                "cod_sit_preco"     =>$codSitPreco,
                "vl_liquido"        =>$vlLiqPed,
                "vl_desconto"       =>$vlDesconto,
                "vl_bruto"          =>$vlBruto,
                "cod_wp"            =>$codWp,
                "cod_emitente"      =>$codEmitente,
                "preposto"          =>$prepostoWp,
                "origem"            =>$origem,
                "completo"          =>$completo,
                "mo_codigo"         =>$moCodigo
                //"nome_abrev_repres" =>$nomeRep,
                //"perc_repres"       =>$percComis

            );
            $cmdSql = convertArrayEmInsert('PUB."wp_ped_venda"',$array);

            //echo $cmdSql."</br>";
            $comando = sc_exec_sql($cmdSql,"especw");

            //echo "<h1>DEPOIS DO COMANDO</h1></br>";
        }
    }
}
function apagarPesqPedVenda($wp)
{
    $cmdSql = "delete from PUB.wp_ped_venda where cod_wp = '$wp'";
    //echo "criação da pesquisa:".$cmdSql;
    sc_exec_sql($cmdSql,"especw");

}
?>