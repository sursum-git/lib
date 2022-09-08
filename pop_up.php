<?php

function getNoticiasPortalPagInicial($codNoticiaAnt,$login){
    //$dataAtual = systimestamp();
    //echo "<h1>HOje = $dataAtual</h1>";
    $join = 'INNER JOIN PUB.relacs_usuario_noticia ON noticias_portal.cod_noticia_portal = relacs_usuario_noticia.noticia_id' ;
    $condicao = "PUB.noticias_portal.log_exibir_inicio = 1 and PUB.noticias_portal.cod_noticia_portal >".$codNoticiaAnt." and
                 relacs_usuario_noticia.cod_usuario = '$login' and data_hora_fim > systimestamp() and relacs_usuario_noticia.dt_hr_leitura is null order by cod_noticia_portal";
    $aDados = getDados('multi','PUB.noticias_portal',"",
        $condicao,
        'espec',$join);
    //echo "<pre>";
    //var_dump($aDados);
    return $aDados;

}

function updateNaoExibirNoticia($codNoticia,$login){
    $cmd = "update PUB.relacs_usuario_noticia set dt_hr_leitura = systimestamp() where noticia_id = $codNoticia and cod_usuario = '$login'";
    sc_exec_sql($cmd,"especw");
}


function getAnexosNoticiasPortal($codNoticia){
    $aDados = getDados('multi','PUB.anexos_noticias','',
        "cod_noticia_portal = $codNoticia ",'espec');
    return $aDados;
}
