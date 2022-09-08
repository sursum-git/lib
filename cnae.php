<?php
//__NM____NM__FUNCTION__NM__//
function classificarCNAE($cnae, $tipoAtividade, $finalidade)
{
    $cmd = " update pub.cnaes set ind_tipo_atividade = $tipoAtividade, 
				cod_finalidade_venda = $finalidade where cod_cnae = '$cnae'";
    sc_exec_sql($cmd,"especw");
}
?>