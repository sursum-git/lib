<?php
//__NM____NM__FUNCTION__NM__//

function criarTbCodWpEstPreco($codWp)
{
    $tb = "wp_estoque_preco_$codWp";
    //echo "<h1>wp:$codWp</h1>";
    $cmd = "create table $tb like wp_estoque_preco_000 ";
    sc_exec_sql($cmd,"dinamico");
    return $tb;

}
function criarPesqEstoqueUnico($aWpEstoquePrecoPe,$aWpEstoquePrecoPi,$tb)
{
    if(is_array($aWpEstoquePrecoPe) and is_array($aWpEstoquePrecoPi)){
        $aEst = array_merge($aWpEstoquePrecoPe,$aWpEstoquePrecoPi);
    }else{
        if(is_array($aWpEstoquePrecoPe)){
            $aEst = $aWpEstoquePrecoPe ;
        }
        if(is_array($aWpEstoquePrecoPi)){
            $aEst = $aWpEstoquePrecoPi ;
        }
    }
    //ordenar criação por item, ordem ref
    if(is_array($aEst)){
        // Obtem a lista de colunas
        foreach ($aEst as $key => $row) {
            $ordemRef[$key]  = $row['ordem_ref'];
            $item[$key]      = $row['it_codigo'];

        }
        if(is_array($aEst)){
            array_multisort($item,SORT_ASC, $ordemRef, SORT_ASC, $aEst);
        }
    }
    foreach($aEst as $reg){
        $container = $reg['nr_container'];
        if($container <> '' and $container <> 0){
            criarPesqEstoquePrecoPI($reg,'',$tb);
        }else{
            criarPesqEstoquePreco($reg,'',$tb);
        }
    }





}

function criarPesqEstoquePreco($aWpEstoquePreco,$codWp,$tb)
{
    /************************************************************
    Objetivo
    ---------------------------------------------------------
    Criar um novo registro na tabela wp_estoque_saldo
    que armazena os resultados das pesquisas de Saldo e Preço
    ---------------------------------------------------------
    $aWpEstoquePreco - array com os campos e valores a serem inclusos
    na tabela.
    codWp - codigo da pesquisa atual
     *************************************************************/

    if(is_array($aWpEstoquePreco) == true and count($aWpEstoquePreco) > 0)
    {


        $itCodigo			= $aWpEstoquePreco["it_codigo"];
        $descItem           = $aWpEstoquePreco["desc_item"];
        //busca o link do book na memoria da sessao.
        //$descItem          = incrDescrBook($itCodigo,$descItem);
        $descItem           = str_replace(chr(39),chr(34),$descItem);
        $codRefer 			= $aWpEstoquePreco["cod_refer"];
        $qtSaldo	    	= $aWpEstoquePreco["qt_saldo"];
        $qtPedido	 		= $aWpEstoquePreco["qt_pedido"];
        $codEstabel   		= $aWpEstoquePreco["cod_estabel"];
        $container			= $aWpEstoquePreco["nr_container"];
        $qtSaldoVenda  	    = $aWpEstoquePreco["qt_saldo_venda"];
        $qtCarrinho         = $aWpEstoquePreco["qt_carrinho"];
        $qtEmDigitacao      = $aWpEstoquePreco["qt_carrinho_geral"];
        $qtEmDigitacao      = tratarNumero($qtEmDigitacao);
        //$referEmOrdem       = getOrdemCodRefer($codRefer);
        $referEmOrdem       = $aWpEstoquePreco['ordem_ref'];

           $cmdSql = "
                   insert into $tb
                   (it_codigo,
                    cod_refer,
                    qt_saldo,
                    qt_pedido,
                    cod_estabel,
                    qt_saldo_venda,
                    desc_item,
                    nr_container,
                    qt_carrinho,
                    qt_disponivel,
                    qt_em_digitacao,
                    cod_refer_ordem,
                    dt_hr_criacao
                     )
                    values
                   ('$itCodigo',
                     '$codRefer',
                     $qtSaldo,
                     $qtPedido,
                     '$codEstabel',
                     $qtSaldoVenda,
                     '$descItem',
                     $container,
                     $qtCarrinho,
                     $qtSaldo,
                     $qtEmDigitacao,
                     $referEmOrdem,
                    now())" ;
           $comando = sc_exec_sql($cmdSql,"dinamico");

    }

}
function criarPesqEstoquePrecoPI($aWpEstoquePreco,$codWp,$tb)
{
    /************************************************************
    Objetivo
    ---------------------------------------------------------
    Criar um novo registro na tabela wp_estoque_saldo
    que armazena os resultados das pesquisas de Saldo e Preço
    ---------------------------------------------------------
    $aWpEstoquePreco - array com os campos e valores a serem inclusos
    na tabela.
    codWp - codigo da pesquisa atual
     *************************************************************/

    if(is_array($aWpEstoquePreco) == true and count($aWpEstoquePreco) > 0)
    {


        $itCodigo			= $aWpEstoquePreco["it_codigo"];
        $descItem           = $aWpEstoquePreco["desc_item"];
        //busca o link do book na memoria da sessao.
        //$descItem          = incrDescrBook($itCodigo, $descItem);
        $descItem           = str_replace(chr(39),chr(34),$descItem);
        $codRefer 			= $aWpEstoquePreco["cod_refer"];
        $qtSaldo	    	= $aWpEstoquePreco["qt_pedida"];
        $qtPedido	 		= $aWpEstoquePreco["qt_vendida"];
        $codEstabel   		= $aWpEstoquePreco["cod_estabel"];
        $container			= $aWpEstoquePreco["nr_container"];
        $qtProgramada  	    = $aWpEstoquePreco["qt_saldo_com_carrinho"];
        $qtCarrinho         = $aWpEstoquePreco["qt_carrinho"];
        $qtDisp             = $aWpEstoquePreco["qt_disp"];
        $qtEmDigitacao = $aWpEstoquePreco["qt_carrinho_geral"];
        $qtEmDigitacao  = tratarNumero($qtEmDigitacao);
        $qtPedido       = tratarNumero($qtPedido);
        $qtSaldo        = tratarNumero($qtSaldo);
        $qtProgramada   = tratarNumero($qtProgramada);
        $qtCarrinho     = tratarNumero($qtCarrinho);
        //$referEmOrdem   = getOrdemCodRefer($codRefer);
        $referEmOrdem       = $aWpEstoquePreco['ordem_ref'];



        $logAtu = false;
        if($logAtu == false){
            $cmdSql = "
                insert into $tb
                ( it_codigo,
                 cod_refer,
                 qt_saldo,
                 qt_pedido,
                 cod_estabel,                
                 qt_programada,                
				 desc_item,
				 nr_container,
				 qt_carrinho,
				 qt_disponivel,				 			 
				 qt_em_digitacao,
				 cod_refer_ordem,
                 dt_hr_criacao
                  )
                 values
                ('$itCodigo', 
                  '$codRefer',
                  $qtSaldo,
                  $qtPedido,
                  '$codEstabel',                 
                  $qtProgramada,                 
				  '$descItem',
				  $container,
				  $qtCarrinho,
				  $qtDisp,				  			  
				  $qtEmDigitacao,
				  $referEmOrdem,
                 now())" ;
            //--echo $cmdSql."</br>";
            sc_exec_sql($cmdSql,"dinamico");


        }
    }
}

