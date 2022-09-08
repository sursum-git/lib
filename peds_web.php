<?php
//__NM____NM__FUNCTION__NM__//
//$login,$container,$divideComis,$percVend,$percRepres,$agrup
function getPedWebEmAberto($login,$container,$divideComis,$percVend,$percRepres,$agrup,$tbPreco=1,$moeda=1)
{
    $ped = 0;
    $moeda = getCodMoeda($moeda);
    $tabela   = " pub.peds_web ped ";
    $campos   = " ped_web_id ";
    $sitsEmAberto = getSitsPedWebEmAberto();
    $condicao = "  ped.login = '$login'    
    and ped.nr_container        = $container 
    and ped.log_divide_comissao = $divideComis
    and ped.perc_comis_vend     = '$percVend' 
    and ped.perc_comis_repres   = '$percRepres'
    and ped.num_agrup           = $agrup 
    and ped.tb_preco_id         = $tbPreco
    and ped.cod_moeda           = '$moeda'
    and ped.ind_sit_ped_web     in ($sitsEmAberto)";
    $aRet = getDados('unico',$tabela,$campos,$condicao);
    if(is_array($aRet)){
        $ped = $aRet[0]['ped_web_id'];
    }
    return $ped;
}
function getPedWebEmAbertoManual($login,$container,$tbPreco,$moeda)
{
    $moeda = tratarNumero($moeda);
    $ped = 0;
    $tabela   = " pub.peds_web ped ";
    $campos   = " ped_web_id ";
    $sitsAberto = getSitsPedWebEmAberto();
    $condicao = "  ped.login = '$login' 
    and ped.ind_sit_ped_web     in($sitsAberto) 
    and ped.nr_container        = $container     
    and ped.tb_preco_id         = $tbPreco
    and ped.cod_moeda           = $moeda";
    $aRet = getDados('unico',$tabela,$campos,$condicao);
    if(is_array($aRet)){
        $ped = $aRet[0]['ped_web_id'];
    }
    return $ped;
}



function getPedWebEmAbertoTodos($login)
{
    //$ped = 0;
    $tabela   = " pub.peds_web ped ";
    $campos   = " ped_web_id ";
    $sitsAberto = getSitsPedWebEmAberto();
    $condicao = "  ped.login = '$login' and ped.ind_sit_ped_web in($sitsAberto)";
    $aRet = getDados('multi',$tabela,$campos,$condicao);
    return $aRet;
}

function getPedWebEmAbertoComCondPagto($login)
{
    //$ped = 0;
    $tabela   = " pub.peds_web ped ";
    $campos   = " ped_web_id ";
    $sitsAberto = getSitsPedWebEmAberto();
    $condicao = "  ped.login = '$login' 
    and ped.ind_sit_ped_web in ($sitsAberto) 
    and (dias_cond_pagto_esp <> '' or log_a_vista = 1)";
    $aRet = getDados('multi',$tabela,$campos,$condicao);
    return $aRet;
}



function atualizarCondPagtoPedsAberto($logVista,$diasCondPagto,$qtParcelasCred,$codFormPagto)
{
    $aPeds = getPedWebEmAbertoTodos(getLoginCorrente());
    if(is_array($aPeds)){
        $tam = count($aPeds);
        for($i=0;$i< $tam;$i++){
            $pedWebId = $aPeds[$i]['ped_web_id'];
            $aReg = getRegPedWeb($pedWebId,'ped_web_id,log_a_vista,dias_cond_pagto_esp');
            if(is_array($aReg) ){
                $pedidoCorr       = $aReg[0]['ped_web_id'];
                $logVistaPed      = $aReg[0]['log_a_vista'];
                $diasCondPagtoPed = $aReg[0]['dias_cond_pagto_esp'];
                if(($logVista <> $logVistaPed) or ($diasCondPagto <> $diasCondPagtoPed)){
                    $cmd = " update pub.peds_web set log_a_vista = $logVista, 
                              dias_cond_pagto_esp = '$diasCondPagto',
                              parcelas_credito = '$qtParcelasCred',
                              cod_forma_pagto = '$codFormPagto'
                              where ped_web_id = $pedidoCorr";
                    sc_exec_sql($cmd,"especw");
                }
            }
        }
    }
}


function calcTipoFretePedsAberto()
{

    $aRegs = getPedWebEmAbertoTodos(getLoginCorrente());
    if(is_array($aRegs)){
        $tam = count($aRegs);
        for($i=0;$i<$tam;$i++){
            $pedWebId = $aRegs[$i]['ped_web_id'];
            $tbPreco  = $aRegs[$i]['tb_preco_id'];
            atualizarTipoFrete($pedWebId,'',$tbPreco);
        }
    }

}

