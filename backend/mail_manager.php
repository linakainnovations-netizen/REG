<?php
/**
 * Mail Manager Utility
 * Wrapper for PHPMailer to handle system notifications and approvals.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';

class MailManager {
    /**
     * Send a system email
     */
    public static function send($to, $subject, $body, $attachments = []) {
        $mail = new PHPMailer(true);

        try {
            // Server settings - PLACEHOLDERS: User must update these
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Use your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'portal@stpaulchipata.org'; // SMTP username
            $mail->Password   = 'your-password';           // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('portal@stpaulchipata.org', 'St. Paul Chipata Portal');
            $mail->addAddress($to);

            // Attachments
            foreach ($attachments as $path) {
                if (file_exists($path)) {
                    $mail->addAttachment($path);
                }
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Email for Leader Approval (Public Event)
     */
    public static function sendApprovalRequest($leaderEmail, $eventTitle, $token) {
        $approveLink = "http://" . $_SERVER['HTTP_HOST'] . "/St._Paul_Chipata_Portal/verify-event?token=" . $token;
        
        $body = "
        <div style='font-family: sans-serif; padding: 20px; border: 1px solid #e5e7eb; border-radius: 10px;'>
            <h2 style='color: #1e3a8a;'>New Event Approval Request</h2>
            <p>A new event request has been submitted for your group: <strong>$eventTitle</strong></p>
            <p>Please review and approve this event to make it public on the Parish Portal.</p>
            <div style='margin-top: 30px;'>
                <a href='$approveLink' style='background: #3b82f6; color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: bold;'>Approve Now</a>
            </div>
            <p style='margin-top: 20px; color: #6b7280; font-size: 0.8rem;'>If you did not expect this request, please ignore this email.</p>
        </div>";

        return self::send($leaderEmail, "Action Required: Approve $eventTitle", $body);
    }
}
?>
