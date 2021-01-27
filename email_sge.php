<?php
 
 include_once('config.php');
 include_once('library/phpmailer/class.phpmailer.php');
 include_once('library/phpmailer/class.smtp.php');

header('Content-Type: text/html; charset=iso-8859-1');

function envialEmail($mensagem,$mailDestino,$anexoCaminho, $mailDestinoSecundario='')
{
    /*
    *    VERIFICA SE A APLICAÇÃO ESTÁ NO AMBIENTE DE PRODUÇÃO. 
    *    NÃO SENDO O AMBIENTE DE PRODUÇÃO, ENVIA TODOS OS E-MAIL PARA O DESENVOLVEDOR 
    */
   if (!isProduction()) {
       //$mailDestino = 'rodolfosaneto@gmail.com'; // Email pessoal
       $mailDestino = 'diana.carvalho@jcl-tecnologia.com.br'; // Email pessoal
      //$mailDestino = 'jorgeallan@msn.com'; // Email pessoal
       $mailDestinoSecundario = [$mailDestino];
   }

    $nome = 'SEDUC';
    $assunto = 'Portal rematrícula 2021';

    $mail = new PHPMailer();
    /* DUAS VARIÁVEIS UTILIZADAS PARA ATIVAR O MODO DEBUG DO PHPMAILER */
    //$mail->SMTPDebug = 2;
    //$mail->Debugoutput = 'html';

    $mail->IsSMTP();
    $mail->CharSet = 'iso-8859-1';
    // $mail->True;
    $mail->Host = "smtp.gmail.com"; // Servidor SMTP
    $mail->SMTPSecure = "tls"; // conexÃ£o segura com TLS
    $mail->Port = 587;
    $mail->SMTPAuth = true; // Caso o servidor SMTP precise de autenticaÃ§Ã£o
    $mail->Username = "portal.listadeespera@educa.camacari.ba.gov.br"; // SMTP username
    $mail->Password = "Portal2021"; // SMTP password
    $mail->From = "portal.listadeespera@educa.camacari.ba.gov.br"; // From
    $mail->FromName = "Portal rematrícula 2021"; // Nome de quem envia o email
     if($mailDestino!= '' and $mailDestino!= null){
         $mail->AddAddress($mailDestino, $nome);
     }
    if ($mailDestinoSecundario != ''){
        foreach ($mailDestinoSecundario as $emailSecundario){
            $mail->AddAddress($emailSecundario, $nome); // Email e nome de quem receberÃ¡ //Responder
        }
    }
    $mail->WordWrap = 50; // Definir quebra de linha
    $mail->IsHTML = true; // Enviar como HTML
    $mail->Subject = $assunto; // Assunto
    $mail->Body = '<br/>' . $mensagem . '<br/>'; //Corpo da mensagem caso seja HTML
    $mail->AltBody = "$mensagem"; //PlainText, para caso quem receber o email nÃ£o aceite o corpo HTML
  
    /*    
     *    FUNÇÃO PARA ANEXAR ARQUIVOS AO E-MAIL, 
     *    VERIFICA SE A FUNÇÃO RECEBEU ALGUM CAMINHO PARA O ANEXO 
     */

  if ($anexoCaminho != ''){
    $mail->AddAttachment($anexoCaminho);    
  }
  
  if (!$mail->Send()) // Envia o email
  {
    //echo "Erro no envio da mensagem";
  } else {
    //echo 'sucess1';
  }
}
