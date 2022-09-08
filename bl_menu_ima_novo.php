//<?php
if(!isset($_SESSION['glo_usr_login']) or $_SESSION['glo_usr_login'] == ''){
sc_redir('bl_tela_login');
}


[usr_login] 		= $_SESSION['glo_usr_login'];
[usr_priv_admin] 	= $_SESSION['glo_usr_priv_admin'];
[usr_name] 			= $_SESSION['glo_usr_name'];
[usr_email]			= $_SESSION['glo_usr_email'];
[nomeClienteIni]    = $_SESSION['glo_nomeClienteIni'] ;
[nomeClienteFim]	= $_SESSION['glo_nomeClienteFim'];
[nomeRepresIni]		= $_SESSION['glo_nomeRepresIni'];
[nomeRepresFim]     = $_SESSION['glo_nomeRepresFim'];
[codRepIni]			= $_SESSION['glo_codRepIni'];
[codRepFim]			= $_SESSION['glo_codRepFim'];
[prepostoIni]		= $_SESSION['glo_prepostoIni'];
[prepostoFim]		= $_SESSION['glo_prepostoFim'];
[tipoUsuario]		= $_SESSION['glo_tipoUsuario'];
[aplicacaoPrincipal] = $_SESSION['glo_aplicacaoPrincipal'];
[inicio_sessao]		= $_SESSION['glo_inicio_sessao'];
[aprovador]	    	= $_SESSION['glo_aprovador'];



$login         = [usr_login];
$login		   = strtolower($login);
$login         = ucfirst($login);


$src = isset($_GET["src_frame"])?"../".$_GET["src_frame"]."/":"../bl_inicio/";

$linkCarrinho = getLinkCarrinho();


?>



