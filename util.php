<?php

/*function gerarLinkWeb($titulo,$hRef,$class=''): string
{
    return "<a href='$hRef'>$titulo</a>";
}*/
function exibirMsgErro($msg,$indTransformacao=0)
{
    //indTransformacao -> 0-nenhuma 1-encode utf8   2-decode utf8
    if($msg <> ''){
        switch ($indTransformacao){
            case 1:
                $msg = utf8_encode($msg);
                break;
            case 2:
                $msg = utf8_decode($msg);
                break;
        }
        sc_error_message($msg);
    }

}
function convNomeTabProgress($tb)
{
    $tbComTraco = strpos($tb,'-');
    $tbComAspas = strpos($tb, chr(34),1);
    //echo " <h1>tabela:$tb  tb. com traco $tbComTraco - tb.com aspas: $tbComAspas</h1>";
    if( $tbComTraco <> ''){
        if( $tbComAspas == ''){
            $tb = chr(34).$tb.chr(34);
        }
    }
    //echo "<h1>tabela:$tb</h1>";
    if(!strstr(strtolower($tb), "pub.") and !strstr(strtolower($tb), "sysprogress.")){
        //var_dump(strtolower($tb));
        $tb = "pub.{$tb}";
    }
    //echo "<h1>tabela depois:$tb</h1>";
    //echo " <h1>tb.final: $tb</h1>";
    return str_replace('pub.pub.','pub.',$tb);

}
function util_incr_valor($variavel,$incremento,$delimitador = ',',$logDesconsideraBranco=false)
{
    $retorno = $variavel;
    $logIncrementar = false;
    if($logDesconsideraBranco == true){
        if($incremento <> '' ){
            $logIncrementar = true;
        }
    }else{
        $logIncrementar = true;
    }
    if($logIncrementar == true){
        if($variavel == ''){
            $retorno = $incremento;
        }
        else{
            $retorno .= $delimitador.$incremento;
        }
    }

    return $retorno;
}
function retornarOpcoesTxt($campo)
{
	$aCampo = explode(',',$campo);
	$retorno = '';
	for($i = 0;$i < count($aCampo);$i++ )
	{
		if($retorno =='' )
		 $retorno = "'".$aCampo[$i]."'";
		else
		 $retorno .= ",'".$aCampo[$i]."'";

	}
	return $retorno;



}
function converterDecimal($valor)
{
	$valor = str_replace(".","",$valor);
    $valor = str_replace(",",".",$valor);
	return $valor;

}
function buscarVlSequenciaEspec($sequencia,$tabela){

	$valor = 0;
	$sql =" SELECT pub.$sequencia.CURRVAL from pub.\"_File\" WHERE \"_File-Name\" = '$tabela'";
	sc_lookup(seq,$sql,"espec");
	if ({seq} === false){
		echo "Erro de Acesso função buscarvlSequenciaEspec. Messagem=". {seq_erro} ;
	}
	elseif (empty({seq})){
 		//echo "Select command didn't return data";
        echo "<h1>To aqui</h1>";
	}
	else{
		$valor = {seq[0][0]};
	}
	return $valor;
}
function incrementarVlSequenciaEspec($sequencia,$tabela){

	$valor = 0;
	$sql =" SELECT pub.$sequencia.NEXTVAL from pub.\"_File\" WHERE \"_File-Name\" = '$tabela'";
	sc_lookup(seq,$sql,"especw");
	if ({seq} === false){
		echo "Erro de Acesso função buscarvlSequenciaEspec. Messagem=". {seq_erro} ;
	}
	elseif (empty({seq})){
 		//echo "Select command didn't return data";
	}
	else{
		$valor = {seq[0][0]};
	}
	return $valor;
}

