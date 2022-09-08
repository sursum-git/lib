<?php
//__NM____NM__FUNCTION__NM__//
/**
 * Created by PhpStorm.
 * User: sursum_corda
 * Date: 28/01/2019
 * Time: 09:08
 */
function criarLogArquivo($nomeArquivo,$conteudo)
{

    $arquivo = fopen($nomeArquivo, 'w');
    fwrite($arquivo, $conteudo);
    fclose($arquivo);

}

?>