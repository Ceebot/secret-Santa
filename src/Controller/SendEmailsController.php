<?php

namespace App\Controller;

use App\Entity\Player;
use App\Service\SendEmailService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер отправки писем игрокам
 */
class SendEmailsController extends AbstractController
{
    /**
     * Отправка писем игрокам
     *
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws Exception
     */
    #[Route('/send-emails', methods: ['POST'])]
    public function send(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $players = $entityManager
            ->getRepository(Player::class)
            ->findAll();

        if (count($players) < 4) {
            throw new Exception('Требуется минимум 4 участника для игры');
        }

        $pairs = $this->generatePairs($players);

        (new SendEmailService())->sendPairs($pairs);

        return $this->redirectToRoute('index');
    }

    /**
     * Сортировка игроков
     *
     * @param array $players
     * @return array
     */
    private function generatePairs(array $players): array
    {
        do {
            shuffle($players);
            $isValid = true;

            $pairs = [];

            for ($i = 0; $i < count($players); $i++) {
                $santa = $players[$i];
                $recipient = $players[($i + 1) % count($players)];

                if ($santa === $recipient) {
                    $isValid = false;
                    break;
                }

                $pairs[] = [
                    'santa' => $santa,
                    'recipient' => $recipient,
                ];
            }
        } while (!$isValid);

        return $pairs;
    }
}