function criarPedWeb($login,$container,$moeda,$codEstabel,$logManual=false,
                     $divideComis,$percVend,$percRepres,$agrupPed,$tbPreco)
{
    if(isset([perfil_pai]) and  [perfil_pai] <> ''){
        $loginDigitacao = [perfil_pai];
    }else{
        $loginDigitacao = $login;
    }

    /*if($moeda == '1'){
        $moeda = 'real';
    }

    if($moeda == '2'){
        $moeda = 'dolar';
    }*/
    $moeda = getCodMoeda($moeda);
    if($container ==0){
        $codTipoPedido = 'PE';
    }else{
        $codTipoPedido = 'PI';
    }
    //echo "<h1>cod.rep.ini:[codRepIni]</h1>";
    $repres = [codRepIni];

    $dtHrVencto = new DateTime();

    if($codTipoPedido == "PE"){
        $qtMinutos  = getMinutosVenctoPe();

    }else{
        $qtMinutos  = getMinutosVenctoPi();
    }

    $dtHrVencto->add(new DateInterval('PT' . $qtMinutos . 'M'));

    $dtHrVencto = $dtHrVencto->format('Y-m-d H:i');

    //verifica condição de pagamento informada anteriormente
    if(isset([gl_log_a_vista]) and [gl_log_a_vista] <> ''){
      $logVista = [gl_log_a_vista];
    }else{
      $logVista = 0;
    }

    if(isset([gl_dias_cond_pagto]) and [gl_dias_cond_pagto] <> ''){
        $diasCondPagto = [gl_dias_cond_pagto];
    }else{
        $diasCondPagto = '';
    }

    if(isset([gl_form_pagto]) and [gl_form_pagto] <> ''){
        $formPagto = [gl_form_pagto];
    }else{
        $formPagto = 0;
    }

    if(isset([gl_parcelas_credito]) and [gl_parcelas_credito] <> ''){
        $parcelasCredito = [gl_parcelas_credito];
    }else{
        $parcelasCredito = 0;
    }

    if($logManual == true){
        $logManual = 1;
    }else{
        $logManual = 0;
    }

    if(getTipoUsuarioCorrente() == 5){
        $loginPreposto = getLoginCorrente();
    }else{
       $loginPreposto = '';
    }
    $codEmpresa = substr($codEstabel,0,1)."00"  ;
    $percComis = getPercComisRepres($codEmpresa,$repres);

    $aInsert = array('ped_web_id'           => 'pub.seq_ped_web.NEXTVAL',
                    'login'                 => $login,
                    'nr_container'          => $container,
                    'dt_hr_registro'        => 'now()',
                    'ind_sit_ped_web'       => 1,
                    'cod_moeda'             => $moeda,
                    'cod_estabel'           => $codEstabel,
                    'repres_id'             => $repres,
                    'cod_tipo_pedido'       => $codTipoPedido,
                    'login_digitacao'       => $loginDigitacao,
                    'dt_hr_vencto'          => $dtHrVencto,
                    'log_a_vista'           => $logVista,
                    'dias_cond_pagto_esp'   => $diasCondPagto,
                    'log_pedido_manual'     => $logManual,
                    'login_preposto'        => $loginPreposto,
                    'parcelas_credito'      => $parcelasCredito,
                    'cod_forma_pagto'       => $formPagto,
                    'perc_comis'            => $percComis,
                    'num_agrup'             => $agrupPed,
                    'log_divide_comissao'   => $divideComis,
                    'perc_comis_vend'       => $percVend,
                    'perc_comis_repres'     => $percRepres,
                    'tb_preco_id'           => $tbPreco
                    );
    $cmd = convertArrayEmInsert('pub.peds_web',$aInsert,'1,3,4,5,8');
    sc_exec_sql($cmd,"especw");
    $ped = buscarVlSequenciaEspec('seq_ped_web','peds_web');
    return $ped;
}

function sincrPedWeb($login, $container, $paramMoeda, $codEstabel, $divideComis,
                     $percVend, $percRepres, $agrup, $tbPreco)
{
    habilitarLogSql(1);
    inserirLogDb('logsql',getLogSql(),__FUNCTION__);
    //echo "<h1>moeda: $paramMoeda</h1>";
    $ped = getPedWebEmAberto($login,$container,$divideComis,$percVend,$percRepres,$agrup,$tbPreco,$paramMoeda);
    //echo "<h1>pedido: $ped</h1>";

    habilitarLogSql(0);
    inserirLogDb('ped. retorno busca ped.venda',$ped,__FUNCTION__);
    inserirLogDb('Parametros busca pedido web em aberto->login - container - dividecomis - percvend - percrepres - agrup - tbpreco - moeda',
        "$login - $container - $divideComis - $percVend - $percRepres - $agrup - $tbPreco - $paramMoeda",__FUNCTION__);
    if($ped == 0){
        //echo "<h2>Não encontrei pedido e vou criar novo pedido</h2>";

        inserirLogDb('Pedido de Venda Encontrado?' ,"NAO",__FUNCTION__);
        $ped = criarPedWeb($login,$container,$paramMoeda,$codEstabel,
            false,$divideComis,$percVend,$percRepres,$agrup,$tbPreco);

    }else{
        inserirLogDb('Pedido de Venda Encontrado?' ,"SIM",__FUNCTION__);
        //echo "<h2>encontrei o pedido $ped  e NAO vou criar novo pedido</h2>";
    }

    return $ped;
}

