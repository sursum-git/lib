<?php
//__NM____NM__FUNCTION__NM__//
//__NM____NM__FUNCTION__NM__//
function iniciarLogDb($apl,$usuarioLogId)
{

    $usuario = getUsuarioCorrente();
    $cmd = "insert into logs(aplicacao,login,dt_hr_inicio,usuario_log_id)
            values('$apl','$usuario',current_timestamp, '$usuarioLogId' ) ";
    //sc_exec_sql($cmd,"log_db");
    $conBase = conectarBase('log_db',getUserLogDb(),getAutLogDb());
    $aRet = execAcaoPDO($cmd,$conBase,'cmd');
    //echo "<h1>depois execAcaoPDO</h1>";
    //var_dump($aRet);
    if(isset($aRet['erro'])){
        if($aRet['erro'] <> ''){
            echo "<h1>{$aRet['erro']}</h1>";
        }
    }
    //echo "<h1>depois execAcaoPDO 2 </h1>";
    $id = getUltIdTabela('logs');
    setIdCorrenteLogDb($id);
    return $id;
}

/*function getUsuarioCorrente()
{
        return getLoginCorrente();
}*/
function setIdCorrenteLogDb($id)
{
    $_SESSION['log_db']['id_corrente'] = $id;
}


function getIdCorrenteLogDb()
{
    if(isset($_SESSION['log_db']['id_corrente'])){
        $retorno = $_SESSION['log_db']['id_corrente'];
    }else{
        $retorno = -1;
    }
     return $retorno;
}
function  habilitarLogSql($log)
{
    setVarSessao('log_db_sql',$log);
}

function getLogSql()
{
    return getVarSessao('log_db_sql');
}
function inserirLogDb($titulo,$descricaoParam='',$funcao='',$id=0)
{
    //echo "<h1>inserirLogDb - inicio</h1>";
    if($id == 0){
        $idLog = getIdCorrenteLogDb();
    }else{
        $idLog = $id;
    }
    if($idLog > 0 ){
        if(is_array($titulo)){
            $titulo = print_r($titulo,true);
        }else{
            $titulo =  tratarAspasSimples($titulo);
            $titulo = retirarAcentoSimples($titulo);
        }
        if(is_array($descricaoParam) ){
            $descricaoParam = print_r($descricaoParam,true);
        }else{
            $descricaoParam = tratarAspasSimples($descricaoParam);
            //$descricaoParam = retirarAcentoSimples($descricaoParam);
        }
        $nivel = getNivelCorrenteLogDb();
        $cmd = "insert into itens_log(log_id,nivel,funcao,titulo,descricao,dt_hr_reg)
            values($idLog,$nivel,'$funcao','$titulo','$descricaoParam',CURRENT_TIMESTAMP )";
        //echo "<h1>$descricaoParam</h1>";
        //echo "<h1>$cmd</h1>";
        //sc_exec_sql($cmd,"log_db");
        $aConBase = conectarBase('log_db',getUserLogDb(),getAutLogDb());
        $erroConexao = $aConBase['erro'];
        if($erroConexao <> ''){
            echo "<h1>{$erroConexao}</h1>";
        }else{
            //echo "<h1>".getAutLogDb()."</h1>";
            //$cmdSql = tratarAspasSimples($cmd);
            //echo "<h1> comando antes acertar aspas simples: $cmd </h1>";
            //$cmd = acertarAspasSimplesInsert($cmd);
            $aRet = execAcaoPDO($cmd,$aConBase,'cmd');

            //var_dump($aRet);
            //echo "<h1>ponto 12</h1>";
            if(isset($aRet['erro'])){
                if($aRet['erro'] <> ''){
                    echo "<h1>{$aRet['erro']} - Comando: $cmd</h1>";
                    $cmd = acertarAspasSimplesInsert($cmd);
                    echo "<h2>após tratamento: $cmd</h2>";

                }
            }
        }
        //echo "<h1>ponto 13</h1>";
    }
}

