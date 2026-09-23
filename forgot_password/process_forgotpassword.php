<?php
session_start();
require_once '../db_connection.php';
require_once '../vendor/autoload.php';

header('Content-Type: application/json'); // Set JSON response header

// Check if email is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        exit();
    }
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT f.faculty_id, u.user_id, f.full_name FROM faculty f 
                          JOIN users u ON f.faculty_id = u.faculty_id 
                          WHERE f.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Email not found in our system']);
        exit();
    }
    
    $user = $result->fetch_assoc();
    $faculty_id = $user['faculty_id'];
    $user_id = $user['user_id'];
    $full_name = $user['full_name'];
    $stmt->close();
    
    // Generate reset link
    $reset_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
                 "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . 
                 "/resetpassword.php?user_id=$user_id";
    
    // Send password reset email
    $email_sent = sendPasswordResetEmail($email, $full_name, $reset_link);
    
    if ($email_sent) {
        echo json_encode(['success' => true, 'message' => 'Password reset link has been sent to your email']);
        exit();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send reset email. Please try again later.']);
        exit();
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

function sendPasswordResetEmail($email, $full_name, $reset_link) {
    $subject = "Password Reset Request - PLP Faculty Portal";
    
    $message = "
    <html>
    <head>
        <title>Password Reset Request</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #017a2b; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .button { 
                display: inline-block; 
                padding: 10px 20px; 
                background-color: #acacac; 
                color: white; 
                text-decoration: none; 
                border-radius: 5px; 
                margin: 15px 0;
            }
            .button:hover { background-color: #807f7f; }
            .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>PLP Faculty Portal</h2>
            </div>
            <div class='content'>
                <p>Hello $full_name,</p>
                <p>We received a request to reset your password for the PLP Faculty Portal.</p>
                <p>Please click the button below to reset your password:</p>
                <p><a href='$reset_link' class='button'>Reset Password</a></p>
                <p>If you didn't request this password reset, you can safely ignore this email.</p>
                <p>This link will expire in 24 hours for security reasons.</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

function sendEmail($to, $subject, $htmlBody) {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $smtpHost = getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com';
        $smtpPort = (int)(getenv('SMTP_PORT') ?: 587);
        $smtpUser = getenv('SMTP_USER') ?: '8cc35a002@smtp-brevo.com';
        $smtpPass = getenv('SMTP_PASS') ?: (getenv('SMTP_PASSWORD') ?: 'JSh1qV4zbR7DWaI0');
        $smtpFrom = getenv('SMTP_FROM_EMAIL') ?: 'plp.no.reply1@gmail.com';
        $smtpFromName = getenv('SMTP_FROM_NAME') ?: 'PLP Faculty Portal';
        $smtpSecure = strtolower(getenv('SMTP_SECURE') ?: 'tls');

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = ($smtpSecure === 'ssl') ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtpPort;

        // Email content
        $mail->setFrom($smtpFrom, $smtpFromName);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>