function limparCarrinho()
{
    $sitsAberto = getSitsPedWebEmAberto();
    $cmd = "update pub.peds_web set ind_sit_ped_web = 3 
            where login = '".getLoginCorrente()."' and ind_sit_ped_web in($sitsAberto) ";
    sc_exec_sql($cmd,"especw");

}
function cancelarPedido($id)
{
    $cmd = "update pub.peds_web set ind_sit_ped_web = 3 
            where ped_web_id = $id ";
    sc_exec_sql($cmd,"especw");

}
function efetivarPedido($id)
{

    $aPed = getRegPedWeb($id,'cod_tipo_pedido');
    if(is_array($aPed)){
        $tipo = $aPed[0]['cod_tipo_pedido'];
    }else{
        $tipo = '';
    }
    $aprovarGer = getAprovGerenciaPedWeb($tipo);
    if($aprovarGer == 1){
        $sit = 8;
        $lAvaliarPed = true;
    }else{
        $sit = 2;
        $lAvaliarPed = false;
    }
    $cmd = "update pub.peds_web set ind_sit_ped_web = $sit 
            where ped_web_id = $id ";
    sc_exec_sql($cmd,"especw");
    if($lAvaliarPed){
        $usuarioErp = getUsuarioERP(getUsuarioCorrente());
        avaliarPedido($id,1,1,0,6,'',$usuarioErp);
    }

}

function efetivarPedidosEmAberto()
{
    $aIds = getPedWebEmAbertoTodos(getLoginCorrente());
    if(is_array($aIds)){
        $tam = count($aIds);
        for($i=0;$i<$tam;$i++){
            $id = $aIds[$i]['ped_web_id'];
            efetivarPedido($id);
        }
    }
}

function getDescCondPagtoPedWeb($condPagtoId,$diasCondEsp)
{
    if($condPagtoId <> 0){
        $descricao = getDescCondPagto($condPagtoId);
    }else{
        if($diasCondEsp <> ''){
            $descricao = "ESPECIAL($diasCondEsp)";
        }else{
            $descricao = "<span style='color:red;font-weight: bold;'>Não Informada</span>";
        }
    }
    return $descricao;
}

function getRegPedWeb($id,$campos='',$filtroCompl='',$logUTF8=0)
{
    //echo "<h1>id:$id</h1>";
    $campoChave = "ped_web_id";
    $tabela   = "peds_web";
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
    $aRet = getDados($tipo,$tabela,$campos,$condicao,$conexao,'',$logUTF8);
    return $aRet;
}

function getCodigoClienteNovo($CNPJ)
{
    $msg = '';
    $aCliente = existClienteCNPJ($CNPJ);
    $codCliente = $aCliente['cod_cliente'];
    $codRepres	= $aCliente['cod_rep'];
    $nomeRepres = $aCliente['nome_abrev_repres'];
    if($codCliente <> 0){ // cliente encontrado
        echo"<h1>".[codRepIni]." - $codRepres </h1>";
        if([codRepIni] <> $codRepres){
            $msg = "Cliente Cadastrado com o numero $codCliente, mas, atribuido ao representante $codRepres - $nomeRepres";
        }
    }
    $aRetorno = array('cod_cliente' => $codCliente, 'msg' => $msg);
    return $aRetorno;
}

function atualizarCodClienteNovo($pedWebId,$codCliente,$tipo) //tipo-> principal ou triangular
{
    if($tipo == 'principal'){
        $campo = "cliente_id";
    }else{
        $campo = "cliente_triang_id";
    }
    $cmd = "update pub.peds_web set $campo = $codCliente
	where ped_web_id = $pedWebId ";
    sc_exec_sql($cmd,"especw");

}