function dataAtual()
{
    $dia= date('d');
    $mes= date('m');
    $ano= date('Y');
    $completa = "$dia/$mes/$ano";
    $completaUs = "{$ano}-{$mes}-{$dia}";
    return array('dia' => $dia, 'mes' => $mes, 'ano' => $ano, 'completa' => $completa,
        'completa_us'=> $completaUs);
}
function intervaloData($dtHrIni,$dtHrFim,$tipoRetorno = '')
{
    $retorno = 0;
    $oDataIni = new DateTime($dtHrIni);
    $oDataFim = new DateTime($dtHrFim);
    //var_dump($oDataIni);
    //var_dump($oDataFim);

    $oDif     	= $oDataIni->diff($oDataFim);
    $anoDif		= $oDif->format("%Y");
    $mesDif		= $oDif->format("%M");
    $diaDif		= $oDif->format("%D");
    $horaDif	= $oDif->format("%H");
    $minutoDif	= $oDif->format("%I");
    $segundoDif	= $oDif->format("%S");

    $aRetorno = array('ano' => $anoDif, 'mes' => $mesDif, 'dia' => $diaDif,
        'hora' => $horaDif , 'minuto' => $minutoDif, 'segundo' => $segundoDif	);
    switch($tipoRetorno){
        case 'dia':
            $retorno = $oDif->days;
            break;
        case 'hora':
            $retorno = ($oDif->days * 24) + $horaDif + ($minutoDif / 60);
            break;
        case 'minuto':
            $retorno = ($oDif->days * 24 * 60) + ($horaDif * 60) + $minutoDif;
            break;
        default:
            $retorno = $aRetorno;
    }


    return $retorno;
}
function preencherZerosAEsquerda($num,$tamanho=0)
{
    $tamanhoNumero = strlen($num);

    $qtRepeticoes = $tamanho - $tamanhoNumero;
    if($qtRepeticoes < 0){
        $qtRepeticoes = 0;
    }
    $zeros = str_repeat('0',$qtRepeticoes);
    $numero = $zeros.$num;
    return $numero;

}
function retirarAcento($palavra)
{

	//return preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//IGNORE', $palavra ) );
	return $palavra;
}
function retirarAcentoSimples($texto)
{
    return preg_replace(
        array("/(á|à|ã|â|ä)/",
        "/(Á|À|Ã|Â|Ä)/",
            "/(é|è|ê|ë)/",
            "/(É|È|Ê|Ë)/",
            "/(í|ì|î|ï)/",
            "/(Í|Ì|Î|Ï)/",
            "/(ó|ò|õ|ô|ö)/",
            "/(Ó|Ò|Õ|Ô|Ö)/",
            "/(ú|ù|û|ü)/",
            "/(Ú|Ù|Û|Ü)/",
            "/(ñ)/",
            "/(Ñ)/",
            "/(ç)/",
            "/(Ç)/"),
        explode(" ","a A e E i I o O u U n N c C"),$texto
    );

}
function formatarNumero($numero,$qtDecimais=2)
{
    $numero = number_format($numero,$qtDecimais,',','.');
    return $numero;
}
function tratarNumero($num)
{
	if(trim($num) == ''){
		$num = 0;
	}
	return $num;

}
function getVlLogico($valor)
{
    return $valor == 1 ? 'Sim' : 'Não';

}
function formatarPreco($moeda,$vl)
{
    $valor  = round($vl,2);
	if(strstr($valor,'.') ==false){
		$valor = "$valor.00";
	}
    if($moeda =='real' or $moeda == '1'){
        $sinal = "R$";
    }else{
        $sinal = "US$";
    }
    if($valor  <> '' and $valor <> 0){

		$valor = number_format($valor,2,',','.');
	}
       /* $aValor = explode('.',$valor);
        if( strlen($aValor[1]) == 1 ){
            $aValor[1] .= '0';
        }
        $valor = $aValor[0].",".$aValor[1];
    }else{
        $valor = "0,00";
    }*/

    $valor = "$sinal $valor";

	if($vl == 0){
		$valor = '';
	}
    return $valor;

}
function desformatarValor($valor)
{
    $valor = str_replace("R$",'',$valor);
    $valor = str_replace("US$",'',$valor);
    $valor = str_replace(".",'',$valor);
    $valor = str_replace(",",'.',$valor);
    return $valor;

}
function validaCNPJ($cnpj = null)
{

	// Verifica se um número foi informado
	if(empty($cnpj)) {
		return false;
	}

	// Elimina possivel mascara
	$cnpj = preg_replace("/[^0-9]/", "", $cnpj);
	$cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);

	// Verifica se o numero de digitos informados é igual a 11
	if (strlen($cnpj) != 14) {
		return false;
	}

	// Verifica se nenhuma das sequências invalidas abaixo
	// foi digitada. Caso afirmativo, retorna falso
	else if ($cnpj == '00000000000000' ||
		$cnpj == '11111111111111' ||
		$cnpj == '22222222222222' ||
		$cnpj == '33333333333333' ||
		$cnpj == '44444444444444' ||
		$cnpj == '55555555555555' ||
		$cnpj == '66666666666666' ||
		$cnpj == '77777777777777' ||
		$cnpj == '88888888888888' ||
		$cnpj == '99999999999999') {
		return false;

	 // Calcula os digitos verificadores para verificar se o
	 // CPF é válido
	 } else {

		$j = 5;
		$k = 6;
		$soma1 = "";
		$soma2 = "";

		for ($i = 0; $i < 13; $i++) {

			$j = $j == 1 ? 9 : $j;
			$k = $k == 1 ? 9 : $k;

			$soma2 += ($cnpj[$i] * $k);

			if ($i < 12) {
				$soma1 += ($cnpj[$i]* $j);
			}

			$k--;
			$j--;

		}

		$digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
		$digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

		return (($cnpj[12] == $digito1) and ($cnpj[13] == $digito2));

	}
}
function mascara($val, $mask)
{

 $maskared = '';

 $k = 0;

 for($i = 0; $i<=strlen($mask)-1; $i++) {
     if($mask[$i] == '#') {

        if(isset($val[$k])) $maskared .= $val[$k++];
    }
    else{
        if(isset($mask[$i]))  $maskared .= $mask[$i];

    }
 }
 return $maskared;

}
function atualizarPg()
{
    sc_redir($this->Ini->nm_cod_apl);
}
function tratarAspasSimples($texto)
{
    $texto = str_replace("'","''",$texto);
    return $texto;

}
function convArrayParaLista($array,$logEntreAspas=false)
{
    $lista = '';
    $tot = count($array);
    for($i=0;$i<$tot;$i++){
        if($logEntreAspas == true){
            $incr = "'{$array[$i]}'";
        }else{
            $incr = $array[$i];
        }
        $lista = util_incr_valor($lista,$incr,",",true);
    }
    return $lista;
}
function convArrayMultParaLista($array,$indice,$logEntreAspas=false)
{
    $lista = '';
    if(is_array($array)){
        foreach($array as $reg){
            if($logEntreAspas == true){
                $incr = "'{$reg[$indice]}'";
            }else{
                $incr = $reg[$indice];
            }
            $lista = util_incr_valor($lista,$incr,",",true);
        }
    }

    return $lista;
}
function setAplCorrente(){
    [apl_corrente] = $this->Ini->nm_cod_apl;
}
function getQtLista($lista,$separador=',')
{
    $qt = 0;
    if($lista <> ''){
        $aLista = explode($separador,$lista);
        if(is_array($aLista)){
            $qt = count($aLista);
        }
    }

    return $qt;
}
function unique_multidim_array($array, $key) {

    //var_dump($array);
    $temp_array = array();
    $i = 0;
    $key_array = array();
    if(is_array($array)){
        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
    }


    return $temp_array;
}
function getDadosEmpresa($empresa)
{
    switch($empresa)
    {
        case '1':
            $nrTabPre = 'TAB A12';
            $conexao = "ima";
            break;
        case '5':
            $nrTabPre = 'TAB E12';
            $conexao = "med";
            break;
        default:
            $nrTabPre = '';
            $conexao = '';
            break;

    }
    return array('nr_tab_pre'=>$nrTabPre,'conexao'=>$conexao);
}