function getGerarLogUsuarioCorrente($apl='')
{
    //$id = 0;
    $usuario = strtolower(getUsuarioCorrente()) ;
    if($apl == ''){
        $apl= $this->Ini->nm_cod_apl;
    }
    $aReg = getReg('log_db','usuario_log','cod_usuario',
        "'$usuario'",       'top 1 id as id',
        " dt_hr_inicial <= GETDATE() and dt_hr_final >= GETDATE()
                   and ( cod_programa = '$apl' or cod_programa = '' 
                   or cod_programa is null) order by id desc  ",false);
    if(is_array($aReg)){
        $id = $aReg[0]['id'];
        if($id == ''){
            $id = -1;
        }
    }else{
        $id = -1;
    }

    //echo "log id:$id<br>";
    return $id;

}
function incrNivelCorrenteLogDb()
{
    if(isset($_SESSION['log_db']['nivel_corrente'])){
        $_SESSION['log_db']['nivel_corrente'] += 1 ;
    }else{
        $_SESSION['log_db']['nivel_corrente'] = 1;
    }
    return $_SESSION['log_db']['nivel_corrente'];

}
function decrNivelCorrenteLogDb()
{
    if(isset($_SESSION['log_db']['nivel_corrente'])){
        $_SESSION['log_db']['nivel_corrente'] -= 1 ;
    }else{
        $_SESSION['log_db']['nivel_corrente'] = 1;
    }
    if($_SESSION['log_db']['nivel_corrente'] < 1){
        $_SESSION['log_db']['nivel_corrente'] = 1;
    }
    return $_SESSION['log_db']['nivel_corrente'];
}

function getNivelCorrenteLogDb()
{
    if(isset($_SESSION['log_db']['nivel_corrente'])){
        $nivel = $_SESSION['log_db']['nivel_corrente'];
    }else{
        $nivel = 1;
        $_SESSION['log_db']['nivel_corrente'] = 1;
    }
    return $nivel;
}
function zerarLogDb()
{
    //echo "<h1>Zerar Log DB</h1>";
    $aplCorrente = $this->Ini->nm_cod_apl;
    setIdCorrenteLogDb(-1);
    $idUsuarioLog = getGerarLogUsuarioCorrente($aplCorrente);
    //echo "id usuario: $idUsuarioLog<br>";
    if($idUsuarioLog >= 1){
        //echo "<h1>antes de chamar iniciarlogdb</h1>";
        iniciarLogDb($aplCorrente,$idUsuarioLog);
        //echo "<h1>depois de chamar iniciarlogdb</h1>";
    }
}
function excluirLogDb($idLog = 0)
{
    if($idLog > 0){
        $filtroLog = " where id = $idLog";
        $filtroItensLog = " where log_id = $idLog";
    }else{
        $filtroLog = "";
        $filtroItensLog = "";
    }
    $cmd = "delete from logs $filtroLog";
    sc_exec_sql($cmd,"log_db");
    $cmd = "delete from itens_log $filtroItensLog";
    sc_exec_sql($cmd,"log_db");


}
/*function validarAltDtInicialUsuarioLog($log)
{
    $msg = '';
    $qtItensLog = getQtItensLog($log);
    if($qtItensLog > 0){
        $msg = "<h3>A data/hora inicial não pode ser alterada, pois existem registros de log associados</h3>";

    }
    return $msg;

}*/
function validarAltUsuarioLog($usuarioLog,$dtHrIni,$dtHrFim)
{
    $msg = '';
    $aRegUsuarioLog = getRegUsuarioLog($usuarioLog);
    if(is_array($aRegUsuarioLog)){
       $dtHrIniAtual = $aRegUsuarioLog[0]['dt_hr_inicial'];
       $dtHrFimAtual  = $aRegUsuarioLog[0]['dt_hr_final'];

    } else{
        $dtHrIniAtual = '';
        $dtHrFimAtual  = '';
    }
    $qtLogs = getQtLogsUsuarioLg($usuarioLog);
    $agora = getAgora('Ymd H:i:su');
    if($qtLogs > 0){
        //echo substr($agora,0,17) .">". substr($dtHrFim,0,17)."<br>";
        if(substr($agora,0,17) > substr($dtHrFim,0,17) and
            ! compararDtHoraForm($dtHrFim ,$dtHrFimAtual) ){
            $msg = "<h3>A data/hora final não pode ser menor que o horário atual</h3>";
        }


        if(! compararDtHoraForm($dtHrIni,$dtHrIniAtual)){
            $msg .= "<h3>A data/hora inicial não pode ser alterada, pois existem registros de log associados</h3>";
        }
    }
    return $msg;

}
function getQtItensLog($log)
{
    $qt= 0;
    $aReg = getReg('log_db',
                   'itens_log',
    'log_id',$log,
    'count(id) as qt');
    if(is_array($aReg)){
        $qt = $aReg[0]['qt'];
    }
    return $qt;
}
function getQtLogsUsuarioLg($usuarioLog)
{
    $qt= 0;
    $aReg = getReg('log_db',
        'logs',
        'usuario_log_id',$usuarioLog,
        'count(id) as qt');
    if(is_array($aReg)){
        $qt = $aReg[0]['qt'];
    }
    return $qt;
}
function getRegUsuarioLog($log)
{
    $qt= 0;
    $aReg = getReg('log_db',
        'usuario_log',
        'id',$log,
        'id,dt_hr_inicial,dt_hr_final');
    return $aReg;
}

?>
