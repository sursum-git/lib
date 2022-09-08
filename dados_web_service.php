<?php

function convDadosFormWs($conexao,$tabela,$tipo,$cnpj=''){


    switch ($tipo){

        case "cabecalho":

            $aCampos = getCpsTbSessao($conexao, $tabela);

            $aCp = $aCampos['campos'];
            $aCp = explode(',', $aCp);

            $aTipo = $aCampos['tipos'];
            $aTipo = explode(',', $aTipo);

            $tam = count($aCp);
            for ($i = 0;
            $i<$tam;
            $i++) {

            $campo = $aCp[$i];
            $tipo = $aTipo[$i];

            $aRet[] = array('name' => $campo, 'label' => $campo, 'type' => getConvTipo($tipo));

            }
            break;

    //registro

        case "registro":

            $aReg = getRegDadosClientes($cnpj);
            $aRegDePara = getDadosRelacsCpErp('emitente');
            foreach ($aRegDePara as $reg) {
                $cp = $reg['cp_origem'];
                if (isset($aReg[0][$cp])) {
                    $aJson[0][utf8_encode($reg['cp_destino'])] = utf8_encode($aReg[0][$cp]);
                }
            }
            $aRet = $aJson;
            break;
        //fim registro
}
    return $aRet;

}

function convDadosCamposPdr($tabela,$tp){

    $aCampos = getDadosCamposPadrao($tabela);
    $aReg = getDadosCamposPadrao($tabela,$campos='');

    switch ($tp) {

    case "cabecalho";
        $tam = count($aCampos);
        for ($i = 0;
        $i<$tam;
        $i++) {

        $campo = $aCampos[$i]['campo'];
        $tipo = $aCampos[$i]['tp_dado'];

        $aRet[] = array('name' => $campo, 'label' => $campo, 'type' => getConvTipo($tipo));
        break;
    }

        case "registro":

            foreach($aReg as $reg){
                $aJson[0][utf8_encode($reg['campo'])] = utf8_encode($reg['valor_pdr']);
            }
           $aRet = $aJson;
}
    return $aRet;
}

function getDadosRelacsCpErp($tabdest){

    $aReg = getDados('multi', 'relacs_cps_erp','tb_origem,cp_origem,tb_destino,cp_destino',"tb_destino = '$tabdest'",'integracoes');
    return $aReg;

}


function getConvTipo($tipo){

    switch ($tipo){

        case "int":
        case 'inteiro':
            $retorno = "integer";
            break;

        case "varchar":
        case 'texto':
            $retorno = "character";
            break;

        case "date":
        case 'data':
            $retorno = "date";
            break;

        case "bit":
            $retorno = "logical";
            break;
        default:
            $retorno = $tipo;

    }
    return $retorno;


}