function setVarSessao($variavel,$valor,$incremento=0,$logEscopoApl=false,$varPai='')
{
    if($logEscopoApl){
        $variavel = getNomeVarSessaoComApl($variavel);
    }
    if($varPai <> ''){
        if($incremento == 0 or ! isset($_SESSION[$varPai][$variavel]) ){
            $_SESSION[$varPai][$variavel] = $valor;
        }else{
            $_SESSION[$varPai][$variavel] =  util_incr_valor($_SESSION[$varPai][$variavel],$valor);
        }
    }else{
        if($incremento == 0 or ! isset($_SESSION[$variavel]) ){
            $_SESSION[$variavel] = $valor;
        }else{
            $_SESSION[$variavel] =  util_incr_valor($_SESSION[$variavel],$valor);
        }
    }
}
function getNomeVarSessaoComApl($var)
{
    $apl = getAplCorrente();
    $variavel = $apl."_".$var;
    return $variavel;
}
function getVarSessao($varParam,$logEscopoApl=false)
{
    if($logEscopoApl){
        $varParam = getNomeVarSessaoComApl($varParam);
    }

    if(isset($_SESSION[$varParam])){
        $retParam =  $_SESSION[$varParam];
    }else{
        $retParam = '';
    }
    return $retParam;
}
function limparVarSessao($variavel,$logEscopoApl=false)
{
    if($logEscopoApl){
        $varParam = getNomeVarSessaoComApl($variavel);
    }

    if($variavel <> ''){
        $aVar = explode(',',$variavel);
        foreach ($aVar as $var) {
            unset($_SESSION[$var]);
        }
    }

}
function getNumEmLista($lista,$numero)
{
    $lAchou = false;
    if($lista <> ''){
        $aLista = explode(',',$lista);
        foreach ($aLista as $numLista){
            if($numLista == $numero){
                $lAchou = true;
                break;
            }
        }
    }

    return $lAchou;
}
function convArrayEmMsg($aMsg,$tagHeader='h1')
{
    $msg = '';
    if(is_array($aMsg)){
        $tam = count($aMsg);
        for($i=0;$i<$tam;$i++){
            $incr = $aMsg[$i];
            if($tagHeader <> ''){
                $tagHeaderIni = "<$tagHeader>";
                $tagHeaderFim = "</$tagHeader>";

            }
            $msg = util_incr_valor($msg,"{$tagHeaderIni}{$incr}{$tagHeaderFim}");
        }
    }
    return $msg;
}
function gerarListaPartes($campo, $lista,$ciclo=400)
{
    $iContCiclo = 0;
    $condSqlFinal = '';
    $listaCorrente = '';
    $aLista = explode(",",$lista);
    if(is_array($aLista)){
        $tam = count($aLista);
        if($tam > $ciclo){
            inserirLogDb("menor que o clico de $ciclo?",'nao',__FUNCTION__);
            for($i=0;$i<$tam;$i++){

                $iContCiclo++;
                if($iContCiclo == $ciclo){
                    inserirLogDb("entrei na condicao de clico igual ao contador",'sim',__FUNCTION__);
                    $iContCiclo = 0;
                    $condSql = "$campo in ($listaCorrente)";
                    inserirLogDb('condsql',$condSql,__FUNCTION__);
                    $condSqlFinal = util_incr_valor($condSqlFinal,$condSql," AND ",
                        true);
                    inserirLogDb('condsqlfinal',$condSqlFinal,__FUNCTION__);
                    $listaCorrente = '';
                }
                $listaCorrente = util_incr_valor($listaCorrente,$aLista[$i],',',true);
                inserirLogDb("lista - posicao $i",$listaCorrente,__FUNCTION__);
                if($tam == $i + 1 and $iContCiclo < $ciclo){
                    inserirLogDb("lista - posicao $i - menor que ciclo",$listaCorrente,__FUNCTION__);
                    $condSql = "$campo in ($listaCorrente)";
                    $condSqlFinal = util_incr_valor($condSqlFinal,$condSql," AND ",
                        true);
                }
            }
        }else{
            inserirLogDb("menor que o clico de $ciclo?",'sim',__FUNCTION__);
            $condSqlFinal = " $campo in ($lista)";
        }

    }
    return $condSqlFinal;

}
function convArrayHtml($array,$elementoChave,$elementoValor)
{
    /*
     *  <h1>pedido</h1><h1>1236548</h1>
     * $a = array('Pedido: {pedido_resumo} = convArrayHtml($a,'span style="font-weigth:900; text-align;right;" ', 'span');
     */

    $props   ='';
    $propsVl ='';

    $html = '';
    if(is_array($array) and $elementoChave <> '' and $elementoValor <> ''){
        foreach($array as $chave =>$reg){
            $aElementoChave = explode(' ',$elementoChave);
            $tagChave = $aElementoChave[0];
            for($i=1;$i < count($aElementoChave);$i++){
                $props = util_incr_valor($props,$aElementoChave[$i],' ');
            }

            $aElementoValor = explode(' ',$elementoValor);
            $tagValor = $aElementoValor[0];
            for($i=1;$i < count($aElementoValor);$i++){
                $propsVl = util_incr_valor($props,$aElementoValor[$i],' ');
            }

            $html .= "<{$tagChave} $props>{$chave}</{$tagChave}>
            <{$tagValor} $propsVl >{$reg}</{$tagValor}></br>";
        }
    }
    return $html;
}
function getMsgAnaliseDif($descricao,$descOrigem,$descDestino,$s1,$s2): string
{
    $retorno = '';
    if($s1 <> $s2){
        $retorno = "$descricao $descOrigem $s1 <> $descDestino $s2";
    }
    return $retorno;

}
function getAplCorrente()
{
    return $this->Ini->nm_cod_apl;
}
function convNomeVar($nomeVar,$sufixo='')
{
    $novaVar = '';
    $logExplode = false;
    $aNomeVar = array();
    $nomeVar = str_replace(chr(34),'',$nomeVar);
    if(strstr($nomeVar,'_')){
        $aNomeVar = explode('_',$nomeVar);
        $logExplode = true;
    }
    if(strstr($nomeVar,'-')){
        $aNomeVar = explode('-',$nomeVar);
        $logExplode = true;
    }
    if($logExplode == false){
        $aNomeVar = explode(' ',$nomeVar);
    }

    if(is_array($aNomeVar)){
        $tam = count($aNomeVar);
        for($i=0;$i<$tam;$i++){
            switch($i){
                case 0:
                    $incr = $aNomeVar[$i];
                    break;
                default:
                    $incr = ucfirst($aNomeVar[$i]) ;
            }
            $novaVar .=$incr;
        }
    }
    if($sufixo <> ''){
        $sufixo = ucfirst($sufixo);
        $novaVar = util_incr_valor($novaVar,$sufixo,'');
    }
    return $novaVar;

}
function removerIndicesArray($array,$listaIndices)
{
    $aInd = explode(',',$listaIndices);
    if(is_array($aInd)){
        $tam = count($aInd);
        for($i=0;$i<$tam;$i++){
            unset($array[$aInd[$i]]);
        }
    }
    return $array;
}

