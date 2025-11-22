<?php
/**
 * Email Class - Gửi email qua SMTP hoặc mail() function
 * 
 * Để gửi email thật, cần cấu hình SMTP trong config.php:
 * - SMTP_HOST: smtp.gmail.com (hoặc SMTP server khác)
 * - SMTP_PORT: 587 (TLS) hoặc 465 (SSL)
 * - SMTP_USERNAME: Email của bạn
 * - SMTP_PASSWORD: App Password (với Gmail) hoặc mật khẩu
 * - SMTP_ENCRYPTION: 'tls' hoặc 'ssl'
 */
class Email {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $smtp_encryption;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        // Lấy cấu hình từ config hoặc dùng giá trị mặc định
        $this->smtp_host = defined('SMTP_HOST') ? SMTP_HOST : '';
        $this->smtp_port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $this->smtp_username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
        $this->smtp_password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $this->smtp_encryption = defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : 'tls';
        $this->from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@cinehub.com';
        $this->from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'CineHub';
    }
    
    /**
     * Gửi email - tự động chọn phương thức (SMTP hoặc mail())
     */
    public function send($to, $subject, $message, $is_html = true) {
        // Nếu có cấu hình SMTP, thử dùng SMTP
        if (!empty($this->smtp_host) && !empty($this->smtp_username) && !empty($this->smtp_password)) {
            $result = $this->sendViaSMTP($to, $subject, $message, $is_html);
            if ($result) {
                return true;
            }
            // Nếu SMTP fail, fallback về mail()
            error_log("SMTP send failed, falling back to mail() function");
        }
        
        // Sử dụng mail() function
        return $this->sendViaMailFunction($to, $subject, $message, $is_html);
    }
    
    /**
     * Gửi email qua SMTP sử dụng stream_socket_client
     */
    private function sendViaSMTP($to, $subject, $message, $is_html) {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            // Kết nối SMTP
            $host = $this->smtp_encryption === 'ssl' ? 'ssl://' . $this->smtp_host : $this->smtp_host;
            $socket = @stream_socket_client(
                $host . ':' . $this->smtp_port,
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );
            
            if (!$socket) {
                error_log("SMTP Connection failed: $errstr ($errno)");
                return false;
            }
            
            // Đọc welcome message
            $this->readResponse($socket);
            
            // EHLO
            $this->sendCommand($socket, "EHLO " . $this->smtp_host);
            
            // STARTTLS nếu cần
            if ($this->smtp_encryption === 'tls') {
                $this->sendCommand($socket, "STARTTLS");
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->sendCommand($socket, "EHLO " . $this->smtp_host);
            }
            
            // AUTH LOGIN
            $this->sendCommand($socket, "AUTH LOGIN");
            $this->sendCommand($socket, base64_encode($this->smtp_username));
            $response = $this->sendCommand($socket, base64_encode($this->smtp_password));
            
            if (strpos($response, '235') === false) {
                error_log("SMTP Authentication failed: $response");
                fclose($socket);
                return false;
            }
            
            // MAIL FROM
            $this->sendCommand($socket, "MAIL FROM: <" . $this->from_email . ">");
            
            // RCPT TO
            $this->sendCommand($socket, "RCPT TO: <" . $to . ">");
            
            // DATA
            $this->sendCommand($socket, "DATA");
            
            // Headers và body
            $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
            $headers .= "Reply-To: " . $this->from_email . "\r\n";
            $headers .= "To: <" . $to . ">\r\n";
            $headers .= "Subject: " . $subject . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            if ($is_html) {
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            } else {
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            }
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            
            fwrite($socket, $headers . "\r\n" . $message . "\r\n.\r\n");
            $response = $this->readResponse($socket);
            
            // QUIT
            $this->sendCommand($socket, "QUIT");
            fclose($socket);
            
            if (strpos($response, '250') !== false) {
                error_log("Email sent successfully via SMTP to: $to");
                return true;
            } else {
                error_log("SMTP Send failed: $response");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gửi command và đọc response
     */
    private function sendCommand($socket, $command) {
        fwrite($socket, $command . "\r\n");
        return $this->readResponse($socket);
    }
    
    /**
     * Đọc response từ SMTP server
     */
    private function readResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }
    
    /**
     * Gửi email sử dụng mail() function
     */
    private function sendViaMailFunction($to, $subject, $message, $is_html) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        if ($is_html) {
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        } else {
            $headers .= "Content-type:text/plain;charset=UTF-8" . "\r\n";
        }
        $headers .= "From: " . $this->from_name . " <" . $this->from_email . ">" . "\r\n";
        $headers .= "Reply-To: " . $this->from_email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        
        $result = @mail($to, $subject, $message, $headers);
        if ($result) {
            error_log("Email sent successfully via mail() to: $to");
        } else {
            error_log("Email send failed via mail() to: $to");
        }
        return $result;
    }
}
?>

