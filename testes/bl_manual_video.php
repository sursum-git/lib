<?php
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Latest compiled and minified CSS & JS -->
    <script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>
    <link rel="stylesheet" media="screen" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script>
        function exibirDivMan(opcao){
            if(opcao == 1){
                document.getElementById('versoes').hidden ="hidden";
                document.getElementById('manuais').hidden ="";
            }else{
                document.getElementById('manuais').hidden ="hidden";
                document.getElementById('versoes').hidden ="";
            }
        }
    </script>
    <title>Template Manuais</title>
</head>
<body>

<div class="container">
<br>
    <div class="row">
        <div class="col-md-12 col-sx12" >
            <h2><span class="glyphicon glyphicon-book" aria-hidden="true"></span> bl_inicio - Indicadores e Links Iniciais</h2>
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#">Descrição</a><br></li>
            </ul>
            <div id="descricao">
                XXXXXXXXXXXXXXXXXXXXXX XXXXXXXXXXXXXXXXXXXX XXXXXXXXXXXXXXXXXXXXXXXXXXX
            </div>
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#" onclick="exibirDivMan(1)">Manuais</a><br></li>
                <li role="presentation"><a href="#" onclick="exibirDivMan(2)">Versões</a>  </li>
            </ul>
            <div id="manuais" >
                <table class="table table-striped">
                    <thead>
                    <th>Titulo</th>
                    <th>Link</th>
                    </thead>
                    <tbody>
                    <tr><td>Descobra todas as funcionalidades da tela inicial do portal de vendas</td>
                        <td><a href="https://www.youtube.com/B8K6HJHiOe0" >Vídeo</a></td></tr>
                    </tbody>

                </table>
            </div>
            <div id="versoes" hidden="hidden">
                <table class="table table-striped">
                    <thead>
                    <th>Versão</th>
                    <th>Descrição</th>
                    </thead>
                    <tbody>
                    <tr><td>1.0</td>
                        <td>Inicial</td></tr>
                    </tbody>
                </table>
            </div>


        </div>

    </div>

    <br>






</div>
<!--<video controls title="TESTE">
    <source src="https://www.youtube.com/watch?v=B8K6HJHiOe0&feature=youtu.be" type="">
</video>
<hr>
<iframe width="420" height="315"
        src="https://www.youtube.com/watch?v=B8K6HJHiOe0&feature=youtu.be">
</iframe>-->
</body>
</html>

<?php
