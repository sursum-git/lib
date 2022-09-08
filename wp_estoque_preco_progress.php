<?php
//__NM__Estoque __NM__FUNCTION__NM__//

function criarPesqEstoquePreco($aWpEstoquePreco,$codWp)
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

    if(is_array($aWpEstoquePreco) == true)
    {
        for($i = 0;$i < count($aWpEstoquePreco);$i++)
        {
            
            $itCodigo			= $aWpEstoquePreco[$i]["it_codigo"];
            $descItem           = $aWpEstoquePreco[$i]["desc_item"];
            //busca o link do book na memoria da sessao.
            $descItem          = incrDescrBook($itCodigo,$descItem);

            $codRefer 			= $aWpEstoquePreco[$i]["cod_refer"];
            $qtSaldo	    	= $aWpEstoquePreco[$i]["qt_saldo"];
            $qtPedido	 		= $aWpEstoquePreco[$i]["qt_pedido"];
            $codEstabel   		= $aWpEstoquePreco[$i]["cod_estabel"];
            //$qtProgramada		= $aWpEstoquePreco[$i]["qt_programada"];
            //$precoVista			= $aWpEstoquePreco[$i]["preco_prazo01"];
            //$preco30Dias		= $aWpEstoquePreco[$i]["preco_prazo02"];
            //$preco60Dias		= $aWpEstoquePreco[$i]["preco_prazo03"];
            //$preco90Dias    	= $aWpEstoquePreco[$i]["preco_prazo04"];
            $container			= $aWpEstoquePreco[$i]["nr_container"];
            $qtSaldoVenda  	    = $aWpEstoquePreco[$i]["qt_saldo_venda"];
            $qtCarrinho         = $aWpEstoquePreco[$i]["qt_carrinho"];
            $idPreco            = $aWpEstoquePreco[$i]["id_preco"];
            $descPreco          = $aWpEstoquePreco[$i]["desc_preco"];
            //$moeda              = $aWpEstoquePreco[$i]["moeda"];
            /*$idPreco        	= tratarNumero($idPreco);
            $qtPedido       	= tratarNumero($qtPedido);
            $qtSaldo        	= tratarNumero($qtSaldo);
            $qtSaldoVenda   	= tratarNumero($qtSaldoVenda);
            $qtCarrinho     	= tratarNumero($qtCarrinho)
			*/
            $qtEmDigitacao      = $aWpEstoquePreco[$i]["qt_carrinho_geral"];
            $qtEmDigitacao      = tratarNumero($qtEmDigitacao);
            $referEmOrdem       = getOrdemCodRefer($codRefer);

            /*if($moeda == 'real' or $moeda == 0){
                $moeda = 1;
            }
            if($moeda == 'dolar'){
                $moeda = 2;
            }*/

            $cmdSql = "
                insert into PUB.wp_estoque_preco
                (wp_estoque_preco_id,
				 it_codigo,
                 cod_refer,
                 qt_saldo,
                 qt_pedido,
                 cod_estabel,
                 cod_wp,
                 qt_saldo_venda,                
				 desc_item,
				 nr_container,
				 qt_carrinho,
				 qt_disponivel,
				 cod_controle_preco,
				 desc_preco,
				 qt_em_digitacao,				 
				 cod_refer_ordem
                  )
                 values
                (pub.seq_wp_estoque_preco.NEXTVAL,
				'$itCodigo', 
                  '$codRefer',
                  $qtSaldo,
                  $qtPedido,
                  '$codEstabel',                 
                 '$codWp',
                  $qtSaldoVenda,                 
				  '$descItem',
				  $container,
				  $qtCarrinho,
				  $qtSaldo,
				  $idPreco,
				  '$descPreco',
				  $qtEmDigitacao,				  
				  $referEmOrdem)" ;
            //--echo $cmdSql."</br>";
            $comando = sc_exec_sql($cmdSql,"especw");

        }
    }
}