<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title> ImaOnline </title>
    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- #CSS Links -->
    <!-- Basic Styles -->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo sc_url_library('prj','smartAdmin_seed','seed/css/bootstrap.min.css');?> ">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo sc_url_library('prj','smartAdmin_seed','seed/css/font-awesome.min.css');?>">


    <!-- SmartAdmin Styles : Caution! DO NOT change the order -->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo sc_url_library('prj','smartAdmin_seed','seed/css/smartadmin-production.min.css');?>">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo sc_url_library('prj','smartAdmin_seed','seed/css/smartadmin-skins.min.css');?>">


    <!-- We recommend you use "your_style.css" to override SmartAdmin
         specific styles this will also ensure you retrain your customization with each SmartAdmin update.
    <link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

    <!-- #FAVICONS
    <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon/favicon.ico" type="image/x-icon">
    -->

    <!-- #GOOGLE FONT -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    <!-- #APP SCREEN / ICONS -->
    <!-- Specifying a Webpage Icon for Web Clip
         Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
    <link rel="apple-touch-icon" href="img/splash/sptouch-icon-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/splash/touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/splash/touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/splash/touch-icon-ipad-retina.png">

    <!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <!-- Startup image for web apps -->
    <link rel="apple-touch-startup-image" href="img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
    <link rel="apple-touch-startup-image" href="img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
    <link rel="apple-touch-startup-image" href="img/splash/iphone.png" media="screen and (max-device-width: 320px)">


    <!-- #HEADER -->
    <header id="header">
        <div id="logo-group">

            <!-- PLACE YOUR LOGO HERE -->
            <span id="logo"> <img src="http://imaonline.imatextil.com.br/img/_logo.png" alt="logo_ima"> </span>
            <!-- END LOGO PLACEHOLDER -->


            <!-- AJAX-DROPDOWN : control this dropdown height, look and feel from the LESS variable file -->
            <div class="ajax-dropdown">

                <!-- the ID links are fetched via AJAX to the ajax container "ajax-notifications" -->
                <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-default">
                        <input type="radio" name="activity" id="ajax/notify/mail.html">
                        Msgs (14) </label>
                    <label class="btn btn-default">
                        <input type="radio" name="activity" id="ajax/notify/notifications.html">
                        notify (3) </label>
                    <label class="btn btn-default">
                        <input type="radio" name="activity" id="ajax/notify/tasks.html">
                        Tasks (4) </label>
                </div>

                <!-- notification content -->
                <div class="ajax-notifications custom-scroll">

                    <div class="alert alert-transparent">
                        <h4>Click a button to show messages here</h4>
                        This blank page message helps protect your privacy, or you can show the first message here automatically.
                    </div>

                    <i class="fa fa-lock fa-4x fa-border"></i>

                </div>
                <!-- end notification content -->

                <!-- footer: refresh area -->
                <span> Last updated on: 12/12/2013 9:43AM
						<button type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Loading..." class="btn btn-xs btn-default pull-right">
							<i class="fa fa-refresh"></i>
						</button> </span>
                <!-- end footer -->

            </div>
            <!-- END AJAX-DROPDOWN -->
        </div>



        <!-- #TOGGLE LAYOUT BUTTONS -->
        <!-- pulled right: nav area -->
        <div class="pull-right">

            <!-- collapse menu button -->
            <div id="hide-menu" class="btn-header pull-right">
                <span> <a href="javascript:void(0);" data-action="toggleMenu" title="Menu"><i class="fa fa-reorder"></i></a> </span>
            </div>
            <!-- end collapse menu -->

            <!-- #MOBILE -->
            <!-- Top menu profile link : this shows only when top menu is active -->
            <ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
                <li class="">
                    <a href="#" class="dropdown-toggle no-margin userdropdown" data-toggle="dropdown">
                        <img src="../../img/avatar.png" alt="John Doe" class="online" />
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0"><i class="fa fa-cog"></i> Setting</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#ajax/profile.html" class="padding-10 padding-top-0 padding-bottom-0"> <i class="fa fa-user"></i> <u>P</u>rofile</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="toggleShortcut"><i class="fa fa-arrow-down"></i> <u>S</u>hortcut</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="javascript:void(0);" class="padding-10 padding-top-0 padding-bottom-0" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i> Full <u>S</u>creen</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="login.html" class="padding-10 padding-top-5 padding-bottom-5" data-action="userLogout"><i class="fa fa-user"></i> <strong><u>L</u>ogout</strong></a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- logout button -->
            <div id="logout" class="btn-header transparent pull-right">
                <span> <a href="../bl_tela_login/bl_tela_login.php" title="Sair" data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fa fa-sign-out"></i></a> </span>
            </div>
            <!-- end logout button -->

            <!-- search mobile button (this is hidden till mobile view port) -->
            <div id="search-mobile" class="btn-header transparent pull-right">
                <span> <a href="javascript:void(0)" title="Search"><i class="fa fa-search"></i></a> </span>
            </div>
            <!-- end search mobile button -->




    </header>
    <!-- END HEADER -->



</head>

<!--

TABLE OF CONTENTS.

Use search to find needed section.

===================================================================

|  01. #CSS Links                |  all CSS links and file paths  |
|  02. #FAVICONS                 |  Favicon links and file paths  |
|  03. #GOOGLE FONT              |  Google font link              |
|  04. #APP SCREEN / ICONS       |  app icons, screen backdrops   |
|  05. #BODY                     |  body tag                      |
|  06. #HEADER                   |  header tag                    |
|  07. #PROJECTS                 |  project lists                 |
|  08. #TOGGLE LAYOUT BUTTONS    |  layout buttons and actions    |
|  09. #MOBILE                   |  mobile view dropdown          |
|  10. #SEARCH                   |  search field                  |
|  11. #NAVIGATION               |  left panel & navigation       |
|  12. #MAIN PANEL               |  main panel                    |
|  13. #MAIN CONTENT             |  content holder                |
|  14. #PAGE FOOTER              |  page footer                   |
|  15. #SHORTCUT AREA            |  dropdown shortcuts area       |
|  16. #PLUGINS                  |  all scripts and plugins       |

===================================================================

-->

