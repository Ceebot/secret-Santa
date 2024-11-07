<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер добавления пользователя в БД
 */
class AddPlayerController extends AbstractController
{
    /**
     * Добавление пользователя
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws Exception
     */
    #[Route('/add-player', methods: ['POST'])]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $fio = $request->request->get('fio');
        $email = $request->request->get('email');

        if (empty($fio) || empty($email)) {
            throw new Exception('Не заполнены необходимые поля!');
        }

        $player = new Player();
        $player->setFio($fio);
        $player->setEmail($email);

        $entityManager->persist($player);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }
}
