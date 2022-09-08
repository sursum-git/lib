<?php 
function montarFiltroPedido($aFiltros,$aApelidoTb)
{ /* chaves aFiltros: 
     cod_estab,cod_emitente,nr_pedido,nf,dt_inicial,
     dt_final,cod_rep, sit_cred, sit_preco, sit_ped,cod_rep
    */
    $aFiltroCond = array();
    $tabela ='ped-venda';
    if(is_array($aApelidoTb) and isset($aApelidoTb[$tabela]) ){
        $tabela = $aApelidoTb[$tabela];
    }
    $logNF       = false;
    $logPedido   = false;
    $logFiltrar  = true;
    if($aFiltros['nf'] <> ''){
        $logNF = true;
        $logFiltrar = false;
    } 
    if($aFiltros['nr_pedido'] <> ''){
        $logPedido = true;
        $logFiltrar = false;
    } 
    if($logFiltrar){
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-estabel',$aFiltros['cod_estab']);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-emitente',$aFiltros['cod_emitente'],'=',true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'dt-implant',$aFiltros['dt_inicial'],'>=');
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'dt-implant',$aFiltros['dt_final'],'<=');
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-sit-aval',$aFiltros['sit_cred'],'in',true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-sit-preco',$aFiltros['sit_preco'],'in', true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-sit-ped',$aFiltros['sit_ped'],'in',true);
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-rep',$aFiltros['cod_rep'],'in',true );  
    }
    if(!$logNF){
        $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nr-pedido',$aFiltros['nr_pedido'],'=',true);
    }    
    $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nr-nota-fis',$aFiltros['nf'],'=');
    
    
    return convArrayToCondSql($aFiltroCond);
}


function getDescrSitPed($sit)
{
    $retorno = '';
    switch($sit){
        case 1:
            $retorno = 'Aberto';
            break;
        case 2:
            $retorno = 'Atend.Parcial';
            break;    
        case 3:
            $retorno = 'Faturado';
            break;
        case 4:
            $retorno = 'Pendente';
            break;
        case 5:
            $retorno = 'Suspenso';
            break;
        case 6:
            $retorno = 'Cancelado';
            break;            
        case 7:
            $retorno = 'Fat.Balcão';
            break;                

    }
    return $retorno;


}
function getDescrSitAval($sit)
{
    $retorno = '';
    switch($sit){
        case 1:
            $retorno = 'Não Avaliado';    
            break;        
        case 2:
            $retorno = 'Aprovado';
            break;     
        case 3:
            $retorno = 'Avaliado';
            break;    
        case 4:
            $retorno = 'Reprovado';
            break;
        case 5:
            $retorno = 'Pend.Informação';
            break;
        case 6:
            $retorno = 'Em Analise';
            break;
        case 6:
            break;            

    }
    return $retorno;

}
function getDescrSitPreco($sit)
{
    $retorno = '';
    switch($sit){
        case 1:
            break;
        case 2:
            break;    
        case 3:
            break;
        case 4:
            break;
        case 4:
            break;
        case 6:
            break;            

    }
    return $retorno;

}
function getCodsPedAberto()
{
    return '1,2,4,5,7';
}
function getCodsFaturados()
{
    return '3';
}
function getCodsCancelado()
{
    return '6';

}
function getCodsPedNaoAval()
{
   return '1,2,5';

}
function getCodsReprov()
{
    return '4';

}
function getCodsAprov()
{
    return '3';

}

function getHtmlSitPed($sitPed,$descricao)
{    
    $classe= '';
    if(stristr(getCodsPedAberto(),$sitPed) <> false){
        $avatar = 'bi bi-cart-fill';
        $classe = 'aberto';
    }else{
        if(stristr(getCodsFaturados(),$sitPed) <> false){            
            $classe = 'faturado';
        }else{
            $classe = 'cancelado';
        }
    }
    $descrSit = getDescrSitPed($sitPed);

    return ' <span class="'.$classe.'"> <i class="bi bi-cart-fill"></i>&nbsp;&nbsp; '.$descrSit.' | '.$descricao.'</span>';


    
}

function getHtmlSitAval($sitAval)
{    /*
        bi bi-question-square-fill -> não avaliado e Limbo
        bi bi-exclamation-square-fill -> reprovado
        bi bi-check-square-fill -> aprovado

    
    */
    $classe= '';
    $avatar = '';
    if(stristr(getCodsAprov(),$sitAval) <> false){
        $avatar = 'bi bi-check-square-fill';
        $classe = 'aprovado';
    }else{
        if(stristr(getCodsReprov(),$sitAval) <> false){            
            $classe = 'reprovado';
            $avatar = 'bi bi-exclamation-square-fill';
        }else{
            $classe = 'naoavaliado';
            $avatar = 'bi bi-question-square-fill';
        }
    }
    $descrSit = getDescrSitAval($sitAval);

    return ' <span class="'.$classe.'"> <i class="'.$avatar.'"></i>&nbsp;&nbsp; Pedido '.$descrSit.'</span>';

}

?>