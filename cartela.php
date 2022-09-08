<?php
//__NM____NM__FUNCTION__NM__//
function retornarCartelaCor($cor)
{
    $aCores = array('00','A','B','C','D','E','F','G','H','I','J','K','L');
    $imagem = '';
    $diretorio = '/var/www/clients/client1/web1/web/estampas/424x600/etiqueta_cores';
    //$diretorio = 'c:\inetpub\wwwroot\vendermais\_lib\img\estampas';
    $dirRel = "../../estampas/424x600/etiqueta_cores";
    /*if(substr($referencia,0,1) == '0'){
        $referencia = substr($referencia,1,2);
    }*/

    $letraCor  = $aCores[$cor];
    $filtro    = "/$letraCor/";
    //$item      = str_replace('/','//',$item);
    //echo "<h1>Filtro Item: $item</h1>";
    //echo "<h1>filtro:$filtro</h1>";
    $dir = new DirectoryIterator($diretorio);
    $filtro = new RegexIterator($dir, $filtro);
    foreach ($filtro as $arquivo)
    {

        $arquivo = limparNomeArquivo($arquivo);


        $imagem[]= array('caminho'=> "$dirRel/$arquivo");
    }
    if($imagem == '')
        $imagem[0] = array('caminho'=> "$dirRel/branco.jpg");

    return $imagem;
}
?>