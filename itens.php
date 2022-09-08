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

?>