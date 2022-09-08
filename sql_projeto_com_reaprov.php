<?php
//__NM____NM__NFUNCTION__NM__//

function retornoSimplesTb01_reaprov($tabela, $campos, $condicao, $conexao = '',
                            $inner = '',$logUTF8=0,$logSql=0)
{
    $aRetorno = array();
    $lAchou = false;
    $txtArray = '';
    $txtComum = '';
    $tipoConexao = 'progress';
    $aCampos = explode(',', $campos);
    /*IMPORTANTE: sempre que existirem virgulas dentro de um campo, este campo deve substituir as virgulas por pape(|)*/
    $campos = str_replace("|", ",", $campos);
    $sql = "select $campos from $tabela $inner
		  where $condicao";
    if($logSql == 1){
        echo "<h1>$sql</h1>";
    }
    $aResult = getDadosBd($conexao,'lookup',$sql);
    $resultado = $aResult['resultado'];
    $erro = $aResult['erro'];
    if ($resultado   === false){
      echo "Erro de acesso retornoSimplesTB01. CONEXAO $conexao - Mensagem = $erro";
    }elseif (empty($resultado)){
      //echo "Comando select não retornou dados ";
    }else{
      for ($i = 0; $i < count($aCampos); $i++) {
          $aApelido = explode(' as ', $aCampos[$i]);
          if (count($aApelido) > 1) {
              $nomeCampo = ltrim(rtrim($aApelido[1]));
          } else {
              $nomeCampo = ltrim(rtrim($aApelido[0]));
          }

          if($logUTF8 == 1){
             $cp = utf8_encode($resultado[0][$i]);
          }
          else{
              $cp = $resultado[0][$i];
          }
          $aRetorno[0][$nomeCampo] = $cp;
		  $lAchou = true;

	  }
  }
   //var_dump($aRetorno);
   if ($lAchou == false) {
       $aRetorno = '';
   }
   return $aRetorno;
}

function getTipoConexao($conexao)
{
    switch ($conexao) {
        case 'ems5':
            $tipoConexao = 'progress';
            break;
        case 'espec':
            $tipoConexao = 'progress';
            break;
        case 'comum':
            $tipoConexao = 'progress';
            break;
        case 'ima':
            $tipoConexao = 'progress';
            break;
        case 'med':
            $tipoConexao = 'progress';
            break;
        case 'multi':
            $tipoConexao = 'progress';
            break;
        case 'cfsc':
            $tipoConexao = 'sql';
            break;
        case 'log_db':
            $tipoConexao = 'sql';
            break;
        case 'integracoes':
            $tipoConexao = 'sql';
            break;
        case 'geral':
            $tipoConexao = 'sql';
            break;
        case 'dinamico':
            $tipoConexao = 'sql';
            break;
        default:
            $tipoConexao = 'progress';
    }
    return $tipoConexao;

}
function getDadosBd($conexao,$tipo,$sql)
{
    switch ($conexao) {
        case 'ems5':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "ems5");}
            else{  sc_select(result, $sql, "ems5");}
            break;
        case 'espec':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "espec");}
            else{  sc_select(result, $sql, "espec");}
            break;
        case 'comum':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "comum");}
            else{  sc_select(result, $sql, "comum");}
            //echo 'entrei no ima';
            break;
        case 'ima':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "ima");}
            else{  sc_select(result, $sql, "ima");}
            //echo 'entrei no ima';
            break;

        case 'med':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "med");}
            else{  sc_select(result, $sql, "med");}
            break;
        case 'multi':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "multi");}
            else{  sc_select(result, $sql, "multi");}
            break;
        case 'cfsc':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "cfsc");}
            else{  sc_select(result, $sql, "cfsc");}
            break;
        case 'log_db':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "log_db");}
            else{  sc_select(result, $sql, "log_db");}
            break;
        case 'integracoes':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "integracoes");}
            else{  sc_select(result, $sql, "integracoes");}
            break;
        case 'dinamico':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "dinamico");}
            else{  sc_select(result, $sql, "dinamico");}
            break;
        case 'geral':
            if($tipo == 'lookup') {sc_lookup(result, $sql, "geral");}
            else{  sc_select(result, $sql, "geral");}
        default:
            if($tipo == 'lookup') {sc_lookup(result, $sql);}
            else{  sc_select(result, $sql);}

    }
    /*if(isset({result})){
        $resultado = {result};
    }else{
        $resultado = false;
    }*/
    echo "result<br>";
    var_dump({result});
    echo "<br>";
    if(isset({result_erro})){
        $erro = {result_erro};
    }else{
        $erro = '';
    }
}
    $aResult = array('resultado'=>{result},'erro'=> $erro);
    return $aResult ;
}
function retornoMultReg($tabela, $campos, $condicao, $conexao = '', $inner = '',$logUTF8=0,$logSql=0)
{
    $iCont = 0;
    $aRetorno = array();
    $lAchou = false;
    $txtArray = '';
    $txtComum = '';
    $tipoConexao = 'progress';
    $aCampos = explode(',', $campos);
    /*IMPORTANTE: sempre que existirem virgulas dentro de um campo, este campo deve substituir as virgulas por pape(|)*/
    $campos = str_replace("|", ",", $campos);
    $sql = "select $campos from $tabela $inner
		  where $condicao ";
    if($logSql == 1){
        echo "<h1>$sql</h1>";
    }
    $aResult = getDadosBd($conexao,'select',$sql);
    $resultado = $aResult['resultado'];
    $erro = $aResult['erro'];
    if ($resultado  === false){
        echo "Erro de acesso. CONEXAO $conexao - Mensagem = $erro" ;
    }else{
        while (!$resultado->EOF) {
            for ($i = 0; $i < count($aCampos); $i++) {
                $aApelido = explode(' as ', $aCampos[$i]);
                if (count($aApelido) > 1) {
                    $nomeCampo = ltrim(rtrim($aApelido[1]));
                } else {
                    $nomeCampo = ltrim(rtrim($aApelido[0]));
                }
                if($logUTF8 == 1){
                    $cp = utf8_encode($resultado->fields[$i]);
                }
                else{
                    $cp =$resultado->fields[$i];
                }
                $aRetorno[$iCont][$nomeCampo] = $cp ;
                $lAchou = true;
            }
            $iCont++;
            $resultado->MoveNext();
        }
        $resultado->Close();
    }
    if ($lAchou == false) {
         $aRetorno = '';
    }
	 return $aRetorno;
}

