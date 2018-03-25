<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;

$mail = new \Minimail\Mailer([
    'host'     => '',
    'port'     => '',
    'username' => '',
    'password' => '',
]);

list($list, $letter) = [trim(readline("[*] Locate list(email): ")), trim(readline("[*] Locate letter: "))];
if (file_exists($list) and file_exists($letter)) {
    foreach (explode("\n", file_get_contents($list)) as $email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            continue;
        }

        $mail->setSender('foo@bar.com', 'Foo')
            ->withHeaders(['X-Foo' => 'Bar'])
            ->withSubject('Foo subject')
            ->withBody($letter, [
                '{url}' => '#',
            ])->to($email);

        try {
            $mail->send();
            printf("[%s] %s => OK" . PHP_EOL, date('H:i:s'), $email);
        } catch (Exception $e) {
            printf("[!] Error: %s", $mail->ErrorInfo);
            break;
        }
    }
}