function criarPesqEstoquePrecoPI($aWpEstoquePreco,$codWp)
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

    if(is_array($aWpEstoquePreco) == true)
    {
        for($i = 0;$i < count($aWpEstoquePreco);$i++)
        {

            $itCodigo			= $aWpEstoquePreco[$i]["it_codigo"];
            $descItem           = $aWpEstoquePreco[$i]["desc_item"];
            //busca o link do book na memoria da sessao.
            $descItem          = incrDescrBook($itCodigo, $descItem);

            $codRefer 			= $aWpEstoquePreco[$i]["cod_refer"];
            $qtSaldo	    	= $aWpEstoquePreco[$i]["qt_pedida"];
            $qtPedido	 		= $aWpEstoquePreco[$i]["qt_vendida"];
            $codEstabel   		= $aWpEstoquePreco[$i]["cod_estabel"];
            //$qtProgramada		= $aWpEstoquePreco[$i]["qt_programada"];
            /*$precoVista			= $aWpEstoquePreco[$i]["preco_vista"];
            $preco30Dias		= $aWpEstoquePreco[$i]["preco_30"];
            $preco60Dias		= $aWpEstoquePreco[$i]["preco_60"];
            $preco90Dias    	= $aWpEstoquePreco[$i]["preco_90"];*/
            $container			= $aWpEstoquePreco[$i]["nr_container"];
            $qtProgramada  	    = $aWpEstoquePreco[$i]["qt_saldo_com_carrinho"];
            $qtCarrinho         = $aWpEstoquePreco[$i]["qt_carrinho"];
            $qtDisp             = $aWpEstoquePreco[$i]["qt_disp"];
            $idPreco            = $aWpEstoquePreco[$i]["id_preco"];
            $descPreco          = $aWpEstoquePreco[$i]["desc_preco"];
            $qtEmDigitacao = $aWpEstoquePreco[$i]["qt_carrinho_geral"];
            $qtEmDigitacao  = tratarNumero($qtEmDigitacao);
            $idPreco        = tratarNumero($idPreco);
            $qtPedido       = tratarNumero($qtPedido);
            $qtSaldo        = tratarNumero($qtSaldo);
            $qtProgramada   = tratarNumero($qtProgramada);
            $qtCarrinho     = tratarNumero($qtCarrinho);
            $referEmOrdem   = getOrdemCodRefer($codRefer);
            //$moeda          = $aWpEstoquePreco[$i]["moeda"];

            //echo "Antes <h1>Item -  $itCodigo  - ref - $codRefer - moeda - $moeda </h1>";

          /*  if($moeda == 'real' or $moeda == 0 ){
               $convMoeda = 1;
            }
            if($moeda == 'dolar'){
               $convMoeda = 2;
            }*/

            //echo "Depois <h1>Item -  $itCodigo  - ref - $codRefer - moeda - $moeda  - $convMoeda</h1>";

            /*comentado , pois, será feito um agrupamento ou invés de sincronizar-se os registros
            pois é necessário ter os registros separados, pois, podem existir mais de um container
            por item
             * $logAtu = atuItemRefEstWp($codWp,$itCodigo,$codRefer,$qtProgramada);
            */
            $logAtu = false;
            if($logAtu == false){
                $cmdSql = "
                insert into PUB.wp_estoque_preco
                (wp_estoque_preco_id,
				 it_codigo,
                 cod_refer,
                 qt_saldo,
                 qt_pedido,
                 cod_estabel,
                 cod_wp,
                 qt_programada,                
				 desc_item,
				 nr_container,
				 qt_carrinho,
				 qt_disponivel,
				 cod_controle_preco,
				 desc_preco,				 
				 qt_em_digitacao,
				 cod_refer_ordem
                  )
                 values
                (pub.seq_wp_estoque_preco.NEXTVAL,
				'$itCodigo', 
                  '$codRefer',
                  $qtSaldo,
                  $qtPedido,
                  '$codEstabel',                 
                 '$codWp',
                  $qtProgramada,                 
				  '$descItem',
				  $container,
				  $qtCarrinho,
				  $qtDisp,
				  $idPreco,
				  '$descPreco',				  
				  $qtEmDigitacao,
				  $referEmOrdem)" ;
                //--echo $cmdSql."</br>";
                sc_exec_sql($cmdSql,"especw");

            }
        }
    }
}

