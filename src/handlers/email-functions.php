<?php
// Email sending function using SMTP
function sendEmail($to, $subject, $body, $isHtml = true) {
    require_once __DIR__ . '/../config.php';
    
    $smtp_host = env('SMTP_HOST');
    $smtp_port = env('SMTP_PORT');
    $smtp_username = env('SMTP_USERNAME');
    $smtp_password = env('SMTP_PASSWORD');
    $from_email = env('MAIL_FROM_ADDRESS');
    $from_name = env('MAIL_FROM_NAME');
    
    // Create email headers
    $headers = "From: $from_name <$from_email>\r\n";
    $headers .= "Reply-To: $from_email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    
    if ($isHtml) {
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    } else {
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    }
    
    // Use PHPMailer for better SMTP support
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;
            $mail->SMTPSecure = env('SMTP_ENCRYPTION', 'tls');
            $mail->Port = $smtp_port;
            
            // Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email send failed: " . $mail->ErrorInfo);
            return false;
        }
    } else {
        // Fallback to PHP mail() function
        return mail($to, $subject, $body, $headers);
    }
}

// Send verification email
function sendVerificationEmail($email, $firstName, $token) {
    $app_url = env('APP_URL');
    $verify_link = "$app_url/src/handlers/verify-handler.php?token=$token";
    
    $subject = "Verify Your Email - " . env('APP_NAME');
    
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .button { display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; color: #777; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to BOOK HUB!</h1>
            </div>
            <div class='content'>
                <h2>Hello $firstName,</h2>
                <p>Thank you for registering with BOOK HUB. To complete your registration, please verify your email address by clicking the button below:</p>
                <p style='text-align: center;'>
                    <a href='$verify_link' class='button'>Verify Email Address</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all; color: #667eea;'>$verify_link</p>
                <p>This link will expire in 24 hours.</p>
                <p>If you didn't create an account with BOOK HUB, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>&copy; 2025 BOOK HUB. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body, true);
}

// Send password reset email
function sendPasswordResetEmail($email, $firstName, $token) {
    $app_url = env('APP_URL');
    $reset_link = "$app_url/reset-password.html?token=$token";
    
    $subject = "Reset Your Password - " . env('APP_NAME');
    
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .button { display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; color: #777; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Password Reset Request</h1>
            </div>
            <div class='content'>
                <h2>Hello $firstName,</h2>
                <p>We received a request to reset your password. Click the button below to create a new password:</p>
                <p style='text-align: center;'>
                    <a href='$reset_link' class='button'>Reset Password</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all; color: #667eea;'>$reset_link</p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
            </div>
            <div class='footer'>
                <p>&copy; 2025 BOOK HUB. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body, true);
}
?>

