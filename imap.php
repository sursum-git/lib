<?php
function getXmlEmail($estab)
{
    $result = lerCaixaEmail(getCaixaNFe($estab),
                 getUsuarioCaixaNFe($estab),
                 getSenhaCaixaNFe($estab),
                getDirAnexosEmail($estab)
    );
}
function lerCaixaEmail($mailbox,$usuario,$senha,$dirAnexos,$filtro='UNSEEN'){

    echo "<h1>diretorio: $dirAnexos  </h1>";
    $msg = array();
    $aRetorno = array();
    $aEmail = array();
    $inbox = imap_open($mailbox, $usuario, $senha);
    if($inbox){
        $emails = imap_search($inbox, $filtro);
        if (!count($emails)) {
            $msg['aviso'] = 'Nenhum email encontrado';
        } else {
            // ordena
            rsort($emails);
            foreach ($emails as $email) {
                $overview = imap_fetch_overview($inbox, $email, 0);
                $cabecalho = (imap_fetchbody($inbox, $email, 0));
                $cabecalho = nl2br($cabecalho);
                $body = (imap_fetchbody($inbox, $email, 1));
                $body =nl2br($body);
                $VarRethtml = pegaCorpoEmail($inbox, $email);
                $structure = imap_fetchstructure($inbox, $email);
               /*
                echo "<pre>";
                echo "<h2>overview</h2>";
                var_dump($overview);
                echo "<h2>cabeçalho</h2>";
                var_dump($cabecalho);
                echo "<h2>body</h2>";
                var_dump($body);
                echo "<h2>corpo email</h2>";
                var_dump($VarRethtml);
                echo "<h2>estrutura</h2>";
                var_dump($structure);
                */
                //echo "<br>" . $overview[0]->seen ? "<font color='gren'>LIDA</font>" : "<font color='red'>NÃO LIDA</font>";

                /*echo "<br><b>Assunto :</b>" . decodeIMAPTexto($overview[0]->subject);
                echo "<br><b>De :</b>" . decodeIMAPTexto($overview[0]->from);
                echo "<br><b>Data :</b>" . $overview[0]->date . "</b>";
                echo "<hr>";*/
                $attachments = array();

                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if (strtolower($object->attribute) == 'filename') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if (strtolower($object->attribute) == 'name') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email, $i + 1);

                            // 3 = BASE64
                            // 4 = QUOTED-PRINTABLE
                            if ($structure->parts[$i]->encoding == 3) {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            } elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }

                foreach($attachments as $attachment)
                {
                    if($attachment['is_attachment'] == 1)
                    {
                        $filename = $attachment['name'];
                        if(empty($filename)) $filename = $attachment['filename'];

                        if(empty($filename)) $filename = time() . ".dat";
                        $logGerarArquivo = false;
                        //Caso tiver mesmo nome salva com prefixo.
                        $aFileName = explode('.',$filename);
                        $logGerarArquivo = compararExtensaoArq($filename,'xml');
                        if($logGerarArquivo){
                            echo "<h1>$filename</h1>";
                            $fp = fopen($dirAnexos. '/' . $filename, "w+");
                            fwrite($fp, $attachment['attachment']);
                            fclose($fp);
                        }
                        //echo '<p><a href="'.$email . "-" . $filename.'">'.$filename.'</a></p>';
                    }

                }

                imap_close($inbox);
            }
        }
    }else{
        $msg['erro'] = imap_last_error();
    }

    return $aRetorno;
}


function pegaCorpoEmail($imap, $uid)
{
    $body = get_part($imap, $uid, "TEXT/HTML");
    // se corpo do HTML estiver vazio tenta colocar o texto no corpo
    if ($body == "") {
        $body = get_part($imap, $uid, "TEXT/PLAIN");
    }
    return $body;
}

function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false)
{
    if (!$structure) {
        $structure = imap_fetchstructure($imap, $uid);
    }
    if ($structure) {
        if ($mimetype == get_mime_type($structure)) {
            if (!$partNumber) {
                $partNumber = 1;
            }
            $textoEmail = imap_fetchbody($imap, $uid, $partNumber);
            switch ($structure->encoding) {
                case 0:
                case 1:
                    return imap_8bit($textoEmail);
                case 2:
                    return imap_binary($textoEmail);
                case 3:
                    return imap_base64($textoEmail);
                case 4:
                    return imap_qprint($textoEmail);
                default:
                    return $textoEmail;
            }
        }


        // multipart
        if ($structure->type == 1) {
            foreach ($structure->parts as $index => $subStruct) {
                $prefix = "";
                if ($partNumber) {
                    $prefix = $partNumber . ".";
                }
                $data = get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
                if ($data) {
                    return $data;
                }
            }
        }
    }
    return false;
}

function get_mime_type($structure)
{
    $primaryMimetype = ["TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"];

    if ($structure->subtype) {
        return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
    }
    return "TEXT/PLAIN";
}
function decodeIMAPTexto($str)
{
    $op = '';
    $decode_header = imap_mime_header_decode($str);

    foreach ($decode_header as $obj) {
        $op .= htmlspecialchars(rtrim($obj->text, "\t"));
    }

    return $op;
}
?>
