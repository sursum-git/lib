<?php
function verExistTbConsPorBanco($banco,$tabela)
{
    $sql = "SELECT table_name as tb FROM
        information_schema.tables WHERE table_schema = '$banco'
    AND table_name = '$tabela'";
    $aResult = getRegsSqlLivre($sql,'tb','geral');
    return is_array($aResult);
}


function verExistTbCons($tabela)
{
    $banco = getNomeBdUsuarioCorrente();
    $sql = "SELECT table_name as tb FROM information_schema.tables WHERE table_schema = '$banco'
    AND table_name = '$tabela'";
    $aResult = getRegsSqlLivre($sql,'tb','geral');
    return is_array($aResult);
}

function sincrTbWp()
{
    $lExiste = verExistTbCons('wp');
    if(! $lExiste){
        criarTbWp();
    }

}
function sincrTbWpEstoquePreco()
{
    $lExiste = verExistTbCons('wp_estoque_preco_000');
    if(! $lExiste){
        criarTbWpEstoquePreco();
    }

}


function criarTbWp()
{
$cmd = "CREATE TABLE wp(
cod_wp varchar(50) NOT NULL,
login varchar(30) DEFAULT NULL,
data_hora datetime DEFAULT NULL,
pagina varchar(100) DEFAULT NULL,
filtro varchar(2000) DEFAULT NULL,
tabela varchar(100) DEFAULT NULL,
PRIMARY KEY (cod_wp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
sc_exec_sql($cmd,"dinamico");
}
function criarTbWpEstoquePreco()
{
    $cmd= "CREATE TABLE wp_estoque_preco_000 (
  wp_estoque_preco_id int(11) NOT NULL AUTO_INCREMENT,  
  cod_estabel varchar(3) DEFAULT NULL,
  it_codigo varchar(20) DEFAULT NULL,
  cod_refer varchar(5) DEFAULT NULL,
  qt_pedido decimal(18,2) DEFAULT NULL,
  qt_saldo decimal(18,2) DEFAULT NULL,
  qt_disponivel decimal(18,2) DEFAULT NULL,
  qt_reservada decimal(18,2) DEFAULT NULL,
  qt_programada decimal(18,2) DEFAULT NULL,
  qt_solicitada decimal(18,2) DEFAULT NULL,
  ped_web_id int(11) DEFAULT NULL,
  desc_item varchar(150) DEFAULT NULL,
  nr_container int(11) DEFAULT NULL,
  preco_informado decimal(18,2) DEFAULT NULL,
  dt_prev_chegada date DEFAULT NULL,
  liquida_ima decimal(8,2) DEFAULT NULL,
  qt_saldo_venda decimal(18,2) DEFAULT NULL,
  qt_carrinho decimal(18,2) DEFAULT NULL,
  num_id_liquida_ima int(11) DEFAULT NULL,
  cod_controle_preco int(11) DEFAULT NULL,
  desc_preco varchar(100) DEFAULT NULL,
  log_atualizado int(1) DEFAULT NULL,
  preco_liquida_ima decimal(18,10) DEFAULT NULL,
  qt_em_digitacao decimal(18,2) DEFAULT NULL,
  vl_informado decimal(18,2) DEFAULT NULL,
  cod_refer_ordem int(11) DEFAULT NULL,
  dt_hr_criacao datetime DEFAULT NULL,
  vl_real decimal(18,10) DEFAULT NULL,
  vl_dolar decimal(18,10) DEFAULT NULL,
  vl_preco_prazo decimal(18,10) default null,
  tb_preco_id int(11) DEFAULT NULL,
  num_moeda int(11) DEFAULT NULL,
  agrup_pedido int(11) DEFAULT NULL,
  log_divide_comis int(1) DEFAULT NULL,
  perc_comis_vend   decimal(5,2) DEFAULT NULL,
  perc_comis_repres decimal(5,2) DEFAULT NULL,    
  PRIMARY KEY (wp_estoque_preco_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    sc_exec_sql($cmd,"dinamico");
}
function getAgrupPedido($numMoeda,$nrContainer,$tbPreco,$logDivideComis,$percComisVend,$percComisRep)
{
    //echo "<h1>$numMoeda,$nrContainer,$tbPreco,$logDivideComis,$percComisVend,$percComisRep</h1>";
    $percComisVend = tratarNumero($percComisVend);
    $percComisRep  = tratarNumero($percComisRep);

    $agrup = 0 ;
    if(strtolower($numMoeda) == 'real'){
        $numMoeda = 1;
    }
    if(strtolower($numMoeda) == 'dolar'){
        $numMoeda = 2;
    }
    switch($numMoeda){
        case 1:
            $agrup += 1;
            break;
        case 2:
            $agrup += 2;
    }

    switch($nrContainer){
        case 0:
            $agrup += 4;
            break;
        default:
            $agrup += 8;
    }
    switch($logDivideComis){
        case 0:
            $agrup += 16;
            break;
        case 1:
            $agrup += 32;
            break;
    }
    $agrup += ($tbPreco + 1) * 64;
    $agrup += ($percComisVend + 1) * 128;
    $agrup += ($percComisRep  + 1) * 256;


    return $agrup;

}
/*function criarPesquisaMysql($filtro,$pagina)
{

}*/
?>