/**
 * @param $nivel 1- consulta(atualiza todos os registros a partir do WP 2-registro(atualiza a partir do id apenas um registro)
 * @param $id( se nivel igual 1 deve ser passado o codwp senão deve ser passado o id do registro(wp_estpque_preco_id)
 */
function setCamposCalcEstPreco($id,$nivel=1,$campos='')
{
    switch ($nivel){
        case 1:
            $condicao = "cod_wp = '$id'";
            break;
        case 2:
            $condicao = "wp_estoque_preco_id = $id";
            break;
    }
    $tipo     = "multi"; // unico ou multi
    $tabela   = " pub.wp_estoque_preco ";
    if($campos == ''){
        $aCampos  = getCpsTbSessao('espec','wp_estoque_preco');
        $campos  = $aCampos['campos'];
    }
    $conexao  = "espec";
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


function getRegsEstPrecoPorWp($wp)
{
    $tipo     = "multi"; // unico ou multi
    $tabela   = " pub.wp_estoque_preco ";
    $aCampos  = getCpsTbSessao('espec','wp_estoque_preco');
    $campos   = $aCampos['campos'];
    $condicao = "  cod_wp = '$wp' ";
    $conexao  = "espec";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    return $aDados;


}

function getRegItemEstoqueWp($id,$campos,$filtroCompl='')
{
    $campoChave ='wp_estoque_preco_id' ;
    $tabela   = "wp_estoque_preco";
    $condFiltroCompl = '';
    if($campos == ''){
        $aCampos = getCpsTbSessao('espec',$tabela);
        $campos = $aCampos['campos'];
    }
    if($id <> 0){
        $condicao = "$campoChave = $id";
    } else{
        $condicao = '1 = 1 ';
    }

    if($filtroCompl <> ''){
        $condicao = util_incr_valor($condicao,$filtroCompl,' AND ',true);
    }

    $tabela   = "pub.$tabela";
    $tipo     = "unico"; // unico ou multi
    $conexao  = "espec";
    $aRet = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    return $aRet;
}

function atuItemRefEstWp($wp,$item,$ref,$qtProgramada)
{
    $logAchou = false;
    $filtroCompl = " it_codigo = '$item' and cod_refer = '$ref' and cod_wp = '$wp' ";
    $aRegItem = getRegItemEstoqueWp(0,'',$filtroCompl);
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
    $tabela = 'wp_estoque_preco';
    if($campos == ''){
        $aCampos = getCpsTbSessao('espec',$tabela);
        //var_dump($aCampos);
        $campos  = $aCampos['campos'];

    }

    $aReg = getDados('multi',"pub.$tabela",$campos,"cod_wp = '$wp' and qt_carrinho > 0 and qt_saldo_venda + qt_programada > 0 " ,'espec') ;
    return $aReg;
}

function getItensWpTodos($wp,$campos = '')
{
    $tabela = 'wp_estoque_preco';
    if($campos == ''){
        $aCampos = getCpsTbSessao('espec',$tabela);
        //var_dump($aCampos);
        $campos  = $aCampos['campos'];
		
    }
     
    $aReg = getDados('multi',"pub.$tabela",$campos,"cod_wp = '$wp' and qt_saldo_venda + qt_programada > 0 order by it_codigo, cod_refer" ,'espec');
	//echo "<h1>Entreiiii</h1>";
    return $aReg;
}




function  atuSaldoItensWp($wp)
{
    $aReg = getItensWpSolic($wp);
    $aSitItens = array();
    $logSitItens = 0;
    $listaItensRefZerados ='';
    $logDivergSaldo = false;
    $aRetorno = '';
    $log = '';
    if(is_array($aReg)){
        $tam = count($aReg);
        for($i=0;$i<$tam;$i++){
           $itemWp      =    $aReg[$i]['wp_estoque_preco_id'];
           $item        =    $aReg[$i]['it_codigo'];
           $ref         =    $aReg[$i]['cod_refer'];
           $container   =    $aReg[$i]['nr_container'];
           $qtPedida    =    $aReg[$i]['qt_carrinho'];
           //echo "<h1>Antes getPrecosSaldoItemRef</h1>";
           $aSaldo      = getPrecosSaldoItemRef($item,$ref,$container);
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

           $aQtCarrinho = getItemRefPedWeb($item,$ref,$container);
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
               $cmd = " update pub.wp_estoque_preco set  qt_carrinho = '$qtSaldo', $setSaldo  where wp_estoque_preco_id = $itemWp";
               sc_exec_sql($cmd,"especw");
               $qtDiverg = $qtSaldo - $qtPedida ; // alterado
               if($qtSaldo == 0) {
                 $listaItensRefZerados = util_incr_valor($listaItensRefZerados,"$item - $ref",",",true);
               }
               $aSitItens[$item][$ref] = $qtDiverg;
               $logSitItens = 1;
           }
        }
        if($logSitItens == 0 ){
            $aSitItens = '';
        }
        $aRetorno = array('log_diverg_saldo' => $logDivergSaldo,'array'=> $aSitItens,'lista_zerados' => $listaItensRefZerados,'log'=>$log);
    }
    return $aRetorno;
}

