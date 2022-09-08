<?php
$antigo = "insert into pub.metas_repres(meta_repres_id,ano,mes,cod_repres,vl_meta)
            values(pub.seq_meta_repres.NEXTVAL,'ASDFASDFADF ','','','')";
$novo = acertarAspasSimplesInsert($antigo);
echo $novo;
