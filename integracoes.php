<?php
function getJsonDadosCliente($cnpj)
{
    $token = 0;
    $api = getAPIDadosCliente();
    $token = getTokenAPI($api);
    $url = "$api"."cnpj=$cnpj"."&token=$token";
    //echo $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,450);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,10);
    $result = curl_exec($ch);
    //var_dump($result);
    if($result  <> false){
        $result = json_decode($result);
        //Verifica se o resultado foi OK
        if($result->return == "OK" ){
            return $result;
        }else{
            Echo "CNPJ não econtrado!!";
        }
    }else{

        return array('msg' => "Não foi possivel consultar o cnpj, verifique sua conexão com a internet!!");

    }
    curl_close($ch);

}

function getAPIDadosCliente()
{
    $api = "http://ws.hubdodesenvolvedor.com.br/v2/cnpj/?";
    $vlParam = buscarParametroIma('api_dados_cliente',$api);
    return $vlParam;
    //$url = "$api?cnpj=$cnpj&token=$token";
}
function getTokenAPI($api='')
{
    $token = "168200EgqOSWdsnC303680";
    $vlParam = buscarParametroIma('token_acesso',$token);
    return $vlParam;
}
function gravarDadosCliente($oJson)
{
    //$oJson = getJsonDadosCliente('06013812000239');

    $status       = $oJson->status ;                       $retorno       = $oJson->return ;
    $numInscricao = $oJson->result->numero_de_inscricao;   $tipo          = $oJson->result->tipo;
    $abertura     = $oJson->result->abertura;              $nomeCli       = $oJson->result->nome;
    $nomeFantasia = $oJson->result->fantasia;              $natJuridica   = $oJson->result->natureza_juridica;
    $logradouro   = $oJson->result->logradouro;            $NumRua        = $oJson->result->numero;
    $complemento  = $oJson->result->complemento;           $cep           = $oJson->result->cep;
    $bairro       = $oJson->result->bairro;                $municipio     = $oJson->result->municipio;
    $uf           = $oJson->result->uf;                    $email         = $oJson->result->email;
    $telefone     = $oJson->result->telefone;              $entRederResp  = $oJson->result->entidade_federativo_responsavel;
    $situacao     = $oJson->result->situacao;              $dtSitCad      = $oJson->result->dt_situacao_cadastral;
    $sitEspecial  = $oJson->result->situacao_especial;     $dtSitEspecial = $oJson->result->data_situacao_especial;
    $capSocial    = $oJson->result->capital_social;

        /*echo "<h1>$status / $retorno / $numInscricao / $tipo / $abertura / $nomeCli / $nomeFantasia / $natJuridica
                  $logradouro / $NumRua / $complemento / $cep / $bairro / $municipio / $uf / $email
                  $telefone / $entRederResp / $situacao / $dtSitCad / $sitEspecial / $dtSitEspecial / $capSocial</h1>" ;*/

    $cmd = "insert into dados_clientes(transacao_id,status_cli,retorno,numero_inscricao,tipo,abertura,nome_cli,nome_fantasia,
            natureza_juridica,logradouro,num_logradouro,complemento,cep,bairro,municipio,uf,email,telefone,
            ent_feder_resp,situacao,dt_sit_cadastral,sit_especial,dt_sit_especial,capital_social)
            values(".getTransacaoCorrente().",'$status','$retorno','$numInscricao','$tipo', '$abertura','$nomeCli','$nomeFantasia','$natJuridica',
                   '$logradouro','$NumRua','$complemento','$cep','$bairro','$municipio','$uf','$email',
                   '$telefone', '$entRederResp','$situacao','$dtSitCad','$sitEspecial','$dtSitEspecial','$capSocial') ";
    sc_exec_sql($cmd,"integracoes");

}

function inserirDadosClientesCnpj($status,$retorno,$numInscricao,$tipo,$abertura,$nomeCli,$nomeFantasia,$natJuridica,
                   $logradouro,$NumRua,$complemento,$cep,$bairro,$municipio,$uf,$email,
                   $telefone,$entRederResp,$situacao,$dtSitCad,$sitEspecial,$dtSitEspecial,$capSocial,$cnpj){

    $cmd = "insert into dados_clientes(transacao_id,status_cli,retorno,numero_inscricao,tipo,abertura,nome_cli,nome_fantasia,
            natureza_juridica,logradouro,num_logradouro,complemento,cep,bairro,municipio,uf,email,telefone,
            ent_feder_resp,situacao,dt_sit_cadastral,sit_especial,dt_sit_especial,capital_social,cnpj)
            values(".getTransacaoCorrente().",'$status','$retorno','$numInscricao','$tipo', '$abertura','$nomeCli','$nomeFantasia','$natJuridica',
                   '$logradouro','$NumRua','$complemento','$cep','$bairro','$municipio','$uf','$email',
                   '$telefone', '$entRederResp','$situacao','$dtSitCad','$sitEspecial','$dtSitEspecial','$capSocial','$cnpj') ";
    sc_exec_sql($cmd,"integracoes");
}

