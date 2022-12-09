<?php
function getRegObEtq($codEstabel,$numEtq,$filtroCompl='')
{
    return  getReg('espec',
        'ob-etiqueta',
        '"cod-estabel","num-etiqueta"',
        "'$codEstabel',$numEtq",
        '',
        $filtroCompl);

}

function getTotalQtPorListaEtq($listaEtqs,$codEstabel='1')
{
    $aReg = getDados('unico','pub."ob-etiqueta" ','sum(quantidade) as qt',
    "\"num-etiqueta\" in ($listaEtqs) and \"cod-estabel\" = '$codEstabel'",
    "espec");
    return getVlIndiceArray($aReg,'qt',0);

}
function verifDispEtq($numEtq,$codEstabel='1')
{
    $aEtq = getRegObEtq($codEstabel,$numEtq,"  situacao = 3 ");
    return  is_array($aEtq);
}
function getRegsEtqPorLista($listaEtq,$codEstabel='1')
{
  $aEtq = getDados('multi','ob-etiqueta','"cod-estabel","num-etiqueta","nr-lote","corte-comerc","it-codigo","cod-refer"'," \"num-etiqueta\" in($listaEtq) 
  and \"cod-estabel\"=1",'espec');
   return $aEtq;
}
function setSitObEtiqueta($numEtq,$situacao,$codEstabel = '1')
{
    $aUpdate = array('situacao'=>$situacao);
    $cmd = convertArrayEmUpdate('"ob-etiqueta"',$aUpdate,"\"num-etiqueta\" = $numEtq and \"cod-estabel\"='1'");
    sc_exec_sql($cmd,"especw");
}


?>