function setTipoFrete($pedWebId,$tipoFrete,$logAtuFrete=false)
{

    if($logAtuFrete == false){
        $aRegPedWeb = getRegPedWeb($pedWebId,'cod_tipo_frete_cliente, cliente_id,cliente_triang_id');
        //var_dump($aRegPedWeb);
        $tipoFreteCliente = $aRegPedWeb[0]['cod_tipo_frete_cliente'];
        $cliente        = $aRegPedWeb[0]['cliente_id'];
        $clienteTriang  = $aRegPedWeb[0]['cliente_triang_id'];
        if($tipoFreteCliente == 0){
            $logAtuFrete = true;
        }
    }


    switch($tipoFrete){
        case 1:
            //echo "<h1>entrei tipo frete = 1</h1>";
            if($clienteTriang == 0 or $clienteTriang == ''){
                $transp =  getTranspPadraoCliente($cliente);
            }else{
                $transp =  getTranspPadraoCliente($clienteTriang);
            }

            $cmdTransp =", transp_id = $transp";
        break;
        case 3:
            $cmdTransp = " ,transp_id = 0 ";
        break;
        default:
            $cmdTransp = '';

    }

    if($logAtuFrete == true){
        $cdmFrete = ",cod_tipo_frete = $tipoFrete";
    }else{
        $cdmFrete= '';
        $cmdTransp = '';
    }

    $cmd = "update pub.peds_web set cod_tipo_frete_calc = $tipoFrete $cdmFrete $cmdTransp 
            where ped_web_id = $pedWebId";
    //echo "<h1>Atualizei o Tipo do Frete</h1>";
    sc_exec_sql($cmd,"especw");


}

function getDescrCliente($cliente,$cnpj ='')
{
    $descricao = '';
    $nomeAbrev = getNomeAbrevCliente($cliente,true);
    if($nomeAbrev == ''){
        if($cnpj <> ''){
            $cnpj = mascara($cnpj,"##.###.###/####-##");
            $descricao = "CLIENTE NOVO:$cnpj";
        }else{
            $descricao = " ";
        }
    }else{
        $descricao = $nomeAbrev;
    }
    return $descricao;
}

function setObservacao($pedWebId,$compl)
{
    $cmd = "update pub.peds_web set comentario = comentario + '$compl' where ped_web_id = $pedWebId ";
    sc_exec_sql($cmd,"especw");
}

function setObsSemEmail($pedWebId)
{
    setObservacao($pedWebId,'\n Cliente sem e-mail cadastrado');
}

function setObsTelefonesTransp($pedWebId,$telefoneTransp,$telefoneTranspRedesp='')
{
    if($telefoneTransp <> ''){
        $compl = " \n Nova Transportadora - telefone para contato: $telefoneTransp  " ;
    }

    if($telefoneTranspRedesp <> ''){
        $compl .= " \n Nova Transportadora Resdespacho - telefone para contato: $telefoneTranspRedesp  " ;
    }

    setObservacao($pedWebId,$compl);
}
function limparCNPJ($pedWebId)
{
    $aReg = getRegPedWeb($pedWebId,'cnpj_novo_cliente, cnpj_novo_cliente_triang');
    if(is_array($aReg)) {

        $cnpj = $aReg[0]['cnpj_novo_cliente'];
        $cnpj = str_replace('.', '', $cnpj);
        $cnpj = str_replace('/', '', $cnpj);



        $cnpjTriang = $aReg[0]['cnpj_novo_cliente_triang'];
        $cnpjTriang = str_replace('.', '', $cnpjTriang);
        $cnpjTriang = str_replace('/', '', $cnpjTriang);


        $cmd = "update pub.peds_web set cnpj_novo_cliente = '$cnpj', cnpj_novo_cliente_triang = '$cnpjTriang'
 where ped_web_id = $pedWebId ";
        sc_exec_sql($cmd,"especw");
    }

}

function tratarCondPagtoEspecial($valor)
{
    if($valor <> ''){
        $aValor = explode(',',$valor);
        if(is_array($aValor)){
            for($i=0;$i<count($aValor);$i++){
                $dia = $aValor[$i];
                if($dia <> "" and $dia <> 0){
                    $aDias[] = $dia;
                }
            }
        }
        if(is_array($aDias)){
            asort($aDias);
            $retorno = implode($aDias,',');
        }else{
            $retorno = $aDias;
        }


    }else{
        $retorno = '';
    }
    return $retorno;
}

function setCondpagtoPedWeb($pedWebId,$logVista,$diasCondPagto)
{
    $cmd = " update pub.peds_web set log_a_vista = $logVista, dias_cond_pagto_esp = '$diasCondPagto'
             where ped_web_id = $pedWebId";
    sc_exec_sql($cmd,"especw");
}

function setClientePedWeb($pedWebId,$cliente)
{
    $cmd = " update pub.peds_web set cliente_id = $cliente
             where ped_web_id = $pedWebId";
    sc_exec_sql($cmd,"especw");
}

