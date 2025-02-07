<?php

namespace Hcode;

use Rain\Tpl;

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{

  const USERNAME = "example@example.com";
  const PASSWORD = "example";
  const NAME_FROM = "Hcode Store";

  private $mail;

  public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
  {

    $config = array(
      "tpl_dir"       => $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."email".DIRECTORY_SEPARATOR,
      "cache_dir"     => $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR,
      "auto_escape"   => true,
      "base_url" => null,
      "debug"         => false // set to false to improve the speed
    );

    Tpl::configure($config);

    $tpl = new Tpl;

    foreach ($data as $key => $value) {
      $tpl->assign($key, $value);
    }

    $html = $tpl->draw($tplName, true);

    //Create a new PHPMailer instance
    $this->mail = new PHPMailer();

    //Tell PHPMailer to use SMTP
    $this->mail->isSMTP();

    //Enable SMTP debugging
    //SMTP::DEBUG_OFF = off (for production use)
    //SMTP::DEBUG_CLIENT = client messages
    //SMTP::DEBUG_SERVER = client and server messages
    $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;

    //Set the hostname of the mail server
    $this->mail->Host = 'smtp.gmail.com';
    //Use `$this->mail->Host = gethostbyname('smtp.gmail.com');`
    //if your network does not support SMTP over IPv6,
    //though this may cause issues with TLS

    //Set the SMTP port number:
    // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
    // - 587 for SMTP+STARTTLS
    $this->mail->Port = 465;

    //Set the encryption mechanism to use:
    // - SMTPS (implicit TLS on port 465) or
    // - STARTTLS (explicit TLS on port 587)
    $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    //Whether to use SMTP authentication
    $this->mail->SMTPAuth = true;

    //Username to use for SMTP authentication - use full email address for gmail
    $this->mail->Username = Mailer::USERNAME;

    //Password to use for SMTP authentication
    $this->mail->Password = Mailer::PASSWORD; // Para o gmail, gerar senha específica para essa aplicação veja mais em: https://support.google.com/accounts/answer/185833?hl=en

    //Set who the message is to be sent from
    //Note that with gmail you can only use your account address (same as `Username`)
    //or predefined aliases that you have configured within your account.
    //Do not use user-submitted addresses in here
    $this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

    //Set an alternative reply-to address
    //This is a good place to put user-submitted addresses
    $this->mail->addReplyTo(Mailer::USERNAME, Mailer::NAME_FROM);

    //Set who the message is to be sent to
    $this->mail->addAddress($toAddress, $toName);

    //Set the subject line
    $this->mail->Subject = $subject;

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $this->mail->msgHTML($html);

    //Replace the plain text body with one created manually
    $this->mail->AltBody = 'This is a plain-text message body';

    //Attach an image file
    //$this->mail->addAttachment('images/phpmailer_mini.png');

    //Section 2: IMAP
    //IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
    //Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
    //You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
    //be useful if you are trying to get this working on a non-Gmail IMAP server.
    // function save_mail($this->mail)
    // {
    //   //You can change 'Sent Mail' to any other folder or tag
    //   $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

    //   //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    //   $imapStream = imap_open($path, $this->mail->Username, $this->mail->Password);

    //   $result = imap_append($imapStream, $path, $this->mail->getSentMIMEMessage());
    //   imap_close($imapStream);

    //   return $result;
    // }
        // //send the message, check for errors
        // if (!$this->mail->send()) {
        //   echo 'Mailer Error: ' . $this->mail->ErrorInfo;
        // } else {
        //   echo 'Message sent!';
        //   //Section 2: IMAP
        //   //Uncomment these to save your message in the 'Sent Mail' folder.
        //   #if (save_mail($this->mail)) {
        //   #    echo "Message saved!";
        //   #}
        // }
  }

  public function send()
  {

   return $this->mail->send();

  }

}
