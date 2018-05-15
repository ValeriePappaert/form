<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPmailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/src/Exception.php';
//https://www.arclab.com/en/kb/email/how-to-enable-imap-pop3-smtp-gmail-account.html
if($_SERVER["REQUEST_METHOD"]=="POST"){
$name=trim(filter_input(INPUT_POST,"name",FILTER_SANITIZE_STRING));
$phone=trim(filter_input(INPUT_POST,"phone",FILTER_SANITIZE_STRING));
$email=trim(filter_input(INPUT_POST,"email",FILTER_SANITIZE_EMAIL));
$subject=$_POST["subject"];
$message=trim(filter_input(INPUT_POST,"message",FILTER_SANITIZE_FULL_SPECIAL_CHARS));
if($name=="" OR $email=="" OR $subject =="" OR $message==""){
  $error_message = "Gelieve uw voor- en familienaam, emailadres, onderwerp en bericht in de vereiste velden in te vullen.";
}
if (!isset($error_message) && $_POST["address"]!="") {
  $error_message = "Bad form input";
}
if(!isset($error_message) && !PHPMailer::validateAddress($email)) {
  $error_message = "Ongeldig Emailadres.";
}
if (!isset($error_message)) {
    $email_body = "";
    $email_body.= "Voornaam en familienaam: " . $name . "\n";
    $email_body.= "Emailadres: " . $email . "\n";
    $email_body.= "Telefoonnummer: " . $phone . "\n";
    $email_body.= "Onderwerp: " . $subject . "\n";
    $email_body.= "Bericht: " . $message . "\n";
    $mail = new PHPMailer;
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //Set the hostname of the mail server
    $mail->Host = 'smtp.gmail.com';
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6
    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 587;
    //Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = 'tls';
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = "valerie.pappaert@gmail.com";
    //Password to use for SMTP authentication
    $mail->Password = "qeipdxyepgddpkxj";
    //It's important not to use the submitter's address as the from address as it's forgery,
    //which will cause your messages to fail SPF checks.
    //Use an address in your own domain as the from address, put the submitter's address in a reply-to
    $mail->setFrom($email, $name);
    $mail->addReplyTo($email, $name);
    $mail->addAddress("valerie.pappaert@gmail.com", 'Valerie Pappaert');
    $mail->Subject = 'Contact formulier ingevuld door ' . $name;
    $mail->Body = $email_body;
    if ($mail->send()) {
      header("location:index.php?status=thanks");
      exit;
    }
    $error_message = "Mailer Error: " . $mail->ErrorInfo;
  }
}
?>

<!DOCTYPE html>
<html>
<body>

  <?php
    if(isset($error_message)) {?>
      <br/><br/><p class="text-base align-center message_error"><?php echo $error_message;
    }
    ?></p><?php
    if(!isset($error_message) && isset($_GET["status"]) && $_GET["status"]=="thanks"){ ?>
      <p class="text-base align-center message_succes"> Bedankt voor je bericht! Ik neem spoedig contact met je op. </p><?php ;
    } else { ?>
      <div class="form align-center">
        <form method = "POST" action="contact.php#contact">
          <label for="name">Voornaam en familienaam*</label><br/>
          <input type="text" id="name" name="name" value="<?php if (isset ($name)) echo $name; ?>"/><br/>
          <label for="email">Email*</label><br/>
          <input type="text" id="email" name="email" value="<?php if (isset ($email)) echo $email; ?>"/><br/>
          <label for="phone">Telefoonnummer</label><br/>
          <input type="text" id="phone" name="phone" value="<?php if (isset ($phone)) echo $phone; ?>"/><br/>
          <label for="subject">Onderwerp*</label><br/>
          <select id="subject" name="subject">
            <option <?php if (isset($subject) && $subject=="-") echo "selected";?>>-</option>
            <option <?php if (isset($subject) && $subject=="Vakantieopvang") echo "selected";?>>Vakantieopvang</option>
            <option <?php if (isset($subject) && $subject=="Workshops") echo "selected";?>>Workshops</option>
            <option <?php if (isset($subject) && $subject=="Andere") echo "selected";?>>Andere</option>
          </select><br/>
          <label for="message">Bericht*</label><br/>
          <textarea name="message" id="message" cols="60" rows="15" placeholder="Typ hier je bericht."><?php if (isset ($message)) echo $message; ?></textarea><br/>
          <label class="honey" for="address">Address</label>
          <input class="honey" type="text" name="address" id="address"/>
          <p class="honey">Gelieve dit veld leeg te laten.</p>
          <input class="btn btn-whitebg-red" type="submit" value="Verzenden"/>
        </form>
      </div><?php
    } ?>

</body>
</html>
