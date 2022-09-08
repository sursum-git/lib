<?php
//__NM____NM__NFUNCTION__NM__//
//__NM____NM__NFUNCTION__NM__//
//__NM____NM__NFUNCTION__NM__//


function execAcaoPDO($cmd,$conexaoBase,$acao='')
{
    //echo "<h1>ponto50</h1>";
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
    //echo "<h1>ponto 51</h1>";
    return array('comando'=>$cmd,'resultado'=>$resultado,'erro'=>$erro);

}
function getODBCDireto($conexaoSC)
{
    $base = getBase();
    $base = strtolower($base);
    switch ($conexaoSC){
        case 'especw':
            $retorno = $base =='producao'? 'espec_w_pro':'espec_w_tst';
            break;
        case 'ems2w':
            $retorno = $base =='producao'? 'ems2_w':'ems2_w_tst';
            break;
        case 'ems2':
            $retorno = $base =='producao'? 'ems2':'ems2_tst';
            break;
        case 'ems5':
            $retorno = $base =='producao'? 'ems5':'ems5_tst';
            break;    
        case 'espec':
            $retorno = $base =='producao'? 'espec':'espec_tst';
            break;
        case 'log_db':
            $retorno = $base =='producao'? 'log_db':'log_db_tst';
            break;
        default:
            $retorno = $conexaoSC;
    }
    return $retorno;

}

function retornoSimplesTb01($tabParam, $campos, $condicao, $conexao = '', $inner = '', 
                            $logUTF8=0, $logSql=0,$formato='queryParams')
{
    $aRetorno = array();
    $lAchou = false;
    $txtArray = '';
    $txtComum = '';
    $tipoConexao = 'progress';
    if ($campos == '' ) {
        $aCampos = getCpsTbSessao($conexao, retirarPubTab($tabParam) );
        $campos = $aCampos['campos'];
    }
    $aCampos = explode(',', $campos);
    //echo "tab1: $tabParam";
    /*IMPORTANTE: sempre que existirem virgulas dentro de um campo, este campo deve substituir as virgulas por pape(|)*/
    $campos = str_replace("|", ",", $campos);
    $tpConexao = getTipoConexao($conexao);
    if($tpConexao == 'progress'){
        $tabParam = convNomeTabProgress($tabParam);
    }

    $sql = "select $campos from $tabParam $inner
		  where $condicao";

    if($logSql == 1){
        echo "<h1>$sql</h1>";
    }

    //echo "sql retornosimples:$sql<br>";
    //echo "conexao:$conexao<br>";
    switch ($conexao) {
        case 'ems5':
            sc_lookup(meus_dados, $sql, "ems5");
            break;
        case 'espec':
            sc_lookup(meus_dados, $sql, "espec");
            break;
        case 'ems2':
            sc_lookup(meus_dados, $sql, "ems2");            
            break;        
        case 'multi':
            sc_lookup(meus_dados, $sql, "multi");
            break;        
        case 'log_db':
            sc_lookup(meus_dados, $sql, "log_db");
            break;        
        default:
            sc_lookup(meus_dados, $sql);
    }
    //echo'logsql:'. getLogSql().'<br>';
    if(getLogSql() == 1){
        inserirLogDb('comando SQL reg unico',$sql,__FUNCTION__);
    }

    if($conexao == 'api'){
        $aParam = convertDadosSqlParaAPI($tabParam,$campos,$condicao,$inner,$formato);

        /*echo "<h1>var_dump dados convertidos para api</h1>";
        var_dump($aParam);*/
        $aRet = getApiTotvs('fnd/v1/consdin','GET',$aParam);
        $aRetorno = getValsArrayMultiDin($aRet, convertCpParaAPI($campos) );
        //return $retorno;
    }else{ //conexoes ODBC
        if ({meus_dados}   === false){
            if(getLogSql() == 1){
                $erro = {meus_dados_erro};
                inserirLogDb("erro comando SQL - conexao:$conexao",$erro,__FUNCTION__);
            }
            echo "Erro de acesso retornoSimplesTB01. Mensagem = " . {meus_dados_erro};
        }elseif (empty({meus_dados})){
              //echo "Comando select não retornou dados ";
              if(getLogSql() == 1){
                  inserirLogDb('retorno comando SQL unico','VAZIO',__FUNCTION__);
              }
        }
        else{
          for ($i = 0; $i < count($aCampos); $i++) {
              $aApelido = explode(' as ', $aCampos[$i]);
              if (count($aApelido) > 1) {
                  $nomeCampo = ltrim(rtrim($aApelido[1]));
              } else {
                  $nomeCampo = ltrim(rtrim($aApelido[0]));
              }
              //echo "nome campo:".$nomeCampo;

              /* $txtComum = "'".$nomeCampo."'=> '". {meus_dados[0][$i]}."'"  ;
              if($txtArray == '')
              {
                  $txtArray = $txtComum;
              }
              else
              {
                  $txtArray .= ",".$txtComum;
              }*/
              if($logUTF8 == 1){
                  $cp = utf8_encode({meus_dados[0][$i]});
              }
              else{
                  $cp = {meus_dados[0][$i]};
              }
              $aRetorno[0][$nomeCampo] = $cp;
              $lAchou = true;

          }

      /*$txtEval = "return array(".$txtArray.");";
      //echo "txteval:".$txtEval;
      $aRetorno[] = eval($txtEval);*/
        }
       //var_dump($aRetorno);
       if ($lAchou == false) {
           $aRetorno = '';
       }
    }
   return $aRetorno;
}

