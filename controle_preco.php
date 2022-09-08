<?php

function getRegPrecoAtu($id)
{
    $aReg = getReg('espec','controle_preco',
              'cod_controle_preco',$id);
    return $aReg;
}

function getExistPrecoItemRefTb($tbPreco,$itemParam,$empresa='5',$refParam='')
{
    inserirLogDb('parametros -> tabela - item - empresa -referencia',
        "$tbPreco - $itemParam - $empresa - $refParam",__FUNCTION__);
    $lAchou = 0;
    $lBuscaEsp = false;
    switch($tbPreco){
        case 1: //tabela padrao e pode estar buscando no ERP dependendo do parametro
            $lBuscarPrecoTbERP = getParamPrecoTbERP();
            inserirLogDb('busca tabela do ERP?',getVlLogico($lBuscarPrecoTbERP),__FUNCTION__);
            if($lBuscarPrecoTbERP == 1){
                $aPreco = getPrecoTbERP($empresa,$itemParam,$refParam);
                if(is_array($aPreco)){
                    inserirLogDb('achou preço no ERP','sim',__FUNCTION__);
                    $lAchou = 1;
                }else{
                    inserirLogDb('achou preço no ERP','nao',__FUNCTION__);
                }
            }else{
                    inserirLogDb('busca tabela especifica - 01','sim',__FUNCTION__);
                    $lBuscaEsp = true;
            }
            break;
        default:
            inserirLogDb('busca tabela especifica - 02','sim',__FUNCTION__);
                $lBuscaEsp = true;
            break;
    }
    if($lBuscaEsp){
        inserirLogDb('buscou tab.esp','sim',__FUNCTION__);
        $aPreco = getPriPrecoAtuItemRef($tbPreco,$itemParam,$refParam);
        if(is_array($aPreco)) {
            $lAchou = 1;
            inserirLogDb('achou preco tab.esp','sim',__FUNCTION__);
        }else{
            inserirLogDb('achou preco tab.esp','nao',__FUNCTION__);
        }
    }else{
        inserirLogDb('buscou tab.esp','nao',__FUNCTION__);
    }
    return $lAchou;


}
function getPrecoAtu($tabela,$tpPreco,$nrContainer,$item,$ref,$logComSaldo=0)
{
    //echo "<h2>5</h2>";
    //busca primeiramente por referencia
    $aPrecoRef = getPrecoAtuNivel($tabela,2, $tpPreco,$nrContainer,$item,$ref,$logComSaldo);
    if(is_array($aPrecoRef)){
        $aRet =  array('id'=> $aPrecoRef[0]['id'],
            'vl_real' => $aPrecoRef[0]['vl_real'],
            'vl_dolar' => $aPrecoRef[0]['vl_dolar'],
            'campanha_id'=>$aPrecoRef[0]['campanha_id'],
            'log_achou'=> 1);
    }else{ // senão achar busca o preço por item
        $aPrecoItem = getPrecoAtuNivel($tabela,1, $tpPreco,$nrContainer,$item,'',$logComSaldo);
        if(is_array($aPrecoItem)){
            $aRet =  array('id'=> $aPrecoItem[0]['id'],
                'vl_real' => $aPrecoItem[0]['vl_real'],
                'vl_dolar' => $aPrecoItem[0]['vl_dolar'],
                'campanha_id'=>$aPrecoItem[0]['campanha_id'],
                'log_achou'=>1);
        }
        else{
            $aRet = array('id'=>0,
                'vl_real'=>0,
                'vl_dolar'=>0,
                'campanha_id'=>0,
                'log_achou'=>0);
        }
    }
    //echo "<h2>6</h2>";
    return $aRet;
}
function getPrecoAtuOutlet($tabela,$item,$ref,$logComSaldo=0)
{
    $aPreco = getPrecoAtu($tabela,3,0,$item,$ref,$logComSaldo);
    if(is_array($aPreco)){
        $aRet =  array('id'=> $aPreco['id'], 'vl_real' => $aPreco['vl_real'],
                       'vl_dolar' => $aPreco['vl_dolar'],
                      'campanha_id'=>$aPreco['campanha_id']);

    }
    else{
        $aRet = array('id'=>0,'vl_real'=>0,'vl_dolar'=>0,'campanha_id'=>0);
    }
    return $aRet;

}

function getPrecoAtuPE($tabela,$item,$ref)
{
    inserirLogDb('Tabela de Preço:',$tabela,__FUNCTION__);
    $aPreco = getPrecoAtu($tabela,1,0,$item,$ref);
    //var_dump($aPreco);
    if(is_array($aPreco)){
        $aRet =  array('id'=> $aPreco['id'],'vl_real' => $aPreco['vl_real'], 'vl_dolar' => $aPreco['vl_dolar'],"log_achou"=>1);
    }
    else{
        $aRet = array('id'=> 0,'vl_real'=>0,'vl_dolar'=>0,"log_achou"=>0);
    }
    inserirLogDb('array final getPrecoAtuPe',$aRet,__FUNCTION__);
    //echo "<h2>2</h2>";
    return $aRet;

}
function getPrecoAtuPI($tabela,$nrContainer,$item,$ref)
{
    //echo "<h1>vou buscar preço PI</h1>";
    //echo "<h2>3</h2>";
    $aPreco = getPrecoAtu($tabela,2,$nrContainer,$item,$ref);
    //var_dump($aPreco);
    if(is_array($aPreco)){
        $aRet =  array('id'=> $aPreco['id'],
                       'vl_real' => $aPreco['vl_real'],
                       'vl_dolar' => $aPreco['vl_dolar'],
                        'log_achou'=>$aPreco['log_achou']);
    }
    else{
        $aRet = array('id'          => 0,
                      'vl_real'     => 0,
                      'vl_dolar'    => 0,
                      'log_achou'   => 0) ;
    }
    //echo "<h2>4</h2>";
    return $aRet;

}


function getPriPrecoAtuItemRef($tabela,$itemParam,$ref='')
{
    $filtroRef ="";
    if( $ref <> ''){
        $filtroRef = " and cod_refer = '$ref'";
    }
    $hoje = date('Y-m-d');
    $aReg = getDados('unico',
        'pub.controle_preco',
        ' top 1 cod_controle_preco as id, dt_inicial,dt_final, vl_real,vl_dolar, campanha_id',
        "tb_preco_id = $tabela  
              and it_codigo = '$itemParam'
              $filtroRef             
             and dt_inicial <='$hoje' and dt_final >= '$hoje' 
             and log_vencido = 0 order by cod_controle_preco desc ",
        'espec');
    return $aReg;

}