function aplicarSubstArray($cmd,$listaCaracterDivisao,$listaCaracterBusca,$listaCaracterSubst)
{
    $novoComando = '';
    $aListaDivisao = explode(',', $listaCaracterDivisao);
    $aListaBusca = explode(',', $listaCaracterBusca);
    $aListaSubst = explode(',', $listaCaracterSubst);
    $qtNiveis = count($aListaDivisao);

    for ($a = 0; $a < $qtNiveis; $a++) {
        if ($a == 0) {
            $array = explode($aListaDivisao[$a], $cmd);
        } else {
            $array = subdividirArray($array, $aListaDivisao[$a]);
        }
    }
}



function subdividirArray($array,$caracterDivisor)
{
    $tam = count($array);
    for($i=0;$i<$tam;$i++){
        if($caracterDivisor <> '*'){
            $aParte = explode($caracterDivisor,$array[$i]);
            $array[$i] = $aParte;
        }
    }
    return $array;

}
function iniciarApl($gerarLinkManual=false)
{
    $aplCorrente = $this->Ini->nm_cod_apl;
    if($gerarLinkManual){
        [manual_corrente] =  "  <a href='../bl_manual_dinamico/bl_manual_dinamico.php?codigo=$aplCorrente' target='_blank'  >Manual<a/>";
    }


}

