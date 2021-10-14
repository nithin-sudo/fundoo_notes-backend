<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * @since 24-sep-2021
 * 
 * This class is respnsible for sending the message to the given email id and token.
 */
class SendEmailRequest 
{

     /**
     * This function takes two args from the function in ForgotPasswordcontroller and successfully 
     * sends the token as a reset link to the user email id. 
     */
    public function sendEmail($email,$token)
    {
        $name = 'Nithin Krishna';
        $email = $email;
        $subject = 'Regarding your Password Reset';
        $data ="Your password Reset Link <br>".$token;
          
        require '..\vendor\autoload.php';
        $mail = new PHPMailer(true);

        try
        {                                       
            $mail->isSMTP();                                          
            $mail->Host       = 'smtp.gmail.com';                        
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = 'nithinkrishnasathram@gmail.com';                  
            $mail->Password   = 'WesAnderson@123W';                              
            $mail->SMTPSecure = 'tls'; 
            $mail->Port       = 587;
            $mail->setFrom('nithinkrishnasathram@gmail.com', 'nithin'); 
            $mail->addAddress($email,$name);
            $mail->isHTML(true);  
            $mail->Subject =  $subject;
            $mail->Body    = $data;
            $dt = $mail->send();

            if($dt)
                return true;
            else
                return false;

        }
        catch (Exception $e) 
        {
            return back()->with('error','Message could not be sent.');
        }
    }

    /**
     * This function takes three args and sends the data 
     * to Given Email. 
     */
    public function sendEmailToCollab($email,$data,$currentUserEmail)
    {
        $name = 'Nithin Krishna';
        $email = $email;
        $subject = 'Note shared with you:';
        $data = $currentUserEmail.' shared a Note with you <br>'.$data;
          
        require '..\vendor\autoload.php';
        $mail = new PHPMailer(true);

        try
        {                                       
            $mail->isSMTP();                                          
            $mail->Host       = 'smtp.gmail.com';                        
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = 'nithinkrishnasathram@gmail.com';                  
            $mail->Password   = 'WesAnderson@123W';                              
            $mail->SMTPSecure = 'tls'; 
            $mail->Port       = 587;
            $mail->setFrom('nithinkrishnasathram@gmail.com','nithin'); 
            $mail->addAddress($email,$name);
            $mail->isHTML(true);  
            $mail->Subject =  $subject;
            $mail->Body    = $data;
            $dt = $mail->send();

            if($dt)
                return true;
            else
                return false;

        }
        catch (Exception $e) 
        {
            return back()->with('error','Message could not be sent.');
        }
    }

}
