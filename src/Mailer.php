<?php
namespace VirushPrivateCode;

use Carbon\Carbon;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Mailer Classes
 * @see https://github.com/phpindonesia/phpindonesia.or.id-membership2/
 */
class Mailer
{
    /**
     * @var PHPMailer
     */
    protected $mail;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Debug mode
     *
     * @var array
     */
    private $debugMode = [
        'debug'       => 3,
        'development' => 2,
        'production'  => 1,
        'testing'     => 0,
    ];

    /**
     * Mailer constructor.
     *
     * @param array $settings
     */
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
        $this->mail->Encoding   = 'base64';
        $this->mail->CharSet    = 'UTF-8';
        $this->mail->Priority   = $this->settings['priority'] ?? random_int(1, 3);

        // SMTP Details
        $this->mail->Host       = $this->settings['host'];
        $this->mail->Port       = $this->settings['port'];
        $this->mail->Username   = $this->settings['username'];
        $this->mail->Password   = $this->settings['password'];
        $this->mail->SMTPAuth   = $this->settings['auth'];
        $this->mail->SMTPSecure = $this->settings['secure'];
    }

    /**
     * Setup Sender
     *
     * @param string $senderEmail
     * @param string $senderName
     */
    public function setSender($senderEmail, $senderName)
    {
        $this->mail->setFrom($senderEmail, $senderName);
        return $this;
    }

    /**
     * Add headers email
     *
     * @param array $headers
     */
    public function withHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            if ($header === 'Date') {
                $carbon = new Carbon($value);
                $value  = $carbon->toRfc7231String();
                // $this->mail->MessageDate = $carbon->toRfc7231String();
            }

            $this->mail->addCustomHeader($header, $value);
        }

        return $this;
    }

    /**
     * Set mailer debug mode
     *
     * @param string $mode
     * @return $this
     */
    public function debugMode($mode)
    {
        $this->mail->SMTPDebug = $this->debugMode[$mode] ?? 1;
        return $this;
    }

    /**
     * Add recipient email address.
     *
     * @param string $address
     * @return $this
     */
    public function to($address, $name = '')
    {
        $this->mail->addAddress($address, $name);
        return $this;
    }

    /**
     * Add email subject.
     *
     * @param string $subject
     * @return $this
     */
    public function withSubject($subject)
    {
        $this->mail->Subject = $subject;
        return $this;
    }

    /**
     * Write email body.
     *
     * @param string $letter
     * @param array  $data
     * @return $this
     */
    public function withBody($letter, array $datas = [])
    {
        $virush = new \Diactoros\Diactoros;
        if (file_exists($letter)) {
            // set content-type: text/html
            $this->mail->isHTML(true);

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

    /**
     * Add attachments.
     *
     * @param array $files
     * @return $this
     */
    public function addAttachments(array $files)
    {
        foreach ($files as $filepath) {
            if (file_exists($filepath)) {
                $this->mail->addAttachment($filepath);
            }
        }
        return $this;
    }

    /**
     * Send the thing.
     *
     * @return mixed
     */
    public function send()
    {
        $this->mail->send();
    }
}
