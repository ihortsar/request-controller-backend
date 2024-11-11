<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

class SendMail
{
    private $mail;
    private $IATARequests;
    public function __construct($IATARequests)
    {
        $this->IATARequests = $IATARequests;
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'ihortsarkov@gmail.com'; //SMTP username
        $this->mail->Password   = ''; //SMTP password (for many providers as app password)
        $this->mail->SMTPSecure = 'tls'; //tls or ssl
        $this->mail->Port       = 587; //587 (TLS) or 465 (SSL)
        $this->mail->setFrom('ihortsarkov@gmail.com', 'World Transfer'); // email address of a sender
        $this->mail->addAddress('igortsarkov1988@gmail.com'); // email address of a receiver
        $this->mail->isHTML(true);
        $this->mail->Subject = 'IATA Warnung';
    }

    public function sendEmail()
    {
        $lowCountFound = false;
        $emailBody = $this->setEmailBody();
        if (is_array($this->IATARequests)) {
            $this->transformEmailBodyAndLowCountToTrue($emailBody, $lowCountFound);
            if ($lowCountFound) {
                return $this->lowCountFound($emailBody);
            } else {
                return ['message' =>  "Keine Flughaefen unter 50 Anfragen gefunden."];
            }
        } else {
            return ['message' => "Keine Info vorhanden."];
        }
    }


    private function setEmailBody()
    {
        return '<h1>IATA Warnung</h1>
        <p>Die folgenden Flughaefen wurden in den letzten 24 Stunden unter 50 Mal angefragt:</p>';
    }


    private function transformEmailBodyAndLowCountToTrue(&$emailBody, &$lowCountFound)
    {
        $emailBody .= '<ul>';
        foreach ($this->IATARequests as $IATARequest) {
            if ($IATARequest['count'] < 50) {
                $emailBody .= '<li>' . htmlspecialchars($IATARequest['iata_code']) . ': ' . htmlspecialchars($IATARequest['count']) . ' Anfragen</li>';
                $lowCountFound = true;
            }
        }
        $emailBody .= '</ul>';
    }


    private function lowCountFound($emailBody)
    {
        $this->mail->Body = $emailBody;
        try {
            $this->mail->send();
            return ['message' => "Warning email gesendet."];
        } catch (Exception $e) {
            return ['message' => "Email Verschicken ist schief gelaufen. Error: {$this->mail->ErrorInfo}"];
        }
    }
}
