<?php
use Resend\Client;

class Mailer {
    private $resend;

    public function __construct() {
        $this->resend = Resend::client('re_b5AWjYqU_B2XiAkg4iRkNodBvaJANw6zn'); // Replace with your actual API key
    }

    public function sendVerificationEmail($to, $code) {
        try {
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

            $this->resend->emails->send([
                'from' => 'Sistema de Chamados TI',
                'to' => $to,
                'subject' => 'Verificação de E-mail - Sistema de Chamados TI',
                'html' => $body,
                'text' => "Seu código de verificação é: {$code}"
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$e->getMessage()}");
            return false;
        }
    }

    public function sendTicketNotification($to, $ticketId, $status) {
        try {
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

            $this->resend->emails->send([
                'from' => 'Sistema de Chamados TI',
                'to' => $to,
                'subject' => "Atualização do Chamado #{$ticketId} - Sistema de Chamados TI",
                'html' => $body,
                'text' => "Seu chamado #{$ticketId} foi atualizado para o status: {$status}"
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$e->getMessage()}");
            return false;
        }
    }
}
?>