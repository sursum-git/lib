//<?php

$a = renomearArqs(true);
?>
<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Latest compiled and minified JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body>

<div class="container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Item</th>
                <th>Ref</th>
                <th>Resultado</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(is_array($a)){
                $tam = count($a);
                for($i=0;$i<$tam;$i++){
                    $item = $a[$i]['item'];
                    $ref  = $a[$i]['ref'];
                    $resultado = $a[$i]['resultado'];

            ?>

            <tr>
                <td><?php echo $item;?></td>
                <td><?php echo $ref;?></td>
                <td><?php echo $resultado;?></td>
            </tr>
            <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php
