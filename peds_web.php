<?php
//__NM____NM__FUNCTION__NM__//
function desenharPedWebHtml($pedWebId,$clienteId,$dtHrReg,$totPed,$clienteTriangId)
{

    $aCliente = getDadosCliente($clienteId,'"ind-cre-cli" as ind_cre_cli,"nome-emit" as nome_emit');
    $indCreCli = $aCliente[0]['ind_cre_cli'];
    $nomeEmit  = $aCliente[0]['nome_emit'];
    $nomeEmit = $clienteId."-".$nomeEmit;

    $aClienteTriang = getDadosCliente($clienteTriangId,'"nome-emit" as nome_emit');
    $nomeEmitTriang = $aClienteTriang[0]['nome_emit'];
    if($clienteTriangId == 0){
        $nomeEmitTriang = '';
    }else{
        $nomeEmitTriang = "Triangular:".$clienteTriangId."-".$nomeEmitTriang;
    }


    $htmlCliente = desenharHtmlClienteComSitCred($nomeEmit,$indCreCli);
    //$htmlCliente = dividirTexto($htmlCliente,28);
    $vlTotPed   = formatarNumero($totPed,2);
    $dtHrReg  = convDataTimeEnToBr($dtHrReg);
    $aLinks = array();
    $aLinks[] = array('href'=>"../form_peds_web/form_peds_web.php?id_corrente_ped_web=$pedWebId&novo_registro=0&deletar=&alterar=1",'descricao'=>'Alterar');
    $aLinks[] = array('href'=>"../form_peds_web/form_peds_web.php?id_corrente_ped_web=$pedWebId&deletar=1&novo_registro=0&alterar=0",'descricao'=>'Excluir');
    $aLinks[] = array('href'=>"../cons_itens_ped_web/cons_itens_ped_web.php?ped_web_id_corrente=$pedWebId&log_considerar_mobile=1",'descricao'=>'Itens');
    //$aLinks[] = array('href'=>"../cons_itens_ped_web/cons_itens_ped_web.php?ped_web_id_corrente=$pedWebId&log_considerar_mobile=0",'descricao'=>'Itens Tb.');
    $aLinks[] = array('href'=>"../bl_inserir_web_ped_venda/bl_inserir_web_ped_venda.php?ped_web_id_corrente=$pedWebId&voltar=cons_peds_web",'descricao'=>'Efetivar');
    $links  	= '';
    $links  	= criarLinks($aLinks," | ",'links');
    $retorno = <<<RET
        <div class="container">            
	 <h3>ID: $pedWebId</h3> 
      $htmlCliente  <hr> $nomeEmitTriang                   
	<p><i class="bi bi-calendar-week-fill"></i>&nbsp;&nbsp;  Dt.Implant.: $dtHrReg</p>	
	<p><span class="vl"> <i class="bi bi-cash"></i> &nbsp;&nbsp;Valor: $vlTotPed</span></p>
	<p>$links</p>                  
</div>	
RET;
    return $retorno;

}
function excluirPedWeb($pedWebId)
{
    excluirItensPedWeb($pedWebId);
    $cmd = " delete from peds_web where id = $pedWebId";
    sc_exec_sql($cmd,"aux");

}

