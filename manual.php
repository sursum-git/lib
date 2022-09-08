<?php

function getManuaisPorProg($prog)
{
    $tb = 'programas_versoes pv';
    $cps ='pv.cod_programa_versao as versao, pvm.cod_prog_versao_manual as id, pvm.titulo as titulo,
     pvm.url as url, 
     pvm.dt_hr_publicacao as dt_hr_publicacao,
     pvm.dt_hr_aviso_novo as dt_hr_aviso_novo';
    $condicao = "p.codigo = '$prog' and pv.cod_programa_versao in (select max(pv1.cod_programa_versao) from programas_versoes pv1
  where pv1.cod_programa = p.cod_programa)";
    $inner = " inner join prog_versao_manuais pvm on pv.cod_programa_versao = pvm.cod_programa_versao
            inner join programas p on p.cod_programa = pv.cod_programa";
    $aRegs = getDados('multi',$tb,$cps,$condicao,"ticontrole", $inner);
    return $aRegs;
}

function getVersoesPorCodProg($codProg)
{
    $tb = 'programas p';
    $cps = 'pv.cod_versao as cod_versao, pv.objetivo as objetivo,
     Convert(varchar(10)|pv.dt_implentacao|103) as dt_implementacao';
    $condicao = "p.codigo = '$codProg' ";
    $inner = 'inner join programas_versoes pv on p.cod_programa = pv.cod_programa';
    $aRegs = getDados('multi',$tb,$cps,$condicao,'ticontrole',$inner);
    return $aRegs;


}
function getProgramasComManual()
{
    $tb = 'programas p';
    $cps = ' distinct p.codigo as codigo, p.titulo as titulo';
    $condicao = "  pv.cod_programa_versao in (select max(pv1.cod_programa_versao) from programas_versoes pv1
  where pv1.cod_programa = p.cod_programa) and p.cod_modulo in (select cod_modulo from modulos
where modulos.cod_sistema = 5)";
    $inner = 'inner join programas_versoes pv on p.cod_programa = pv.cod_programa
inner join prog_versao_manuais pvm on pv.cod_programa_versao = pvm.cod_programa_versao';
    $aRegs = getDados('multi',$tb,$cps,$condicao,'ticontrole',$inner);
    return $aRegs;

}
function criarMenuSCManuaisPorProg($menu,$apl)
{
    $tituloProg = getTitPorCodigo($apl);
    //$tituloProg = utf8_encode($tituloProg);
    $tituloProg = strtolower($tituloProg);
    $tituloProg = ucwords($tituloProg);
    $nomeAplicacao = "$apl - $tituloProg";
    sc_appmenu_reset($menu);
    sc_appmenu_create($menu);
    sc_appmenu_add_item($menu, "prog", "", $tituloProg, "bl_manual_dinamico","nivel=prog;codigo=$apl;nome=$nomeAplicacao");
    $aManuais = getManuaisPorProg($apl);
    foreach ($aManuais as $reg){
        $id = $reg['id'];
        $tituloManual = $reg['titulo'];
        //$tituloManual = utf8_encode($tituloManual);
        sc_appmenu_add_item($menu, "item_{$id}", "prog", $tituloManual, "bl_manual_dinamico",
            "nivel=manual;codigo=$id;nome=$tituloManual");
    }
    sc_appmenu_add_item($menu, "tudo", "", 'Todos os Programas', "bl_manual_dinamico");
}

function desenharPgProg($codProg)
{

    $aProg = getRegProgramaPorCodigo($codProg);

    /*
     * descrição da logica
     * 1- busca o titulo e a descrição do programa e mostra no cabeçalho
     * 2- busca as versões existentes e coloca em uma aba
     * 3- busca os manuais existentes para a última versão e coloca na outra aba
     * */




function desenharInfsPrograma($aProg)
{
    

}
function desenharPgManual($idManual)
{
    /*
     * descrição da logica
     * 1- busca o titulo do programa e mostra no cabeçalho(deixar link para que a pessoa consiga clicar e ir para
     * o manual do programa como um todo )
     * 2- busca a versão e seu objetivo
     * 3- busca o manual e suas descrições gerais
     * 4- desenha o conteudo com as seguintes regras:
     * a) se o tipo for video por arquivo desenha uma tag video
     * b) se o tipo for video por link desenha uma iframe(serão utilizados videos do youtube)
     * c) se o tipo for link de conteúdo de texto sera desenha uma iframe
     * d) se o tipo for conteúdo estruturado será buscado a estrutura do conteúdo
     *  e colocada em ordem ate o último nível(verificar necessidade de colocar esta opção)
     * */

}

?>
