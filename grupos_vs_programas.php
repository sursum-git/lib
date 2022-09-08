<?php
//__NM__Grupos x Programas__NM__FUNCTION__NM__//
	function retornarIDprograma($cod_programa)
	{
		$retorno = 0;
		$sql = "
                select idi_dtsul from pub.prog_dtsul
                where cod_prog_dtsul = '$cod_programa' 
               ";
		sc_lookup(rs, $sql);
		if (isset({rs[0][0]}))     // Row found
        {
           $retorno = {rs[0][0]};           
        }	
		return $retorno;
	}
    function retornarIDGrupoUsuario($cod_grupo)
	{
		$retorno = 0;
		$sql = "select idi_dtsul from pub.grp_usuar
                where cod_grp_usuar = '$cod_grupo' ";
		sc_lookup(rs, $sql);
		if (isset({rs[0][0]}))     // Row found
        {
           $retorno = {rs[0][0]};           
        }	
		return $retorno;
	}
    function setPermissaoProgProced($cod_programa,$cod_grupo)
	{ 
	    $qtProced = 0;
		//$origem = verificarPrograma($cod_programa);
		$sql = "select pd.cod_prog_dtsul , pr.cod_proced  from pub.procedimento pr, pub.prog_dtsul pd
                       where cod_prog_dtsul_base = '$cod_programa'
						and pr.cod_proced = pd.cod_proced 
                        and pd.cod_prog_dtsul <> '$cod_programa'
                      ";					
		sc_select(meus_dados,$sql);
        if ({meus_dados} === false)
        {
           echo "Erro de acesso setPermissaoProgProced. Mensagem = " . {meus_dados_erro};
        }
        else
        {
          while (!$meus_dados->EOF)
		  {
                /*busca programas ligados ao procedimento do programa passado por parametro*/
				$codPrograma = $meus_dados->fields[0];
				$codProcedimento = $meus_dados->fields[1];
			    $idi_dtsul_grp_usuar  = retornarIDGrupoUsuario($cod_grupo);
			    $idi_dtsul_prog_dtsul = retornarIDprograma($codPrograma);
			    $idi_dtsul = retornarProxID('prog_dtsul_segur');
			    $cmd = "insert into pub.prog_dtsul_segur(idi_dtsul, 
                                                       cod_grp_usuar,
                                                       cod_prog_dtsul, 
                                                       idi_dtsul_grp_usuar,
                                                        idi_dtsul_prog_dtsul)
                       values($idi_dtsul,
                              '$cod_grupo',
                              '$codPrograma',
                              $idi_dtsul_grp_usuar,
							  $idi_dtsul_prog_dtsul)";
			    
			    $chave = buscarChaveProgSegur($cod_grupo,$codPrograma);
			    if($chave == '')
				{
		           echo "insert:".$cmd."</br>";
				   sc_exec_sql($cmd);
                }			  
                $meus_dados->MoveNext();
          }
          $meus_dados->Close();
		}  
		 /*busca os procedimentos de consulta do procedimento do programa passado por parametro*/
		 /*falta testar para retirar o comentario
	     $sql = "select cod_proced_con  from proced_consult_proced where cod_proced = $codProcedimento ";
		 sc_select(meus_dados02,$sql);
		 if ({meus_dados02} === false)
         {
           echo "Erro de acesso setPermissaoProgProcedconsultas. Mensagem = " . {meus_dados02_erro};
         }
         else
         {
           while (!$meus_dados02->EOF)
		   {
		     $codProcedimentoCons = $meus_dados02->fields[0];  
			 $sql ="select cod_prog_dtsul from prog_dtsul where cod_proced = '$coProcedimentoCons'";
			 sc_select(procCons,$sql);
			 if ({procCons} === false)
             {
                 echo "Erro de acesso setPermissaoProgProcedconsultasprog. Mensagem = " . {procCons_erro};
             } 
			 else
			 {
			    while (!$procCons->EOF)
		        { 	$codProgramaCons = $procCons->fields[0];  
				    $idi_dtsul_grp_usuar  = retornarIDGrupoUsuario($cod_grupo);
			        $idi_dtsul_prog_dtsul = retornarIDprograma($codProgramaCons);
			        $idi_dtsul = retornarProxID('prog_dtsul_segur');
					$cmd = "insert into pub.prog_dtsul_segur(idi_dtsul, 
                                                       cod_grp_usuar,
                                                       cod_prog_dtsul, 
                                                       idi_dtsul_grp_usuar,
                                                        idi_dtsul_prog_dtsul)
                       values($idi_dtsul,
                              '$cod_grupo',
                              '$codProgramaCons',
                              $idi_dtsul_grp_usuar,
							  $idi_dtsul_prog_dtsul)";	
					$chave = buscarChaveProgSegur($cod_grupo,$codProgramaCons);
			        if($chave == '')
				       sc_exec_sql($cmd);		  
                }				 
			 }			 
			$meus_dados02->MoveNext(); 
           }
		   $meus_dados02->Close();
	     } 
		 */
		 /*busca os procedimentos de relatorio do procedimento do programa passado por parametro*/
		 
		 /* falta testar para retirar o comentario
	     $sql = "select cod_proced_relat from proced_relat_proced where cod_proced = $codProcedimento ";
		 sc_select(meus_dados03,$sql);
		 if ({meus_dados03} === false)
         {
           echo "Erro de acesso setPermissaoProgProcedcrelatorios. Mensagem = " . {meus_dados03_erro};
         }
         else
         {
           while (!$meus_dados03->EOF)
		   {
		     $codProcedimentoRel = $meus_dados03->fields[0];  
			 $sql ="select cod_prog_dtsul from prog_dtsul where cod_proced = '$coProcedimentoRel'";
			 sc_select(procRel,$sql);
			 if ({procRel} === false)
             {
                 echo "Erro de acesso setPermissaoProgProcedconsultasprog. Mensagem = " . {procRel_erro};
             } 
			 else
			 {
			    while (!$procRel->EOF)
		        { 	$codProgramaRel = $procRel->fields[0];  
				    $idi_dtsul_grp_usuar  = retornarIDGrupoUsuario($cod_grupo);
			        $idi_dtsul_prog_dtsul = retornarIDprograma($codProgramaRel);
			        $idi_dtsul = retornarProxID('prog_dtsul_segur');
					$cmd = "insert into pub.prog_dtsul_segur(idi_dtsul, 
                                                       cod_grp_usuar,
                                                       cod_prog_dtsul, 
                                                       idi_dtsul_grp_usuar,
                                                        idi_dtsul_prog_dtsul)
                       values($idi_dtsul,
                              '$cod_grupo',
                              '$codProgramaRel',
                              $idi_dtsul_grp_usuar,
							  $idi_dtsul_prog_dtsul)";	
					$chave = buscarChaveProgSegur($cod_grupo,$codProgramaRel);
			        if($chave == '')
				       sc_exec_sql($cmd);		  
                }				 
			 }			 
			$meus_dados03->MoveNext(); 
           }
		   $meus_dados03->Close();
	     }	
		 */
	}
    function verificarPrograma($cod_programa)
	{ /*essa funcao verifica se o codigo passado é um programa ou um procedimento.*/
	  $retorno = '';
	  $sql = "select cod_prog_dtsul from pub.prog_dtsul where cod_prog_dtsul = $cod_programa ";
	  sc_lookup(rs, $sql);
	  if (isset({rs[0][0]}))     // Row found
      {
          $retorno = 'prog';           
      } 
	  if($retorno == '')     
	  {
	     $sql = "select cod_proced from procedimento where cod_proced = $cod_programa ";
		 sc_lookup(rs1, $sql);
	     if (isset({rs1[0][0]}))     // Row found
         {
           $retorno = 'proc';           
         }
	  }
	  return $retorno;
	  
	
	}
    function retornarProxID($tabela)
	{
		$retorno = 0;
		$sql = "select max(idi_dtsul) from pub.".$tabela;		
		sc_lookup(rs, $sql);
		if (isset({rs[0][0]}))     // Row found
        {
           $retorno = {rs[0][0]} + 1;           
        }
		return $retorno;
	}
    
     function buscarChaveProgSegur($cod_grupo,$cod_programa)
	{
		$retorno = ''; 
		$sql = "select top 1 cod_prog_dtsul from pub.prog_dtsul_segur 
                where cod_grp_usuar = '$cod_grupo' and cod_prog_dtsul = '$cod_programa'";
		sc_lookup(rs, $sql);
		echo 'sql progsegur:'.$sql."</br>";
		if (isset({rs[0][0]}))     // Row found
        {
           $retorno = {rs[0][0]};           
        }	
		echo "retorno buscarChaveProgSegur".$retorno;
		return $retorno;
	}
		
    function copiarPermissoes($usuarioOrigem,$usuarioDestino)
	{
		
		
		
	}

     
?>