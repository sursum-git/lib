
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/grid/">



    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


    <!-- Custom styles for this template -->
    <link href="grid.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <title>Manual</title>
</head>

<body>
<div class="container">

    <div class="page-header">
        <h1> <span class="glyphicon glyphicon-book" aria-hidden="true"> </span>Indicadores e Links Iniciais</h1>
        <p class="lead">bl_inicio</p>
    </div>
    <h3>Descrição</h3>
    <p>XXX XXX XXX XXXXXXXXXXXXXXX XXXXXXXXXXXXX XXXXXXXXXXXXXXXXXXXXX XXXXXXXX XXX</p>
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
                <td><a href="https://youtu.be/RhcHkC6PTYA" target="_blank">Vídeo</a></td></tr>
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



</div> <!-- /container -->


<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
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

</body>
</html>
