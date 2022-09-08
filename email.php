<?php
$servidor = buscarParam('servidor_email');
$usuario  = buscarParam('usuario_email');
$senha    = buscarParam('senha_email');
$porta    =	buscarParam('porta_especifica');
$remetente = buscarParam('email_envio');

function getServidorEmail()
{
    $vlParam = buscarParametroIma('servidor_email');
    if($vlParam == ''){
        $vlParam = 'smtp.dreamhost.com';
    }
    return $vlParam;

}
function getUsuarioEmail()
{
    $vlParam = buscarParametroIma('usuario_email');
    if($vlParam == ''){
        $vlParam = 'imatextil@prolinx.net.br';
    }
    return $vlParam;

}
function getPortaEspecifica()
{
    $vlParam = buscarParametroIma('porta_especifica');
    if($vlParam == ''){
        $vlParam = '587';
    }
    return $vlParam;
}
function getEmailEnvio()
{
    $vlParam = buscarParametroIma('email_envio');
    if($vlParam == ''){
        $vlParam = 'maladireta@imatextil.com.br';
    }
    return $vlParam;
}

function getSenhaEmail()
{
    $vlParam = buscarParametroIma('senha_email');
    if($vlParam == ''){
        $vlParam = 'gy22GcsY';
    }
    return $vlParam;
}

function getDadosServidor()
{
    $aRetorno = array(
        'servidor_email' => getServidorEmail(),
        'usuario_email'  => getUsuarioEmail(),
        'porta_especifica' => getPortaEspecifica(),
        'email_envio'    => getEmailEnvio(),
        'senha_email'   => getSenhaEmail());
    return $aRetorno;
}

function enviarEmail($destinatario,$titulo,$corpo,$anexos='',$destCopia='',$tipoDestCopia='BCC',$formatoMsg='H')
{
    $aDadosEmail = getDadosServidor();
    $servidor    = $aDadosEmail['servidor_email'];
    $usuario     = $aDadosEmail['usuario_email'];
    $senha       = $aDadosEmail['senha_email'];
    $remetente   = $aDadosEmail['email_envio'];
    $porta       = $aDadosEmail['porta_especifica'];
    sc_mail_send(
        $servidor,      //SMTP	SMTP server name or IP address. (String or Variable that contains the server name)
        $usuario,       // Usr	SMTP user name. (String or Variable that contains the user name)
        $senha,         // Pw	SMTP password. (String or Variable that contains the password)
        $remetente,     // From	From email. (String or Variable that contains the email)
        $destinatario,  //to	to email. (String or Variable that contains the email)
        $titulo,        //Subject	Message subject. (String or Variable that contains the subject)
        $corpo,         //Message	Message body. (String or Variable that contains the message)
        $formatoMsg,    //Mens_Type	Message format: (T)ext or (H)tml.
        $destCopia,     //Copies	List of the emails that will recieve the message, it could be a string or variable that cointains one or more emails separated by ";" or one variable that contains one array of emails.
        $tipoDestCopia, // tipo de copias CCC visiveis ou BCC oculta .  em branco fica oculta
        $porta,         //porta
        '',             // tipo conexão S for SSL, T for TLS or N for non secure connections
        $anexos        // arquivo ou arquivos separados por ; ou um array com os arquivos
    );
    if ({sc_mail_ok}){
        //echo "Enviados {sc_mail_count} e-mail com sucesso !!";
        return {sc_mail_count};
    }else
    {
        //sc_error_message({sc_mail_erro});
        return {sc_mail_erro};
    }
}

function getEmailsErroArqDesign()
{

    $vlParam = buscarParametroIma('emails_erros_arq_design');
    if($vlParam == ''){
        $vlParam = 'tadeu.parreiras@gmail.com;tadeu.parreiras@sursumcorda.com.br';
    }
    return $vlParam;
}
function getMsgErroBook()
{
    $vlParam = buscarParametroIma('msg_erro_book');
    if($vlParam == ''){
        $vlParam = 'O PDF não será gerado devido a falta de cadastro de imagens.
                    Favor entrar em contato com o setor de design gráfico.';
    }
    return $vlParam;
}

?>
