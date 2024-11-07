<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер главной страницы
 */
class IndexController extends AbstractController
{
    /**
     * Главная страница
     *
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $title = 'Тайный Санта';

        $playersRepo = $entityManager
            ->getRepository(Player::class)
            ->findAll();

        $players = [];

        foreach ($playersRepo as $player) {
            $players[] = [
                'fio' => $player->getFio(),
                'email' => $player->getEmail(),
            ];
        }

        return $this->render(
            'index.twig',
            [
                'title' => $title,
                'players' => $players,
            ]
        );
    }
}
