<?php
require_once '../app/services/GoogleMailBoxService.php';

class EmailSend extends Controller {
  public function __construct() {

  }
  public function index($auth_service) {
    return parent::view('emailsend/index');
  }

  public function sendEmailWithAttach($auth_service) {
        // echo(var_dump($_POST));
        $mail_service = new GoogleMailBoxService($auth_service);
        $result = $mail_service->send_mails(var_dump($_POST));
        echo json_encode(['send' => 1]);
        exit;
    // Email configuration 
    // $to = $_POST["userEmail"]; 
    // $from = '@gmail.com'; 
    // $fromName = 
    // $subject = $_POST["subject"];  
    // // Attachment files 
    // $files = $_FILES["attachment"]; 
    // $htmlContent = $_POST["content"]; 
    // // Call function and pass the required arguments 
    // $sendEmail = self::multi_attach_mail($to, $subject, $htmlContent, $from, $fromName, $files); 

    // // Email sending status 
    // if($sendEmail){ 
    //     echo 'The email has sent successfully.'; 
    // }else{ 
    //     echo 'Mail sending failed!'; 
    // }
    // foreach ($_FILES["attachment"]["name"] as $k => $v) {
    // $mail->AddAttachment( $_FILES["attachment"]["tmp_name"][$k], $_FILES["attachment"]["name"][$k] );
  //   $mail = new PHPMailer();
  //   $mail->IsSMTP();
  //   $mail->SMTPDebug = 0;
  //   $mail->SMTPAuth = TRUE;
  //   $mail->SMTPSecure = "ssl";
  //   $mail->Port     = 465;  
  //   $mail->Username = "YOUR USER_NAME";
  //   $mail->Password = "YOUR PASSWORD";
  //   $mail->Host     = "YOUR HOST";
  //   $mail->Mailer   = "smtp";
  //   $mail->SetFrom($_POST["userEmail"], $_POST["userName"]);
  //   $mail->AddReplyTo($_POST["userEmail"], $_POST["userName"]);
  //   $mail->AddAddress("...@gmail.com");	
  //   $mail->Subject = $_POST["subject"];
  //   $mail->WordWrap   = 80;
  //   $mail->MsgHTML($_POST["content"]);

  //   foreach ($_FILES["attachment"]["name"] as $k => $v) {
  //       $mail->AddAttachment( $_FILES["attachment"]["tmp_name"][$k], $_FILES["attachment"]["name"][$k] );
  //   }

  //   $mail->IsHTML(true);

  //   if(!$mail->Send()) {
  //     echo "<p class='error'>Problem in Sending Mail.</p>";
  //   } else {
  //     echo "<p class='success'>Mail Sent Successfully.</p>";
  //   }	
  // }
  /* 
 * Custom PHP function to send an email with multiple attachments 
 * $to Recipient email address 
 * $subject Subject of the email 
 * $message Mail body content 
 * $senderEmail Sender email address 
 * $senderName Sender name 
 * $files Files to attach with the email 
 */ 
  // public function multi_attach_mail($to, $subject, $message, $senderEmail, $senderName, $files = array()){ 
  
  //     $from = $senderName." <".$senderEmail.">";  
  //     $headers = "From: $from"; 
  
  //     // Boundary  
  //     $semi_rand = md5(time());  
  //     $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  
  
  //     // Headers for attachment  
  //     $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";  
  
  //     // Multipart boundary  
  //     $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
  //     "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";  
  
  //     // Preparing attachment 
  //     if(!empty($files)){ 
  //       echo(count($files));
  //         for($i=0;$i<count($files);$i++){ 
  //             if(is_file($files[$i])){ 
  //                 $file_name = basename($files[$i]); 
  //                 $file_size = filesize($files[$i]); 
  //                 $message .= "--{$mime_boundary}\n"; 
  //                 $fp =    @fopen($files[$i], "rb"); 
  //                 $data =  @fread($fp, $file_size); 
  //                 @fclose($fp); 
  //                 $data = chunk_split(base64_encode($data)); 
  //                 $message .= "Content-Type: application/octet-stream; name=\"".$file_name."\"\n" .  
  //                 "Content-Description: ".$file_name."\n" . 
  //                 "Content-Disposition: attachment;\n" . " filename=\"".$file_name."\"; size=".$file_size.";\n" .  
  //                 "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
  //             } 
  //         } 
  //     } 
      
  //     $message .= "--{$mime_boundary}--"; 
  //     $returnpath = "-f" . $senderEmail; 
      
  //     // Send email 
  //     $mail = @mail($to, $subject, $message, $headers, $returnpath);  
      
  //     // Return true, if email sent, otherwise return false 
  //     if($mail){ 
  //         return true; 
  //     }else{ 
  //         return false; 
  //     } 
  }
}