function convertDadosSqlParaAPI($tabParam, $campos, $condicao, $inner,$formato='json')
{
    $aJson = array();
    $tabelas = $tabParam;
    $tabelas = str_replace('\"','',$tabelas);
    $tabelas = str_replace('pub.','',$tabelas);
    $tabelas = str_replace('"','',$tabelas);
    $campos = str_replace('"','',$campos);
    $condicao = str_replace('"','',$condicao);
    $condicao = str_replace("\'",'',$condicao);
    switch ($formato){
        case 'json':
            $aJson['ttParam'] = array('tabelas'=>$tabelas,
                'campos'=> $campos,
                'condicao'=> $condicao,
                'inner'=>$inner);
            $retorno = json_encode($aJson);
            break;
        case 'queryParams':
            $retorno = array('tabelas'=>$tabelas,
                'campos'=> $campos,
                'condicao'=> $condicao,
                'inner'=>$inner) ;
    }

    return $retorno;
}



function getTipoConexao($conexao)
{
    switch ($conexao) {
        case 'espec':
        case 'comum':
        case 'ima':
        case 'med':
        case 'multi':
        case 'ems5':
            $tipoConexao = 'progress';
            break;
        case 'log_db':
        case 'integracoes':
        case 'geral':
        case 'dinamico':
        case 'ticontrole':
        case 'wdfe':
        case 'cfsc':
        case 'tss':
            $tipoConexao = 'sql';
            break;

        default:
            $tipoConexao = 'progress';
    }
    return $tipoConexao;
}