function getArrayBody($listaNiveis)
{
    while($lFim or $qtWhile == 100){
        $qtWhile++;
        foreach( $obj as $chave =>$valor ){

            //$retorno = $valor->ttRetorno;
            //var_dump($retorno);
            //$lRetorno = false;

            if($valor == 'items'){
                $obj = $obj->$valor;
            }
            if($valor == 'ttRetorno'){
                $obj = $obj->$valor;
                $lRetorno = true;
                continue;
            }
            if($lRetorno){
                $tam = count($obj);
                $qt++;
                $aRet[] = array('codigo'=> $valor->codigo,'descricao'=>$valor->descricao ) ;
                if($qt == $tam -1){
                    $lFim = false;
                }
           }

        }
    }
}
function getValsArrayMultiDin($arrayParam, $chavesParam)
{
    $agora = time();
    $usuario = getUsuarioCorrente();
    $escopo = "{$agora}_{$usuario}";
    $varSessao = "resultado_$escopo";
    setVarSessao($varSessao,array());
    $chaveSessao = 'chave_ref_'.$escopo;
    $aChavesParam = explode(',',$chavesParam);
    setVarSessao($chaveSessao,0);
    $ret2 = getValsArrayMultiDinAux($arrayParam,$chavesParam,$varSessao,$chaveSessao);
    $retorno = getVarSessao($varSessao);
    limparVarSessao($varSessao);
    limparVarSessao($chaveSessao);
    return $retorno;
}

function getValsArrayMultiDinAux($arrayParam, $chavesParam,$varSessaoParam,$chaveSessao)
{
    //var_dump($arrayParam);
    $aRet = array();
    $retNivel = array();
    $lRetornar = false;
    $iCont = 0;
    //$aRetFinal = array();
    $aChave = explode(',',$chavesParam);
    foreach($arrayParam as $chave => $valor ){
        //echo "<h1>chave: $chave</h1>";a
        $lRetornar = false;
        if(is_array($valor) or is_object($valor)){
            $retNivel = getValsArrayMultiDinAux($valor,$chavesParam,$varSessaoParam,$chaveSessao);
            $lRetornar = true;
            //$qtNivel = count($retNivel);
            //echo "<h1>array $chave </h1>";
            //var_dump($retNivel);
            if(isset($retNivel['log_achou']) and $retNivel['log_achou'] == true){
                //echo "<h1>entrei no nível que achou</h1>";
                $aRet[] = $retNivel;
                //var_dump($aRet);

            }else{
                //echo "<h1>INICIO - NAO entrei no nível que achou</h1>";
                //var_dump($retNivel);
                //echo "<h1>FIM - NAO entrei no nível que achou</h1>";
            }

            //echo "<h1>array $chave</h1>";
            //var_dump($valor);
        }else{
            //echo "<h1>valor: $valor</h1>";
            $lAchou = getNumEmLista($chavesParam,$chave);
            if($lAchou){
                $aRet[$chave] = $valor;
                $result = getVarSessao($varSessaoParam);
                $qtResult = count($result);

                $idObjAnt = getVarSessao($chaveSessao);
                if(spl_object_id($arrayParam) <> $idObjAnt){
                    $qtResult++;
                }
                setVarSessao($chaveSessao,spl_object_id($arrayParam));
                $result[$qtResult][$chave] =$valor;
                //var_dump($result);
                setVarSessao($varSessaoParam,$result);

            }
            else{
                //echo "<h1>nao achou chaves:$chavesParam, Chave: $chave</h1>";
            }
            $aRet['log_achou'] = $lAchou;
        }
    }
    if($lRetornar == true){
        $aRet = $retNivel;
   }
   return $aRet;
}
function getInputPhpToArq($origem)
{
    $client_data = file_get_contents("php://input");
    //criamos o arquivo
    $num = idate("U");
    $arquivo = fopen("/tmp/{$origem}_{$num}.txt",'w');
//verificamos se foi criado
    if ($arquivo == false) die('Não foi possível criar o arquivo.');
//escrevemos no arquivo
//$texto = "lá mundo";

    fwrite($arquivo, $client_data);
//Fechamos o arquivo após escrever nele
    fclose($arquivo);

}
function getCondAnoMesPer($data,$per){

    $qt = $per - 1;
    $inicio = sc_date($data, "aaaa-mm-dd", "-", 0, $qt, 0);
    $dtInicio =  substr($inicio,0,8). "01";
    $anoIni = substr($dtInicio,0,4);
    $mesIni = substr($dtInicio,5,2);
    //echo "<h1>ano ini:$anoIni</h1>";
    $cond = '';
    $anoCorrente = $anoIni;
    $mesCorrente = $mesIni - 1;
    for($i=0;$i<$per;$i++)
    {

        $mesCorrente  += 1;
        if($mesCorrente > 12){

            $anoCorrente += 1;
            $mesCorrente = $mesCorrente % 12;

        }
        $cond = util_incr_valor($cond,"(ano = $anoCorrente and mes= $mesCorrente)"," OR ");
    }
    $cond = " ($cond) ";
    return $cond;
}

