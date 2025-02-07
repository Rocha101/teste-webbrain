<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        // Configurações do servidor
        $this->mail->isSMTP();
        $this->mail->Host = 'live.smtp.mailtrap.io'; // Altere para seu servidor SMTP
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'smtp@mailtrap.io'; // Altere para seu email
        $this->mail->Password = '252cb9840cd8fcceed53548e7ed8817f'; // Altere para sua senha de aplicativo
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->CharSet = 'UTF-8';

        // Remetente
        $this->mail->setFrom('smtp@mailtrap.io', 'Sistema de Chamados TI'); // Altere para seu email
    }

    public function sendVerificationEmail($to, $code) {
        try {
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Verificação de E-mail - Sistema de Chamados TI';
            
            // Template do e-mail
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #0d6efd;'>Verificação de E-mail</h2>
                <p>Obrigado por se cadastrar no Sistema de Chamados TI!</p>
                <p>Seu código de verificação é:</p>
                <div style='background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                    {$code}
                </div>
                <p>Este código é válido por 24 horas.</p>
                <p>Se você não solicitou este código, por favor ignore este e-mail.</p>
                <hr style='border: 1px solid #dee2e6; margin: 20px 0;'>
                <p style='color: #6c757d; font-size: 12px;'>
                    Este é um e-mail automático, por favor não responda.
                </p>
            </div>
            ";

            $this->mail->Body = $body;
            $this->mail->AltBody = "Seu código de verificação é: {$code}";

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendTicketNotification($to, $ticketId, $status) {
        try {
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = "Atualização do Chamado #{$ticketId} - Sistema de Chamados TI";
            
            // Template do e-mail
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #0d6efd;'>Atualização de Chamado</h2>
                <p>Seu chamado #{$ticketId} foi atualizado!</p>
                <p>Status atual: <strong>{$status}</strong></p>
                <p>Para visualizar os detalhes, acesse o sistema:</p>
                <p style='text-align: center;'>
                    <a href='http://localhost/chamados-ti/views/tickets/view.php?id={$ticketId}' 
                       style='background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>
                        Ver Chamado
                    </a>
                </p>
                <hr style='border: 1px solid #dee2e6; margin: 20px 0;'>
                <p style='color: #6c757d; font-size: 12px;'>
                    Este é um e-mail automático, por favor não responda.
                </p>
            </div>
            ";

            $this->mail->Body = $body;
            $this->mail->AltBody = "Seu chamado #{$ticketId} foi atualizado para o status: {$status}";

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}
?>
