<?php
function getRegFornecComPorNome($nome){
    $id = getRegFornecCom('nome',$nome,'id,telefone');
    return $id;
}

function getRegFornecComPorId($idParam){
    $aReg = getRegFornecCom('id',$idParam,'nome,telefone');
    return $aReg;

}

function getRegFornecCom($campo,$valor,$campoRet){

    $cps = array();
    $aReg = getReg('integracoes', 'fornecs_com', $campo, "'$valor'", $campoRet);
    $aCampoRet = explode(',',$campoRet);
    if(is_array($aCampoRet)){
        foreach($aCampoRet as $cp){
            $cps[$cp] = $aReg[0][$cp];
        }
    }
    return $cps;
}

function inserirFornecCom($nome,$telefone){

    $cmd = "insert into fornecs_com(nome,telefone) values ('$nome','$telefone')";

    sc_exec_sql($cmd,"integracoes");
    $id = getUltIdTabela('fornec_com', 'sql', 'integracoes');
    return $id;
}

function sincrFornecCom($nome,$telefone){

    $aReg = getRegFornecComPorNome($nome);
    if(isset($aReg['id'])){
        $id = $aReg['id'];
    }else{
        $id = 0;
    }

    if($id == 0){
        $id = inserirFornecCom($nome, $telefone);
    }
    return $id;
}