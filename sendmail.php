<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';


/**
 * Sends an email using PHPMailer.
 *
 * @param string $subject The subject of the email.
 * @param string $recipient_email The email address of the primary recipient.
 * @param string $from_email The email address of the sender.
 * @param string $body The HTML body of the email.
 * @param string $recipient_name The name of the primary recipient.
 * @param string|null $cc A comma-separated string of email addresses for CC.
 * @param string|null $bcc A comma-separated string of email addresses for BCC.
 * @return bool True on success, false on failure.
 */
function sendmail($subject, $recipient_email, $from_email, $body, $recipient_name, $cc = null, $bcc = null)
{
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // === SERVER SETTINGS ===
        // IMPORTANT: Replace the following with your own SMTP server details.
        // For security, it is highly recommended to use environment variables instead of hardcoding credentials.
        // Example: $mail->Host = getenv('SMTP_HOST');

        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Or PHPMailer::ENCRYPTION_SMTPS for port 465
        $mail->Port       = SMTP_PORT;

        // === RECIPIENTS ===
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME); // Sender's email and name
        $mail->addAddress($recipient_email, $recipient_name); // Primary recipient
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

        // Add CC recipients if provided
        if ($cc) {
            $cc_emails = explode(',', $cc);
            foreach ($cc_emails as $cc_email) {
                $mail->addCC(trim($cc_email));
            }
        }

        // Add BCC recipients if provided
        if ($bcc) {
            $bcc_emails = explode(',', $bcc);
            foreach ($bcc_emails as $bcc_email) {
                $mail->addBCC(trim($bcc_email));
            }
        }

        // === CONTENT ===
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // Create a plain-text version of the email

        $mail->send();
        return true;
    } catch (Exception $e) {
        // You can log the error for debugging purposes
        // error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>