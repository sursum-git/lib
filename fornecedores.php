<?php
//__NM__Fornecedor__NM__FUNCTION__NM__//
function buscarDadosFornecedor($cods_fornecedor)
{
    $fornecedores = '';
    $sql_fornecedor = 'SELECT "nome-emit" from PUB.emitente where "cod-emitente" in ('.$cods_fornecedor.')';
    sc_select(fornecedor, $sql_fornecedor,"ems2cad");
    if ({fornecedor} === false)
		{
            echo "Erro de acesso. Mensagem = " . {fornecedor_erro};
		}
		else
		{
            while (!$fornecedor->EOF)
            {
                if($fornecedores == '')
                    $fornecedores =  $fornecedor->fields[0];
                else
                    $fornecedores .= "," . $fornecedor->fields[0];

                $fornecedor->MoveNext();
            }
            $fornecedor->Close();
        }
	    return $fornecedores;
}

function buscarEmailsFornecedor($cods_fornecedor)
{
    $emails = '';
    $sql_contato = ' SELECT "e-mail" from pub."cont-emit" where "area" = \'comercial\' and "cod-emitente" in ('.$cods_fornecedor.')';
    //echo $sql_contato;
    sc_select(email, $sql_contato,"ems2cad");
    if ({email} === false)
		{
            echo "Erro de acesso. Mensagem = " . {email_erro};
		}
		else
		{
            while (!$email->EOF)
            {
                if($emails == '')
                    $emails =  $email->fields[0];
                else
                    $emails .= ";" . $email->fields[0];

                $email->MoveNext();
            }
            $email->Close();
        }
	    return $emails;
	}
?>