function getUltDiasMesAnoCorrente($logMesPosterior=1)
{
    $union = '';
    if($logMesPosterior == 1){
        $union = "union
            select distinct dt_referencia as ult
            from pub.repres_sit_cli r1  where dt_referencia    in
            (	select max(dt_referencia) from pub.repres_sit_cli r2
            where dt_referencia >=
            to_date(to_char(month(curdate())) + '/01/' + to_char(year(curdate()))))";
    }
    $sql = "select distinct to_date (to_char(month(dt_referencia)) + '/01/' + to_char(year(dt_referencia))) -1 as ult
  from pub.repres_sit_cli r    
   where 
   dt_referencia >= to_date('02/01/' + to_char(year(curdate())   )   ) 
   $union
   ";
    $aRegs = getRegsSqlLivre($sql,'ult','espec');
    $lista = convArrayMultParaLista($aRegs,'ult');
    $aLista = explode(',',$lista);
    $lista = convArrayParaLista($aLista,true);
    return $lista;

}

function getUltDiasMesAnoCorrente2()
{
    $sql = "select distinct to_date (to_char(month(dt_referencia)) + '/01/' + to_char(year(dt_referencia))) -1 as ult
  from pub.repres_sit_cli r    
   where 
   dt_referencia >= to_date('02/01/' + to_char(year(curdate())   )   ) 
   ";
    $aRegs = getRegsSqlLivre($sql,'ult','espec');
    //var_dump($aRegs);
    $lista = convArrayMultParaLista($aRegs,'ult');
    $aLista = explode(',',$lista);
    $lista = convArrayParaLista($aLista,true);
    return $lista;

}
function inserirAspasEmLista($lista)
{
    $listaRet = '';
    if($lista <> ''){
        if(strpos($lista,"'") === false){
            $aLista = explode(',',$lista);
            $listaRet = convArrayParaLista($aLista,true);
        }
    }
    
    return $listaRet;
}
function retirarPubTab($tabela)
{
    if( strstr( strtolower($tabela),'pub.') <> false){
        $tabela = substr($tabela,4,strlen($tabela) - 4);
    }
    return $tabela;
}

function compararExtensaoArq($fileName,$extensao)
{
    $extArq = getExtensaoArquivo($fileName);
    return strtolower($extensao) == strtolower($extArq) ;
}
function getExtensaoArquivo($fileName)
{
    $extensao = '';
    $aFileName = explode('.',$fileName);
    $tam= count($aFileName);
    if($tam > 0){
        $extensao = $aFileName[$tam - 1];

    }
    return $extensao;
}
function gravarConteudoArquivo($nomeArquivo,$conteudo)
{
   return file_put_contents($nomeArquivo,$conteudo);

}
function execOperMat(string $operacao, int $primeiroTermo, int $segundoTermo=0)
{
    $resultado = 0;
    switch ($operacao){
        case 'soma':
            $resultado = tratarNumero($primeiroTermo) + tratarNumero($segundoTermo);
            break;
        case 'subtracao':
            $resultado= tratarNumero($primeiroTermo) - tratarNumero($segundoTermo);
            break;
        case 'multiplicao':
            $resultado = tratarNumero($primeiroTermo) * tratarNumero($segundoTermo);
            break;
        case 'divisao':
            if(tratarNumero($segundoTermo) <> 0){
                $resultado = tratarNumero($primeiroTermo) / tratarNumero($segundoTermo);
            }
            break;
        case 'elevacao':
            $resultado = pow(tratarNumero($primeiroTermo),tratarNumero($segundoTermo));
            break;
        case 'modulacao':
            if(tratarNumero($segundoTermo) <> 0){
                $resultado = tratarNumero($primeiroTermo) % tratarNumero($segundoTermo);
            }
            break;
        case 'raiz_quadrada':
             $resultado = sqrt($primeiroTermo);
            break;

    }
    return $resultado;

}
function getVlUltPosicaoArray($array)
{
    $ultPosicao = '';
    $qt = count($array);
    if($qt > 0){
        $qt -=1;
        $ultPosicao = $array[$qt];
    }
    return $ultPosicao;
}
function compararVlUltPosicaoArquivo($array,$vlComparar): bool
{
    $ultPos = getVlUltPosicaoArray($array);
    return strtolower($ultPos) == $vlComparar;
}
function getDispositivo()
{
    $dispositivo = '';
    $detect = new Mobile_Detect();
    if($detect->isMobile()){
        if(!$detect->isTablet()){
            $dispositivo = 'tablet';
        }
        $dispositivo = 'celular';        
    }else{
        $dispositivo = 'computador';
    }
    return $dispositivo ;

}

function verificMobile()
{
    $logMobile = false;
    $detect = new Mobile_Detect();
    if($detect->isMobile()){        
        $logMobile = true;        
    }
    return $logMobile ;
}