function setDadosPedWeb($pedWebId,$logOperacTriang,$logNovoCliente,$clienteId,$cnpjNovoCliente,
                        $logNovoClienteTriang,$cnpjNovoClienteTriang,$clienteTriangId,
                        $emailsAdicionais, $codFinalidade,$codPrioridade, $indSitPedWeb,
                        $logPedidoManual,$logVista,$diasCondPagtoEsp,
                        $logDtFixa,$comentario,$codFormaPagto,$parcelasCredito)
{

    $msg = '';
    $logDtFixa = tratarNumero($logDtFixa);
    $logOperacTriang = tratarNumero($logOperacTriang);
    $logNovoClienteTriang = tratarNumero($logNovoClienteTriang);
    $logNovoCliente  = tratarNumero($logNovoCliente);
    $clienteTriangId = tratarNumero($clienteTriangId);
    if($codPrioridade   == ''){
        $codPrioridade = '10';
    }
    if($codFormaPagto == 2){ //  A VISTA
        $diasCondPagtoEsp = 0;
    }

    /*$msg = validacoesPedWeb($logOperacTriang,$logNovoCliente,$clienteId,$cnpjNovoCliente,
        $logNovoClienteTriang,$cnpjNovoClienteTriang,$clienteTriangId,
        $emailsAdicionais, $codFinalidade,$codPrioridade, $indSitPedWeb,
        $logPedidoManual,$logVista,$diasCondPagtoEsp,
        $logDtFixa,$comentario);*/
    //echo "entrei no setDadosPedWeb - $msg<br>";
    if($msg == ''){
        //echo "entrei no comando<br>";
        $cmd = "update pub.peds_web
              set log_operac_triang = $logOperacTriang ,
              log_novo_cliente = $logNovoCliente,
              cliente_id = $clienteId,
              cnpj_novo_cliente = '$cnpjNovoCliente',
              log_novo_cliente_triang = $logNovoClienteTriang,
            cnpj_novo_cliente_triang = '$cnpjNovoClienteTriang',
            cliente_triang_id = $clienteTriangId,
            emails_adicionais = '$emailsAdicionais',
            cod_finalidade = $codFinalidade,
            cod_prioridade = $codPrioridade ,
            log_a_vista = $logVista ,
            dias_cond_pagto_esp = '$diasCondPagtoEsp' ,
            log_dt_fixa = $logDtFixa ,
            comentario = '$comentario',
            cod_forma_pagto = $codFormaPagto,
            parcelas_credito = $parcelasCredito
            where ped_web_id = $pedWebId
            ";
        sc_exec_sql($cmd,"especw");
    }
    return $msg;
}


/*function validacoesPedWeb($logOperacTriang, $logNovoCliente, $clienteId, $cnpjNovoCliente,
                          $logNovoClienteTriang, $cnpjNovoClienteTriang, $clienteTriangId,
                          $emailsAdicionais, $codFinalidade, $codPrioridade, $indSitPedWeb,
                          $logPedidoManual, $logVista, $diasCondPagtoEsp,
                          $logDtFixa, $comentario, $logAVista, $logArray=0, $logDivideComis=0, $numAgrup=0,
                          $logPercNegoc, $percComisNegoc,$niveis=1)
{
    //nivel -> 1- dados do pedido  2-Comissão 3-frete e transp


    //echo "<h1>dias:$diasCondPagtoEsp</h1>";
    $msg      = '';
    $msgAviso = '';
    //echo "cliente id: $clienteId";
    $aNiveis = explode(',',$niveis);
    foreach($aNiveis as $nivelCorrente){
        switch ($nivelCorrente){
            case 1:
               $msg1 = validacoesDadosPedWeb($logOperacTriang,
                   $logNovoCliente, $clienteId, $cnpjNovoCliente,
                   $logNovoClienteTriang, $cnpjNovoClienteTriang,
                   $clienteTriangId,$emailsAdicionais, $codFinalidade,
                   $codPrioridade, $indSitPedWeb,
                   $logPedidoManual, $logVista, $diasCondPagtoEsp,
                   $logDtFixa, $comentario, $logAVista, $logArray=0,
                   $logDivideComis=0, $numAgrup=0);
               $msg = util_incr_valor($msg,$msg1,"</br>");

                break;
            case 2:
               $msg1 = validacoesComisPedWeb($logPercNegoc, $percComisNegoc);
                $msg = util_incr_valor($msg,$msg1,"</br>");
                break;
            case 3:
                $msg1 = validacoesFreteTranspPedweb();
                break;
        }
    }

    // $ret = validarClienteRepres($clienteId);
    //$msg = util_incr_valor($msg,"<h3>$ret</h3>",'',
   //     true);
    if($logArray == 0){
        //echo "<h1>converti</h1>";
        return utf8_encode($msg);
    }else{
        return array('erros' => $msg,'avisos' => $msgAviso);
    }

}*/

