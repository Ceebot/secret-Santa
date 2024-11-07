<?php

namespace App\Services;

use Exception;

/**
 * Сервис по отправке почты
 */
class SendEmailService
{
    private string $url;
    private string $emailDomain;
    private string $key;

    public function __construct()
    {
        $this->url = 'https://api.smtp.bz/v1/smtp/';
        $this->emailDomain = $_ENV['EMAIL_DOMAIN'];
        $this->key = $_ENV['EMAIL_API_KEY'];
    }

    /**
     * Отправка писем на почту
     *
     * @param array $pairs
     * @return void
     * @throws Exception
     */
    public function sendPairs(array $pairs): void
    {
        foreach ($pairs as $pair) {
            $text = 'Уважаемый ' . $pair['santa']->getFio() . ', вы должны поздравить участника ' . $pair['recipient']->getFio() . ' и подарить ему подарок';

            $params = [
                'subject' => 'Задание от секретного Санты',
                'name' => "Секретный Санта",
                'html' => $text,
                'from' => $this->emailDomain,
                'to' => $pair['santa']->getEmail(),
                'to_name' => $pair['santa']->getFio(),
                'headers' => "[{ 'x-tag': 'my_newsletter_ids' }]",
            ];

            $res = $this->request('send', $params, 'POST');

            error_log(print_r(json_encode($res), true) . "\n", 3, "/tmp/log1");
        }
    }

    /**
     * Запрос к почтовому сервису на отправку письма на почту
     *
     * @param string $url
     * @param array $params
     * @param string $method
     * @return array
     * @throws Exception
     */
    public function request(string $url, array $params, string $method = 'POST'): array
    {
        $url = $this->url . $url;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                "authorization: $this->key",
            ],
            CURLOPT_POSTFIELDS => http_build_query($params),
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return json_decode($err, 1);
        } else {
            return json_decode($response, 1);
        }
    }
}