function gravarAtividadeCliente($oJson){

    //$oJson = getJsonDadosCliente('00063960000109');

    $AtivPricipal  = 1;
    $codAtividade  = $oJson->result->atividade_principal->code;
    $descAtividade = $oJson->result->atividade_principal->text;
    //echo "<h1>$codAtividade / $descAtividade / $AtivPricipal </h1>";
    inserirAtividadesClientes($codAtividade, $descAtividade, $AtivPricipal);
        foreach($oJson->result->atividades_secundarias as $atividade_secundaria){
            $AtivPricipal  = 0;
            $codAtividade  = $atividade_secundaria->code;
            $descAtividade = $atividade_secundaria->text;
            inserirAtividadesClientes($codAtividade, $descAtividade, $AtivPricipal);
            //echo "<h1>$codAtividade / $descAtividade / $AtivPricipal</h1>";
        }

}
function inserirAtividadesClientes($codAtividade, $descAtividade, $AtivPricipal, $cnpj){

    $cmd = "insert into atividades_clientes(transacao_id,cod_atividade,desc_atividade,atividade_principal,cnpj)
            values(".getTransacaoCorrente().",'$codAtividade','$descAtividade','$AtivPricipal',$cnpj) ";
    sc_exec_sql($cmd,"integracoes");
}

function gravarSociosClientes($oJson){

    //$oJson = getJsonDadosCliente('00063960000109');
    foreach($oJson->result->quadro_socios as $socios){
        $nomeSocio  = '';
        $codTpSocio = '';
        $dadosSocio = $socios;
        $aSocios    = explode(" ", $dadosSocio );
        $tam	    = count($aSocios);
        $tamNome    = $tam - 1;
        for($i=0;$i<$tamNome;$i++){
            $nomeSocio   = util_incr_valor($nomeSocio,$aSocios[$i],' ') ;
        }
        $tpSocio    = end($aSocios);
        $aTpSocio   = explode('-', $tpSocio);
        $tamCodTp   = count($aTpSocio);
        for($j=0;$j<$tamCodTp;$j++){
            $codTpSocio   = $aTpSocio[0];
            $descTpSocio  = $aTpSocio[1];
        }

        inserirDadosSociosClientes($nomeSocio,$codTpSocio,$descTpSocio);

    }

}
function inserirDadosSociosClientes($nomeSocio,$codTpSocio,$descTpSocio){

    $cmd = "insert into socios_clientes(transacao_id,nome_socio,tp_socio,cod_tp_socio) values(".getTransacaoCorrente().",'$nomeSocio','$descTpSocio','$codTpSocio') ";
    sc_exec_sql($cmd,"integracoes");

}
function iniciarTransacao($programa){

    $usuario  = getLoginCorrente();

    $cmd = "insert into transacoes(dt_hr_inicial,cod_usuario,cod_programa,sit_integracao)
            values(current_timestamp,'$usuario','$programa',1)";
    sc_exec_sql($cmd,"integracoes");



   $ultTransacao = getUltIdTabela('transacoes', 'sql', "integracoes");
   setTransacaoCorrente($ultTransacao);
   return $ultTransacao;

}

function setTransacaoCorrente($id){

    $_SESSION['id_transacao_corrente'] = $id;

}
function getTransacaoCorrente(){

    return $_SESSION['id_transacao_corrente'];

}
function finalizarTransacao($id=0){

 if($id == 0){
     $id = getTransacaoCorrente();
 }
 $cmd = "update transacoes set dt_hr_final = current_timestamp where id = $id";
 sc_exec_sql($cmd,"integracoes");

}

function execDadosCliente($oJson,$cnpj,$programa){

    if($oJson == '') {
        $oJson = getJsonDadosCliente($cnpj);
    }
    if (is_array($oJson) and isset($oJson['msg'])){
        return $oJson['msg'];
    }else {
        iniciarTransacao($programa);
        gravarDadosCliente($oJson);
        gravarAtividadeCliente($oJson);
        gravarSociosClientes($oJson);
        finalizarTransacao($id = 0);
        return '';
    }
}

function getRegClienteNovo($cnpj,$campos='',$filtroCompl='')
{
    $aReg = getReg('integracoes','dados_clientes','numero_inscricao',
        $cnpj,$campos,$filtroCompl );
    return $aReg;
}

function getRegDocsDadosCli($transacaoId,$campos='',$filtroCompl='' ){

    $aReg = getReg('integracoes', 'docs_dados_clientes','transacao_id',
        $transacaoId,$campos);
    return $aReg;
}

function getRegDadosClientes($cnpj,$campos='',$filtroCompl='')
{
    $aReg = getReg('integracoes','dados_clientes','numero_inscricao',
        $cnpj,$campos,$filtroCompl );
    return $aReg;
}

function getCamposPadrao($tabela,$campo,$campos=''){

    $aReg = getReg('integracoes', 'campos_pdr','tabela,campo',
        "'$tabela','$campo'",$campos);
    return $aReg;
}

function getDadosCamposPadrao($tabela,$campos=''){

    if($campos ==''){
        $campos = 'tabela,campo,tp_dado,valor_pdr';
    }
    $aReg = getDados('multi', 'campos_pdr',$campos,"tabela = '$tabela'",'integracoes');
    return $aReg;
}



/*
 * 1 funções de inserir para cada tabela
 * 2 função para iniciar(retornar numero da transacao corrente e gravar em variavel de sessao) e outra para finalizar transacao
 *  função getTransacaoCorrente
 *  vou te mandar um aundio explicando uma situação em particular.
 * 3 função com a quantidade de dias limite para descartar uma integração
 * 4 função para verificar se já existe uma integração pendente para o CNPJ informado considerando a qte
 * de dias limite
 * IMPORTANTE:as funções getTokenAPI,getAPIDadosCliente e de quantidade de dias limite devem ter um valor fixo
 *  e devem buscar um parametro. Caso não seja encontrado o parametro retorna o valor fixo.
 * */
//Usa a biblioteca Curl para fazer a busca no WS


?>