function validacoesDadosPedWeb($logOperacTriang, $logNovoCliente, $clienteId, $cnpjNovoCliente,
                               $logNovoClienteTriang, $cnpjNovoClienteTriang, $clienteTriangId,
                               $emailsAdicionais, $codFinalidade, $codPrioridade, $indSitPedWeb,
                               $logPedidoManual, $logVista, $diasCondPagtoEsp,
                               $logDtFixa, $comentario, $logAVista, $logDivideComis=0, $numAgrup=0)
{

    $msg = '';
    if($clienteId == 0 and $logNovoCliente == 0){

        $msg1 = '1 - O cliente precisa ser informado.';
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }

    if($clienteId == $clienteTriangId and $clienteId <> 0){
        $msg1 = '2 - O cliente Triangular não pode ser igual ao cliente do Pedido.';
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }

    if($clienteTriangId == 0 and $logOperacTriang == 1 and $logNovoClienteTriang == 0){
        $msg1 ='3 - O cliente Triangular precisa ser informado.';
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }

    $sit = getSitCliente($clienteId);

    $msg1= getMsgErroSitCliente($sit, $logAVista, $clienteId);
    if($msg1 <> '' and $clienteId <> 0){
        $msg1 = "13 - $msg1";
        $msg  = util_incr_valor($msg,$msg1,"</br>");
    }

    $bloqAdvVendas = getSitCliAdmVendas($clienteId);
    if($bloqAdvVendas == 1){
        $msg1 = '13.1 - Cliente bloqueado pela ADM Vendas';
        $msg  = util_incr_valor($msg,$msg1,"</br>");
    }



    if($diasCondPagtoEsp == '' and $logVista == 0){
        $msg1 = '4 - Informe uma condição de pagamento Especial';
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }

    if($logNovoCliente == 1 and $cnpjNovoCliente == '' ){
        $msg1 = '5 - Preencha o campo CNPJ, pois, foi marcado que este pedido é para um cliente novo.';
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }
    if( $logNovoClienteTriang == 1 and   $cnpjNovoClienteTriang == ''){
        $msg1 = '6 - Preencha o campo CNPJ Triang, pois, foi marcado que este pedido é 	
        para um cliente novo e que é um pedido de operação triangular.';
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }
    if(strlen($diasCondPagtoEsp) > 3 and strstr($diasCondPagtoEsp,',') == false){
        $msg1= '7 - A condição de pagamento deve estar separada por virgulas';
        $msg = util_incr_valor($msg,$msg1,"</br>");

    }


    if($cnpjNovoCliente <> ''){
        $aMsg = validarClienteNovo($cnpjNovoCliente);
        if(isset($aMsg['erro'])){
            $erro = $aMsg['erro'];
            $msg = util_incr_valor($msg,"$erro",'</br>',
                true);
        }
    }

    if($cnpjNovoClienteTriang <> ''){
        $aMsg = validarClienteNovo($cnpjNovoClienteTriang);
        if(isset($aMsg['erro'])){
            $erro = $aMsg['erro'];
            $msg = util_incr_valor($msg,"$erro",'</br>',true);
        }
    }



    /*if({cond_pagto_id} == 0 and {dias_cond_pagto_esp} == ''){
        sc_error_message('Informe uma condição de pagamento valida ou informe que a condição de pagamento é <b>especial</b> e preencha o campo <b>Dias</b>');
    }*/

    if((strstr('10,16,17,18',$codPrioridade) == false or  $codPrioridade < 10) and ($codPrioridade <> '' and $codPrioridade <> 0) ){
        $msg1 = "8 - Código de Prioridade($codPrioridade) Inválido. Os valores válidos são: 10,16,17,18";
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }
    /*if($logNovoCliente == 0){
        $logOperacTriangCalc = getUltsNotaTriang($clienteId);
        if($logOperacTriangCalc <> $logOperacTriang and $logOperacTriang == true ){
            $msgAviso = '<h3>Este cliente nas últimas 3 compras utilizou a Operação Triangular, favor verificar.';
        }
    }*/

    if(getTodosClienteCorrente() <> 1){
        $vendLoja		= verifVendLoja();
        $aCliOutroVend = verifCliOutroRepres($clienteId);
        //echo "<h1>cliente id: $clienteId</h1>";
        $lCliOutroVend = $aCliOutroVend['log_outro_vend'];
        $nomeRepresCli = $aCliOutroVend['nome_repres_cli'];
        $admVendas = verificGrupoLogin(4);
        $tpUsuario = getTipoUsuarioCorrente();
        if($vendLoja == 0 and $lCliOutroVend and $admVendas == false and $tpUsuario <> 5){
            $msg1 ="9-O Cliente $clienteId  pertence ao representante $nomeRepresCli 
                e não está inativo. Favor alterar o cliente ou solicitar alteração ao setor de cadastro";
            $msg = util_incr_valor($msg,$msg1,"</br>");
        }
        if( $logDivideComis == 0 and $numAgrup <> 0  and  $lCliOutroVend           and $vendLoja == 1 ){
            $msg1 ='12 - Este pedido não permite divisão de comissão. Escolha um cliente que esteja
                na sua carteira ou inativo.';
            $msg = util_incr_valor($msg,$msg1,"</br>");
        }
        /* verifica se um vendedor não está tentando fazer pedido para cliente de outro vendedor.
         Só poderia fazer para cliente de outro representante.*/
        if($vendLoja == 1 and $lCliOutroVend ){
            //echo "<h1>nome abre repres cli: $nomeRepresCli</h1>";
            //echo "<h1>p0</h1>";
            $tipoVend = getTipoVendedor($nomeRepresCli);
            //echo "<h1>p1</h1>";
            if($tipoVend == 'interno'){
                $msg1 ="11 - O cliente escolhido pertence a outro vendedor interno e não 
                    está inativo";
                $msg = util_incr_valor($msg,$msg1,"</br>");
            }
            //echo "<h1>p2</h1>";
        }
    }
    return $msg;
}
function validacoesComisPedWeb($logPercNegoc, $percComisNegoc,$percLimite)
{
    $msg = '';
    if($logPercNegoc == 1 and ($percComisNegoc == 0 or $percComisNegoc == '')){
        $msg1 = "10 - Quando a comissão é negociada é necessário informar o % de comissão negociada";
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }
    if($percComisNegoc >= $percLimite and $logPercNegoc == 1){
        $msg1 = "20 - O percentual de comissão negociado($percComisNegoc) deve ser menor que  $percLimite .";
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }
    return $msg;
}
function validacoesFreteTranspPedweb($tipoFrete, $transpId, $logNovoTransp,
                                     $transpRedespId, $logNovoTranspRedesp,
                                     $telefoneNovoTransp, $telefoneNovoTranspRedesp,$tpFreteCliente=0){
    $msg = '';
    if($tipoFrete == 3 and $transpId == 0 and $logNovoTransp == 0){
        $msg1 = "<h4>Para o tipo de frete FOB é necessário informar uma transportadora</h4>";
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }

    if($tipoFrete == 2 and $transpRedespId == 0 and $logNovoTranspRedesp == 0){
        $msg1 = "<h4>Para o tipo de frete CIF Parcial é necessário informar uma transportadora de redespacho</h4>";
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }
    if($transpRedespId == 0 and $logNovoTranspRedesp == 0 and $tpFreteCliente == 2){
        $msg1 = "<h4>Para o tipo de frete CIF Parcial é necessário informar uma transportadora de redespacho</h4>";
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }

    if($logNovoTransp == 1 and $telefoneNovoTransp == ''){
        $msg1 = "<h4>Informe um Telefone para na nova transportadora</h4>";
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }

    if($logNovoTranspRedesp == 1 and $telefoneNovoTranspRedesp == ''){
        $msg1 = "<h4>Informe um Telefone para na nova transportadora de redespacho</h4>";
        $msg = util_incr_valor($msg,$msg1,"</br>");
    }
    return $msg;
}