function getPrecoAtuNivel($tabela,$nivel,$tpPreco,$nrContainer,$item,$ref='',$logComSaldo=0)
{
    $nrContainer = tratarNumero($nrContainer);
    inserirLogDb('parametros',"tabela:$tabela - nivel: $nivel - tp.Preco: $tpPreco 
    - nr.container:$nrContainer - item:$item ref:$ref",__FUNCTION__);
    $filtroRef ="";
    //echo "<h1>REF: $ref</h1>";
    if($nivel == 2
        and $ref <> ''
    ){
        $filtroRef = " and cod_refer = '$ref'";
    }
    if($logComSaldo == 1){
        if($nivel == 1){
            $inner = 'inner join med.PUB."saldo-estoq" saldo on saldo."it-codigo" = preco.it_codigo 
                  and saldo."qtidade-atu" > 0 ';
        }else{
            $inner = 'inner join med.PUB."saldo-estoq" saldo on saldo."it-codigo" = preco.it_codigo 
                  and saldo."cod-refer" = preco.cod_refer and saldo."qtidade-atu" > 0 ';
        }
    }else{
        $inner = '';
    }
    $hoje = date('Y-m-d');
    $aReg = getDados('unico',
        'pub.controle_preco preco' ,
        ' top 1 cod_controle_preco as id, dt_inicial,dt_final, vl_real,vl_dolar, campanha_id',
        "tb_preco_id = $tabela and num_nivel = $nivel
             and nr_container = $nrContainer and it_codigo = '$item'
             $filtroRef
             and tp_preco = $tpPreco 
             and dt_inicial <='$hoje' and dt_final >= '$hoje' 
             and log_vencido = 0 order by cod_controle_preco desc ",
        'multi',$inner);
    inserirLogDb('01 - retorno final getPrecoAtuNivel',$aReg,__FUNCTION__);
    return $aReg;


}



function getDadosPrecosConcorrente($dtRef,$tabela,$nivel,$tpPreco,$nrContainer,$item,$ref='',$logNivelInf=true,$idExcecao=0)
{

    $nrContainer = tratarNumero($nrContainer);
    if($nivel == 1 and $logNivelInf){
        $ref = '';
        $condNivel = " "; // deve vencer o nível 1 e 2
        $condRef = '';

    }else{
        $condNivel = " and num_nivel = $nivel ";
        $condRef = "and cod_refer = '$ref'";
    }
    
    $id = 0;
    $aReg = getDados('multi',
        'pub.controle_preco',
        ' cod_controle_preco as id, dt_inicial,dt_final',
     " log_vencido = 0  
             and dt_final >= '$dtRef'             
             $condRef
             and it_codigo = '$item'
             and nr_container = $nrContainer
             and tp_preco = $tpPreco
             $condNivel
             and tb_preco_id = $tabela 
             and cod_controle_preco <> $idExcecao",
    'espec');
    return $aReg;

}

function vencerPreco($dtIni,$tabela,$nivel,$tpPreco,$nrContainer,$itemParam,$ref='',$logNivelInf,$idExcecao=0)
{
    $erro = '';
    $hoje = date('Y-m-d');
    $aReg = getDadosPrecosConcorrente($dtIni,$tabela,$nivel,$tpPreco,$nrContainer,$itemParam,$ref,$logNivelInf,$idExcecao);
    //var_dump($aReg);
    if(is_array($aReg)){
        $tam = count($aReg);
        for($i=0;$i<$tam;$i++){
            $id = $aReg[$i]['id'];
            $dtInicial = $aReg[$i]['dt_inicial'];
            $dtFinal   = $aReg[$i]['dt_final'];
            if($dtIni == $hoje){
               $logVencido = 1;
               $dtVencto = $dtIni;
            }else{
                //IMPORTANTE: não pode existir dtIni(data novo preco) menor que hoje
                //dtini maior que hoje
                echo "<h1>$dtIni</h1>";
                $dtVencto   = sc_date($dtIni, "aaaa-mm-dd", "-", 1,0,0);
                $logVencido = 0;
            }

            $usuario = getUsuarioERP(getLoginCorrente());
            if($usuario == ''){
                $erro = "Usuário corrente sem usuário ERP associado.";
            }else{
                $a = array('log_vencido'=>$logVencido,
                    'dt_hr_alteracao'=> 'systimestamp' ,
                    'cod_usuario_alteracao'=>$usuario,
                    'dt_final'=> $dtVencto);
                $cmd = convertArrayEmUpdate('controle_preco',$a,
                    "cod_controle_preco = $id",'1,2');
                sc_exec_sql($cmd,"especw");

            }


        }
    }
    return $erro;
}

function getListaRowidSldComOutlet()
{
    $listaRowid = '';
    $aReg = getDados('multi','med.pub."saldo-estoq" saldo ',
    'saldo.rowid as id',
    '"qtidade-atu" > 0 ',
    "multi",
    "INNER JOIN espec.pub.controle_preco preco
    on ((preco.num_nivel = 1 and preco.it_codigo = saldo.\"it-codigo\") or
    (preco.num_nivel = 2 and preco.it_codigo = saldo.\"it-codigo\" 
    and preco.cod_refer = saldo.\"cod-refer\")) 
    and dt_inicial <= sysdate and dt_final >= sysdate");
    foreach ($aReg as $reg) {
        $id = $reg['id'];
        $listaRowid = util_incr_valor($listaRowid,$id);
    }
    return $listaRowid;
}
function getListaItemRefSemOutlet()
{
    $listaItemRef = '';
    $sql = "select \"it-codigo\" as it_codigo, \"cod-refer\" cod_refer
FROM MED.pub.\"saldo-estoq\" saldo
minus select  it_codigo , cod_refer from pub.controle_preco preco
	where      dt_inicial <= sysdate and dt_final >= sysdate";
    $aReg =getRegsSqlLivre($sql,"it_codigo,cod_refer","multi");
    foreach ($aReg as $reg) {
        $itCodigo = $reg['it_codigo'];
        $codRef = $reg['cod_ref'];
        $chave = $itCodigo."_".$codRef;
        $listaItemRef = util_incr_valor($listaItemRef,"'$chave'");
    }
    return $listaItemRef;
}
function getPrecoInd($preco,$ind,$moeda)
{
    $precoInd = $preco * $ind;
    $precoInd = formatarPreco($moeda,$precoInd);
    return $precoInd;
}
function getPrecoPrazoInd($qtDias,$preco,$empresa='5')
{
    $vlInd = getIndFinancPrazo($qtDias,$empresa);
    $vlPrecoInd = $preco * $vlInd;
    return $vlPrecoInd;
}
function getPrecoPrazo($diasCondPagto,$precoVista,$preco30,$preco60,$preco90)
{
    $prazoMedio = getPrazoMedioInf($diasCondPagto);
    inserirLogDb('diasCondPagto -> Prazo Medio'," $diasCondPagto -> $prazoMedio",__FUNCTION__);
    //echo "<h1>prazo Medio:$prazoMedio</h1>";
    if($prazoMedio <=1){
        $vlUnit = $precoVista ;
    }
    if($prazoMedio <= 30 and $prazoMedio > 1) {
        $vlUnit = $preco30;
    }
    if($prazoMedio > 30 and $prazoMedio <= 60){
        $vlUnit = $preco60;
    }
    if($prazoMedio > 60){
        $vlUnit = $preco90;
    }
    $vlUnit = round($vlUnit,2);
    return $vlUnit;
}

function getPrecoSaldoItemRefCondPagtoContainer($item,$ref,$diasCondPagtoEsp=0,$container=0,$tbPreco=1)
{
    $vlUnit = 0;
    $aPrecos = getPrecosSaldoItemRef($item,$ref,$container,$tbPreco);
    //echo "<h1>getPrecoSaldoItemRefCondPagtoContainer -> array</h1>";
    inserirLogDb('array retorno getPrecosSaldoItemRef',$aPrecos,__FUNCTION__);

    //var_dump($aPrecos);
    $aPre             = $aPrecos['array'][0];
    $precoReal        = $aPre['preco_real'];
    $precoDolar       = $aPre['preco_dolar'];
    $preco90Real      = getPrecoPrazoInd(90,$precoReal);
    $preco90Dolar     = getPrecoPrazoInd(90,$precoDolar);
    $codControlePreco = $aPre['id_preco'];
    $percLiquida	  = $aPre['perc_liquida_ima'];
    $precoLiquidaIma  = $aPre['preco_liquida_ima'];
    $idLiquida	      = $aPre['id_liquida_ima'];
    $qtSaldo		  = $aPre['qt_saldo'];
    $logDivideComis   = $aPre['log_divide_comis'];
    $percComisVend    = $aPre['perc_comis_vend'];
    $percComisRepres  = $aPre['perc_comis_repres'];



    if($precoLiquidaIma > 0){
        inserirLogDb('Preco outlet maior que zero','sim',__FUNCTION__);
        $preco90Real = tratarNumero($preco90Real);
        if($preco90Real == 0){ //nao existe preço na tabela de preço padrao
            $fator = 1;
            inserirLogDb('Preço tabela maior que zero?','nao',__FUNCTION__);
        }else{
            $fator =  $precoLiquidaIma / $preco90Real;
            inserirLogDb('Preço tabela maior que zero?','sim',__FUNCTION__);
            inserirLogDb('Fator = precoliquidaima / preco90real',"$fator = $precoLiquidaIma / $preco90Real ",__FUNCTION__);
        }


    }else{
        inserirLogDb('Preco outlet maior que zero','nao',__FUNCTION__);
        $fator = 0; //fator zero para zerar o valor do precoLiquidaIma
    }
    //$vlUnit = getPrecoPrazo($diasCondPagtoEsp,$precoVista,$preco30,$preco60,$preco90);
    $vlUnitReal = getPrecoPrazoInd($diasCondPagtoEsp,$precoReal);
    $vlUnitDolar = getPrecoPrazoInd($diasCondPagtoEsp,$precoDolar);
    inserirLogDb('valor unit real',$vlUnitReal,__FUNCTION__);
    inserirLogDb('valor unit dolar',$vlUnitDolar,__FUNCTION__);

    if($vlUnitReal > 0){
        $precoLiquidaIma = $vlUnitReal * $fator;
        inserirLogDb('Valor unitario real maior que zero?','sim - será calculado o vl.outlet pelo fator',__FUNCTION__);
    }else{
        inserirLogDb('Valor unitario real maior que zero?','nao - vl. outlet zerado, pois, o preço de tabela está zerado',__FUNCTION__);
    }


    if($precoLiquidaIma <> 0 and $precoLiquidaIma <> ''){
        $vlUnitFinal = $precoLiquidaIma ;
    }else{
        $vlUnitFinal = $vlUnitReal;
    }


    //echo "<h1>vl.unit:$vlUnit   - preco liquida ima: $precoLiquidaIma -> $vlUnitFinal </h1>";
    $aRetorno = array('vl_unit_tabela'  => $vlUnitReal,
            'vl_unit_final'             => $vlUnitFinal,
            'cod_controle_preco'        => $codControlePreco,
            'num_id_liquida_ima'        => $idLiquida,
            'perc_liquida_ima'          => $percLiquida,
            'qt_saldo'                  => $qtSaldo,
            'preco_liquida_ima'         => $precoLiquidaIma,
            'vl_unit_dolar'             => $vlUnitDolar,
            'log_divide_comis'          => $logDivideComis,
            'perc_comis_vend'           => $percComisVend,
            'perc_comis_repres'         => $percComisRepres

    );
    inserirLogDb('array de retorno',$aRetorno,__FUNCTION__);
    return $aRetorno;
}
function getPrecoContainerPriCheg($item,$tbPreco=1,$ref='')
{
    //echo "<h1>local 1</h1>";
    $id= 0;
    $precoReal90 = 0;
    $precoDolar90 = 0;
    $precoFormatado = '';
    $lAchou = 0;
    $container = getContainersCheg($item,$ref);
    //var_dump($container);
    //echo "<h1>local 2</h1>";
    if(is_array($container)){
        $tam = count($container) ;
        for($i=0;$i<$tam;$i++){
            $nrContainer = $container[$i];

            $aPreco = buscarPreco('5',2,$item,$ref,$nrContainer,false,$tbPreco);
            if(is_array($aPreco) and $aPreco[0]['log_achou'] == 1){
                $id = $aPreco[0]['id'];
                $aInd   = buscarIndice();
                $precoReal90  = $aPreco[0]['vl_real']  * $aInd[3]['indice'] ;
                $precoDolar90 = $aPreco[0]['vl_dolar'] * $aInd[3]['indice'];
                $precoFormatado = '';
                if($precoDolar90 <> 0){
                    $precoFormatado = util_incr_valor($precoFormatado, formatarPreco('dolar',$precoDolar90) ,"");
                }
                if($precoReal90 <> 0){
                    $precoFormatado = util_incr_valor($precoFormatado,formatarPreco('real',$precoReal90),"<br>");
                }
                $lAchou = 1;
                break;
            }
        }
        return array('id'=>$id, 'preco_real_90'=>$precoReal90,'preco_dolar_90'=>$precoDolar90,
            'preco_formatado'=> $precoFormatado, 'log_achou'=>$lAchou);
    }




    if(is_array($aPreco)){


    }


    /*
     switch(strtolower($moeda)){
        case 'real':
            $simb = "R$";
            break;
        case 'dolar':
            $simb = "US$";
            break;

    }
    */


}



function getPercLiquidaIma($item,$ref='')
{
    $perc = 0;
    $id   = 0;
    if($ref<>''){
        $filtroRef = " AND promocao.\"cod-refer\" = '$ref'";
    }else{
        $filtroRef = '';
    }
    $lAchou = 0;
    $tabela   = " pub.\"liquida-ima\"  promocao";
    $campos   = " top 1  promocao.\"perc-descto\" as perc_descto ,\"num-id-liquida-ima\" as id ";
    $condicao = "  promocao.\"it-codigo\" = '$item' and promocao.\"dt-inicio\" <= curdate() 
    and (promocao.\"dt-final\" >= curdate() or promocao.\"dt-final\" is null) $filtroRef 
    and \"cod-estabel\" = '5'";
    $aRet = getDados('unico',$tabela,$campos,$condicao,"espec");
    if(is_array($aRet)){
        $perc = $aRet[0]['perc_descto'];
        $id = $aRet[0]['id'];
        $lAchou = 1;

    }
    $aRetorno = array('perc' =>$perc, 'id' =>$id ,"log_achou"=> $lAchou);
    return $aRetorno;
}

function getPrecoLiquidaIma($item,$ref='',$tbPreco=1,$logComSaldo=0)
{
    $logDivideComis  = 0;
    $percComisVend   = 0;
    $percComisRepres = 0;
    $lTbLiquidaIma = getPrecoTbLiquidaIma();
    $lAchou = 0;
    $lAchouCamp = 0;
    //echo "<h2>parametro liquidaIma: $lTbLiquidaIma</h2>";
    inserirLogDb('buscar outlet na tabela liquida_ima?',$lTbLiquidaIma,__FUNCTION__);

    $id   = 0;
    $preco = 0;
    if($lTbLiquidaIma == 1){
        inserirLogDb('entrei na busca pela tabela liquida-ima','sim',__FUNCTION__);
        $origem = 1; // tabela liquida-ima
        if($ref<>''){
            $filtroRef = " AND promocao.\"cod-refer\" = '$ref'";
        }else{
            $filtroRef = '';
        }
        $tabela   = " pub.\"liquida-ima\"  promocao";
        $campos   = " top 1  promocao.\"preco-item\" as preco_descto ,\"num-id-liquida-ima\" as id ";
        $condicao = "  promocao.\"it-codigo\" = '$item' and promocao.\"dt-inicio\" <= curdate() 
    and (promocao.\"dt-final\" >= curdate() or promocao.\"dt-final\" is null) $filtroRef 
    and \"cod-estabel\" = '5' and promocao.\"preco-item\" > 0 ";
        $aRet = getDados('unico',$tabela,$campos,$condicao,"espec");
        if(is_array($aRet)){
            inserirLogDb('encontrou preço outlet','sim',__FUNCTION__);
            $preco = $aRet[0]['preco_descto'];
            $id = $aRet[0]['id'];
            $logDivideComis  = 0;
            $percComisVend   = 0;
            $percComisRepres = 0;
            $lAchou          = 1;
        }else{
            inserirLogDb('encontrou preço outlet','nao',__FUNCTION__);
            $lAchou          = 0;
        }
    }else{
        inserirLogDb('entrei na busca pela tabela controle_preco','sim',__FUNCTION__);
        //echo "<h2>oi 1</h2>";
        $origem = 2; //tabela controle_preco
        //echo "<h2>oi 1</h2>";
        $aRet = getPrecoAtuOutlet($tbPreco,$item,$ref,$logComSaldo);

        //var_dump($aRet);
        $preco = $aRet['vl_real'];
        $id     = $aRet['id'];
        $campanhaId = $aRet['campanha_id'];

        if($id <> 0){
            $lAchou          = 1;
            inserirLogDb('achou preço outlet?','sim',__FUNCTION__);
        }else{
            inserirLogDb('achou preço outlet?','nao',__FUNCTION__);
            $lAchou          = 0;
        }

        if($campanhaId <> 0 ){
            inserirLogDb('campanha informada?','sim',__FUNCTION__);
            $aCamp = getRegCampanha($campanhaId);
            if(is_array($aCamp)){
                $aCamp = $aCamp[0];
                inserirLogDb('campanha encontrada?','sim',__FUNCTION__);
                $logDivideComis     = $aCamp['log_dividir_comis'];
                $percComisVend      = $aCamp['perc_vendedor'];
                $percComisRepres    = $aCamp['perc_repres'];
                $lAchouCamp = 1;
            }else{
                inserirLogDb('campanha encontrada?','nao',__FUNCTION__);
                $logDivideComis   = 0;
                $percComisVend    = 0;
                $percComisRepres  = 0;
                $lAchouCamp = 0;
            }
        }else{
            inserirLogDb('campanha informada?','nao',__FUNCTION__);
        }
    }
    //echo "<h2>oi 2</h2>";
    $aRetorno = array('preco_descto' => $preco, 'id' =>$id ,'origem'=>$origem,
            'log_divide_comis'       => $logDivideComis,
            'perc_comis_vend'        => $percComisVend,
            'perc_comis_repres'      => $percComisRepres,
            'log_achou' => $lAchou,
            'log_achou_camp'=>$lAchouCamp);
    inserirLogDb('array final de retorno',$aRetorno,__FUNCTION__);
    return $aRetorno;
}

function getPrecoTbERP($empresa,$itCod,$referencia='')
{
    $aEmpresa = getDadosEmpresa($empresa);
    $lAchou = 0;
    $nrTabPre = $aEmpresa['nr_tab_pre'];
    $conexao  = $aEmpresa['conexao'];
    if($referencia <> ''){
        $filtroRef = " and \"cod-refer\" = '$referencia'";
    }else{
        $filtroRef = '';
    }

    $aReg = getReg($conexao,'preco-item',
        '"nr-tabpre","it-codigo"',
        "'$nrTabPre','$itCod'",
        'top 1 "preco-venda" as preco',"\"dt-inival\" <= SYSDATE  $filtroRef "   );

    if(is_array($aReg)) {
        $vlReal     = $aReg[0]['preco'];
        $lAchou     = 1;
    }else{
        $vlReal     = 0;
    }
    $vlDolar    = 0;
    $id         = 0;
    return array('id'=> $id,'vl_real'=>$vlReal ,'vl_dolar'=> $vlDolar,"log_achou"=>$lAchou);
}

function setDescrPreco($preco90,$preco60,$preco30,$precoVista,$moeda)
{
    if(strtoupper($moeda) == 'DOLAR') {
        $sinalMoeda = "US$";
    }else {
        $sinalMoeda = "R$";
    }
    $descPreco = "90DD:".
        "$sinalMoeda ".formatarNumero($preco90)  ."- 60DD:".
        "$sinalMoeda ".formatarNumero($preco60)  ."- 30DD:".
        "$sinalMoeda ".formatarNumero($preco30) ."- A vista:".
        "$sinalMoeda ".formatarNumero($precoVista)  ;

    return $descPreco;
}

function buscarPreco($empresa,$tipoPreco,$itCod,$referencia='',$nrContainer=0,$logLiquidaIma=false,$tbPreco=1,$logComSaldo=0)
{
    /***********************************************************************************************
    Importante: O preço promocional tem prioridade com relação ao preço de pronta entrega,
    além de ter relevancia apenas para pronta entrega, não interferindo no preço PI.

     ************************************************************************************************/
    /*$nrContainerIni = 0;
    $nrContainerFim = 0;
    $idLiq = 0;
    $moeda = '';
    $precoLiquidaIma = 0;*/
    inserirLogDb('item - ref - container',"$itCod - $referencia - $nrContainer",__FUNCTION__);
    $aPreco             = array();
    $aRetorno           = array();
    $logDivideComis     = 0;
    $percComisVend      = 0;
    $percComisRepres    = 0;
    //$lAchou = false;
    if($nrContainer != '' || $nrContainer != 0)
    {
        $nrContainerIni = $nrContainer;
        $nrContainerFim = $nrContainer;
    }
    if($referencia <> ''){
        $filtroTipo1_2 = " and cod_refer = '$referencia' ";
        $filtroTipo4   = " and  \"cod-refer\" = '$referencia' ";

    }else{
        $filtroTipo1_2 = '';
        $filtroTipo4   = '';
    }
    inserirLogDb('Tipo de Preço',$tipoPreco,__FUNCTION__);
    switch($tipoPreco) //1 - Pronta Entrega 2- Programação de Importados 3- Promocional 4-Tabela de Preço Datasul(provisorio)
    {
        case '1':
            $aPreco = getPrecoAtuPE($tbPreco,$itCod,$referencia);

            break;
        case '2':
            $aPreco = getPrecoAtuPI($tbPreco,$nrContainer,$itCod,$referencia);
            break;
        case '4':
            $lBuscaPrecoERP = getParamPrecoTbERP();
            if($tbPreco == 2){
                $lBuscaPrecoERP = 0;
            }
            inserirLogDb('Buscar Preco ERP?',$lBuscaPrecoERP,__FUNCTION__);

            if($lBuscaPrecoERP == 1){
                $aPreco = getPrecoTbERP($empresa,$itCod,$referencia);
            }else{
                $aPreco = getPrecoAtuPE($tbPreco,$itCod,$referencia);
            }
            break;

    }
    //echo "<h1>array apreco depois case tipopreco</h1>";
    //var_dump($aPreco);
    if(is_array($aPreco) and $aPreco['log_achou'] == 1){
        //echo "<h1>eh array e com log achou = true</h1>";
        $vlReal     = $aPreco['vl_real'];
        $vlDolar    = $aPreco['vl_dolar'];
        $id         = $aPreco['id'];
        //busca o liquida ima
        $precoLiquidaIma = 0;
        if($logLiquidaIma == true ) {
            inserirLogDb('Busca Outlet?','sim',__FUNCTION__);
           // echo "<br>entrei no buscar liquida ima <br>";
            $aPrecoLiquidaIma = getPrecoLiquidaIma($itCod,$referencia,$tbPreco,$logComSaldo);
           //echo "<h1>array liquida ima</h1>";
            //var_dump($aPrecoLiquidaIma);
            $precoLiquidaIma  = $aPrecoLiquidaIma['preco_descto'];
            $idLiq            = $aPrecoLiquidaIma['id'];
            $oriLiqIma        = $aPrecoLiquidaIma['origem'];
            $logDivideComis   = $aPrecoLiquidaIma['log_divide_comis'];
            $percComisVend    = $aPrecoLiquidaIma['perc_comis_vend'];
            $percComisRepres  = $aPrecoLiquidaIma['perc_comis_repres'];
        }else{
            inserirLogDb('Busca Outlet?','nao',__FUNCTION__);
            //echo "<br>não entrei no buscar liquida ima <br>";
            $precoLiquidaIma  = 0;
            $idLiq            = 0;
            $oriLiqIma        = 0;
        }
        if($precoLiquidaIma <> 0){
            $precoFinal = $precoLiquidaIma;
        }else{
            $precoFinal = $vlReal ;
        }
        if($vlReal > 0){
            $fator = $precoLiquidaIma / $vlReal;
        }else{
            $fator = 0;
        }

        $aRetorno[] = array("vl_real"           => $vlReal,
                            "vl_dolar"          => $vlDolar,
                            "id"                => $id,
                            "fator_liq"         => $fator ,
                            "id_liq"            => $idLiq,
                            'preco_liquida_ima' => $precoLiquidaIma,
                            'preco_final'       => $precoFinal,
                            'origem_liquida_ima' => $oriLiqIma,
                            'log_divide_comis'  => $logDivideComis,
                            'perc_comis_vend'   => $percComisVend,
                            'perc_comis_repres' => $percComisRepres,
                            'log_achou'=>1

        );

    }
    else{
        $aRetorno[] = array(
            "vl_real"           => 0,
            "vl_dolar"          => 0,
            "id"                => 0,
            "fator_liq"         => 0,
            "id_liq"            => 0,
            "preco_liquida_ima" => 0,
            "preco_final"  => 0,
            'origem_liquida_ima'=>0,
            'log_achou'=>0);
        //$lAchou = false;
    }
    inserirLogDb('array retorno final buscarPreco',$aRetorno,__FUNCTION__);
    return $aRetorno;
}
function getLinkPreco($id,$preco)
{
    $link = "<a href='..\cons_preco_por_id\cons_preco_por_id.php?id_corrente=$id' target='_blank'>$preco</a>";
    return $link;
}
function getPrecosInd($vlUnit,$indiceBase){

    $aInd     =  buscarIndice();
    if($vlUnit > 0){
        if($indiceBase <> ''){
            $vlIndice = $aInd[$indiceBase]['indice'];
            $vlBase   =  round($vlUnit / $vlIndice,10);
            //echo "valor ind:".$vlIndice."<br> vl.base:".$vlUnit;
        }else{
            $vlBase = round($vlUnit,10);
        }

        $vlAvista = round($vlBase * $aInd[0]['indice'],2);
        $vl30     = round($vlBase * $aInd[1]['indice'],2);
        $vl60     = round($vlBase * $aInd[2]['indice'],2);
        $vl90     = round($vlBase * $aInd[3]['indice'],2);


        $aRetorno = array(

            'vl_a_vista'   => $vlAvista,
            'vl_30'        => $vl30,
            'vl_60'        => $vl60,
            'vl_90'        => $vl90,
            'vl_base'      => $vlBase
        );


    }else {
        $aRetorno = array(
            'vl_a_vista'   => 0,
            'vl_30'        => 0,
            'vl_60'        => 0,
            'vl_90'        => 0,
            'vl_base'      => 0
        );

    }
    return $aRetorno;
}

function inserirPreco($tabela,$tipoPreco,$dtIni,$dtFim,$nivel,$nrContainer,$itCodigo,$codRefer,$precoReal,
                      $precoDolar,$campanha=0,$logNivelInf=true)
{
    $login = getLoginCorrente();
    if($itCodigo <> '' or $codRefer <> ''){
        $codRefer = trim($codRefer);
        $tamRefer = strlen($codRefer);

        if($tamRefer < 3 and $codRefer <> ''){

            $codRefer = str_repeat('0',3 - $tamRefer).$codRefer;
        }
        if($codRefer == ''){
            $nivel = 1;
        }
        $a = array('cod_controle_preco'=>'pub.seq_controle_preco.NEXTVAL',
            'tp_preco'=>$tipoPreco,
            'dt_inicial'=>$dtIni,
            'dt_final'=>$dtFim,
            'it_codigo'=>$itCodigo,
            'cod_refer'=>$codRefer,
            'nr_container'=>$nrContainer,
            'vl_dolar' =>trim($precoDolar),
            'vl_real'=>trim($precoReal),
            'cod_usuario_criacao'=> getUsuarioERP($login),
            'dt_hr_criacao'=>'systimestamp',
            'tb_preco_id'=>trim($tabela),
            'num_nivel'=>$nivel,
            'campanha_id'=>$campanha
        );
        $cmd = convertArrayEmInsert('pub.controle_preco',$a,'1,7,11,12,13,14');

        sc_exec_sql($cmd,"especw");
        $id = buscarVlSequenciaEspec('seq_controle_preco','controle_preco');
        // $con = conectarBase('especw');
        //$ret = execAcaoPDO($cmd,$con,'cmd');
        //echo "<h1>$cmd</h1>";
        vencerPreco($dtFim,$tabela,$nivel,$tipoPreco,$nrContainer,$itCodigo,$codRefer,$logNivelInf,$id);


    }

}
function getPrecoPrior($itCodigo,$codRefer,$nrContainer,
                               $diasCondPagtoEsp,$moedaPrefer=1,$tbPrefer=1,
                               $ordemBusca=1,$codEstabel=5)

{

    $lAchou             = false;
    $precoBase          = 0;
    $moeda              = 0;
    $vlOutlet           = 0;
    $tabela             = 0;
    $precoPrazoFormat   = '';
    $precoOutletFormat  = '';
    $precoOutlet        = 0;
    $idPreco            = 0;
    $idLiq              = 0;
    $vlDolar            = 0;
    $vlReal             = 0;
    $logZerarVariaveis  = false;
    $lAchouRegistro     = false;


    $aCondPreco 		= getVarSessaoCondPreco();
    if($diasCondPagtoEsp == ''){
        $diasCondPagtoEsp 	= $aCondPreco['dias_cond_pagto'];
    }
    if($moedaPrefer == ''){
        $moedaPrefer 		= $aCondPreco['moeda'];
    }
    if($tbPrefer == ''){
        $tbPrefer 			= $aCondPreco['tb_prefer'];
    }
    if($ordemBusca == ''){
        $ordemBusca			= $aCondPreco['ordem_busca'];
    }
    inserirLogDb('Parametros Preferenciais -> DiasCondPagtoEsp - Moeda - tb.preço - ordem busca',
        "$diasCondPagtoEsp - $moedaPrefer - $tbPrefer - $ordemBusca",__FUNCTION__);

    if($nrContainer == 0){
        $tipoPreco = 4;
        $logBuscarLiquidaIma = true;
    }else{
        $tipoPreco = 2;
        $logBuscarLiquidaIma = false;
    }
    inserirLogDb('ITEM  - REF - CONTAINER',
        "$itCodigo - $codRefer - $nrContainer", __FUNCTION__);
    for($i=0;$i<2;$i++){ // duas tabelas de preço
        inserirLogDb('tabela - posicao'   ,"$tbPrefer - $i",__FUNCTION__);
        $aPreco = buscarPreco($codEstabel,$tipoPreco,$itCodigo,$codRefer,$nrContainer,$logBuscarLiquidaIma,$tbPrefer);
        inserirLogDb('array retorno buscarPreco a partir das preferencias selecionadas pelo usuário',
            $aPreco,__FUNCTION__);
        $lAchouRegistro = false;
        if(is_array($aPreco)) {
            $aPreco = $aPreco[0];
            if($aPreco["log_achou"] == 1){
                $lAchouRegistro = true;
                inserirLogDb('Achou Preço','sim',__FUNCTION__);
                $vlReal         = $aPreco['vl_real'];
                $vlDolar        = $aPreco['vl_dolar'];
                $vlOutlet       = $aPreco['preco_liquida_ima'];
                $idPreco        = $aPreco['id'];
                $origemOutlet   = $aPreco['origem_liquida_ima'];
                $idLiq          = $aPreco['id_liq'];
                 //echo "<h1>achou o preço - $vlReal - $vlDolar - $vlOutlet  </h1>";
                $tabela         = $tbPrefer;
                if($moedaPrefer == 1 and $vlReal > 0){
                    inserirLogDb('moeda preferencial igual a real e valor em real maior que zero',"sim - $vlReal",__FUNCTION__);
                    //echo "<h1>moeda prefer. real e real > 0</h1>";
                    $precoBase = $vlReal;
                    $moeda     = $moedaPrefer;
                    $lAchou    = true;
                    $precoOutlet = $vlOutlet;
                }else{
                    inserirLogDb('moeda preferencial igual a real e valor em real maior que zero','nao',__FUNCTION__);
                    if($vlReal > 0){
                        inserirLogDb('valor descartado - moeda real',$vlReal,__FUNCTION__);
                        $aVlDescartado[$i]['vl']            = $vlReal;
                        $aVlDescartado[$i]['moeda']        = 1;
                        $aVlDescartado[$i]['vl_outlet']     = $vlOutlet;
                    }
                    if($moedaPrefer == 2 and $vlDolar > 0){
                        inserirLogDb('moeda preferencial igual a dolar e valor em dolar maior que zero','sim',__FUNCTION__);
                        //echo "<h1>moeda prefer. dolar e dolar > 0</h1>";
                        $precoBase      = $vlDolar;
                        $moeda          = $moedaPrefer;
                        $lAchou         = true;
                        $precoOutlet    = 0;
                    }else{ //moeda preferencial não encontrada
                        inserirLogDb('moeda preferencial igual a dolar e valor em dolar maior que zero','nao',__FUNCTION__);
                        if($vlDolar > 0){
                            inserirLogDb('valor descartado - moeda dolar',$vlDolar,__FUNCTION__);
                            $aVlDescartado[$i]['vl']           = $vlDolar;
                            $aVlDescartado[$i]['moeda']        = 2;
                            $aVlDescartado[$i]['vl_outlet']    = 0;
                        }
                    }

                }
                if($ordemBusca == 2){ //tabela - preco
                    break;
                }
            }else{
                inserirLogDb('Achou Preço','nao',__FUNCTION__);
                //echo "<h1>log achou = false</h1>";
            }
            if($tbPrefer == 1){
                $tbPrefer +=1;
            }else{
                $tbPrefer -=1;
            }
        }
        if($lAchou or $lAchouRegistro){
            break;
        }
    }
    if($lAchou == false){
        if(is_array($aVlDescartado)){
            inserirLogDb('Existe Valor Descartado','sim',__FUNCTION__);

            inserirLogDb('Array vl descartado',$aVlDescartado,__FUNCTION__);
            foreach($aVlDescartado as $reg){

                inserirLogDb('Atribuiu o valor descartado?','sim',__FUNCTION__);
                $precoBase = $reg['vl'];
                $moeda     = $reg['moeda'];
                $lAchou    = true;
                $precoOutlet = $reg['vl_outlet'];
                break;

            }
            if($lAchou == false){
                inserirLogDb('Existe Valor Descartado - ponto 1','nao',__FUNCTION__);
                $logZerarVariaveis = true;
            }
        }else{
            inserirLogDb('Existe Valor Descartado - ponto 2','nao',__FUNCTION__);
            $logZerarVariaveis = true;
        }
    }
    if($logZerarVariaveis){
        $tabela     = 0;
        $moeda      = 0;
        $precoBase  = 0;
        $lAchou     = false;
        $precoOutlet = 0;
        $origemOutlet = 0;
        $idPreco = 0;
        $idLiq = 0;
    }
    inserirLogDb('diasCondPagtoEsp - precoBase - codEstabel',"$diasCondPagtoEsp - $precoBase - $codEstabel",__FUNCTION__);
    $precoPrazo = getPrecoPrazoInd($diasCondPagtoEsp,$precoBase,$codEstabel);
    inserirLogDb('precoPrazo ',$precoPrazo,__FUNCTION__);
    if($precoPrazo > 0){
        $precoPrazoFormat =  formatarPreco($moeda,$precoPrazo);
        inserirLogDb('Preço Prazo maior que zero ','sim',__FUNCTION__);
    }else{
        inserirLogDb('Preço Prazo maior que zero ','nao',__FUNCTION__);
    }
    inserirLogDb('Preço Prazo Formatado',$precoPrazoFormat,__FUNCTION__);
    if($precoOutlet > 0){
        inserirLogDb('Preço Outlet maior que zero',$precoOutlet,__FUNCTION__);
        if($origemOutlet == 1){ //preco outlet é 90 dias
            inserirLogDb('preço tabela liquida-ima?','sim',__FUNCTION__);
            $aIndice = buscarIndice();
            $ind90 = $aIndice[3]['indice'];
            $precoOutlet = $precoOutlet / $ind90;
            inserirLogDb("preço outlet após proporção indice 90 dias($ind90)",$precoOutlet,__FUNCTION__);

        }else{
            inserirLogDb('preço tabela liquida-ima?','nao',__FUNCTION__);
        }
        $precoOutlet = getPrecoPrazoInd($diasCondPagtoEsp,$precoOutlet,$codEstabel);

        inserirLogDb('preco outlet apos aplicacao de indice conforme prazo',$precoOutlet,__FUNCTION__);
        $precoOutletFormat = formatarPreco($moeda,$precoOutlet);
        if(is_array($aPreco)){
            $logDivideComis = $aPreco['log_divide_comis'];
            $percComisRep   = $aPreco['perc_comis_repres'];
            $percComisVend  = $aPreco['perc_comis_vend'];
        }else{
            $logDivideComis = 0;
            $percComisRep   = 0;
            $percComisVend  = 0;
        }
    }else{
        $logDivideComis = 0;
        $percComisRep   = 0;
        $percComisVend  = 0;
    }

    return array(   'log_achou'             =>$lAchou,
                    'preco_prazo'           =>$precoPrazo,
                    'moeda'                 =>$moeda,
                    'tabela'                =>$tabela,
                    'preco_outlet'          =>$precoOutlet,
                    'preco_prazo_formatado' =>$precoPrazoFormat,
                    'preco_outlet_formato'  =>$precoOutletFormat,
                    'log_divide_comis'      =>$logDivideComis,
                    'perc_comis_repres'     =>$percComisRep,
                    'perc_comis_vend'       =>$percComisVend,
                    'id_liq'                =>$idLiq,
                    'id'                    =>$idPreco,
                    'vl_dolar'              =>$vlDolar,
                    'vl_real'               =>$vlReal);

}
/*function getPrecosPrior($itCodigo,$codRefer,$nrContainer,
                        $diasCondPagtoEsp,$moedaPrefer=1,$tbPrefer=1,
                        $ordemBusca=1,$codEstabel=5)
{
    $aRet = getPrecoPrior($itCodigo,$codRefer,$nrContainer,$diasCondPagtoEsp,$moedaPrefer,$tbPrefer,$ordemBusca,$codEstabel);

}*/
function getQtPrecosCamp($campanhaId)
{
    $qt = 0;
    $aResult = getDados('unico',
        'pub.controle_preco','count(campanha_id) as qt',
    "campanha_id = $campanhaId",'espec');
    if(is_array($aResult)){
        $qt = $aResult[0]['qt'];
    }
    return $qt;
}
function atuDtPrecosCamp($campanhaId,$dtIni,$dtFim)
{
    $aCmd = array('dt_inicial'=>$dtIni,'dt_final'=>$dtFim);
    $cmd  =  convertArrayEmUpdate('pub.controle_preco',$aCmd,"campanha_id = $campanhaId");
    sc_exec_sql($cmd,"especw");


}
function getRegControlePrecoPorId($id)
{
    $aRet = getReg('espec','controle_preco','cod_controle_preco',
    $id);
    return $aRet;

}
function getFiltroLiquidaIma($tb,$estab=5)
{
    $listaItens = '';
    $lTbLiquidaIma = getPrecoTbLiquidaIma();
    if($lTbLiquidaIma){
        $tabela   = " pub.\"liquida-ima\"  promocao ";
        $campos   = " distinct \"it-codigo\" as item  ";
        $condicao = "  promocao.\"dt-inicio\" <= curdate() 
    and (promocao.\"dt-final\" >= curdate() or promocao.\"dt-final\" is null)  ";

    }
    else{
        $tabela = 'pub.controle_preco';
        $campos = ' distinct it_codigo as item';
        $condicao = "tb_preco_id = 1  and tp_preco = 3 
             and dt_inicial <= curdate() and dt_final >= curdate() 
             and log_vencido = 0 ";

    }
    $aRet = getDados('multi',$tabela,$campos,$condicao,'espec');
    /*echo "<pre>";
    var_dump($aRet);*/
    $listaItens = convArrayMultParaLista($aRet,'item',true);
    /*if(is_array($aRet)){
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $item  = $aRet[$i]['item'];

            $listaItens = util_incr_valor($listaItens,"'$item'");
        }
    }*/

    if($listaItens <> ''){
        $cond = " $tb.\"it-codigo\" in ($listaItens) ";
        //echo "<h1>cond=$cond</h1>";
    }else {
        $cond = '';
    }
    return $cond;
}
function getRefsItemComOutlet($itemParam,$codRefer,$codEstab=5)
{
    if($codRefer <> ''){
        $condRefer1 = " and  promocao.\"cod-refer\" = '$codRefer'";
        $condRefer2 = " and  cod_refer = '$codRefer'";
    }else{
        $condRefer1 = '';
        $condRefer2 = '';
    }
    $lTbLiquidaIma = getPrecoTbLiquidaIma();
    $lTodasRefs = false;
    if($lTbLiquidaIma) {
        $tabela   = " espec.pub.\"liquida-ima\"  promocao ";
        $campos   = " \"cod-refer\" as refer ";
        $condicao = "  promocao.\"it-codigo\" = '$itemParam'
        $condRefer1 
        and promocao.\"dt-inicio\" <= curdate()  and (promocao.\"dt-final\" >= curdate() 
        or promocao.\"dt-final\" is null ) and \"cod-estabel\" = '$codEstab'
        and promocao.\"preco-item\" > 0 ";
        $aRet = getDados('multi',$tabela,$campos,$condicao,'espec');
    }else{
        // caso voltem outros estabs é necessário adaptar para buscar a relação da tb.preco com o estab.
        $tabela = 'pub.controle_preco';
        $campos = ' cod_refer as refer';
        $condicao = "tb_preco_id = 1  and tp_preco = 3 
             and num_nivel = 1 and dt_inicial <= curdate() and dt_final >= curdate() 
             and log_vencido = 0 and it_codigo = '$itemParam'
             ";
        $aRet = getDados('multi',$tabela,$campos,$condicao,'espec');
        if(is_array($aRet)){
            $lTodasRefs = true;
        }else{
            $tabela = 'pub.controle_preco';
            $campos = ' cod_refer as refer';
            $condicao = "tb_preco_id = 1  and tp_preco = 3 
             and num_nivel = 2 and dt_inicial <= curdate() and dt_final >= curdate() 
             and log_vencido = 0 and it_codigo = '$itemParam' $condRefer2";

        }
        $aRet = getDados('multi',$tabela,$campos,$condicao,'espec');
    }
    $listaRefs = convArrayMultParaLista($aRet,'refer',true);
    return array('lista'=>$listaRefs,'log_todas_refers'=> $lTodasRefs);
}
