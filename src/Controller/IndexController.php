<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.smtp.bz/v1/smtp/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "authorization: lIrACnrgHC5qkLPxrSn1nunIPYBLt2fEAx64"
            ],

            CURLOPT_POSTFIELDS => http_build_query([
                'subject' => "Тема письма", // Обязательно
                'name' => "Секретный Санта",
                'html' => "<html><head></head><body><p>My text</p></body></html>", // Обязательно
                'reply' => "info@mycompany.com",
                'from' => "mihailceeb8@mail.ru", // Обязательно
                'to' => "griskomihail548@gmail.com", // Обязательно
                'to_name' => "Имя получателя",
                'headers' => "[{ 'x-tag': 'my_newsletter_ids' }]",
                'text' => "Text version message"
            ])
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }

        return $this->render('index.twig', ['text' => $response]);
    }
}
