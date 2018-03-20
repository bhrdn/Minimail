<?php
namespace VirushPrivateCode;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    protected $mail, $body, $settings;

    private $debugMode = [
        'debug'       => 3,
        'development' => 2,
        'production'  => 1,
        'testing'     => 0,
    ];

    public function __construct(array $settings = [])
    {
        $this->settings = array_merge([
            'host'        => '',
            'port'        => '',
            'username'    => '',
            'password'    => '',
            'auth'        => true,
            'secure'      => 'tsl',
            'senderEmail' => '',
            'senderName'  => '',
            'priority'    => '',
        ], $settings);

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();

        $this->mail->headerLine("format", "flowed");
        $this->mail->Encoding   = 'base64';
        $this->mail->CharSet    = 'UTF-8';
        $this->mail->Priority   = $this->settings['priority'] ?? random_int(1, 3);
        $this->mail->Host       = $this->settings['host'];
        $this->mail->Port       = $this->settings['port'];
        $this->mail->Username   = $this->settings['username'];
        $this->mail->Password   = $this->settings['password'];
        $this->mail->SMTPAuth   = $this->settings['auth'];
        $this->mail->SMTPSecure = $this->settings['secure'];
    }

    public function setSender($senderEmail, $senderName)
    {
        $this->mail->setFrom($senderEmail, $senderName);
        return $this;
    }

    public function debugMode($mode)
    {
        $this->mail->SMTPDebug = $this->debugMode[$mode] ?? 1;
        return $this;
    }

    public function to($address, $name = '')
    {
        $this->mail->addAddress($address, $name);
        return $this;
    }

    public function withSubject($subject)
    {
        $this->mail->Subject = $subject;
        return $this;
    }

    public function withBody($letter, array $datas = [])
    {
        $virush = new \Diactoros\Diactoros;
        if (file_exists($letter)) {
            $letter = strtr(file_get_contents($letter), $datas);
            $letter = preg_replace_callback(
                "/(?<=>)([^>]+)(?=<)/",
                function ($matches) use ($virush) {
                    $virush->addText($matches[1]);
                    return $virush->encode();
                }, $letter);
        }
        
        $this->mail->Body = strtr($letter, $datas);
        return $this;
    }

    public function addAttachments(array $files)
    {
        foreach ($files as $filepath) {
            if (file_exists($filepath)) {
                $this->mail->addAttachment($filepath);
            }
        }
        return $this;
    }

    public function send()
    {
        $this->mail->send();
    }
}
