<?php
//__NM__Datas__NM__FUNCTION__NM__//
//__NM__Datas__NM__FUNCTION__NM__//
function dataCorrenteProgress()
{

    $dt = getdate();
    $data_hora = $dt['mon']."/".$dt['mday']."/".$dt['year']." ".$dt['hours'].":".$dt['minutes'].":".$dt['seconds'];
    return $data_hora;
}
function getDatasMesCorrente()
{
    $aRetorno = array();
    $dt =getDate();
    $proximoAno = 0;
    //echo "<h1>data</h1>";
    //var_dump($dt);
    $dtIni = $dt['mon']."/01/".$dt['year'];
    //echo "<h1>data inicial: $dtIni</h1>";
    $proximoMes = $dt['mon'] + 1;
    if($proximoMes == 13){
        $proximoMes = 01;
        $proximoAno = $dt['year'] + 1;
    }else{
        $proximoAno = $dt['year'];
    }
    if($proximoMes < 10){
        $proximoMes = '0'.$proximoMes;
    }

    $diaProximoMes = $proximoMes.'/01/'.$proximoAno;
    //echo "<h1>dia proximo mes: $diaProximoMes</h1>";
    $dtFim = sc_date($diaProximoMes, "mm/dd/aaaa", "-", 1, 0, 0);
    //echo "<h1>data fim: $dtFim</h1>";
    $aRetorno[] = array("dtIni" => $dtIni, "dtFim" => $dtFim);
    return $aRetorno;
}

function getParteDtCorrente($parte)
{
    $retorno = "";
    switch ($parte){
        case 'dia':
            $retorno = 'mday';
            break;
        case 'mes':
            $retorno = 'mon';
            break;
        case 'ano':
            $retorno = 'year';
            break;
        case 'hora':
            $retorno = 'hours';
            break;
        case 'minuto':
            $retorno = 'minutes';
            break;
        case 'segundo':
            $retorno = 'seconds';
            break;

    }
    $hoje = getdate();
    return $hoje[$retorno];
}

function diasUteisMes($aDataParam = '')
{    //var_dump($aDataParam);
    $qtDiasPassados = 0;
    $qtDiasRestantes = 0;
    if($aDataParam == ''){
        $aDataParam = array();
        $aDataParam['dia'] = getParteDtCorrente('dia');
        $aDataParam['mes'] = getParteDtCorrente('mes');
        $aDataParam['ano'] = getParteDtCorrente('ano');
    }
    //var_dump($aDataParam);
    $dia = $aDataParam['dia'];
    $mes = $aDataParam['mes'];
    $ano = $aDataParam['ano'];
    $qtDiasMes = cal_days_in_month ( CAL_GREGORIAN , $mes , $ano );
    for($i=0;$i < $qtDiasMes;$i++){
        $j = $i + 1;
        $diaSemana = jddayofweek(cal_to_jd(CAL_GREGORIAN, $mes, $j, $ano) , 0);
        if($j < 10){
            $diaCorrente = "0{$j}";
        }else{
            $diaCorrente = $j;
        }
        $diaUtilCalend = getUtilizDiaCalend("{$ano}-{$mes}-{$diaCorrente}");
        //echo "<h1>{$ano}-{$mes}-{$diaCorrente} -> $diaUtilCalend  </h1>";

        if($diaSemana > 0 and $diaSemana < 6 and $diaUtilCalend == 1){ //desconsidera sabado(6) e domingo(0)
            if($j <= $dia){
                $qtDiasPassados++;
            }else{
                $qtDiasRestantes++;
            }
        }
    }
    $qtDiasUteis = $qtDiasPassados + $qtDiasRestantes;
    $retorno = array('qt_dias_uteis' => $qtDiasUteis,
        'qt_dias_passados' => $qtDiasPassados,
        'qt_dias_restantes' => $qtDiasRestantes);
    return $retorno;
}

/**
 * @param $data
 * @return string
 */
function getQuinzenaMesAnoData($data)
{
    //considera que a data passada tem o formato aaaa-mm-dd
    $dia = substr($data,8,2);
    $mes = substr($data,5,2);
    $ano = substr($data,0,4);
    if($dia <= 15){
        $quinzena = "1ª Quinzena";
    }else{
        $quinzena = "2ª Quinzena";
    }
    $mes = convMes($mes);
    $retorno = "$quinzena/$mes";
    return $retorno;
}

/**
 * @param $tipo(abrev - nome abreviado do mes, completo - nome completo
 * @param $mes
 */
function convMes($mes,$tipo='abrev')
{
    $retAbrev = '';
    $retCompl = '';
    $retorno  = '';
    switch ($mes){
        case '01':
            $retAbrev = "Jan";
            $retCompl = "Janeiro";
            break;
        case '02':
            $retAbrev = "Fev";
            $retCompl = "Fevereiro";
            break;
        case '03':
            $retAbrev = "Mar";
            $retCompl = "Março";
            break;
        case '04':
            $retAbrev = "Abr";
            $retCompl = "Abril";
            break;
        case '05':
            $retAbrev = "Mai";
            $retCompl = "Maio";
            break;
        case '06':
            $retAbrev = "Jun";
            $retCompl = "Junho";
            break;
        case '07':
            $retAbrev = "Jul";
            $retCompl = "Julho";
            break;
        case '08':
            $retAbrev = "Ago";
            $retCompl = "Agosto";
            break;
        case '09':
            $retAbrev = "Set";
            $retCompl = "Setembro";
            break;
        case '10':
            $retAbrev = "Out";
            $retCompl = "Outubro";
            break;
        case '11':
            $retAbrev = "Nov";
            $retCompl = "Novembro";
            break;
        case '12':
            $retAbrev = "Dez";
            $retCompl = "Dezembro";
            break;
    }
    switch($tipo)
    {
        case 'abrev':
            $retorno = $retAbrev;
            break;
        case 'completo':
            $retorno = $retCompl;
            break;

    }

    return $retorno;
}

function getAgora($formato="en")
{
    $agora = '';
    switch ($formato){
        case 'en':
            $agora = date('Y-m-d H:i:s');
            break;
        case 'br':
            $agora = date('d/m/Y H:i:s');
            break;
        default:
            $agora =date($formato);

    }
    return $agora;
}

function compararDtHoraForm($dtHrForm,$dtHrPhp)
{
    $dtHrForm = str_replace(':000','',$dtHrForm);
    $dtHrPhp = str_replace('-','',$dtHrPhp);
    return $dtHrForm == $dtHrPhp;
}
function convDataEnToBr($data)
{
    return sc_date_conv($data,"aaaa-mm-dd","dd/mm/aaaa");
}

?>