function getAplRedir($apelidoAplic)
{
    $logMobile = verificMobile();
    
    switch($apelidoAplic){
        case 'cons_emitente':
            if($logMobile){
               $aplicacao = "cons_emitente_mobile" ;
            }else{
               $aplicacao = "cons_emitente"; 
            }
            break;
        default:
        if($logMobile){
            $aplicacao = $apelidoAplic."_mobile";
        }else{
            $aplicacao = $apelidoAplic;
        }
        
    }
    return $aplicacao;
}
function convVlComTraco($valor)
{
    if(stristr($valor, '-') <> false){
        $valor = "\"$valor\"";
    }
    return $valor;
}
function convArrayToCondSql($array)
{
    /*
    chaves do array: tabela( ou apelido),campo,valor,operador,logSemAspas,oper_cond

    */
    $vlFinal = '';
    $condicao = '';

    //var_dump($array);
    foreach($array as $chave=>$valor){
        $tabela      = $valor['tabela'];
        $campo		 = $valor['campo'];
        $campo       = convVlComTraco($campo);
        $vl 		 = $valor['valor'];
        $operador	 = $valor['operador'];
        $logSemAspas = $valor['log_sem_aspas'];
        $operCond	 = $valor['oper_cond'];
        if($chave == 0){
            $juncao = " where ";
        }else{
            $juncao =  $operCond;
        }

        //$vlLog = getVlLogico($logSemAspas);
        if($logSemAspas){
            $vlFinal = $vl;
        }else{
            $vlFinal = "'$vl'";
        }
        if($tabela <> ''){
            $tabela = convVlComTraco($tabela);
            $campo = "{$tabela}.{$campo}";
        }
        $condicao = util_incr_valor($condicao,"$campo $operador $vlFinal",$juncao);

    }
    return $condicao;
}
function convArrayMultiToCondSql($array)
{
    /******************************************************************************************************
    *  chaves do array: condicao,operadorCond,ordem
     * ainda não implementada a ordem
     *******************************************************************************************************/
    //ordenar array pelo campo ordem

    $vlFinal   = '';
    $condicao  = '';
    $textoCond = '';
    foreach($array as $chave=>$valor){
        $condicao     = $valor['condicao'];
        $operadorCond = $valor['operador'];
        $ordem        = $valor['ordem'];
        if(is_array($condicao)){
            $tamanho    = count($condicao);
            foreach($condicao as $cond){
                echo "<h1>é array</h1>";
                $textoCond  = util_incr_valor($textoCond,convArrayToCondSql($cond))  ;
                echo "<h1>após conversão: $textoCond</h1>";
            }
            if($tamanho > 1){
                $textoCond = "({$textoCond})";
            }
            echo "<h2>$textoCond</h2>";
        }else{
            $textoCond = $condicao;
        }


        $condicao = util_incr_valor($condicao,$textoCond,$operadorCond);
    }
    return $condicao;
}
function convertArrayEmUpdate($tabela,$aDados,$condicao,$cpsSemAspas='',$logPub=true)
{
    //echo "cheguei até aqui";
    if(! strstr(strtolower($tabela),'pub.') and $logPub == true){
        $tabela = "pub.{$tabela}";
        //echo "<h3>tabela SEM pub</h3>";
    }else{
        //echo "<h3>tabela com pub</h3>";
    }
    if(is_array($aDados)){
        $aCampos = array_keys($aDados);
        //$campos    = '';
        $valores    = '' ;

        if(is_array($aCampos)){
            $tam = count($aCampos);
            //echo $tam;
            for($i=0;$i<$tam;$i++){
                if(getNumEmLista($cpsSemAspas, $i + 1)){
                    $incr = $aDados[$aCampos[$i]];
                }else{
                    $incr = "'".$aDados[$aCampos[$i]]."'";
                }
                $incr = $aCampos[$i]." = ".$incr;
                $valores = util_incr_valor($valores,$incr)  ;

                //$vlCampos = util_incr_valor($vlCampos,"'".$aDados[$aCampos[$i]]."'")  ;

            }
        }
    }else{
        $valores = $aDados;
    }

    $cmd = "update {$tabela} set $valores where $condicao ";
    return $cmd;
}
function convertArrayEmInsert($tabela,$aDados,$cpsSemAspas='')
{
    //echo "<h1>oi</h1>";
    $aCampos = array_keys($aDados);
    $campos    = '';
    $vlCampos = '' ;
    if(is_array($aCampos)){
        $tam = count($aCampos);
        //echo $tam;
        for($i=0;$i<$tam;$i++){
            $campos  = util_incr_valor($campos,$aCampos[$i]);
            //echo "<h1>$cpsSemAspas -> $i </h1>///////";
            if(getNumEmLista($cpsSemAspas,$i + 1)  == true){
                $incr = $aDados[$aCampos[$i]];
            }else{
                $incr = "'".$aDados[$aCampos[$i]]."'";
            }
            //echo "<h1>campo:$incr</h1>";
            $vlCampos = util_incr_valor($vlCampos,$incr)  ;

            //$vlCampos = util_incr_valor($vlCampos,"'".$aDados[$aCampos[$i]]."'")  ;

        }
    }
    $cmd = "insert into {$tabela}({$campos})values({$vlCampos})";
    return $cmd;
}
function setCondWhere($cond)
{
    if($cond <> ''){
        if (empty({sc_where_atual})){
            sc_select_where(add) = "where $cond ";
        }else{
            sc_select_where(add) = "AND $cond ";
        }
    }
}
function convertCpParaAPI($campos)
{
    $campos = str_replace(chr(34),'',$campos);

    return $campos;
}
function deixarNumero($string){
    return preg_replace("/[^0-9]/", "", $string);
}
function criarLinksTelefone($telefones,$separador)
{
    $retorno = '';
    $telsComNumeros = deixarNumero($telefones);
    if($telsComNumeros <> ''){
        $aTels = explode($separador,$telefones);
        foreach($aTels as $tel){
            if($tel <> ''){
                $telPuro = deixarNumero($tel);
                $retorno = util_incr_valor($retorno,"<a href='tel:{$telPuro}'>{$tel}</a>",' | ');    
                if(strlen($telPuro) > 10 or (strlen($telPuro) == 10 and substr($telPuro,2,1) == '9')  ){
                    if(stristr(substr($telPuro,0,3),'55') == false ){
                        $telWz = "+55{$telPuro}" ;
                    }else{
                        $telWz = $telPuro;        
                    }
                    $retorno = util_incr_valor($retorno," <a href='https://wa.me/{$telWz}' target='_blank'><i class='bi bi-whatsapp'></i></a>",'');       
                }
            
            }        
        }
    } else{
        $retorno ="Não Informado";
    }   
    return $retorno;
}
function criarLinks($aLinksParam,$sepResult,$classe='')
{
    $retorno = '';    
    foreach($aLinksParam as $chave=>$vl){
        if($vl['href']<> ''){
            $hRef = $vl['href'];
            $descricao = $vl['descricao'];
            $parte = "<a href='$hRef' ";
            if($classe <> '') {
                $parte = util_incr_valor($parte,"class='$classe'",' ');
            }   
            $parte = util_incr_valor($parte,">$descricao</a>",'');
            $retorno = util_incr_valor($retorno,$parte,$sepResult);

        }        
    }     
     return $retorno;

}
function inserirArrayCond($aFiltro,$tabela,$campo,$valor,$operador='=',$logSemAspas=false,$logAceitaBranco=false,$operCond=' and ')
{

    if($valor <> '' or ($valor == '' and $logAceitaBranco)){
        $valor = setMascaraPorOper($operador,$valor);
        $operador = convOperador($operador);
        $aFiltro[] = array('tabela'=> $tabela,
            'campo'=> $campo,
            'valor'=> $valor,
            'operador'=> $operador,
            'log_sem_aspas' => $logSemAspas,
            'oper_cond'=> $operCond);
    }
    return $aFiltro;

}

