<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Utils\PillUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PillController extends AbstractController
{
    #[Route('/user/pill', name: 'app_user_pill', methods: ['POST'])]
    public function index(PillUtils $pillUtils, Request $request, UserRepository $userRepository): Response
    {
        $userId = intval($request->request->get('user_id')) ?? null;
        $user = $userRepository->findOneById($userId);

        $firstDayOfWeek = new \DateTime(date('Y-m-d', strtotime("this week")));

        $aPillsWeek = [];

        for($i = 0; $i < 7; $i++) {
            $aPillsWeek[$firstDayOfWeek->format('d/m/Y')] = $pillUtils->getDayPills($user, $firstDayOfWeek);
            $firstDayOfWeek->modify('+ 1 day');
        }

        return $this->render('pill/user.html.twig', [
            'pillWeeks' => $aPillsWeek
        ]);
    }
}