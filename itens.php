<?php 
function getDescrCpItemMobile($item,$descItem,$un)
{
    $retorno = <<<ITEM
    <div class="container">
             <h4><span class='lb'><i class="bi bi-flag-fill"></i></span> $item  | <span class='lb'><i class="bi bi-unity"></i></span> $un</h4>
             <h6>$descItem</h6>
     </div>        
ITEM;
return $retorno;
}

function getRegItem($itCodigo,$campos='')
{
    $aReg = getReg('ems2','item','"it-codigo"',$itCodigo,$campos);
    return $aReg;

}


function getDescrItem($itCod,$complemento='')
{
     $descItem = '';
     $aDescItem		     = getRegItem($itCod,'"desc-item" as desc_item');
    if(is_array($aDescItem)){
        $descItem			= $aDescItem[0]['desc_item'];
        $descItem = util_incr_valor($descItem,$complemento," ");
        //echo "<h1>2</h1>";
    }
    return $descItem;

}

?>