function inserirArrayCondMultiNivel($aFiltroMulti,$filtro,$operador=' and ',$ordem=0)
{
    //operador-> and, or
    $aFiltroMulti[] = array('condicao'=>$filtro,'operador'=>$operador,'ordem'=>$ordem);
    return $aFiltroMulti;


}
function convOperador($operador):string
{
    switch($operador){
            case 'not_in':
                $retorno = "not in";
                break;
            default:
                $retorno = $operador;
    }
    return $retorno;
}


function setMascaraPorOper($operador,$valor)
{
    $retorno = '';
    $mascara = getMascaraVlSQL($operador);
    if($mascara == ''){
        $retorno = $valor;
    }else{
        $retorno = str_replace('#valor#',$valor,$mascara) ;
    }
    return $retorno;

}
function getMascaraVlSQL($tipo)
{
    $mascara = '';

    switch(strtolower(trim($tipo))){ //minusculo e sem espaço
        case 'like':
            $mascara = '%#valor#%';    
            break;
        case 'in':
            $mascara = '(#valor#)';
            break;    
        case 'not_in':
            $mascara = '(#valor#)';
            break;        
    }
    return $mascara;

}
function getVlIndiceArray($array,$indice,$vlPadrao)
{
   
    if(is_array($array)){
        foreach ($array as $item) {
            $logSair = false;
            if(isset($item[$indice])){
                $retorno = $item[$indice];
                $logSair = true;
            }else{
                $retorno = $vlPadrao;
            }
            if($logSair){
                break;
            }            
        }
    }else{
        $retorno = $vlPadrao;
    }
    return $retorno;

}
function getVlIndiceArrayDireto($array,$indice,$vlPadrao)
{
    if(isset($item[$indice])){
        $retorno = $item[$indice];
    }else{
        $retorno = $vlPadrao;
    }
    return $retorno;

}

function getCssComum()
{

    $css= <<<CSS
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedidos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <style>
        .lb{font-weight: bold; text-align:right;}        
        .faturado{color:blue;font-size: 18px;}
        .aberto{color:green;font-size: 18px;}
        .cancelado{color:darkred;font-size: 18px;}
		.naoavaliado{color:orange;font-size: 13px;}
        .aprovado{color:green;font-size: 13px;}
        .reprovado{color:darkred;font-size: 13px;}
        .okNomeCli{color:blue;font-size: 13px;}
        .nokNomeCli{color:red;font-size: 13px;}
        .medioNomeCli{color:orange;font-size: 13px;}
        .vl{color:#2c3034;font-size: 15px;font-weight: bolder}
		.endereco{font-size:12px;}
		.telefone{font-size:12px;}
		.email{font-size:12px;}
		.nome{font-size:13px;}
		.links{font-size:14px;}
		
    </style>

CSS;    

    return $css;
}
?>