function validarPedWeb($tipoPedido,$clienteId,$clienteTriangId,$condPagto,$reservaId,$tpEntrega,$dtTpEntrega,$tpFrete,$logAlterar)
{
    $msgErro = '';


    switch($tipoPedido){
    case '3': //reserva
            if($reservaId == 0 and $logAlterar == 0){

                $msgErro = util_incr_valor($msgErro,
                    "<h3>1-Necessário Informar a reserva, pois o tipo de Pedido é de Reserva.</h3>",
                '');
            }
            break;
        case '5': // a vista
            if($condPagto <> 1 and $logAlterar = 0){
                $msgErro = util_incr_valor($msgErro,
                    "<h3>2-A condição de Pagamento precisa ser 'à vista'(código 1).</h3>",
                    '');
            }
            break;

        case '6': // Operação Triangular
            if(($clienteTriangId == 0 or $clienteTriangId == '') and $logAlterar == 0){
                $msgErro = util_incr_valor($msgErro,
                    "<h3>3-Necessário Informar o cliente triangular, pois o tipo de pedido é de Oper. Triangular.</h3>",
                    '');
            }

            break;
        default:
            if(($clienteId == 0 or $clienteId == '') and $logAlterar == 0) {
                $msgErro = util_incr_valor($msgErro,
                    "<h3>4-Necessário Informar o Cliente.</h3>",
                    '');
            }
            if($condPagto == 0 or $condPagto == '' ){
                $msgErro = util_incr_valor($msgErro,
                    "<h3>5-A condição de Pagamento precisa ser informada.</h3>",
                    '');
            }
            if($dtTpEntrega == ''){
                $msgErro = util_incr_valor($msgErro,
                    "<h3>6-A Data do Tipo de Entrega Precisa ser informada.</h3>",
                    '');
            }
            if($tpFrete == 0 or $tpFrete == '' ){
                $msgErro = util_incr_valor($msgErro,
                    "<h3>7-O Tipo de Frete deve ser informado.</h3>",
                    '');
            }
            $aCliente = getDadosCliente($clienteId,'"ind-cre-cli" as ind_cre_cli,"nome-emit" as nome_emit');
            $indCreCli = $aCliente[0]['ind_cre_cli'];
            if($indCreCli == getNumCredSuspenso() and $logAlterar == 0){ //suspenso
                $msgErro = util_incr_valor($msgErro,
                    "<h3>8-Cliente SUSPENSO</h3>",
                    '');
            }
            if($indCreCli == getNumCredAVista() and $condPagto <> 1 and $logAlterar == 0){
                $msgErro = util_incr_valor($msgErro,
                    "<h3>9-Cliente Com restrição de Pagamento à vista. Cond.Pagto Inválida. Deve ser informada
                                a condição igual a 1</h3>",
                    '');
            }
    }
    return $msgErro;
}
function calcDiasTpEntrega($tpEntrega)
{
    $a = array();
    $lAchou = false;
    $aDtsMes = getDatasMesCorrente();
    $ultDiaMes = $aDtsMes[0]['dtFim'];
    switch($tpEntrega){
        case '1': //primeira Quinzena
            $mes = trim(getParteDtCorrente('mes'));
            $mes = mascara($mes,"##");
            $ano = trim(getParteDtCorrente('ano'));
            $data = "$ano-$mes-15";
            $a['data'] = $data;
            $a['log_desabilitar'] = false;
            $lAchou = true;
            break;
        case '2': //segunda quinzena
        case '3': // no mês
            $a['data'] = substr($ultDiaMes,6,4)."-".substr($ultDiaMes,0,2)."-".substr($ultDiaMes,3,2);
            $a['log_desabilitar'] = false;
            $lAchou = true;
            break;

        case '4': // Na Data
            $a['data'] = '';
            $a['log_desabilitar'] = false;
            $lAchou = true;
            break;
        case '5': // até a data
            $a['data'] = '';
            $a['log_desabilitar'] = false;
            $lAchou = true;
            break;
        case '6': // imediata
            $a['data'] = getHoje();
            $a['log_desabilitar'] = true;
            $lAchou = true;
            break;
    }
    if(!$lAchou){
        $a = '';
    }
    return $a;
}
function criarItensPedWebPorReserva($numReserva,$numPedido)
{
    $aItens = getItensReserva($numReserva);
    if(is_array($aItens)){
        foreach($aItens as $item){
            $itCodigo       = $item['"it-codigo"'];
            $codRefer       = $item['"cod-refer"'];
            $corteComerc    = $item['"corte-comerc"'];
            $nrLote         = $item['"nr-lote"'];
            $seq            = $item['"nr-sequencia"'];
            $qt             = getQtTotalReservaSeq($numReserva,$seq);
            inserirItemPedWeb($numPedido,$itCodigo,$codRefer,$nrLote,$corteComerc,$qt,0);
        }
    }
}
function usarReserva($numReserva,$numPedido)
{
    criarItensPedWebPorReserva($numReserva,$numPedido);
    fecharReserva($numReserva);
}
function getFiltroPedsWebTpUsuario($apelido,$logAplicarFiltro=false)
{
    $condicao = '';
    if($apelido == ''){
        $apelido = 'ped';
    }
    $apelido .= '.';
    switch(getVarSessao('tipo_usuario_id')){
        case getNumTipoUsuarioCliente():
            $condicao = $apelido.'cliente_id = '.getVarSessao('num_cliente');
            break;
        case getNumTipoUsuarioRepresentante():
            $condicao = $apelido.' repres_id = '.getVarSessao('num_rep');
            break;
        //outros tipos de usuários não tem filtro especifico
    }
    if($logAplicarFiltro){
        setCondWhere($condicao);
    }
    return $condicao;
}
function convDescrTpFreteParaNumero($descricao)
{
    $descricao = strtolower($descricao);
    $descricao = retirarAcentoSimples($descricao);
    $descricao = str_replace(' ','_',$descricao);
    $codigo = '0';
    switch($descricao){
        case 'cif_total':
            $codigo = '1';
            break;
        case 'cif_ate_redespacho':
            $codigo = '2';
            break;
        case 'cif_destaque nf':
            $codigo = '3';
            break;
        case 'fob_total':
            $codigo = '4';
            break;
        case 'fob_ate_redespacho':
            $codigo = '5';
            break;
        case 'prepaid_export':
            $codigo = '6';
            break;
    }
    return $codigo;
}
function convNumeroParaDescTpEntrega($numero)
{
    switch($numero){
        case '1':
            $descricao = '1ª Quinzena';
            break;
        case '2':
            $descricao = '2ª Quinzena';
            break;
        case '3':
            $descricao = 'No mês';
            break;
        case '4':
            $descricao = 'Na Data';
            break;
        case '5':
            $descricao = 'Até a Data';
            break;
        case '6':
            $descricao = 'Imediata';
            break;
        default:
            $descricao = $numero;

    }
    return $descricao;

}
function convNumeroParaDescTpPedido($numero)
{
    switch($numero){
        case '1':
            $descricao = 'NORMAL';
            break;
        case '2':
            $descricao = 'EXPORTACAO';
            break;
        case '3':
            $descricao = 'RESERVA';
            break;
        case '4':
            $descricao = 'AMOSTRA';
            break;
        case '5':
            $descricao = 'A VISTA';
            break;
        case '6':
            $descricao = 'OPER. TRIANGULAR';
            break;
        case '7':
            $descricao = 'BONIFICACAO';
            break;
        case '8':
            $descricao = 'DOACAO';
            break;
        case '9':
            $descricao = 'BANCADO';
            break;
        case '10':
            $descricao = 'REFATURAMENTO';
            break;
        case '11':
            $descricao = 'AMOSTRA EXPORTACAO';
            break;
        case '12':
            $descricao = 'REMESSA INDUSTRIALIZACAO';
            break;
        case '13':
            $descricao = 'VENDA CONFECCAO PROPRIA';
            break;
        case '14':
            $descricao = 'VENDA CONFEC. TERCEIRO';
            break;
        default:
            $descricao = $numero;
    }
    return $descricao;

}