function retornarChaveEstr($apelidoConj, $numRetorno)
{
    $tabelas = '';
    $camposJoin = '';
    $camposRet = '';
    $aRetorno = '';
    $retorno = '';
    // buscar tabelas envolvidas
    $aDadosTB = retornoSimplesTb01('tabs_chave_estr t , conj_cp_chave_estr c',
        't.tabela_origem,t.tabela_estrangeira,c.cod_conj_cp_chave_estr',
        'c.apelido = $apelidoConj and c.cod_tab_chave_estr = t.cod_tab_chave_estr');

    if (is_array($aDadosTB)) {
        $tabelas = $aDadosTB[0]['t.tabela_origem'] . "," . $aDadosTB[0]['t.tabela_estranjeira'];


        // buscar campos que farão parte do join entre as tabelas
        $aCamposJoin = retornoSimplesTb01('cps_cj_chave_estr c',
            'c.campo_origem,c.campo_estranjeiro',
            'c.cod_conj_cp_chave_estr =' . $aDadosTB[0]['c.cod_conj_cp_chave_estr']);
        if (is_array($aCamposJoin)) {
            for ($i = 0; $i < count($aCamposJoin); $i++) {
                $comum = $aCamposJoin[$i]['c.campo_origem'] . "=" . $aCamposJoin[$i]['c.campo_estrangeiro'];
                if ($camposJoin == '') $camposJoin = $comum;
                else $camposJoin .= "," . $comum;
            }
        }
        //buscar campos de Retorno
        $aCamposRet = retornoSimplesTb01('rets_cj_cp_chave_estr r, cps_ret_cj_cp_chave_estr c',
                'c.campo',
                'r.cod_conj_cp_chave_estr =' . $aDadosTB[0]['c.cod_conj_cp_chave_estr']) .
            " and r.num_retorno = $numRetorno ";
        if (is_array($aCamposRet)) {
            for ($i = 0; $i < count($aCamposRet); $i++) {
                $comum = $aCamposRet[$i]['c.campo'];
                if ($camposRet == '') $camposRet = $comum;
                else $camposRet .= "," . $comum;
            }
        }
        $aRetorno = retornoSimplesTb01($tabelas, $camposRet, $camposJoin);
        $aLstCamposJoin = explode(",", $camposJoin);
        if (is_array($aRetorno)) {
            for ($i = 0; $i < count($aLstCamposJoin); $i++) {
                $comum = $aRetorno[0][$aLstCamposJoin[$i]];
                if ($retorno == '') $retorno = $comum;
                else $retorno .= "-" . $comum;
            }
        }
    }
    return $retorno;
}

