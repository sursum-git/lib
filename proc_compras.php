<?php
//__NM__Processo de Compras__NM__FUNCTION__NM__//
function buscarItensProcesso($cod_processo)
{
    $itens = array();
    $lAchou = false;
    $sql_itens =  'SELECT  "numero-ordem", "it-codigo",  "data-emissao", "cod-cond-pag",  "qt-solic" FROM 
    						 PUB."ordem-compra"	where  "nr-processo" ='. $cod_processo;

    sc_select(item, $sql_itens);

    if ({item} === false){
            echo "Erro de acesso. Mensagem = " . {item_erro};
    }
	else{
            while (!$item->EOF)
            {

                $itens[] = array("numero-ordem" => $item->fields[0], "it-codigo" => $item->fields[1],
                    "data-emissao" => $item->fields[2], "cod-cond-pag" => $item->fields[3] ,
                    "qt-solic" => $item->fields[4]);
                $lAchou = true;
                $item->MoveNext();
            }
            $item->Close();
	}
	if($lAchou == false){
	    $itens = '';
    }
    return $itens;
}
function criarEmail($cod_processo,$cod_fornecedor)
{
    $erro = '';
    $aRetorno = array();
    $html = '';
    $nome_fornecedor = buscarDadosFornecedor($cod_fornecedor);
    if($nome_fornecedor == '')
    {
        $erro = "Fornecedor não encontrado.";
    }
    else
    {
        $html =
            '	
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>Ficha de Cotação</title>
				<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
				</head>
				<body>
				<h4>Fornecedor:'.$cod_fornecedor.'-'.$nome_fornecedor.'  - Cond.Pagto:_______ - Frete:_________ <br> TOTAL GERAL:_______</h4>
				<table style="width:100%;border-style:groove; border-width:1px;">
				<tr>
				<th style="border-style:groove; border-width:1px;text-align:left;">Item</th>
				<th style="border-style:groove; border-width:1px;text-align:left;">Descrição</th>
				<th style="border-style:groove; border-width:1px;">Qte.</th>
				<th style="border-style:groove; border-width:1px;">U.M.</th>
				<th style="border-style:groove; border-width:1px;">Vl.Unit.</th>
				<th style="border-style:groove; border-width:1px;">Total</th>
				<th style="border-style:groove; border-width:1px;">U.M. Venda x qt</th>
				</tr>
				';
        $emails = buscarEmailsFornecedor($cod_fornecedor);
        if($emails == '')
        {
            $erro = "Falta Cadastrar E-mail Comercial para o fornecedor:$cod_fornecedor-$nome_fornecedor.";

        }
        else
        {
            $aItens = buscarItensProcesso($cod_processo);
            if($aItens == '')
            {
                $erro = "Não existem itens cadastrados no processo de compra.";
            }
            else
            {
                for($i=0;$i < count($aItens);$i++)
                {
                    $aItem = buscarDadosItem($aItens[$i]["it-codigo"]);
                    if($aItem == '')
                    {
                        $desc_item = '';
                        $un_item   = '';
                    }
                    else
                    {
                        $desc_item = $aItem[0]["desc-item"];
                        $un_item   =  $aItem[0]["un"];
                    }

                    $html .= '<tr><td style="border-style:groove; border-width:1px;" >'.$aItens[$i]["it-codigo"].'</td>
						<td style="border-style:groove; border-width:1px;">'.$desc_item.'</td>
						<td style="text-align:right;border-style:groove; border-width:1px;">'.$aItens[$i]["qt-solic"].'</td>
					    <td style="border-style:groove; border-width:1px;">'.$un_item.'</td>
						<td style="border-style:groove; border-width:1px;"></td>
						<td style="border-style:groove; border-width:1px;"></td>
						<td style="border-style:groove; border-width:1px;"></td></tr>';

                }

            }
        }
        $html .= '</table></body></html>';
    }
    $aRetorno[] = array("html"=>$html, "erro"=>$erro, "emails"=>$emails);
    return $aRetorno;
}
function enviarEmail($titulo,$corpo,$emails)
{

    // Email parameters
    //$caminhoImagens = "http://vendermais.imatexil.com.br/suporte/_lib/img/";
    $mail_smtp_server    = 'smtp.imatextil.com.br';        // SMTP server name or IP address
    $mail_smtp_user      = 'sergio.oliveira@imatextil.com.br';                   // SMTP user name
    $mail_smtp_pass      = 'ima147';                // SMTP password
    $mail_from           = 'sergio.oliveira@imatextil.com.br';          // From email
    $mail_to             = $emails;         // To email
    $mail_subject        = $titulo; // Message subject
    $mail_format         = 'H';
    $mail_message        = $corpo;        // List of the emails that will recieve the message
    $mail_tp_copies      = '';                        // Type copies: BCC (Hiden copies) or CCC (Regular copies)
    $mail_port           = '465';                     // Server port
    $mail_tp_connection  = 'S';
    $mail_copies = $mail_from; // Connection security (S) or (N)

    // Send email";
    sc_mail_send($mail_smtp_server,
        $mail_smtp_user,
        $mail_smtp_pass,
        $mail_from,
        $mail_to,
        $mail_subject,
        $mail_message,
        $mail_format,
        $mail_copies,
        $mail_tp_copies,
        $mail_port,
        $mail_tp_connection);
}
?>