function atuVlInfItemPrecoWp($codWp,$item,$vlFinal)
{
    $vl = 0;
    $aRegs =  getDados('multi','pub.wp_estoque_preco','it_codigo,preco_prazo04,vl_informado',
        "it_codigo='$item' and cod_wp = '$codWp' ");
    if(is_array($aRegs)){
       $tam  = count($aRegs);
       $aInd = buscarIndice();

       for($i=0;$i<$tam;$i++){
           $itCodigo    = $aRegs[$i]['it_codigo'];
           $preco90     = getPrecoPrazoInd();
           $vlInformado = $aRegs[$i]['vl_informado'];
           $wpId        = $aRegs[$i]['wp_estoque_preco_id'];
           if($vlInformado <> 0 and $vlInformado <> ''){
                $vl = $vlInformado;
           }else{
                $vl = $preco90;
           }
           if($vl == $vlFinal and $item == $itCodigo){
               $cmd = "update pub.wp_estoque_preco set vl_informado = $vlFinal
                       where wp_estoque_preco_id = $wpId ";
               sc_exec_sql($cmd,"especw");
           }
       }
    }

}

function incrDescrBook($itCodigo,$desc)
{

    /*if (isset([books_wp][$itCodigo]['book_pe']) and [books_wp][$itCodigo]['book_pe'] <> '') {
        $desc .= " BOOK PE:".[books_wp][$itCodigo]['book_pe'];
    }
    if (isset([books_wp][$itCodigo]['book_pi']) and [books_wp][$itCodigo]['book_pi'] <> '') {
        $desc .= " BOOK PI:".[books_wp][$itCodigo]['book_pi'];
    }*/
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
            $item       = $aItensWp[$i]['it_codigo'];
            //$ref        = $aItensWp[$i]['cod_refer'];
            $id         = $aItensWp[$i]['wp_estoque_preco_id'];
            if($item == ''){
               if(is_array($regAnt)){
                   $tam2 = count($regAnt);
                   for($j=0;$j< $tam2; $j++){
                       $idAnt = $regAnt[$j]['wp_estoque_preco_id'];
                       if($id == $idAnt){
                           $itemAlterado = $regAnt[$j]['it_codigo'];
                           $comandoAtual = " it_codigo =  '$itemAlterado' ";
                           $comando = util_incr_valor($comando,$comandoAtual,' , ',true);

                           $refAlterado = $regAnt[$j]['cod_refer'];
                           $comandoAtual = "  cod_refer  =   '$refAlterado' ";
                           $comando = util_incr_valor($comando,$comandoAtual,' , ',true);

                           if($comando <> ''){
                               $comandoFinal = " update pub.wp_estoque_preco set $comando where  wp_estoque_preco_id = '$id' ";
                               sc_exec_sql($comandoFinal, "especw");
                           }
                       }
                   }
               }
            }
        }
    }
}

?>