function getItensWpTodos($wp,$campos = '')
{
    $tabela = 'wp_estoque_preco_'.$wp;
    if($campos == ''){
        $campos  = getCpsWpEstPreco();
    }
//echo "<br>campos:$campos<br>";
    $aReg = getDados('multi',"$tabela",$campos,
        " coalesce(qt_saldo_venda,0) + coalesce(qt_programada,0) > 0 order by it_codigo, cod_refer" ,
        'dinamico');
//echo "<h1>Entreiiii</h1>";
    return $aReg;
}
function getCpsWpEstPreco()
{
    $campos   = "wp_estoque_preco_id,cod_estabel,it_codigo,cod_refer,qt_pedido,qt_saldo,
                qt_disponivel,qt_reservada,qt_programada,qt_solicitada,ped_web_id,
                desc_item,nr_container,preco_informado,dt_prev_chegada,liquida_ima,
                qt_saldo_venda,qt_carrinho,num_id_liquida_ima,cod_controle_preco,
                desc_preco,log_atualizado,preco_liquida_ima,qt_em_digitacao,
                vl_informado,cod_refer_ordem,dt_hr_criacao,vl_real,vl_dolar,
                tb_preco_id,num_moeda,agrup_pedido,log_divide_comis,
                perc_comis_vend,perc_comis_repres,vl_preco_prazo";
    return $campos;
}
function setCamposCalcEstPreco($id,$nivel=1,$campos='')
{
    /**
     * @param $nivel 1- consulta(atualiza todos os registros a partir do WP 2-registro(atualiza a partir do id apenas um registro)
     * @param $id( se nivel igual 1 deve ser passado o codwp senão deve ser passado o id do registro(wp_estpque_preco_id)
     */
    switch ($nivel){
        case 1:
            $condicao = "cod_wp = '$id'";
            break;
        case 2:
            $condicao = "wp_estoque_preco_id = $id";
            break;
    }
    $tipo     = "multi"; // unico ou multi
    $tabela   = " wp_estoque_preco ";
    if($campos == ''){
        $aCampos  = getCpsTbSessao('espec','wp_estoque_preco');
        $campos  = $aCampos['campos'];
    }
    $conexao  = "dinamico";
    $aDados = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $tam = count($aDados);
        for($i=0;$i<$tam;$i++){
            $itCodigo = $aDados[$i]['it_codigo'];
            $codRefer = $aDados[$i]['cod_refer'];
            $codEstab = $aDados[$i]['cod_estabel'];
            //$aPercLiquidaIma = getPercLiquidaIma($itCodigo,$codRefer);
            //$percLiquidaIma = $aPercLiquidaIma['perc'];
            $aPrecoLiquidaIma = getPrecoLiquidaIma($itCodigo,$codRefer);
            $precoLiquidaIma = $aPrecoLiquidaIma['preco_descto'];
            $idLiquidaIma = $aPrecoLiquidaIma['id'];
            //echo "<h1>preco liq.ima:$precoLiquidaIma</h1>";
            if($precoLiquidaIma <> '' and $precoLiquidaIma <> 0){
                //setPercLiquidaIma($itCodigo,$codRefer,$percLiquidaIma,$idLiquidaIma);
                setPrecoLiquidaIma($id,$nivel,$itCodigo,$codRefer,$precoLiquidaIma,$idLiquidaIma);

            }
        }
    }
}
function setPercLiquidaIma($itCodigo,$codRefer,$perc,$id)
{
    $perc = tratarNumero($perc);
    $cmd = "update pub.wp_estoque_preco set liquida_ima = $perc, num_id_liquida_ima = '$id'
            where it_codigo = '$itCodigo' and cod_refer = '$codRefer'";
    sc_exec_sql($cmd,"especw");

}
function setPrecoLiquidaIma($id,$nivel,$itCodigo,$codRefer,$valor,$idLiq)
{
    switch ($nivel){
        case 1:
            $condicao = " and cod_wp = '$id'";
            break;
        case 2:
            $condicao = " and wp_estoque_preco_id = $id";
            break;
    }

    $valor= tratarNumero($valor);
    $cmd = "update pub.wp_estoque_preco set preco_liquida_ima = $valor, num_id_liquida_ima = '$idLiq'
            where it_codigo = '$itCodigo' and cod_refer = '$codRefer' $condicao";
    //echo "<br>comando de atualização preco liq.ima:$cmd<br>";
    sc_exec_sql($cmd,"especw");

}
function getRegsEstPrecoPorWp($wp,$idItem=0)
{
    $tipo     = "multi"; // unico ou multi
    $tabela   = " wp_estoque_preco_{$wp} ";
    $campos   = getCpsWpEstPreco();
    if($idItem <> 0){
        $condicao = " wp_estoque_preco_id = $idItem";
    }else{
        $condicao = " 1 = 1 ";
    }

    $conexao  = "dinamico";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    return $aDados;
}
function getRegItemEstoqueWp($wp,$id,$campos,$filtroCompl='')
{
    $campoChave ='wp_estoque_preco_id' ;
    $tabela   = "wp_estoque_preco_$wp";
    $condFiltroCompl = '';
    if($campos == ''){
        $campos = getCpsWpEstPreco();
    }
    if($id <> 0){
        $condicao = "$campoChave = $id";
    } else{
        $condicao = '1 = 1 ';
    }

    if($filtroCompl <> ''){
        $condicao = util_incr_valor($condicao,$filtroCompl,' AND ',true);
    }

    //$tabela   = "pub.$tabela";
    $tipo     = "unico"; // unico ou multi
    $conexao  = "dinamico";
    $aRet = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    return $aRet;
}
function atuItemRefEstWp($wp,$item,$ref,$qtProgramada)
{
    $logAchou = false;
    $filtroCompl = " it_codigo = '$item' and cod_refer = '$ref' and cod_wp = '$wp' ";
    $aRegItem = getRegItemEstoqueWp($wp,0,'',$filtroCompl);
    if(is_array($aRegItem)){
        $idWp = $aRegItem[0]['wp_estoque_preco_id'];
        $cmd = "update pub.wp_estoque_preco set qt_programa = $qtProgramada 
                where wp_estoque_preco_id = $idWp ";
        sc_exec_sql($cmd,"especw");
        $logAchou = true;
    }
    return $logAchou;
}
function getItensWpSolic($wp,$campos='')
{
    $tabela = 'wp_estoque_preco_'.$wp;
    if($campos == ''){
        //$aCampos = getCpsTbSessao('espec',$tabela);
        //var_dump($aCampos);
        //$campos  = $aCampos['campos'];
        $campos = getCpsWpEstPreco();

    }

    $aReg = getDados('multi',$tabela,$campos," qt_carrinho > 0 and coalesce(qt_saldo_venda,0) + coalesce(qt_programada,0) > 0 " ,'dinamico') ;
    return $aReg;
}
function  atuSaldoItensWp($wp)
{
    $aReg                   = getItensWpSolic($wp);
    $aSitItens              = array();
    $logSitItens            = 0;
    $listaItensRefZerados   = '';
    $listaItensRefAlt       = '';
    $logDivergSaldo         = false;
    $aRetorno               = '';
    $log                    = '';
    if(is_array($aReg)){
        $tam = count($aReg);
        for($i=0;$i<$tam;$i++){
            $itemWp         =    $aReg[$i]['wp_estoque_preco_id'];
            $item           =    $aReg[$i]['it_codigo'];
            $ref            =    $aReg[$i]['cod_refer'];
            $container      =    $aReg[$i]['nr_container'];
            $qtPedida       =    $aReg[$i]['qt_carrinho'];
            $agrupPed       =    $aReg[$i]['agrup_pedido'];
            //echo "<h1>Antes getPrecosSaldoItemRef</h1>";
            $aSaldo         = getPrecosSaldoItemRef($item,$ref,$container);
            //$log        .= " <h1> wp_estoque_preco_id: $itemWp -  item: $item - ref: $ref - container: $container </h1> ";
            //echo "<h1>Depois getPrecosSaldoItemRef</h1>";
            //var_dump($aSaldo);
            //echo "<h1>Antes qt_saldo</h1>";
            if(is_array($aSaldo)) {
                $qtSaldo = $aSaldo['array'][0]['qt_saldo'];
            } else{
                $qtSaldo = 0;
            }
            //echo "<h1>depois qt_saldo</h1>";
            $qtSaldo = tratarNumero($qtSaldo);

            $aQtCarrinho = getItemRefPedWeb($item,$ref,$container,$agrupPed);
            //echo "<h1>Antes qt_pedida</h1>";
            if(is_array($aQtCarrinho)){
                $qtCarrinho = $aQtCarrinho['qt_pedida'];
            }else{
                $qtCarrinho = 0;
            }
            //echo "<h1>depois qt.pedida</h1>";
            $qtSaldo += $qtCarrinho;
            $log        .= " <h1> wp_estoque_preco_id: $itemWp -  item: $item - ref: $ref - container: $container - qt.Pedida: $qtPedida - qt.saldo: $qtSaldo - qt.carrinho : $qtCarrinho </h1> ";

            if($container <> 0 and $container <> ''){
                $setSaldo = "qt_programada = '$qtSaldo'";
            }else{
                $setSaldo  = " qt_saldo_venda = '$qtSaldo'";
            }
            $qtSaldo = round($qtSaldo,2);
            $qtPedida = round($qtPedida,2);
            if($qtSaldo < $qtPedida){
                $logDivergSaldo = true;
                $listaItensRefAlt = util_incr_valor($listaItensRefAlt,$itemWp);
                $cmd = " update wp_estoque_preco_{$wp} set  qt_carrinho = '$qtSaldo', 
                         $setSaldo  where wp_estoque_preco_id = $itemWp
                        ";
                sc_exec_sql($cmd,"dinamico");
                $qtDiverg = $qtSaldo - $qtPedida ; // alterado
                if($qtSaldo == 0) {
                    $listaItensRefZerados = util_incr_valor($listaItensRefZerados,
                        "$item - $ref",
                        ",",
                        true);
                }
                $aSitItens[$item][$ref] = $qtDiverg;
                $logSitItens = 1;
            }
        }
        if($logSitItens == 0 ){
            $aSitItens = '';
        }
        $aRetorno = array('log_diverg_saldo' => $logDivergSaldo,
            'array'=> $aSitItens,
            'lista_zerados' => $listaItensRefZerados,
            'log'=>$log,
            'lista_id_alterados'=> $listaItensRefAlt
        );
    }
    return $aRetorno;
}
/* NAO UTILIZADO
 * function atuVlInfItemPrecoWp($codWp,$item,$vlFinal)
{
    $vl = 0;
    $aRegs =  getDados('multi','pub.wp_estoque_preco','it_codigo,vl_informado',
        "it_codigo='$item' and cod_wp = '$codWp' ");
    if(is_array($aRegs)){
        $tam  = count($aRegs);
        $aInd = buscarIndice();

        for($i=0;$i<$tam;$i++){
            $itCodigo    = $aRegs[$i]['it_codigo'];
            $preco90     = getPrecoPrazoInd(90,);
            $vlInformado = $aRegs[$i]['vl_informado'];
            $wpId        = $aRegs[$i]['wp_estoque_preco_id'];
            if($vlInformado <> 0 and $vlInformado <> ''){
                $vl = $vlInformado;
            }else{
                $vl = $preco90;
            }
            if($vl == $vlFinal and $item == $itCodigo){
                $cmd = "update wp_estoque_preco_{$codWp} set vl_informado = '$vlFinal'
                       where wp_estoque_preco_id = $wpId ";
                sc_exec_sql($cmd,"dinamico");
            }
        }
    }

}*/
function incrDescrBook($itCodigo,$desc)
{

    if (isset([books_wp][$itCodigo]['book_pe']) and [books_wp][$itCodigo]['book_pe'] <> '') {
        $desc .= " BOOK PE:".[books_wp][$itCodigo]['book_pe'];
    }
    if (isset([books_wp][$itCodigo]['book_pi']) and [books_wp][$itCodigo]['book_pi'] <> '') {
        $desc .= " BOOK PI:".[books_wp][$itCodigo]['book_pi'];
    }
    return $desc;
}
function acertarItemRefWp($wp,$regAnt)
{
    $aItensWp = getRegsEstPrecoPorWp($wp);
    if(is_array($aItensWp)){
        $tam = count($aItensWp);
        for($i=0;$i< $tam; $i++){
            $comando = '';
            //$qtCarrinho = $aItensWp[$i]['qt_carrinho'];
            $item            = $aItensWp[$i]['it_codigo'];
            //$ref        = $aItensWp[$i]['cod_refer'];
            $id              = $aItensWp[$i]['wp_estoque_preco_id'];
            $precoLiquidaIma = $aItensWp[$i]['preco_liquida_ima'];
            $vlInformado     = $aItensWp[$i]['vl_informado'];
            if(is_null($vlInformado)){
                echo "<h1>valor informado EH nulo</h1>";
            }else{
                echo "<h1>valor informado nao é nulo é $vlInformado</h1>";
            }
            if($item == '' or is_null($precoLiquidaIma) or is_null($vlInformado)){
                if(is_array($regAnt)){
                    $tam2 = count($regAnt);
                    for($j=0;$j< $tam2; $j++){
                        $idAnt = $regAnt[$j]['wp_estoque_preco_id'];
                        if($id == $idAnt){
                            $itemAlterado = $regAnt[$j]['it_codigo'];
                            $comandoAtual = " it_codigo =  '$itemAlterado' ";
                            $comando = util_incr_valor($comando,$comandoAtual,' , ',true);

                            $refAlterado        = $regAnt[$j]['cod_refer'];
                            $precoLiquidaImaAlt = $regAnt[$j]['preco_liquida_ima'];
                            $vlInformadoAlt     = $regAnt[$j]['vl_informado'];
                            $comandoAtual = "  cod_refer  =   '$refAlterado' ";
                            $comando = util_incr_valor($comando,$comandoAtual,' , ',true);
                            if(is_null($precoLiquidaIma)){
                                $comandoAtual = " preco_liquida_ima = '$precoLiquidaImaAlt' ";
                            }
                            $comando = util_incr_valor($comando,$comandoAtual,' , ',true);
                            if(is_null($vlInformado)){
                                $comandoAtual = " vl_informado = '$vlInformadoAlt' ";
                            }
                            $comando = util_incr_valor($comando,$comandoAtual,' , ',true);

                            if($comando <> ''){
                                $comandoFinal = " update wp_estoque_preco_{$wp} set $comando where  wp_estoque_preco_id = '$id' ";
                                sc_exec_sql($comandoFinal, "dinamico");
                            }
                        }
                    }
                }
            }
        }
    }
}
function  excluirItensRefNaoSel($listaSel,$codWp)
{
    $cmd= "delete from wp_estoque_preco_{$codWp} where wp_estoque_preco_id not in($listaSel) ";
    sc_exec_sql($cmd,"dinamico");
}
function getRegWpEstPreco($wp,$id)
{
   $aRet = getReg('dinamico',
       "wp_estoque_preco_{$wp}",
        'wp_estoque_preco_id',$id, getCpsWpEstPreco());
    return $aRet;

}
function atuRegsWp($wp,$idOrigem,$logAtuQt=0){
    $aWpOrigem = getRegWpEstPreco($wp,$idOrigem);
    //$moeda = 1;
    $ret = '';
    inserirLogDb('Array Registro Corrente',$aWpOrigem,__FUNCTION__);
    if(is_array($aWpOrigem)){
        $itemOrigem             = $aWpOrigem[0]['it_codigo'];
        $refOrigem              = $aWpOrigem[0]['cod_refer'];
        $qtOrigem               = $aWpOrigem[0]['qt_carrinho'];
        $vlInformadoOrigem      = $aWpOrigem[0]['vl_informado'];
        $moedaOrigem            = $aWpOrigem[0]['num_moeda'];
        $precoLiquidaImaOrigem  = $aWpOrigem[0]['preco_liquida_ima'];
        $precoLiquidaImaOrigem  = tratarNumero($precoLiquidaImaOrigem);
        $vlPrecoPrazoOrigem     = $aWpOrigem[0]['vl_preco_prazo'];
        $tbPrecoIdOrigem        = $aWpOrigem[0]['tb_preco_id'];
    }
    $aRet = getRegsEstPrecoPorWp($wp);
    inserirLogDb('Array todos Registros do wp',$aRet,__FUNCTION__);
    if(is_array($aRet)){
        foreach($aRet as $reg){
            //$moeda              = 1;
            $logDivideComis     = 0;
            $percComisVend      = 0;
            $percComisRepres    = 0;
            $vlDolar            = 0;
            $vlReal             = 0;
            $codControlePreco   = 0;
            $aAtu = array();
            $id                 = $reg['wp_estoque_preco_id'];
            $item               = $reg['it_codigo'];
            $ref                = $reg['cod_refer'];
            $vlPrecoPrazo       = $reg['vl_preco_prazo'];
            $moeda              = $reg['num_moeda'];
            $precoLiquidaIma    = $reg['preco_liquida_ima'];
            $precoLiquidaIma    = tratarNumero($precoLiquidaIma);
            $container          = $reg['nr_container'];
            $vlInformado        = $reg['vl_informado'];
            $tbPrecoId          = $reg['tb_preco_id'];
            $agrupPedido        = $reg['agrup_pedido'];
            inserirLogDb('Array Registro dentro do foreach',$reg,__FUNCTION__);
            if(is_null($vlInformado) ){
                inserirLogDb('Vl informado nulo','SIM',__FUNCTION__);
                $aCondPreco 		= getVarSessaoCondPreco();
                $diasCondPagtoEsp 	= $aCondPreco['dias_cond_pagto'];
                //$moedaPrefer 		= $aCondPreco['moeda'];
                $tbPrefer 			= $aCondPreco['tb_prefer'];
                $ordemBusca			= $aCondPreco['ordem_busca'];
                $aPreco = getPrecoPrior($item,$ref,$container,$diasCondPagtoEsp,$moeda,
                    $tbPrefer,
                    $ordemBusca);
                //var_dump($aPreco);
                if(is_array($aPreco) and $aPreco['log_achou'] == 1){
                    $vlPrecoPrazo       = round($aPreco['preco_prazo'],2);
                    $precoLiquidaIma    = round($aPreco['preco_outlet'],2);
                    $moeda              = $aPreco['moeda'];
                    $logDivideComis     = $aPreco['log_divide_comis'];
                    $percComisVend      = $aPreco['perc_comis_vend'];
                    $percComisRepres    = $aPreco['perc_comis_repres'];
                    $tbPrecoId          = $aPreco['tabela'];
                    $vlDolar            = $aPreco['vl_dolar'];
                    $vlReal             = $aPreco['vl_real'];
                    $codControlePreco   = $aPreco['id'];
                    $agrupPedido = getAgrupPedido($moeda,$container,
                    $tbPrecoId,
                    $logDivideComis,
                    $percComisVend,
                    $percComisRepres);
                    //echo "<h1>achou o preço para o item $item - ref: $ref  - vl: $vlPrecoPrazo - preco: $precoLiquidaIma - moeda: $moeda</h1>";
                }
            }else{
                inserirLogDb('Vl informado nulo','NAO',__FUNCTION__);
            }
            inserirLogDb('id - idOrigem - atuPreco',
                "$id - $idOrigem - $logAtuQt",
                __FUNCTION__);
            if($id <> $idOrigem){ // pula o registro origem
               if($item == $itemOrigem){
                   if($logAtuQt == 1){
                     //atuQtWpEstPreco($wp,$id,$qtCarrinho);
                       //echo "<h1>$ref</h1>";
                     $qtSaldo = getSaldoItemRef($item,$ref,$container,0);
                     $testeSaldo = $qtSaldo;
                     inserirLogDb(" Item - ref - container - qtSaldo - qtOrigem",
                         "$item - $ref - $container - $qtSaldo - $qtOrigem ",__FUNCTION__);

                     if($qtSaldo >= $qtOrigem ){
                         $aAtu['qt_carrinho'] = $qtOrigem;
                     }else{
                         $aAtu['qt_carrinho'] = $qtSaldo;
                         $qtSaldoFormat = formatarNumero($qtSaldo);
                         $incr = "O Item: $item - Ref: $ref tem saldo de $qtSaldoFormat - $testeSaldo e foi assumida esta quantidade de saldo";
                         $ret = util_incr_valor($ret,$incr,"<br>");
                       }
                   }
                   inserirLogDb('Chave de Busca para atualização de Valor informado',
                       "precoOut: $precoLiquidaIma == precoOutOri: $precoLiquidaImaOrigem
                      and vlPreco: $vlPrecoPrazo == vlPrecoOri: $vlPrecoPrazoOrigem
                     and vl.moeda: $moeda == vl.moeda orig: $moedaOrigem
                     and tb.preco : $tbPrecoId == tb.preco origem: $tbPrecoIdOrigem"    ,__FUNCTION__);

                   /*sc_error_message("<h1>precoOut: $precoLiquidaIma == precoOutOri: $precoLiquidaImaOrigem
                      and vlPreco: $vlPrecoPrazo == vlPrecoOri: $vlPrecoPrazoOrigem
                     and vl.moeda: $moeda == vl.moeda orig: $moedaOrigem
                     and tb.preco : $tbPrecoId == tb.preco origem: $tbPrecoIdOrigem</h1>")
                    ;*/

                   // forçar a igualar sem casas decimais
                   if($precoLiquidaImaOrigem == 0){
                       $precoLiquidaImaOrigem = 0;
                   }


                   if(round($precoLiquidaIma,2)  == round($precoLiquidaImaOrigem,2)
                      and round($vlPrecoPrazo,2) == round($vlPrecoPrazoOrigem,2)
                      and $moeda        == $moedaOrigem
                      and $tbPrecoId    == $tbPrecoIdOrigem) {
                       inserirLogDb('Chave igual','SIM',__FUNCTION__);
                       //echo "<h4>entrei</h4>";
                       $aAtu['vl_informado']        = $vlInformadoOrigem;
                       $aAtu['num_moeda']            = $moeda;
                       $aAtu['log_divide_comis']     = $logDivideComis;
                       $aAtu['perc_comis_vend']      = $percComisVend;
                       $aAtu['perc_comis_repres']    = $percComisRepres;
                       $aAtu['tb_preco_id']          = $tbPrecoId;
                       $aAtu['vl_preco_prazo']       = $vlPrecoPrazo;
                       $aAtu['cod_controle_preco']   = $codControlePreco;
                       $aAtu['vl_dolar']             = $vlDolar;
                       $aAtu['vl_real']              = $vlReal;
                       $aAtu['agrup_pedido']         = $agrupPedido;
                       $aAtu['preco_liquida_ima']    = $precoLiquidaIma;
                       /*if(is_null($vlInformado)){
                           //echo "<h4>valor nulo</h4>";

                       }else{
                           //echo "<h4>valor nao nulo</h4>";
                       }*/

                   }else{
                       inserirLogDb('Chave igual','NAO',__FUNCTION__);
                      // echo "<h4>valores diferentes</h4>";
                   }
               }
            }
            if(count($aAtu) > 0){
                inserirLogDb('Atualização por Array','SIM',__FUNCTION__);
                /*echo "<h1>  array atualizacao  -id: $id </h1>";
                var_dump($aAtu);*/
                atuDadosItemRefEstWpPorId($wp,$id,$aAtu);
            }else{
                inserirLogDb('Atualização por Array','NAO',__FUNCTION__);
            }
        }
    }
    return $ret;
}
function atuQtWpEstPreco($wp,$id,$qt)
{
    $aQt = array('qt_carrinho'=>$qt);
    atuDadosItemRefEstWpPorId($wp,$id,$aQt);

}
function getVarSessaoCondPreco()
{



    if([gl_log_a_vista] == 1){
        $diasCondPagtoEsp = 0;
    }else{
        $diasCondPagtoEsp = [gl_dias_cond_pagto];
    }

    if(isset([gl_moeda])){
        $moedaPrefer = [gl_moeda];
    }else{
        $moedaPrefer = 1;
    }
    {moeda_prefer} = $moedaPrefer;

    if(isset([gl_tb_pref])){
        $tbPrefer = [gl_tb_pref];
    }else{
        $tbPrefer = 1;

    }


    if(isset([gl_ordem_busca])){
        $ordemBusca = [gl_ordem_busca];
    }else{
        $ordemBusca = 1;
    }
    return array('dias_cond_pagto'=>$diasCondPagtoEsp,
        'moeda'=>$moedaPrefer,
        'tb_prefer'=>$tbPrefer,
        'ordem_busca'=>$ordemBusca);
}
function getTotaisWp($wp)
{
    $aRet = getDados('unico',"wp_estoque_preco_$wp",
                    'sum(coalesce(vl_informado|0) * coalesce(qt_carrinho|0)) as tot_inf,
                      sum(coalesce(vl_preco_prazo|0) * coalesce(qt_carrinho|0)) as tot_tb',
                    '1=1');
    if(is_array($aRet)){
        $totInf     = $aRet[0]['tot_inf'];
        $totTb      = $aRet[0]['tot_tb'];
        $variacao   = $totTb / $totInf * 100;
    }else{
        $totInf     = 0;
        $totTb      = 0;
        $variacao   = 0;
    }
    $result = "<h4>Total Informado:$totInf</h4><h4>Total Tb:$totTb</h4>
                <h4>% Variação:$variacao</h4>";
    return array('tot_inf'=>$totInf,'tot_tb'=>$totTb,'result'=>$result);
}
function verifItensSemPrecoQtWp($wp)
{
    $aRet = getRegsEstPrecoPorWp($wp);
    $lAchouPreco = 0;
    $lAchouQt    = 0;
    if(is_array($aRet)) {
        foreach ($aRet as $reg) {
            $aAtu = array();
            $vlInformado = $reg['vl_informado'];
            $qtCarrinho  = $reg['qt_carrinho'];
            if($vlInformado == '' or $vlInformado == 0){
                $lAchouPreco = 1;
                break;
            }
            if($qtCarrinho == '' or $qtCarrinho == 0){
                $lAchouQt = 1;
                break;
            }
        }
    }
    $aRetorno = array('preco'=>$lAchouPreco,'qt'=>$lAchouQt);
    return $aRetorno;
}
function getListaMaxIdPrecoWp($wp)
{
    $lista = 0;
    $sql = "select wp1.wp_estoque_preco_id as id from wp_estoque_preco_$wp wp1
 where wp1.wp_estoque_preco_id in
 (select max(wp2.wp_estoque_preco_id) from wp_estoque_preco_$wp wp2
  where wp1.it_codigo = wp2.it_codigo 
  and coalesce(wp1.preco_liquida_ima,0) = coalesce(wp2.preco_liquida_ima,0)
  and coalesce(wp1.vl_preco_prazo,0) = coalesce(wp2.vl_preco_prazo,0))";
    $aRegs = getRegsSqlLivre($sql,'id','dinamico');
    if(is_array($aRegs)){
        $tam = count($aRegs);
        for($i=0;$i<$tam;$i++){
            $lista = util_incr_valor($lista,$aRegs[$i]['id']);
        }
    }
    return $lista;
}
function sincrPrecoItensComPrecoIguaisWp($wp,$listaIdItemWp)
{
    $aListaId = explode(',',$listaIdItemWp);
    $cpsChave = 'it_codigo,vl_preco_prazo,preco_liquida_ima';
    $cpsAtu   ='vl_informado,cod_controle_preco,num_id_liquida_ima,liquida_ima';
    $cps = $cpsChave.",".$cpsAtu;
    foreach($aListaId as $idCorrente){
        if($idCorrente == 0 or $idCorrente == ''){
            continue;
        }
        $aItemWp = getRegItemEstoqueWp($wp,$idCorrente,$cps);
        /*** atribuição de valores de indices de array a variaveis ****/
        //echo "<h2>array dados item wp</h2>";
        //var_dump($aItemWp[0]);
        $aChaves = array_keys($aItemWp[0]);
        //echo "<h2>chaves array</h2>";
        //var_dump($aChaves);
        foreach($aChaves as $cp){
            //echo "<h2>oi $cp </h2>";
            $cpVar  = convNomeVar($cp,'alt');
            //echo "<br>$cpVar<br>";
            $$cpVar = $aItemWp[0][$cp];
        }
        //echo "<h2>it codigo: $itCodigoAlt</h2>";
        $precoLiquidaImaAlt = round(tratarNumero($precoLiquidaImaAlt),2);
        $vlPrecoPrazoAlt    = round(tratarNumero($vlPrecoPrazoAlt),2);
        //echo "<h1>Preços = $precoLiquidaImaAlt / $vlPrecoPrazoAlt</h1>";
        $sql = "select wp1.wp_estoque_preco_id as id from wp_estoque_preco_$wp wp1
       where wp1.it_codigo = '$itCodigoAlt' 
         and round(coalesce(vl_preco_prazo,0),2)   = '$vlPrecoPrazoAlt'  
        and round(coalesce(preco_liquida_ima,0),2) = '$precoLiquidaImaAlt' 
        and wp_estoque_preco_id <> $idCorrente";
        $aRegs = getRegsSqlLivre($sql,'id','dinamico');
        if(is_array($aRegs)){
            $lista = convArrayMultParaLista($aRegs,'id');
            $aDados =  removerIndicesArray($aItemWp[0],$cpsChave);
            atuDadosItemRefEstWpPorId($wp,$lista,$aDados);
        }

    }

}
function getWpsSemInf($wp,$tipoValidacao)
{
    $aRet = getRegsEstPrecoPorWp($wp);
    $logSemInf = 1;
    if(is_array($aRet)) {
        foreach ($aRet as $reg) {
            $aAtu = array();
            $vlInformado = $reg['vl_informado'];
            $qtCarrinho  = $reg['qt_carrinho'];
            switch ($tipoValidacao){
                case 'valor':
                    if($vlInformado <> '' and $vlInformado <> 0){
                        $logSemInf = 0;
                        break;
                    }
                    break;
                case 'quantidade':
                    if($qtCarrinho <> '' and $qtCarrinho <> 0){
                        $logSemInf = 0;
                        break;
                    }
                    break;
            }

        }
    }

    return $logSemInf;

}
function atuPrecoItensWp($wp,$idItem=0)
{
    //$atuWp = getVarSessao('preco_atu_cons_wp');
    $atuWp = 0; // retirado por enquanto
    if($atuWp <> 1){ // só atualiza o preco se já não tiver atualizado.
        // variaveis de sessao
        $aCondPreco = getVarSessaoCondPreco();
        inserirLogDb('Array Sessao Cond Preço:',$aCondPreco,__FUNCTION__);
        //var_dump($aCondPreco);
        // atribuicao de indice a variavel
        $aChaves = array_keys($aCondPreco);
        //var_dump($aChaves);
        foreach($aChaves as $cp){
            $cpVar  = convNomeVar($cp,'prefer');
            $$cpVar = $aCondPreco[$cp];
        }
        //itens do wp
        $aRet = getRegsEstPrecoPorWp($wp,$idItem);
        if(is_array($aRet)) {
            foreach($aRet as $reg){
                // atribuicao de indice a variavel
                $aChaves = array_keys($reg);
                foreach($aChaves as $cp){
                    $cpVar = convNomeVar($cp,'wp');
                    $$cpVar = $reg[$cp];
                }
                // busca valores do preço prioritário a partir dos dados preferenciais
                $aPrecoPrior = getPrecoPrior($itCodigoWp,$codReferWp,$nrContainerWp,$diasCondPagtoPrefer,$moedaPrefer,$tbPreferPrefer,$ordemBuscaPrefer,$codEstabelWp);
                //var_dump($aPrecoPrior);
                if($aPrecoPrior['log_achou'] == 1){
                    // colunas: preco_outlet,log_divide_comis,perc_comis_vend,perc_comis_repres,id,id_liq,vl_dolar,vl_real,moeda,tabela
                    /*** atribuição de valores de indices de array a variaveis ****/
                    $aChaves = array_keys($aPrecoPrior);
                    foreach($aChaves as $cp){
                        $cpVar  = convNomeVar($cp,'prior');
                        $$cpVar = $aPrecoPrior[$cp];
                    }
                }else{
                    /*** atribuição de valores de indices de array a variaveis ****/
                    $aChaves = array_keys($aPrecoPrior);
                    foreach($aChaves as $cp){
                        $cpVar  = convNomeVar($cp,'prior');
                        $$cpVar = '0';
                    }
                }
                inserirLogDb('array preco prioritario - ponto 54',$aPrecoPrior,__FUNCTION__);
                //calcula agrupamento do pedido
                $agrupPedido = getAgrupPedido($moedaPrior,$nrContainerWp,$tabelaPrior,$logDivideComisPrior,$percComisVendPrior,$percComisRepresPrior);
                $aDadosAtu = array( 'log_divide_comis'	 => $logDivideComisPrior,
                    'perc_comis_vend'	 => $percComisVendPrior,
                    'perc_comis_repres'	 => $percComisRepresPrior,
                    'tb_preco_id'		 => $tabelaPrior,
                    'num_moeda'			 => $moedaPrior,
                    'vl_preco_prazo'     => $precoPrazoPrior,
                    'cod_controle_preco' => $idPrior,
                    'vl_dolar'           => $vlDolarPrior,
                    'vl_real'            => $vlRealPrior,
                    'agrup_pedido'		 => $agrupPedido,
                    'preco_liquida_ima'  => $precoOutletPrior);
                // atualiza dados
                //echo "<h1>id wp: $wpEstoquePrecoIdWp</h1>";
                atuDadosItemRefEstWpPorId($wp,$wpEstoquePrecoIdWp,$aDadosAtu);
                setVarSessao('preco_atu_cons_wp',1,0);
            }
        }
    }
}
function atuDadosItemRefEstWpPorId($wp,$listaId,$aDados,$cpsSemAspas='')
{
    //echo "<h1>lista id: $listaId</h1>";
    if(strstr($listaId,',') <> false){
        $condicao = "wp_estoque_preco_id in ($listaId)";
    }else{
        $condicao = "wp_estoque_preco_id = $listaId";
    }
    $cmd = convertArrayEmUpdate(
        "wp_estoque_preco_{$wp}",
        $aDados,
        $condicao,
        $cpsSemAspas,
        false
    );
    //echo "<h1>$cmd</h1>";
    sc_exec_sql($cmd,"dinamico");
}

?>