<!-- #BODY -->
<!-- Possible Classes

    * 'smart-style-{SKIN#}'
    * 'smart-rtl'         - Switch theme mode to RTL
    * 'menu-on-top'       - Switch to top navigation (no DOM change required)
    * 'no-menu'			  - Hides the menu completely
    * 'hidden-menu'       - Hides the main menu but still accessable by hovering over left edge
    * 'fixed-header'      - Fixes the header
    * 'fixed-navigation'  - Fixes the main menu
    * 'fixed-ribbon'      - Fixes breadcrumb
    * 'fixed-page-footer' - Fixes footer
    * 'container'         - boxed layout mode (non-responsive: will not work with fixed-navigation & fixed-ribbon)
-->



<body class="smart-style-3" border="1" width="100%">

<!-- #NAVIGATION -->
<!-- Left panel : Navigation area -->
<!-- Note: This width of the aside area can be adjusted through LESS/SASS variables -->
<aside id="left-panel">

    <!-- User info -->
    <div class="login-info">
				<span> <!-- User image size is adjusted inside CSS, it should stay as is -->


					<a href="javascript:void(0);" id="show-shortcut" data-action="toggleShortcut">
						<img src="<?php echo getFoto()  ;?>" alt="<?php echo [usr_login]; ?>"  />
						<span class="txt-color-white">

							<?php echo "<h4><b>Olá, $login</b></h4>";  ?>
						</span>
						<i class="fa fa-angle-down"></i>
					</a>

				</span>
    </div>

    <style>

        .login-info img {
            width: 30px;
            height: auto;
            display: inline-block;
            vertical-align: middle;
            margin-top: 1px;
            margin-right: 5px;
            margin-left: 0;
            border-left: 3px solid #fff;
        }

    </style>
    <!-- end user info -->

    <!-- NAVIGATION : This navigation is also responsive

    To make this navigation dynamic please make sure to link the node
    (the reference to the nav > ul) after page load. Or the navigation
    will not initialize.
    -->
    <nav>
        <!--
        NOTE: Notice the gaps after each icon usage <i></i>..
        Please note that these links work a bit different than
        traditional href="" links. See documentation for details.
        -->

        <style>

            .scroll-wrapper {
                -webkit-overflow-scrolling: touch;
                overflow-y: scroll;
                height:100%;


            }

            .scroll-wrapper iframe {

            }
        </style>

        <ul>
            <li><a href="#" title="">
                </a>
            </li>
        </ul>


        <ul>
            <li><a href="bl_menu_ima_novo.php?src_frame=bl_inicio" title="inicio">
                    <i class="fa fa-lg fa-fw fa-home" style='color:#bd282f' ></i><span class="menu-item-parent">Início</span></a>
            </li>
        </ul>

        <ul>
            <li><a href="bl_menu_ima_novo.php?src_frame=controle_filtro_wp_ped_venda" title="Pedidos">
                    <i class="fa fa-lg fa-fw fa-shopping-basket" style='color:#bd282f' ></i><span class="menu-item-parent">Pedidos</span></a>
            </li>
        </ul>

        <ul>
            <li ><a href="bl_menu_ima_novo.php?src_frame=controle_filtro_wp_nota_fiscal" title="Faturamento">
                    <i class="fa fa-lg fa-fw fa-money" style='color:#bd282f' ></i><span class="menu-item-parent">Faturamento</span></a>

            </li>
        </ul>

        <ul>
            <li><a href="bl_menu_ima_novo.php?src_frame=ctrl_filtro_estoq_unificado" title="Produtos">
                    <i class="fa fa-lg fa-fw fa-search" style='color:#bd282f' ></i><span class="menu-item-parent">Produtos</span></a>

            </li>
        </ul>

        <ul>
            <li><a href="bl_menu_ima_novo.php?src_frame=ctrl_book_customizado" title="Books Customizados">
                    <i class="fa fa-lg fa-fw fa-book" style='color:#bd282f' ></i><span class="menu-item-parent">Books Customizados</span></a>

            </li>
        </ul>

        <ul>
            <li><a href="bl_menu_ima_novo.php?src_frame=<?php echo $linkCarrinho;?>" title="Carrinho">
                    <i class="fa fa-lg fa-fw fa-shopping-cart" style='color:#bd282f' ></i><span class="menu-item-parent">Carrinho</span></a>

            </li>
        </ul>

        <ul>
            <li>
                <a href="#"><i class="fa fa-lg fa-fw fa-user" style='color:#bd282f'></i> <span class="menu-item-parent">Clientes</span></a>
                <ul>

                    <li>
                        <a href="bl_menu_ima_novo.php?src_frame=cons_emitente_rep" >Consultar Clientes</a>
                    </li>

                    <?php if([tipoUsuario] == 6){ ?>
                        <li>
                            <a href="bl_menu_ima_novo.php?src_frame=ctrl_cliente_novo">Novo Cliente</a>
                        </li>
                    <?php } ?>
                    <?php if([tipoUsuario] == 3 or [tipoUsuario] == 4){ ?>
                        <li>
                            <a href="bl_menu_ima_novo.php?src_frame=menu_consultas_ger" target="_blank">Novo Cliente</a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        </ul>

        <!--<ul>
            <li><a href="bl_menu_ima_novo.php?src_frame=bl_ponte_cons_cliente" title="Clientes">
                    <i class="fa fa-lg fa-fw fa-user" style='color:#bd282f' ></i><span class="menu-item-parent">Clientes</span></a>

            </li>
        </ul>-->

        <ul>
            <li><a href="bl_menu_ima_novo.php?src_frame=cons_tit_acr" title="titulos">
                    <i class="fa fa-lg fa-fw fa-clipboard" style='color:#bd282f'></i><span class="menu-item-parent">Títulos</span></a>

            </li>
        </ul>

        <ul>
            <li><a href="bl_menu_ima_novo.php?src_frame=cons_pub_noticias_portal_inicio" title="Arquivos">
                    <i class="fa fa-lg fa-fw fa-paperclip" style='color:#bd282f'></i><span class="menu-item-parent">Arquivos</span></a>

            </li>
        </ul>


        <?php if(([aprovador] == 1 )){ ?>
            <ul>
                <li><a href="../bl_menu_aprovacao" title="Avaliar Pedidos de Venda">
                        <i class="fa fa-lg fa-fw fa-tasks" style='color:#bd282f'></i><span class="menu-item-parent">Avaliar Pedidos</span></a>

                </li>
            </ul>
        <?php }?>


        <?php if([tipoUsuario] == 3 or [tipoUsuario] == 4){  ?>
            <ul>
                <li>
                    <a href="#"><i class="fa fa-lg fa-fw fa-cog" style='color:#bd282f'></i> <span class="menu-item-parent">Configurações</span></a>
                    <ul>
                        <?php if([tipoUsuario] == 4){  ?>
                            <li>
                                <a href="bl_menu_ima_novo.php?src_frame=menu_seguranca" >Segurança</a>
                            </li>
                        <?php }?>
                        <li>
                            <a href="bl_menu_ima_novo.php?src_frame=grid_PUB_pp_container_1">Container</a>
                        </li>
                        <li>
                            <a href="bl_menu_ima_novo.php?src_frame=menu_precos">Preços</a>
                        </li>
                        <li>
                            <a href="bl_menu_ima_novo.php?src_frame=menu_item_arquivo">Itens x Imagens</a>
                        </li>
                        <li>
                            <a href="bl_menu_ima_novo.php?src_frame=cons_metas_repres">Metas</a>
                        </li>
                        <!--<li>
                            <a href="chartjs.html">UF x Repres</a>
                        </li>-->
                        <li>
                            <a href="bl_menu_ima_novo.php?src_frame=cons_PUB_noticias_portal">Arquivos</a>
                        </li>

                    </ul>
                </li>
            </ul>
        <?php }?>



    </nav>

    <span class="minifyme" data-action="minifyMenu"> <i class="fa fa-arrow-circle-left hit"></i> </span>

</aside>
<!-- END NAVIGATION -->

<!-- #MAIN PANEL -->

<div id="main" role="main" height="100%">

    <!-- RIBBON -->
    <div id="ribbon">

        <style>
            #ribbon {
                min-height: 0px;
                height:5px;
                background: #fff !important;
                padding: 0 1px;
                position: relative;
            }

            #header {
                background: #f7f7f7 !important;
            }

            .smart-style-3 #logo-group span#activity, .smart-style-3 .btn-header>:first-child>a {
                background: #f3f3f3;
                background-image: -moz-linear-gradient(top,#f3f3f3,#000000);
                background-image: -webkit-gradient(linear,0 0,0 100%,from(#f3f3f3),to(#000000));
                background-image: -webkit-linear-gradient(top,#f3f3f3,#000000);
                background-image: -o-linear-gradient(top,#f3f3f3,#000000);
                background-image: linear-gradient(to bottom,#f3f3f3,#2F4F4F);
                color: #fff!important;
                border: 1px solid #000000;
                text-shadow: #000000 0 -1px;
            }

        </style>
        <!-- breadcrumb -->
        <ol class="breadcrumb"  >

            <li>


            </li>

        </ol>





    </div>

    <div class="scroll-wrapper">
        <iframe height="1200px" name="CMIframe" id="CMIframe" class="embed-responsive-item" src="<?php echo $src; ?>" style="width: 100%; border-style: none;"></iframe>
    </div>

</div>



</div>
<!-- END #MAIN PANEL -->

<!-- #PAGE FOOTER -->



<div class="page-footer">
    <div class="row">

        <style>
            .page-footer {
                height: 40px;
                padding: 15px 13px 0;
                padding-left: 233px;
                border-top: 0px solid #CECECE;
                background: #fff !important;
                width: 100%;
                position: absolute;
                display: block;
                bottom: 0;
            }

            .smart-style-3 #hide-menu i {
                color: #fff!important;
            }


        </style>

        <div class="col-xs-12 col-sm-6">
            <span class="txt-color-white"><span class="hidden-xs"> </span>  </span>
        </div>
    </div>
    <!-- end row -->
</div>
<!-- END FOOTER -->

<!-- #SHORTCUT AREA : With large tiles (activated via clicking user name tag)
     Note: These tiles are completely responsive, you can add as many as you like -->

<style>
    .jarvismetro-tile .iconbox span {
        text-align: center;
    }
</style>

<div id="shortcut" align="center">
    <ul>
        <li>
            <a href="../ctrl_foto/" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-camera fa-4x"></i> <span><b> Foto Perfil </b></span> </span> </a>
        </li>

        <?php if([tipoUsuario] == 3 or [tipoUsuario] == 4){ ?>

            <li>
                <a href="../ctrl_trocar_perfil/" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i class="fa fa-user fa-4x"></i> <span><b>Alterar Perfil </b></span> </span> </a>
            </li>

        <?php } ?>

        <li>
            <a href="../form_config/" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i class="fa fa-key fa-4x"></i> <span><b>Alterar Senha </b></span> </span> </a>
        </li>
    </ul>
</div>
<!-- END SHORTCUT AREA -->

<!--================================================== -->

<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)
<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>-->


<!-- #PLUGINS -->
<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    if (!window.jQuery) {
        document.write('<script src="<?php echo sc_url_library('prj','smartAdmin_seed','seed/js/libs/jquery-3.2.1.min.js');?>"><\/script>');
    }
</script>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    if (!window.jQuery.ui) {
        document.write('<script src="<?php echo sc_url_library('prj','smartAdmin_seed','seed/js/libs/jquery-ui.min.js');?>"><\/script>');
    }
</script>

<!-- IMPORTANT: APP CONFIG -->
<!--<script src="js/app.config.js"></script>-->
<script src="<?php echo sc_url_library('prj','smartAdmin_seed','seed/js/app.config.js');?>"></script>

<!-- BOOTSTRAP JS -->
<!--<script src="js/bootstrap/bootstrap.min.js"></script>-->
<script src="<?php echo sc_url_library('prj','smartAdmin_seed','seed/js/bootstrap/bootstrap.min.js');?>"></script>

<!--if IE 8>
    <h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
<!endif-->

<!-- MAIN APP JS FILE -->
<!--<script src="js/app.min.js"></script>-->
<script src="<?php echo sc_url_library('prj','smartAdmin_seed','seed/js/app.min.js');?>"></script>

<!-- Your GOOGLE ANALYTICS CODE Below -->
<script>

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-XXXXXXXX-X']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();

</script>

</body>
</html>
<?php