function retornoMultReg($tabela, $campos, $condicao, $conexao = '', $inner = '',$logUTF8=0,$logSql=0,$formato='queryParams')
{
    $iCont = 0;
    $aRetorno = array();
    $lAchou = false;
    $txtArray = '';
    $txtComum = '';
    $tipoConexao = 'progress';
    if ($campos == '' ) {
        $aCampos = getCpsTbSessao($conexao, retirarPubTab($tabela) );
        $campos = $aCampos['campos'];
    }
    $aCampos = explode(',', $campos);
    /*IMPORTANTE: sempre que existirem virgulas dentro de um campo, este campo deve substituir as virgulas por pape(|)*/
    $campos = str_replace("|", ",", $campos);
    $tpConexao = getTipoConexao($conexao);
    if($tpConexao == 'progress'){
        $tabela = convNomeTabProgress($tabela);
    }
    $sql = "select $campos from $tabela $inner
		  where $condicao ";
    if($logSql == 1){
        echo "<h1>$sql</h1>";
    }
    switch ($conexao) {
        case 'ems5':
            sc_select(dados, $sql, "ems5");

            break;
        case 'espec':
            sc_select(dados, $sql, "espec");
            break;        
        case 'ems2':
            sc_select(dados, $sql, "ems2");            
            break;        
        case 'multi':
            sc_select(dados, $sql, "multi");
            break;        
        case 'log_db':
            sc_select(dados, $sql, "log_db");
            break;        
        default:
            sc_select(dados, $sql);
    }
    //echo'logsql:'. getLogSql().'<br>';

    if($conexao == 'api') {
        $aParam = convertDadosSqlParaAPI($tabela, $campos, $condicao, $inner, $formato);
        /*echo "<h1>var_dump dados convertidos para api</h1>";
        var_dump($aParam);*/
        $aRet = getApiTotvs('fnd/v1/consdin', 'GET', $aParam);
        $aRetorno = getValsArrayMultiDin($aRet, convertCpParaAPI($campos));
    }else{
        if(getLogSql() == 1){
            inserirLogDb('comando SQL multi',$sql,__FUNCTION__);
        }
        if ({dados}  === false){
            if(getLogSql() == 1){
                inserirLogDb("erro comando SQL - conexao:$conexao",{dados_erro},__FUNCTION__);
        }
            echo "Erro de acesso. Mensagem = " . {dados_erro};
        }else{
            while (!$dados->EOF) {
                for ($i = 0; $i < count($aCampos); $i++) {
                    $aApelido = explode(' as ', $aCampos[$i]);
                    if (count($aApelido) > 1) {
                        $nomeCampo = ltrim(rtrim($aApelido[1]));
                    } else {
                        $nomeCampo = ltrim(rtrim($aApelido[0]));
                    }
                    //echo "nome campo:$nomeCampo  - contador:$i<br>";
                    /*$txtComum = "'".$nomeCampo."'=> '". $dados->fields[$i]."'"  ;
                    if($txtArray == ''){
                        $txtArray = $txtComum;
                    }
                    else{
                        $txtArray .= ",".$txtComum;
                    }*/
                    if($logUTF8 == 1){
                        $cp = utf8_encode($dados->fields[$i]);
                    }
                    else{
                        $cp =$dados->fields[$i];
                    }
                    $aRetorno[$iCont][$nomeCampo] = $cp ;
                    $lAchou = true;
                }
                $iCont++;
                /*$txtEval = "return array(".$txtArray.");";
                //echo "txteval:".$txtEval;
                $aRetorno[] = eval($txtEval);*/
                $dados->MoveNext();
            }
            $dados->Close();
        }
        if ($lAchou == false) {
            $aRetorno = '';
        }
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
            if (stristr($campo, '-') <> false and stristr($campo, chr(34)) == false) {
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
            if (stristr($campo, '-') <> false and stristr($campo, chr(34)) == false) {
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
                $tab = "$tb";

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
    setVarSessao('lista_cps_definidas',$var,1);
    $_SESSION[$var] = $lista;

}

function getDados($tipo, $tab01, $campos, $condicao, $conexao = '', $inner = '', $logUTF8=0, $logSql=0)
{
    //echo "tab2:$tab01";
    switch ($tipo) {
        case 'unico':
            $aRet = retornoSimplesTb01($tab01, $campos, $condicao, $conexao, $inner,$logUTF8,$logSql);
            break;
        case 'multi':
            $aRet = retornoMultReg($tab01, $campos, $condicao, $conexao, $inner,$logUTF8,$logSql);
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
    setVarSessao('sql_corrente',$sql);
    switch ($banco) {
        case 'log_db':
            sc_lookup(id, $sql, "log_db");
            break;
        case 'integracoes':
            sc_lookup(id, $sql, "integracoes");
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


function getRegsSqlLivre($sql,$campos,$conexao,$logUTF8=0,$logSql=0)
{
    if($logSql == 1){
        echo "<h1>$sql</h1>";
    }
    $aCampos = explode(',', $campos);
    $iCont = 0;
    $aRetorno = array();
    $lAchou = false;
    switch ($conexao) {
        case 'ems5':
            sc_select(dados, $sql, "ems5");
            break;
        case 'espec':
            sc_select(dados, $sql, "espec");
            break;
        
        case 'ems2':
            sc_select(dados, $sql, "ima");            
            break;        
        case 'multi':
            sc_select(dados, $sql, "multi");
            break;        
        case 'log_db':
            sc_select(dados, $sql, "log_db");
            break;        
        default:
            sc_select(dados, $sql);
    }
    //echo'logsql:'. getLogSql().'<br>';
    if(getLogSql() == 1){
        inserirLogDb('comando SQL - sql livre',$sql,__FUNCTION__);
    }
    if ({dados}  === false){
    if(getLogSql() == 1){
        inserirLogDb('erro comando SQL - conexao:'.$conexao,{dados_erro},__FUNCTION__);
        }
    echo "Erro de acesso. Mensagem = " . {dados_erro};
    }else{
    while (!$dados->EOF) {
        for ($i = 0; $i < count($aCampos); $i++) {
            $aApelido = explode(' as ', $aCampos[$i]);
            if (count($aApelido) > 1) {
                $nomeCampo = ltrim(rtrim($aApelido[1]));
            } else {
                $nomeCampo = ltrim(rtrim($aApelido[0]));
            }
            if($logUTF8 == 1){
                $cp = utf8_encode($dados->fields[$i]);
            }
            else{
                $cp =$dados->fields[$i];
            }
            $aRetorno[$iCont][$nomeCampo] = $cp ;
            $lAchou = true;
        }
        $iCont++;
        /*$txtEval = "return array(".$txtArray.");";
        //echo "txteval:".$txtEval;
        $aRetorno[] = eval($txtEval);*/
        $dados->MoveNext();
    }
    $dados->Close();
}
    if ($lAchou == false) {
        $aRetorno = '';
    }
	return $aRetorno;
}



function updateDireto($conexaoSC,$tabela,$aDados,$condicao,$cpsSemAspas='')
{
    $usuario = '';
    $aCon =conectarBase($conexaoSC);
    $cmd = convertArrayEmUpdate($tabela,$aDados,$condicao,$cpsSemAspas);
    //echo "<h2>comando convertido:$cmd</h2>";
    $ret = execAcaoPDO($cmd,$aCon,'cmd');
    //echo "<h1>ponto30</h1>";
    $erro = $ret['erro'];
    //echo "<h1>ponto31</h1>";
    $lock = strstr($erro,'failure getting record lock')? 1 : 0;
    if($lock == 1){
        $usuario = getUsuarioLock($tabela,$conexaoSC);
        /*$msgLock = "A ação solicitada não pode ser realizada, pois, o usuário(a)
          $usuario está utilizando o registro neste momento";*/
    }
    return array('resultado'=> $ret,'erro'=>$erro,'lock'=>$lock,'usuario_lock'=>$usuario);
}


function conectarBase($conexaoSC,$usuario='sysprogress',$senha='sysprogress',$logOdbc=true)
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
    //var_dump($aCon);
    $servidor   = $aCon['server'];
    $usuario    = $aCon['user'];
    $senha      = $aCon['password'];
    $conexao    = conectarBase(
        "mysql:host=$servidor;dbname=$nomeBanco",
        $usuario,
        $senha,
        false);
    return $conexao;
    //$conexao = conectarBase('mysql:host=192.168.0.170;dbname=bd_285','root','670477Im@',false);

}

function getUsuarioLock($tabela,$conexaoSC)
{
    $tabela = str_replace(chr(34),"",$tabela);
    $aCon = conectarBase($conexaoSC);
    $sql = " select \"_Connect-Name\" as usuario from pub.\"_lock\"
inner join pub.\"_file\" on pub.\"_file\".\"_file-number\" = pub.\"_lock\".\"_lock-table\"
inner join pub.\"_connect\" on \"_Connect-Usr\" = \"_Lock-usr\"
where pub.\"_file\".\"_file-name\" = '$tabela' with (NOLOCK)   ";
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

function getReg($conexao, $tabelaParam, $camposChave, $valoresCamposChave, $campos = '', $filtroCompl = '', $logUTF8=0, $logSql=0)
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
        $aCampos = getCpsTbSessao($conexao, $tabelaParam);
        $campos = $aCampos['campos'];
    }
    if ($filtroCompl <> '') {
        $condicao = util_incr_valor($condicao, $filtroCompl, ' AND ', true);
    }
    $tpConexao = getTipoConexao($conexao);
    if($tpConexao == 'progress'){
        $tabelaParam = convNomeTabProgress($tabelaParam);
    }
    if ($tipoConexao == 'progress') {
        $tabelaParam = "pub.$tabelaParam";
    }

    $tipo = "unico"; // unico ou multi
    //echo "tabela:$tabelaParam";
    $aRet = getDados($tipo, $tabelaParam, $campos, $condicao, $conexao,'',$logUTF8,$logSql);

    return $aRet;
}

/*function acertarAspasDinamico($cmd,$listaCaracterDivisao,$listaCaracterBusca,$listaCaracterSubst)
{
    $novoComando = '';
    $aListaDivisao = explode(',',$listaCaracterDivisao);
    $aListaBusca = explode(',',$listaCaracterBusca);
    $aListaSubst = explode(',',$listaCaracterSubst);
    $qtNiveis    = count($aListaDivisao);

    for($a=0;$a<$qtNiveis;$a++){
          if($a == 0){
              $array = explode($aListaDivisao[$a],$cmd);
          }else{
              $array = subdividirArray($array,$aListaDivisao[$a]);
          }
    }



    $caracterUpper = strtoupper($caracter);
    $caracterLower = strtolower($caracter);
    $caracterFirst  = ucfirst($caracter);
    $cmd = str_replace($caracterUpper,$caracterLower,$cmd);
    $cmd = str_replace($caracterFirst,$caracterLower,$cmd);
    $aCmd1 = explode($caracterLower,$cmd);
    $tam=count($aCmd1);
    for($i=0;$i<$tam;$i++){
        if($i = 0 or $i % 2 == 0){
            $novoComando = util_incr_valor($novoComando, $aCmd1[$i]," $caracterLower ");
            if($i == 0 ){
                $novoComando = util_incr_valor($novoComando,' in ',"");
            }
        }else{
            $aCmd2 = explode(")",$aCmd1[$i]);
            $tam1 = count($aCmd2);
            for($x=0;$x< $tam1;$x++){
                $lista = $aCmd2[$i];
                if($x == 0){ // só o primeiro importa
                    $lista = tratarAspasSimples($lista);
                }
                $novoComando = util_incr_valor($novoComando,$lista," ) ");
            }
        }
    }
}*/


/*function acertarAspasDinamico($cmd,$listaCaracterDivisao,$listaCaracterBusca,$listaCaracterSubst)
{

    $novoComando = '';
    $caracterUpper = strtoupper($caracter);
    $caracterLower = strtolower($caracter);
    $caracterFirst  = ucfirst($caracter);
    $cmd = str_replace($caracterUpper,$caracterLower,$cmd);
    $cmd = str_replace($caracterFirst,$caracterLower,$cmd);
    $aCmd1 = explode($caracterLower,$cmd);
    $tam=count($aCmd1);
    for($i=0;$i<$tam;$i++){
        if($i = 0 or $i % 2 == 0){
            $novoComando = util_incr_valor($novoComando, $aCmd1[$i]," $caracterLower ");
            if($i == 0 ){
                $novoComando = util_incr_valor($novoComando,' in ',"");
            }
        }else{
            $aCmd2 = explode(")",$aCmd1[$i]);
            $tam1 = count($aCmd2);
            for($x=0;$x< $tam1;$x++){
                $lista = $aCmd2[$i];
                if($x == 0){ // só o primeiro importa
                    $lista = tratarAspasSimples($lista);
                }
                $novoComando = util_incr_valor($novoComando,$lista," ) ");
            }
        }
    }
}*/
function acertarAspasSimplesInsert($cmd)
{
    return $cmd;
}
/*function acertarAspasSimplesInsert($cmd)
{
    $novoComando = '';
    $cmd = str_replace('VALUES','values',$cmd);
    $cmd = str_replace('Values','values',$cmd);
    $aCmd1 = explode('values',$cmd);
    //var_dump($aCmd1);
    $novoComando = util_incr_valor($novoComando,$aCmd1[0]);
    $novoComando = util_incr_valor($novoComando,'values' ,'');
    //echo "<h1>novo comando: $novoComando</h1>";
    $aCmd2 = explode(",",$aCmd1[1]);
    //var_dump($aCmd2);
    $x = 0;
    foreach($aCmd2 as $conteudoCp){
        $x++;
        $conteudoCp = ltrim($conteudoCp);
        //echo "<h2>conteudo cp: $conteudoCp</h2>";
        if(substr($conteudoCp,0,1) =="'"){
            //echo "<h2>inicia com aspa simples</h2>";
            $tamCp = strlen($conteudoCp);
            $cpCorrente = substr($conteudoCp,1,$tamCp - 2) ;
            //echo "<h2>campo cpCorrente após substring: $cpCorrente </h2>";
            $cpCorrNovo = tratarAspasSimples($cpCorrente);
            $cpCorrNovo = "'$cpCorrNovo'";
        }else{

            //echo "<h2>NAO inicia com aspa simples</h2>";
            $cpCorrNovo = $conteudoCp;
        }
        //echo "<h2>campo tratado: $cpCorrNovo</h2>";
        if($x == 1){
            $delimitador = '';
        }else{
            $delimitador = ',';
        }
        $novoComando = util_incr_valor($novoComando,$cpCorrNovo,$delimitador);
        //echo "<h3>novo comando: $novoComando</h3>";
    }
    return $novoComando;
}*/



?>
