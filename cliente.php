<?php

function montarFiltroCliente($aFiltros,$aApelidoTb='')
{ /* chaves aFiltros: 
     filtrar_por,uf,bairro,nome_abrev,nome_emit,cnpj,codigo,cod_rep
    */
    $aFiltroCond = array();
    $tabela = setApelidoTbFiltro($aApelidoTb,'emitente');
    $afiltro['identific'] = ' <> 2 ';
    

    switch($aFiltros['filtrar_por'])
    {
        case 1: //localidade
               if($aFiltros['cidade'] <> ''){
                      $listaCidades = inserirAspasEmLista($aFiltros['cidade']);                      
                      $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cidade',"$listaCidades",'in',true );
                      $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'estado',$aFiltros['uf'] ); 
               }else{                               
                    $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'estado',$aFiltros['uf'] );                 
               }                   
               $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'bairro',$aFiltros['bairro'],'like' );                  
               
               
            break;
        case 2: //nome-abrev                        
            $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nome-abrev',$aFiltros['nome_abrev'],'like' );     
            
            
             break;        
        case 3: //razao_social            
                $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'nome-emit',$aFiltros['nome_emit'],'like');
            break;                
        case 4: //cnpj            
                $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cgc',$aFiltros['cnpj'],'like' );
            break;                   
        case 5: //código
            $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-emitente',$aFiltros['codigo'],'=',true );
            break;                          
            
    }
    $aFiltroCond = inserirArrayCond($aFiltroCond,$tabela,'cod-rep',$aFiltros['cod_rep'],'in',true );  
    
    return convArrayToCondSql($aFiltroCond);
}


function getDadosCliente($codEmitente,$campos){


    $cliente  = 0;
    $tipo     = "unico";
    $tabela   = " pub.emitente ";
    $condicao = "  \"cod-emitente\" = $codEmitente";
    $conexao  = "ems2";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);

    return $aDados;


}
function getRepresCliente($codEmitente){


    $cliente  = 0;
    $tipo     = "unico";
    $tabela   = " pub.emitente ";
    $campos   = ' "cod-rep" as cod_repres ';
    $condicao = "  \"cod-emitente\" = $codEmitente";
    $conexao  = "ems2";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);

    return getVlIndiceArray($aDados,'cod_repres',0);

}

function getNomeAbrevCliente($codEmitente){

    if($codEmitente == 0){
        return '';
    }
    else{
        $tipo     = "unico";
        $tabela   = " pub.emitente ";
        $campos   = ' "nome-abrev" as nome_abrev ';
        $condicao = "  \"cod-emitente\" = $codEmitente";
        $conexao  = "ems2";
        $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);

        return getVlIndiceArray($aDados,'nome_abrev','');
    }


}
function getDescrCliente($codEmitente,$logJuntarCodigo=true)
{
  $codEmitente = tratarNumero($codEmitente);
  //echo "<h1>codigo:$codEmitente</h1>";

    $tipo     = "unico";
    $tabela   = " pub.emitente ";
    $campos   = ' "nome-emit" as nome_emit ';
    $condicao = "  \"cod-emitente\" = $codEmitente";
    $conexao  = "ems2";
    $aDados  = getDados($tipo,$tabela,$campos,$condicao,$conexao);
    $descr = getVlIndiceArray($aDados,'nome_emit','');
    if($logJuntarCodigo){
        $descr = "$codEmitente - $descr";
    }
    return $descr;
}

function getTranspPadraoCliente($cliente)
{
    $transp = 0;
    $tipo     = "unico"; // unico ou multi
    $tabela   = " pub.emitente emit ";
    $campos   = " \"cod-transp\" as transp";
    $condicao = "  emit.\"cod-emitente\" = $cliente ";
    $conexao  = "ems2";
    $aDados= getDados($tipo,$tabela,$campos,$condicao,$conexao);
    if(is_array($aDados)){
        $transp = $aDados[0]['transp'];
    }
    return $transp;

}
function getFiltroClienteTpUsuario($apelido='emit',$logAplicarFiltro=false)
{
    $condicao = '';
    if($apelido == ''){
        $apelido = 'emit';
    }
    $apelido .= '.';
    switch(getVarSessao('tipo_usuario_id')){
        case getNumTipoUsuarioCliente():
            $condicao = $apelido.'"cod-emitente" = '.getVarSessao('num_cliente');
            break;
        case getNumTipoUsuarioRepresentante():
            $condicao = $apelido.'"cod-rep" = '.getVarSessao('num_repres');
            break;
        //outros tipos de usuários não tem filtro especifico
    }
    if($logAplicarFiltro){
        setCondWhere($condicao);
    }

    return $condicao;


}
function getAvatarEClassClientePorSitCred($sitCredito):array
{
    switch($sitCredito){
        case 1: //normal
			$class  = "okNomeCli";
			$avatar = "bi bi-emoji-smile-fill";
		break;
		case 4: //suspenso
			$class  = "nokNomeCli";
		    $avatar = "bi bi-emoji-frown-fill";

		break;
		default: // automatico / a vista / só implanta pedidos
			$class  = "medioNomeCli";
			$avatar = "bi bi-emoji-expressionless-fill";

    }
    return array('avatar'=>$avatar,'classe'=>$class);


}
function desenharHtmlClienteComSitCred($nomeCliente,$sitCredito)
{
   $aCli    = getAvatarEClassClientePorSitCred($sitCredito);
   $class   = $aCli['classe'];
   $avatar  = $aCli['avatar'];
   $nomeCliente = dividirTexto($nomeCliente,25);
    $retorno = <<<RET
    <span class="$class"><i class="$avatar" ></i> $nomeCliente </span>
RET;
   return $retorno;
}
function getNumCredSuspenso()
{
    return 4;
}
function getNumCredAVista()
{
    return 5;
}
?>
