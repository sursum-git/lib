<?php
    function inserirHistAval($estab, $nrPed, $tipoAprov, $sitAval)
    {
        $usuario = getUsuarioERP([usr_login]);
        $cmd = "insert into PUB.hist_aval_ped_venda(hist_aval_ped_venda_id,cod_estab,nr_pedido, cod_tipo_aprov, ind_situacao, dt_hr_aval, cod_usuario)
        values(pub.seq_hist_aval_ped_venda.NEXTVAL,'$estab', $nrPed,$tipoAprov,$sitAval,SYSTIMESTAMP,'$usuario')";
        sc_exec_sql($cmd,"especw");
        
    }
    function avaliarPedido($listaPedidos,$motivo,$origem,$situacao,$tipo,$descMotivo,$codUsuario)
    {
        $api = getApiHistAvalPedVenda();
        $aQueryParam = array('lista_pedidos_aval'=>$listaPedidos,
            'num_motivo'=> $motivo,
            'num_origem' => $origem,
            'num_situacao'=>$situacao,
            'num_tipo'=>$tipo,
            'desc_motivo'=>$descMotivo,
            'cod_usuario'=>$codUsuario);

        $ret = getApiTotvs($api,'GET',$aQueryParam);
        $ret = getValsArrayMultiDin($ret,'codigo,descricao');
        if($origem == 2){
            tratarMsgAvalPedido($ret,$situacao);
        }



    }
    function tratarMsgAvalPedido($ret,$situacao){

        foreach ($ret as $dados){

            $msgAval = $dados['descricao'];
            $codMsg  = $dados['codigo'];
            //$msgIni = substr($msgAval,0,1);
            if($codMsg <> 0)
            {
                $msg = str_replace("erro-","",$msgAval);
                echo'<tr><td><div class="alert alert-danger"><h4>ERRO</h4></div></td><td>'; echo $msg;'</td></tr>';

            }else
            {
                $msg = str_replace("aviso-","",$msgAval);
                echo'<tr><td><div class="alert alert-success"><h4>OK</h4></div></td><td>'; msgRetornoAvalPedido($situacao,$msg); '</td></tr>';

            }

        }

    }

    function msgRetornoAvalPedido($situacao,$msg=''){


        switch ($situacao){

            case 1:


                echo '<div class="alert alert-success" role="alert">  <h4 align="center">O pedido ';echo "$msg"; echo ' foi APROVADO !!!  </h4>  </div>';
                break;

            case 2:

                echo '<div class="alert alert-danger">  <h4 align="center"> O pedido ';echo "$msg"; echo' foi REPROVADO !!!  </h4>  </div>' ;
                echo "<hr>";
                break;

            case 3:

                echo '<div class="alert alert-warning" role="alert">  <h4 align="center"> O pedido ';echo "$msg"; echo' foi para ANÁLISE !!!  </h4>  </div>' ;
                echo "<hr>";
                break;

            case 4:

                echo '<div class="alert alert-warning" role="alert">  <h4 align="center"> Foi prorrogada a análise do pedido ';echo "$msg"; echo'!!!  </h4>  </div>' ;
                echo "<hr>";
                break;

            case 5:

                echo '<div class="alert alert-warning" role="alert">  <h4 align="center"> Foi solicitada alteração no pedido ';echo "$msg"; echo'!!!  </h4>  </div>' ;
                echo "<hr>";
                break;

        }
    }

function getSitAvalPedWeb($numPed){

        $aRet = getReg('espec','hist_aval_ped_venda',
            'ped_web_id',
            $numPed,
            'ind_situacao,cod_usuario,dt_hr_limite_aval',
        " hist_aval_ped_venda_relac_id = 0");
        return $aRet;
}

function getDescSitAvalPed($sitParam){

    $descPed = 'Não definido';
        switch($sitParam){

            case '0':
                $descPed = 'Pendente';
                break;
            case '1':
                $descPed = 'Aprovado';
                break;
            case '2':
                $descPed = 'Reprovado';
                break;
            case '3':
                $descPed = 'Em Análise';
                break;
            case '4':
                $descPed = 'Em Análise Prorrogação';
                break;
            case '5':
                $descPed = 'Solicitada Alteração';
                break;
        }

    return $descPed;
}




?>