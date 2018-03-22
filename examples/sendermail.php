<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;

$faker = new Faker\Generator();
$faker->addProvider(new Faker\Provider\en_US\Person($faker));
$faker->addProvider(new Faker\Provider\en_US\Address($faker));
$faker->addProvider(new Faker\Provider\en_US\PhoneNumber($faker));

$mail = new \VirushPrivateCode\Mailer([
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
            ->withHeaders(['Date' => '2 days ago'])
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
