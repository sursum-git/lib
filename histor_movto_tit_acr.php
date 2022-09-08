<?php

function getHistMovtoTitAcr($codEstab,$numIdTitAcr, $numIdMovtoTitAcr)
{
    $historico = '';
     $aDados = getDados('unico',
        'pub.histor_movto_tit_acr hist',
        'max(hist.num_seq_histor_movto_acr) as max_id ,des_text_histor',
     " cod_estab = '$codEstab' and num_id_tit_acr = $numIdTitAcr 
            and num_id_movto_tit_acr = $numIdMovtoTitAcr 
            group by des_text_histor ",
     'ems5'
           );
     if(is_array($aDados)){
         $historico = $aDados[0]['des_text_histor'];
     }
     return $historico;

}
