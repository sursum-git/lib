<?php
//__NM__regras tipo de usuario__NM__FUNCTION__NM__//
function aplicarFiltroTipoUsuarioPV($tabela,$apelido='')
{
    $filtroTipoUsuario = '';
    //echo "nome repres:".[nomeRepresIni];
    switch($tabela)
    {
        case "ped-venda":
            switch([tipoUsuario])
            {
                case '2':
                case '5':
                    //$filtroTipoUsuario = " and \"no-ab-reppri\" = '[nomeRepresIni]' ";
                    $filtroTipoUsuario = "and ped_rep.\"nome-ab-rep\" >= '[nomeRepresIni]'
                        and ped_rep.\"nome-ab-rep\" <= '[nomeRepresFim]'";

                    break;
                case '3':
                    $codRep = buscarCodRep([usr_login]);
                    $hierarq = getTpHierarquiaGer($codRep);
                    $lDir = verificGrupoLogin(6);
                    if($lDir and $hierarq <> 3){
                        $hierarq = 1;
                    }
                    if($hierarq == 1 or $hierarq == 4){
                        $lExcecao = verificGrupoLogin(16); //Exceção na Hierarquia
                        if ($lExcecao) {
                            $listaRepres = getListaRepresGer('nome','sp');
                            $filtroTipoUsuario = "and ped_rep.\"nome-ab-rep\" in ($listaRepres)";
                        }else{
                            $filtroTipoUsuario = "and ped_rep.\"nome-ab-rep\" >= '[nomeRepresIni]'
                            and ped_rep.\"nome-ab-rep\" <= '[nomeRepresFim]'";
                        }
                    }else{
                        $lGerente = verificGrupoLogin(5); //gerente de vendas
                        if ($lGerente) {
                            $listaRepres = getListaRepresGer();
                            $filtroTipoUsuario = "and ped_rep.\"nome-ab-rep\" in ($listaRepres)";
                        }
                    }
                    break;
            }
            break;
        case "ped-venda-ext":
            switch([tipoUsuario])
            {
                case '5':
                    $filtroTipoUsuario = " preposto = '[prepostoIni]'";
                    break;
            }
            break;
        case "nota-fiscal":
            if($apelido == ''){
                $apelido = "PUB.\"nota-fiscal\"";
            }
            switch([tipoUsuario])
            {

                case '2':
                case '5':
                    //$filtroTipoUsuario = " and $apelido.\"no-ab-reppri\" = '[nomeRepresIni]'";
                $filtroTipoUsuario = "and ped_rep.\"nome-ab-rep\" >= '[nomeRepresIni]'
                        and ped_rep.\"nome-ab-rep\" <= '[nomeRepresFim]'";
                break;

                case '3':
                    $codRep = buscarCodRep([usr_login]);
                    $hierarq = getTpHierarquiaGer($codRep);
                    $lDir = verificGrupoLogin(6);
                    if($lDir and $hierarq <> 3){
                        $hierarq = 1;
                    }
                    if($hierarq == 1 or $hierarq == 4){
                        $lExcecao = verificGrupoLogin(16); //Exceção na Hierarquia
                        if ($lExcecao) {
                            $listaRepres = getListaRepresGer('nome','sp');
                            $filtroTipoUsuario = "and ped_rep.\"nome-ab-rep\" in ($listaRepres)";
                        }else{
                            $filtroTipoUsuario = " and ped_rep.\"nome-ab-rep\" >= '[nomeRepresIni]'
                            and ped_rep.\"nome-ab-rep\" <= '[nomeRepresFim]'";
                        }
                    }else{
                        $lGerente = verificGrupoLogin(5); //gerente de vendas
                        if ($lGerente) {
                            $listaRepres = getListaRepresGer();
                            $filtroTipoUsuario = "and ped_rep.\"nome-ab-rep\" in ($listaRepres)";
                        }
                    }
                    break;
            }
        break;
        case "devol-cli":
            if($apelido == ''){
                $apelido = "PUB.\"devol-cli\"";
            }
            switch([tipoUsuario])
            {
                case '2':
                    $filtroTipoUsuario = " and $apelido.\"cod-rep\" = '[codRepIni]'";
                    break;


                case '5':
                    $filtroTipoUsuario = " and $apelido.\"cod-rep\" = '[codRepIni]'";
                    break;
            }
        break;

    }
    //echo "filtro usuario:".$filtroTipoUsuario;
    return $filtroTipoUsuario;

}

function getVendedores()
{
    $tabela   = " pub.\"usuarios_grupos\" as usuario_grupo , pub.\"user-web\" as usuario";
    $campos   = " usuario_grupo.login_usuario as login_usuario ";
    $condicao = "  usuario_grupo.cod_grupo = 2 and ativo = 1 
                    and usuario.login = usuario_grupo.login_usuario "; //representante interno(vendedor) e ativo
    $listaCodRep = '';
    $codRepCorrente = '';
    $listaCompleta = '';
    $aRet = retornoMultReg($tabela,$campos,$condicao,"espec");
    if(is_array($aRet))	{
        $tam = count($aRet);
        for($i=0;$i<$tam;$i++){
            $nomeAbrev = $aRet[$i]['login_usuario'];
            $codRep = buscarCodRep($nomeAbrev);
            if($codRep <> 0){
                if([usr_login] == $nomeAbrev){
                    $codRepCorrente = $codRep;
                }else{
                    $listaCodRep = util_incr_valor($listaCodRep,$codRep);
                }
            }
        }
        if($codRepCorrente <> ''){
            $listaCompleta = "$codRepCorrente,$listaCodRep";
        }else{
            $listaCompleta = $listaCodRep;
        }
    }
    $retorno = array('cod_rep_corrente' => $codRepCorrente,'lista_cod_rep' => $listaCodRep,
        'lista_completa' => $listaCompleta);
    //var_dump($retorno);
    return $retorno;
}

function verifVendedor()
{
    $logVendedor = false;
    $tabela   = " pub.\"usuarios_grupos\" ";
    $campos   = " login_usuario ";
    $condicao = "  cod_grupo = 2 and login_usuario = '[usr_login]' ";
    $aRet = retornoSimplesTb01($tabela,$campos,$condicao,"espec");
    if(is_array($aRet)) {
         $logVendedor = True;
    }
    return $logVendedor;
}


?>