<?php
$tb= 'pub."im-param"';
if(!strpos(strtolower($tb), "pub.") and !strpos(strtolower($tb), "sysprogress.")) {
    echo "contém pub.";
}
else{
    echo "nao";
}

