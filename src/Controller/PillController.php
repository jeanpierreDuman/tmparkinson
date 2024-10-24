<?php

namespace App\Controller;

use App\Repository\PrescriptionLineRepository;
use App\Utils\PillUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PillController extends AbstractController
{
    #[Route('/pill', name: 'app_pill')]
    public function index(PillUtils $pillUtils): Response
    {        
        $firstDayOfWeek = new \DateTime(date('Y-m-d', strtotime("this week")));

        $aPillsWeek = [];

        for($i = 0; $i < 7; $i++) {
            $aPillsWeek[$firstDayOfWeek->format('d/m/Y')] = $pillUtils->getDayPills($this->getUser(), $firstDayOfWeek);
            $firstDayOfWeek->modify('+ 1 day');
        }

        return $this->render('pill/index.html.twig', [
            'pillWeeks' => $aPillsWeek
        ]);
    }
}