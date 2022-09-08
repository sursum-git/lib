<?php

        $erro = array();
        $msg = '';
        $lErro = 0;
        $lLock = 0;
        $link =  '..\cons_ped_web_carrinho\\';

        $usuario = getUsuarioLock('peds_web','espec');

        $tpUsuario = getTipoUsuarioCorrente();

        //echo "<h1>$tpUsuario</h1>";

        if($usuario <> ''){
            $lLock = 1;
            $msgLock = "Atenção!! Esse registro está em uso no momento, tente salvar novamente clicando no link:"
              .'<a href="bl_efetivar_pedido.php?ped_corrente='.[ped_corrente].' "><h3 style="text-decoration:underline; "> Efetivar Pedido</h3></a>';

        }else{
            $msgLock = '';
            limparCNPJ([ped_corrente]);
            $aReg = getRegPedWeb([ped_corrente]);
            $logNovoCliente 		    = $aReg[0]['log_novo_cliente'];
            $cnpjNovoCliente 		    = $aReg[0]['cnpj_novo_cliente'];
            $logNovoClienteTriang 	    = $aReg[0]['log_novo_cliente_triang'];
            $cnpjNovoClienteTriang	    = $aReg[0]['cnpj_novo_cliente_triang'];
            $logNovoTransp			    = $aReg[0]['log_novo_transp'];
            $logNovoTranspRedesp	    = $aReg[0]['log_novo_transp_redesp'];
            $telefoneNovoTransp		    = $aReg[0]['telefone_novo_transp'];
            $telefoneNovoTranspRedesp	= $aReg[0]['telefone_novo_transp_redesp'];
            $nrContainer				= $aReg[0]['nr_container'];
            $loginPreposto              = $aReg[0]['login_preposto'];


            if($tpUsuario <> 5){
                setPrepostoPedsWeb([ped_corrente],'');
            }



            //tenta atualizar o codigo do cliente principal no caso de ser um cliente novo
            if($logNovoCliente == 1){
                $aCliente = getCodigoClienteNovo($cnpjNovoCliente);
                $cliente  = $aCliente['cod_cliente'];
                $msgCliente = $aCliente['msg'];
                if($cliente <> 0 ){
                    if($msgCliente == ''){
                        atualizarCodClienteNovo([ped_corrente],$cliente,'principal');
                    }else{
                        $erro[] ="<h4>$msgCliente</h4>";
                        $lErro = 1;
                    }
                }
            }

            //tenta atualizar o codigo do cliente triangular no caso de ser um cliente novo
            if($logNovoClienteTriang == 1){
                $aClienteTriang = getCodigoClienteNovo($cnpjNovoClienteTriang);
                $clienteTriang  = $aClienteTriang['cod_cliente'];
                $msgClienteTriang = $aClienteTriang['msg'];
                if($clienteTriang <> 0 ){
                    if($msgClienteTriang == ''){
                        atualizarCodClienteNovo([ped_corrente],$clienteTriang,'triangular');
                    }else{
                        $erro [] ="<h4>$msgClienteTriang</h4>";
                        $lErro = 1;
                    }
                }
            }

            //busca os dados do registro após atualizações
            $aReg = getRegPedWeb([ped_corrente]);
            $clienteId      			= $aReg[0]['cliente_id'];
            $clienteTriangId   			= $aReg[0]['cliente_triang_id'];
            $logNovoCliente 			= $aReg[0]['log_novo_cliente'];
            $cnpjNovoCliente 			= $aReg[0]['cnpj_novo_cliente'];
            $logNovoClienteTriang 		= $aReg[0]['log_novo_cliente_triang'];
            $cnpjNovoClienteTriang		= $aReg[0]['cnpj_novo_cliente_triang'];
            $tipoFrete					= $aReg[0]['cod_tipo_frete'];
            $transpId					= $aReg[0]['transp_id'];
            $transpRedespId				= $aReg[0]['transp_redesp_id'];
            $logNovoTransp				= $aReg[0]['log_novo_transp'];
            $logNovoTranspRedesp    	= $aReg[0]['log_novo_transp_redesp'];
            $telefoneNovoTransp			= $aReg[0]['telefone_novo_transp'];
            $telefoneNovoTranspRedesp 	= $aReg[0]['telefone_novo_transp_redesp'];
            $diasCondPagto				= $aReg[0]['dias_cond_pagto_esp'];
            $logAVista					= $aReg[0]['log_a_vista'];
            $logOperacTriang            = $aReg[0]['log_operac_triang'];
            $emailsAdicionais           = $aReg[0]['emails_adicionais'];
            $codFinalidade              = $aReg[0]['cod_finalidade'];
            $codPrioridade              = $aReg[0]['cod_prioridade'];
            $indSitPedWeb               = $aReg[0]['ind_sit_ped_web'];
            $logPedidoManual            = $aReg[0]['log_pedido_manual'];
            $logVista                   = $aReg[0]['log_vista'];
            $logDtFixa                  = $aReg[0]['log_dt_fixa'];
            $comentario                 = $aReg[0]['comentario'];
            $logDivideComis             = $aReg[0]['log_divide_comissao'];
            $numAgrup                   = $aReg[0]['num_agrup'];
            $logPercNegoc               = $aReg[0]['log_perc_negoc'];
            $percComisNegoc             = $aReg[0]['perc_comis_negoc'];
            $percComisCalc              = $aReg[0]['perc_comis'];

            $msg1 =  validacoesDadosPedWeb($logOperacTriang,
                $logNovoCliente,
                $clienteId,
                $cnpjNovoCliente,
                $logNovoClienteTriang,
                $cnpjNovoClienteTriang,
                $clienteTriangId,
                $emailsAdicionais,
                $codFinalidade,
                $codPrioridade,
                $indSitPedWeb,
                $logPedidoManual,
                $logVista,
                $diasCondPagto,
                $logDtFixa,
                $comentario,
                $logAVista,
                $logDivideComis,
                $numAgrup);


            $msg = util_incr_valor($msg,$msg1,"</br>");

            $msg1 = validacoesComisPedWeb($logPercNegoc,
                $percComisNegoc,
                $percComisCalc);

            $msg = util_incr_valor($msg,$msg1,"</br>");

            $msg1 = validacoesFreteTranspPedweb($tipoFrete, $transpId, $logNovoTransp,
                $transpRedespId, $logNovoTranspRedesp,
                $telefoneNovoTransp, $telefoneNovoTranspRedesp);

            $msg = util_incr_valor($msg,$msg1,"</br>");


            $aVals 		= valorizarItensRefPedWeb([ped_corrente],$nrContainer,$diasCondPagto);
            $vlTotal 	= $aVals['total'] ;
            $qtSemPreco = $aVals['qt_sem_preco'];

            if($vlTotal == 0){
                $msg1 = "<h4>Pedido não pode ter valor total zerado para ser efetivado</h4>";
                $msg = util_incr_valor($msg,$msg1,'');

            }


        }

        if($msg == '' and $lLock == 0){
            //verifica se o cliente tem e-mail cadastrado e caso não tenha preenche as observações
            efetivarPedido([ped_corrente]);
            $email = getEmailCliente($clienteId);
            if($email == '' and $clienteId <> 0){
                //$erro[] = '<h4>E-mail Comercial Não Cadastrado.</h4>';
                setObsSemEmail([ped_corrente]);
            }
            if($telefoneNovoTransp <> '' or $telefoneNovoTranspRedesp <> ''){
                setObsTelefonesTransp([ped_corrente],$telefoneNovoTransp,$telefoneNovoTranspRedesp);
            }
            limparGlobCondPagto();


            echo '<div class="alert alert-success" role="alert">  <h3><i class="fa fa-lg fa-fw fa-check-circle" style="color:#468847" size="5x"></i>  Pedido Efetivado com Sucesso  </h3>  </div>'  ;


            //$link =  '..\cons_ped_web_carrinho\\';
        }else{

            echo '<div class="alert alert-danger" role="alert"> 
                        <h3>
                                <i class="fa fa-lg fa-fw fa-exclamation-circle" style="color:#B94A48" size="7x"></i>
                                 NÃO FOI POSSÍVEL EFETIVAR O SEU PEDIDO !!! 
                         </h3>Devido a problemas com a integração com o ERP seu pedido não pode ser efetivado.
                          </br> $msg $msgLock';

            /* echo' <table class="table table-striped">';
            $tam = count($erro);
            //echo $tam;
            for($i=0;$i<$tam;$i++){
                if($tam <> 0){
                    echo "<tr><td>".$erro[$i]."</td></tr>";
                }else{
                    echo "<tr><td> Devido a problemas com a integração com o ERP seu pedido não pode ser efetivado. Favor entrar em contato com a TI </td></tr>";
                }
            }
            echo "</table></div>";*/
            //$link =  "..\\form_peds_web_cabec\\";
            //$link =  '..\cons_ped_web_carrinho\\';
        }


        ?>
        <h2><a href='<?php echo $link; ?>'>Voltar Para o Carrinho</a> </h2>
    </div>
    </body>
    </html>
<?php