function getDescrIndSitPed($sit)
{
    $descr = "";
    switch ($sit){
        case 1:
            $descr = "Em Digitação";
            break;
        case 2:
            $descr = "Efetivado";
            break;
        case 3:
            $descr = "Cancelado";
            break;
        case 4:
            $descr = "Integrado ERP";
            break;
        case 5:
            $descr = "Rejeitado";
            break;
        case 6:
            $descr = "Vencido";
            break;
    }
    return $descr;
}
function setComisPedWeb($pedWebId, $aComis)
{

    $percComis = $aComis[0] ['perc_comis'];
    echo "<h1>Comissão=$percComis</h1>";
    $percComis = str_replace(',','.',$percComis);
    if(isset($aComis[1])){
        $percComis2 = $aComis[1] ['perc_comis'];
        $percComis2 = str_replace(',','.',$percComis);
        $codRep2    = $aComis[1] ['cod_rep'];
    }else{
        $percComis2 = '';
        $codRep2    = 0;
    }
    $array['perc_comis'] = $percComis;
    if($percComis2 <> ''){
        $array['perc_comis_2'] = $percComis2;
        $array['repres_2_id']  = $codRep2;
    }
    $cmd = convertArrayEmUpdate('peds_web',$array,
        " ped_web_id = $pedWebId ");

    //$cmd = "update pub.peds_web set perc_comis = '$percComis'";
    sc_exec_sql($cmd,"especw");
}

function getFormaPagtoCartao($ped_web_id)
{
    $formaPagto = 0;

    $tabela   = " PUB.peds_web ";
    $campos   = " cod_forma_pagto ";
    $condicao = "  PUB.peds_web.ped_web_id = $ped_web_id ";
    $aRet = getDados('unico',$tabela,$campos,$condicao);
    if(is_array($aRet)){
        $formaPagto = $aRet[0]['cod_forma_pagto'];
    }
    return $formaPagto;

}
function avaliarFormaPagtoCartao($formaPagto)
{
    $lAchou = false;
    if(strstr('3,4',$formaPagto) <> false){
        $lAchou = true;
    }
    return $lAchou;
}
function setPrepostoPedsWeb($pedWebId,$preposto)
{

    $array = array('login_preposto' => $preposto);
    $cmd = convertArrayEmUpdate('pub.peds_web', $array, "ped_web_id = $pedWebId");
    sc_exec_sql($cmd,"especw");

}
function getCodMoeda($moeda)
{
    switch($moeda)
    {
        case 2:
        case 'dolar':
            $ret = 'dolar';
            break;
        default:
            $ret = 'real';
    }
    return $ret;
}

