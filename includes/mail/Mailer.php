<?php

namespace App\Mail;

require_once '../../vendor/autoload.php';

use Resend;

class Mailer
{
    private $resend;
    private $fromEmail = 'suporte@iron-atlas.app';
    private $fromName = 'Sistema de Chamados TI';

    public function __construct()
    {
        // Inicializar o cliente Resend corretamente
        $this->resend = Resend::client('re_ezUuFU3Z_LzwTmKnUhHUt2yLTnPBBmfpa');
    }

    public function sendVerificationEmail($to, $code)
    {
        try {
            $params = [
                'from' => "{$this->fromName} <{$this->fromEmail}>",
                'to' => [$to],
                'subject' => 'Verificação de E-mail - Sistema de Chamados TI',
                'html' => $this->getVerificationEmailBody($code),
                'text' => "Seu código de verificação é: {$code}"
            ];

            $result = $this->resend->emails->send($params);

            // Log para depuração
            error_log("Resposta da API Resend: " . $result->toJson());

            // Verifica se o ID está presente e não está vazio
            $responseArray = json_decode(json_encode($result), true);
            if (isset($responseArray['id']) && !empty($responseArray['id'])) {
                error_log("Email enviado com sucesso! ID: " . $responseArray['id']);
                return true;
            }


            // Se cair aqui, significa que o e-mail não foi enviado com sucesso
            error_log("Falha ao enviar e-mail - ID não encontrado.");
            return false;
        } catch (\Exception $e) {
            error_log("Erro ao enviar e-mail de verificação: " . $e->getMessage());
            return false;
        }
    }

    public function sendTicketNotification($to, $ticketId, $status)
    {
        try {
            $params = [
                'from' => "{$this->fromName} <{$this->fromEmail}>",
                'to' => [$to],
                'subject' => "Atualização do Chamado #{$ticketId} - Sistema de Chamados TI",
                'html' => $this->getTicketNotificationBody($ticketId, $status),
                'text' => "Seu chamado #{$ticketId} foi atualizado para o status: {$status}"
            ];

            $response = $this->resend->emails->send($params);
            return isset($response->id);
        } catch (\Exception $e) {
            error_log("Erro ao enviar notificação de chamado: " . $e->getMessage());
            return false;
        }
    }

    private function getVerificationEmailBody($code)
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #0d6efd;'>Verificação de E-mail</h2>
            <p>Seu código de verificação é:</p>
            <p style='font-size: 24px; font-weight: bold; color: #0d6efd;'>{$code}</p>
            <p>Insira este código no campo de verificação para concluir o cadastro.</p>
            <hr style='border: 1px solid #dee2e6; margin: 20px 0;'>
            <p style='color: #6c757d; font-size: 12px;'>Este é um e-mail automático.</p>
        </div>
        ";
    }

    private function getTicketNotificationBody($ticketId, $status)
    {
        return "
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
            <p style='color: #6c757d; font-size: 12px;'>Este é um e-mail automático.</p>
        </div>
        ";
    }
}
