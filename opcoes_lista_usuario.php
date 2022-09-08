<?php
//__NM____NM__FUNCTION__NM__//
function getFormasPagto($logUtf8=1)
{
    $aRegs = getOpcoesLista('2',1,$logUtf8); //formas de pagamento pedido de venda
    return $aRegs;
}


function getOpcoesLista($lista,$logRestricao=1,$logUtf8=0)
{
    $aRetorno = array();
    $logAchou = 0;
    $aRegs = getDados('multi','pub.opcoes_lista','cod_opcao,desc_opcao,opcao_lista_id',
    "lista_id = $lista ",'espec','',$logUtf8);
    //var_dump($aRegs);
    if($logRestricao and (getTipoUsuarioCorrente() == getNumTpUsuarioRepres()
            or getTipoUsuarioCorrente() == getNumTpUsuarioPreposto() )){
        if(is_array($aRegs)){
            $tam = count($aRegs);
            for($i=0;$i< $tam; $i++){
                $idOpcao = $aRegs[$i]['opcao_lista_id'];
                $logPermOpcao = verificarPermOpcao($idOpcao);
                if($logPermOpcao == 1){
                    $aRetorno[] = $aRegs[$i];
                    $logAchou = 1;
                }
            }
        }
    }else{
        $aRetorno = $aRegs;
        $logAchou = 1;
    }
    if($logAchou == 0){
        $aRetorno = '';
    }
    return $aRetorno;
}

function verificarPermOpcao($opcaoLista)
{
    $logPermissao = 0;
    $aReg = getReg('espec',
        'opcoes_lista_usuario','opcao_lista_id',$opcaoLista,'opcao_lista_id',
          "login = '".getLoginCorrente()."'");
    if(is_array($aReg)){
        $logPermissao = 1;
    }
    return $logPermissao;
}

?>
