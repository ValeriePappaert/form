<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPmailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/src/Exception.php';
//https://www.arclab.com/en/kb/email/how-to-enable-imap-pop3-smtp-gmail-account.html
?>

<!DOCTYPE html>
  <html>
          <?php
            if($_SERVER["REQUEST_METHOD"]=="POST"){
            $name=trim(filter_input(INPUT_POST,"name",FILTER_SANITIZE_STRING));
            $phone=trim(filter_input(INPUT_POST,"phone",FILTER_SANITIZE_STRING));
            $email=trim(filter_input(INPUT_POST,"email",FILTER_SANITIZE_EMAIL));
            $message=trim(filter_input(INPUT_POST,"message",FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            if($name=="" OR $phone=="" OR $email=="" OR $message==""){
              $error_message = "Gelieve uw Voornaam en Naam, telefoonnummer, emailadres en bericht in de vereiste velden in te vullen.";
            }
            if (!isset($error_message) && $_POST["address"]!="") {
              $error_message = "Bad form input";
            }
            if(!isset($error_message) && !PHPMailer::validateAddress($email)) {
              $error_message = "Ongeldig Emailadres.";
            }
            if (!isset($error_message)) {
                $email_body = "";
                $email_body.= "Voornaam en naam: " . $name . "\n";
                $email_body.= "Telefoonnummer: " . $phone . "\n";
                $email_body.= "Emailadres: " . $email . "\n";
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
                $mail->setFrom('valerie.pappaert@gmail.com', $name);
                $mail->addReplyTo($email, $name);
                $mail->addAddress('valerie.pappaert@gmail.com', 'Laura Mignolet');
                $mail->Subject = 'Contact formulier ingevuld door ' . $name;
                $mail->Body = $email_body;
                if ($mail->send()) {
                  header("location:index.php?status=thanks");
                  exit;
                }
                $error_message = "Mailer Error: " . $mail->ErrorInfo;
              }
          }
          if(isset($error_message)) {
            echo $error_message;
          }
          ?>

          <?php if(isset($_GET["status"])&& $_GET["status"]=="thanks"){
            echo "<p>Bedankt voor je bericht!</p>";
          } else { ?>
              <form method = "post" action="index.php">
                <table>
                  <tr>
                    <th><label for="name">Voornaam en naam</label></th>
                    <td><input type="text" id="name" name="name" value="<?php if (isset ($name)) echo $name; ?>"/></td>
                  </tr>
                  <tr>
                    <th><label for="phone">Telefoonnummer</label></th>
                    <td><input type="text" id="phone" name="phone" value="<?php if (isset ($phone)) echo $phone; ?>"/></td>
                  </tr>
                  <tr>
                    <th><label for="email">Emailadres</label></th>
                    <td><input type="text" id="email" name="email" value="<?php if (isset ($email)) echo $email; ?>"/></td>
                  </tr>
                  <tr>
                    <th><label for="message">Bericht</label></th>
                    <td><textarea name="message" id="message" cols="60" rows="15" placeholder="Typ hier je bericht."><?php if (isset ($message)) echo $message; ?></textarea></td>
                  </tr>
                  <tr style="display:none">
                    <th><label for="address">Address</label></th>
                    <td><input type="text" name="address" id="address"/>
                    <p>Gelieve dit veld leeg te laten.</p></td>
                  </tr>
                </table>
                <input type="submit" value="Verzenden"/>
              </form>
            <?php } ?>

</html>