function getCpsTb($catalogo, $tb, $logNomeTabela = false, $logMulti = true)
{
    $aRetorno = '';
    $listaCampos = '';
    $listaTipoCp = '';
    $listaExtentCp = '';

    $tabela = "sysprogress.syscolumns_full  ";
    if ($logMulti == true) {
        $tabela = "$catalogo.$tabela";
        $conexao = "multi";
    } else {
        $conexao = $catalogo;
    }
    $campos = " coltype as tipo_dado, 
                  width as tamanho, 
                  array_extent as extensao, 
                  col as campo,
                  scale as escala,
                  nullflag as log_nulo,
                  format as formato,
                  label  as label,
                  display_order as ordem,
                  owner as esquema,
                  valexp as valexp";
    $condicao = " tbl = '$tb'";
    $aResult = retornoMultReg($tabela, $campos, $condicao, $conexao);
    if (is_array($aResult)) {
        $tam = count($aResult);
        for ($i = 0; $i < $tam; $i++) {
            $campo = $aResult[$i]['campo'];
            $campoSemAspas = $campo;
            if (stristr($campo, '-') <> false and stristr($campo, '"') == false) {
                $campo = "\"$campo\"";
            }
            $tipo = $aResult[$i]['tipo_dado'];
            $extensao = $aResult[$i]['extensao'];
            $esquema = $aResult[$i]['esquema'];
            $tab = '';
            $tabApelido = '';
            if ($logNomeTabela == true) {
                $tabApelido = str_replace('-', '_', $tb);
                if ($logMulti == true) {
                    $tab = "$catalogo.$esquema.$tb.";

                } else {
                    $tab = "$esquema.$tb.";
                }
                $campo = "$tab.$campo";
            }
            if ($extensao > 0) {
                for ($x = 1; $x <= $extensao; $x++) {
                    if ($tabApelido <> '') {
                        //$campo = $tab.$campo;
                        $campoIncr = "pro_element($campo,$x,$x) as {$tabApelido}_{$campoSemAspas}_{$x}";
                    } else {
                        $campoIncr = "pro_element($tab$campo,$x,$x) as {$campoSemAspas}_{$x}";
                    }
                    //echo "<h1>iteração:$x  - campo corrente:$campo</h1>";
                    $listaCampos = util_incr_valor($listaCampos, $campoIncr);
                    $listaTipoCp = util_incr_valor($listaTipoCp, $tipo);
                    $listaExtentCp = util_incr_valor($listaExtentCp, $extensao);
                }


            } else {
                $listaCampos = util_incr_valor($listaCampos, $campo);
                $listaTipoCp = util_incr_valor($listaTipoCp, $tipo);
                $listaExtentCp = util_incr_valor($listaExtentCp, $extensao);
            }

        }
        $aRetorno = array('campos' => $listaCampos, 'tipos' => $listaTipoCp,
            'extensoes' => $listaExtentCp);
    }
    return $aRetorno;
}
function getCpsTbSQLSERVER($conexao, $tb, $logNomeTabela = false)
{
    $aRetorno = '';
    $listaCampos = '';
    $listaTipoCp = '';
    $listaTamanho = '';
    //$listaExtentCp = '';

    $tabela = " sys.columns c ";
    $campos = "    c.name as campo ,
                   t.Name as tipo_dado ,
                   c.max_length as tamanho ";

    $condicao = " c.object_id = OBJECT_ID('$tb')";
    $aResult = retornoMultReg($tabela, $campos, $condicao, $conexao,'INNER JOIN 
               sys.types t ON c.user_type_id = t.user_type_id LEFT OUTER JOIN 
               sys.index_columns ic ON ic.object_id = c.object_id AND ic.column_id = c.column_id');
    if (is_array($aResult)) {
        $tam = count($aResult);
        for ($i = 0; $i < $tam; $i++) {
            $campo = $aResult[$i]['campo'];
            $campoSemAspas = $campo;
            if (stristr($campo, '-') <> false and stristr($campo, '"') == false) {
                $campo = "\"$campo\"";
            }
            $tipo = $aResult[$i]['tipo_dado'];
            $tamanho = $aResult[$i]['tamanho'];
            //$extensao = $aResult[$i]['extensao'];
            //$esquema = $aResult[$i]['esquema'];
            $tab = '';
            $tabApelido = '';
            if ($logNomeTabela == true) {
                $tabApelido = str_replace('-', '_', $tb);
                if ($logMulti == true) {
                    $tab = "$catalogo.$esquema.$tb.";

                } else {
                    $tab = "$esquema.$tb.";
                }
                $campo = "$tab.$campo";
            }
             $listaCampos = util_incr_valor($listaCampos, $campo);
             $listaTipoCp = util_incr_valor($listaTipoCp, $tipo);
             $listaTamanho = util_incr_valor($listaTamanho, $tamanho);
        }
        $aRetorno = array('campos' => $listaCampos, 'tipos' => $listaTipoCp,
            'extensoes' => '','tamanhos'=> $listaTamanho);
    }
    return $aRetorno;
}

function getCpsTbSessao($catalogo, $tabela)
{
    $var = "cps_" . $catalogo . "_" . $tabela;
    if (!isset($_SESSION[$var]) or $_SESSION[$var] == '') {
        setCpsTbSessao($catalogo, $tabela);
    }
    return $_SESSION[$var];

}


function setCpsTbSessao($catalogo, $tabela)
{
    $var = "cps_" . $catalogo . "_" . $tabela;
    $tipoConexao = getTipoConexao($catalogo);
    switch ($tipoConexao){
        case 'progress':
            $lista = getCpsTb($catalogo, $tabela);
            break;
        case 'sql':
            $lista = getCpsTbSQLSERVER($catalogo, $tabela);
            break;
    }

    $_SESSION[$var] = $lista;

}

function getDados2($tipo, $tabela, $campos, $condicao, $conexao = '', $inner = '',$logUTF8=0,$logSql=0)
{
    switch ($tipo) {
        case 'unico':
            $aRet = retornoSimplesTb01($tabela, $campos, $condicao, $conexao, $inner,$logUTF8,$logSql);
            break;
        case 'multi':
            $aRet = retornoMultReg($tabela, $campos, $condicao, $conexao, $inner,$logUTF8,$logSql);
            break;
    }
    return $aRet;
}

function getUltIdTabela($tabela, $tipoBanco = 'sql', $banco = "log_db")
{
    switch ($tipoBanco) {
        case 'sql':
            $sql = "SELECT IDENT_CURRENT ('$tabela') AS Current_Identity";
            break;
    }
    switch ($banco) {
        case 'log_db':
            sc_lookup(id, $sql, "log_db");
            break;
        default:
            sc_lookup(id, $sql);
    }

    if ({id}  === false)
    {
        echo "Erro de acesso. Mensagem = " . {id_erro};
        $id = 0;
    }
    elseif (empty({id}))
    {
        $id = 0;
    }
    else
    {
        $id = {id[0][0]};
    }
    return $id;
}


function getReg2($conexao, $tabela, $camposChave, $valoresCamposChave, $campos = '', $filtroCompl = '',$logUTF8=0,$logSql=0)
{
    $condicao = '';
    $tipoConexao = getTipoConexao($conexao);
    if (strstr($camposChave, ",") <> false) {
        $camposChave = explode(',', $camposChave);
    }
    if (strstr($valoresCamposChave, ",") <> false) {
        $valoresCamposChave = explode(',', $valoresCamposChave);
    }
    if (is_array($camposChave)) {
        $tam = count($camposChave);
        for ($i = 0; $i < $tam; $i++) {
            $incr = $camposChave[$i] . "=" . $valoresCamposChave[$i];

            $condicao = util_incr_valor($condicao, $incr, " AND ");
        }
        if ($condicao == '') {
            $condicao = '1 = 1 ';
        }
    } else {
        if ($camposChave <> '' and $valoresCamposChave <> '') {
            $condicao = " $camposChave = $valoresCamposChave ";
        } else {
            $condicao = "1 = 1 ";
        }
    }

    if ($campos == '' ) {
        $aCampos = getCpsTbSessao($conexao, $tabela);
        $campos = $aCampos['campos'];
    }

    if ($filtroCompl <> '') {
        $condicao = util_incr_valor($condicao, $filtroCompl, ' AND ', true);
    }

    if (strstr($tabela, "-") <> false and strstr($tabela, '"') == false) {
        $tabela = "\"$tabela\"";
    }
    //echo "tipo conexao: $tipoConexao<br/>";
    if ($tipoConexao == 'progress') {
        $tabela = "pub.$tabela";
    }

    $tipo = "unico"; // unico ou multi
    //$conexao  = "espec";
    $aRet = getDados($tipo, $tabela, $campos, $condicao, $conexao,'',$logUTF8,$logSql);

    return $aRet;
}

function getRegsSqlLivre2($sql,$campos,$conexao,$logUTF8=0,$logSql=0)
{
    if($logSql == 1){
        echo "<h1>$sql</h1>";
    }
    $aCampos = explode(',', $campos);
    $iCont = 0;
    $aRetorno = array();
    $lAchou = false;
    $aResult = getDadosBd($conexao,'select',$sql);
    $resultado = $aResult['resultado'];
    $erro = $aResult['erro'];


    if ($resultado  === false){
        echo "Erro de acesso. CONEXAO $conexao - Mensagem = $erro" ;
    }else{
        while (!$resultado->EOF) {
            for ($i = 0; $i < count($aCampos); $i++) {
                $aApelido = explode(' as ', $aCampos[$i]);
                if (count($aApelido) > 1) {
                    $nomeCampo = ltrim(rtrim($aApelido[1]));
                } else {
                    $nomeCampo = ltrim(rtrim($aApelido[0]));
                }
                if($logUTF8 == 1){
                    $cp = utf8_encode($resultado->fields[$i]);
                }
                else{
                    $cp =$resultado->fields[$i];
                }
                $aRetorno[$iCont][$nomeCampo] = $cp ;
                $lAchou = true;
            }
            $iCont++;
            $resultado->MoveNext();
        }
        $resultado->Close();
    }
    if ($lAchou == false) {
        $aRetorno = '';
    }
	return $aRetorno;
}
function convertArrayEmInsert2($tabela,$aDados,$cpsSemAspas='')
{
    $aCampos = array_keys($aDados);
    $campos    = '';
    $vlCampos = '' ;
    if(is_array($aCampos)){
        $tam = count($aCampos);
        //echo $tam;
        for($i=0;$i<$tam;$i++){
            $campos  = util_incr_valor($campos,$aCampos[$i]);
            //echo "<h1>$cpsSemAspas -> $i </h1>";


            if(strstr($cpsSemAspas,strval($i + 1) ) <> false){
                $incr = $aDados[$aCampos[$i]];
            }else{
                $incr = "'".$aDados[$aCampos[$i]]."'";
            }
            $vlCampos = util_incr_valor($vlCampos,$incr)  ;

            //$vlCampos = util_incr_valor($vlCampos,"'".$aDados[$aCampos[$i]]."'")  ;

        }
    }
    $cmd = "insert into {$tabela}({$campos})values({$vlCampos})";
    return $cmd;
}
function convertArrayEmUpdate2($tabela,$aDados,$condicao,$cpsSemAspas='')
{
    //echo "cheguei até aqui";
    if(! strstr(strtolower($tabela),'pub.')){
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
                if(strstr($cpsSemAspas,strval($i + 1) ) <> false){
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
function getODBCDireto($conexaoSC)
{
    $base = getBase();
    $base = strtolower($base);
    switch ($conexaoSC){
        case 'especw':
            $retorno = $base =='producao'? 'espec_w_pro':'espec_w_tst';
            break;
        case 'medw':
            $retorno = $base =='producao'? 'med_w_pro':'med_w_tst';
            break;
        case 'med':
            $retorno = $base =='producao'? 'med_pro':'med_tst';
            break;
        case 'espec':
            $retorno = $base =='producao'? 'espec_pro':'espec_tst';
            break;
        default:
            $retorno = $conexaoSC;
    }
    return $retorno;

}

function updateDireto($conexaoSC,$tabela,$aDados,$condicao,$cpsSemAspas='')
{
    $usuario = '';
    $aCon =conectarBase($conexaoSC);
    $cmd = convertArrayEmUpdate($tabela,$aDados,$condicao,$cpsSemAspas);
    //echo "<h2>comando convertido:$cmd</h2>";
    $ret = execAcaoPDO($cmd,$aCon,'cmd');
    $erro = $ret['erro'];
    $lock = strstr($erro,'failure getting record lock')? 1 : 0;
    if($lock == 1){
        $usuario = getUsuarioLock($tabela,$conexaoSC);
        /*$msgLock = "A ação solicitada não pode ser realizada, pois, o usuário(a)
          $usuario está utilizando o registro neste momento";*/
    }
    return array('resultado'=> $ret,'erro'=>$erro,'lock'=>$lock,'usuario_lock'=>$usuario);
}

/*function updateDiretoTratarLock($cmd,$conexaoSC)
{
    $erro = updateDireto($cmd,$conexaoSC);
    if(strstr($erro,'failure getting record lock')){
        for($i=0;$i < 10;$i++){
           $erro = updateDireto($cmd,$conexaoSC);
           echo "<h1>Tentativa numero:$i</h1>";
           if($erro == ''){
               break;
           }
        }
    }else{
        echo "nao entrei no failure";
    }
    return $erro;

}*/
function execAcaoPDO2($cmd,$conexaoBase,$acao='')
{

    $aCon = $conexaoBase;
    if($aCon['erro']==''){
        $con = $aCon['conexao'];
        switch ($acao){
            case 'cmd':
                $resultado = $con->exec($cmd);
                break;
            default:
                $resultado = $con->query($cmd);
        }
        $erro = $con->errorInfo();
        $erro = strtolower($erro[2]) ;
    }else{
        $erro = $aCon['erro'];
        $resultado = '';
    }
    return array('resultado'=>$resultado,'erro'=>$erro);

}
function getUsuarioLock($tabela,$conexaoSC)
{
    $tabela = str_replace('"',"",$tabela);
    $aCon = conectarBase($conexaoSC);
    $sql = "
    select \"_Connect-Name\" as usuario from pub.\"_lock\"
inner join pub.\"_file\" on pub.\"_file\".\"_file-number\" = pub.\"_lock\".\"_lock-table\"
inner join pub.\"_connect\" on \"_Connect-Usr\" = \"_Lock-usr\"
where pub.\"_file\".\"_file-name\" = '$tabela' with (NOLOCK)
    ";
    $ret = execAcaoPDO($sql,$aCon);
    if($ret['erro']<> ''){
        echo "<h1>{$ret['erro']}</h1>";
    }
    $reg = $ret['resultado']->fetch(PDO::FETCH_ASSOC);
    if(is_array($reg)) {
        $usuario = $reg['USUARIO'];
    }else{
        $usuario = '';
    }
    return $usuario;

}
function conectarBase2($conexaoSC,$usuario='sysprogress',$senha='sysprogress',$logOdbc=true)
{

    $erro = '';
    $con  = '';
    $odbc = getODBCDireto($conexaoSC);
    //echo "<h1>ODBC: $odbc</h1>";
    if($logOdbc){
        $odbc = "odbc:".$odbc;
    }
    try {
        $con = new PDO($odbc, $usuario, $senha);
    }catch(PDOException $exception){
        $erro = strtolower($exception->getMessage()) ;
    }
    return array('conexao'=> $con,'erro'=>$erro);

}
function conectarBaseMysql($nomeBanco)
{
    $aCon       = getDadosConexao($nomeBanco);
    $servidor   = $aCon['server'];
    $usuario    = $aCon['user'];
    $senha      = $aCon['password'];
    $conexao    = conectarBase("mysql:host=$servidor;dbname=$nomeBanco",$usuario,$senha,false);
    return $conexao;
    //$conexao = conectarBase('mysql:host=192.168.0.170;dbname=bd_285','root','670477Im@',false);

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
?>