function convNumeroParaDescrTpFrete($numero)
{


    switch($numero){
        case '1':
            $descricao = 'CIF TOTAL';
            break;
        case '2':
            $descricao = 'CIF ATE REDESPACHO';
            break;
        case '3':
            $descricao = 'CIF DESTAQUE NF';
            break;
        case '4':
            $descricao = 'FOB TOTAL';
            break;
        case '5':
            $descricao = 'FOB ATE REDESPACHO';
            break;
        case '6':
            $descricao = 'PREPAID EXPORT';
            break;
        default:
            $descricao = $numero;
    }
    return $descricao;
}



function getRegPedsWeb($idParam,$campos='')
{
    if($campos == ''){
        $campos = getCpsPedsWeb();
    }
    /*echo "<h1>id ped web:$idParam</h1>";
    echo "<h1>campos:$campos</h1>";*/

    $aReg = getReg('aux','peds_web','id',$idParam,$campos);
    return $aReg;
}
function getClienteIdPedsWeb($idParam)
{
    $aReg = getRegPedsWeb($idParam,'cliente_id');
    return getVlIndiceArray($aReg,'cliente_id',0);

}
function getCpsPedsWeb()
{
    return 'id,dt_hr_registro,ind_sit_ped_web,log_operac_triang,log_a_vista,cliente_id,cliente_triang_id,log_cond_pagto_especial,
    cond_pagto_id,dias_cond_pagto_esp,transp_id,transp_redesp_id,cod_estabel,perc_comis,repres_id,comentario,cod_tipo_pedido,
    login,login_digitacao,login_preposto,tp_entrega,nr_pedido_cliente,nr_pedido_repres,data_tp_entrega,reserva_id,cod_tipo_frete';
}
?>