function calcSitComissao($qtItens,$divideComissao,$percComisRepres,$percComisVend)
{
    $sitPercVend     = '';
    $sitComissaoPerc = '';
    if ($qtItens == 0) {
        $sitComissao = 'Em Aberto - Sem Itens';
    } else {
        if ($divideComissao == 1) {
            $sitComissao = "Comissão Dividida";
        } else {
            $sitComissao = "Comissão Única";
        }
        if ($percComisRepres == 0) {
            $sitPercRepres = "Repres: Comissão Padrão";

        } else {
            $sitPercRepres = "Repres: Comissão Especial:{perc_comis_repres}";
        }
        if ($percComisVend == 0) {
            $sitPercVend = "Vendedor: Comissão Padrão";
        } else {
            $sitPercRepres = "Repres: Comissão Especial:{perc_comis_vend}";
        }

        if (verifVendLoja() == 1) {
            if ($divideComissao == 1) {
                $sitComissaoPerc = " $sitPercVend - $sitPercRepres ";
            } else {
                $sitComissaoPerc = $sitPercVend;
            }
        } else {
            $sitComissaoPerc = $sitPercRepres;
        }
    }
    $sitComissao .= " - $sitComissaoPerc ";

    return $sitComissao;
}
function atuDadosPedWeb($pedWebId,$aDados)
{
    $cmd= convertArrayEmUpdate('peds_web',$aDados, " ped_web_id = $pedWebId");
    sc_exec_sql($cmd,"especw");

}
function atuAgrupPedWeb($pedWebId,$divideComis,$percComisRepres,$percComisVend)
{
    $qt = getQtItensRefPedWeb($pedWebId);
    if($qt == 0){
        $aPedWeb = getRegPedWeb($pedWebId);
        if(is_array($aPedWeb)){
            $codMoeda       = $aPedWeb[0]['cod_moeda'];
            $nrContainer    = $aPedWeb[0]['nr_container'];
            $tbPreco        = $aPedWeb[0]['tb_preco_id'];

            $numAgrup = getAgrupPedido($codMoeda,$nrContainer,$tbPreco,
                $divideComis,$percComisVend,$percComisRepres);
            $array = array('log_divide_comissao'=>$divideComis,
                'perc_comis_repres'=>$percComisRepres,
                'perc_comis_vend'=>$percComisVend,
                'num_agrup'=> $numAgrup
            );
            atuDadosPedWeb($pedWebId,$array);
        }
    }
}
function limparNumAgrupPedWeb($pedWebId)
{
    $aDados = array('num_agrup'=>0);
    atuDadosPedWeb($pedWebId,$aDados);
}
function setSitPedWeb($numSit){
    $array = array();
}

function getQtPedsGerecial($tpUsuario, $usuario=''){


    $tabela   = " PUB.peds_web ";
    $campos   = " ped_web_id ";
    $listaRepres = getListaRepresGer();
    $listaRepres = tratarNumero($listaRepres);
    $codRep = buscarCodRep($usuario);
    $hierarq = getTpHierarquiaGer($codRep);
    $admVendas = verificGrupoLogin(4);
    $aprovador = verificGrupoLogin(13);
    $cond = "PUB.peds_web.ind_sit_ped_web = 8 and login >= '$usuario' and login <= '$usuario'";



    if($tpUsuario == 4 or $admVendas){
        $condicao = "  PUB.peds_web.ind_sit_ped_web = 8";
    }else if($tpUsuario == 2 or $tpUsuario == 5){
        $condicao = "  PUB.peds_web.ind_sit_ped_web = 8 and login >= '$usuario' and login <= '$usuario'";
    }else{
         if($aprovador and $hierarq <> 1){
            $cond = "  PUB.peds_web.ind_sit_ped_web = 8 and login in($listaRepres)";
        }else if($aprovador and $hierarq == 1){
            $cond = "  PUB.peds_web.ind_sit_ped_web = 8";
        }
        $condicao = $cond;

    }
    $aRet = getDados('multi',$tabela,$campos,$condicao,'espec');

    if($aRet <> ''){
        $tam = count($aRet);
    }else{
        $tam = 0;
    }
    return $tam;



}
function getSitsPedWebEmAberto(){
    return '1,9';
}
function getSitsPedWebAlocados(){
    return '1,2,5,9';
}

?>
