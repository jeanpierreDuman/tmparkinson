<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, ActivityRepository $activityRepository): Response
    {
        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $days = $date->format('t');

        $dataActivity = $activityRepository->getAllByMonthAndYear($date->format('Y'), $date->format('m'));
 
        $listActivityDayMonth = [];

        for($i = 1; $i <= $days; $i++) {
            $listActivityDayMonth[(strlen($i) === 1 ? '0' . $i : $i) .'/'. $date->format('m') . '/' . $date->format('Y')] = [];
        }

        foreach($dataActivity as $data) {
            $listActivityDayMonth[$data['date']][] = $data;
        }

        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
            $activity->setDate($now);
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_default_index');
        }

        $aActivity = [];

        foreach($activityRepository->getActivityOfDay() as $aa) {
            $aActivity[$aa['name']] = $aa['count'];
        }

        return $this->render('default/index.html.twig', [
            'form' => $form,
            'activity' => $aActivity,
            'listActivityDayMonth' => $listActivityDayMonth,
            'month' => $this->getFrenchMonth($date->format('m')) . ' ' . $date->format('Y')
        ]);
    }

    private function getFrenchMonth($numMonth) {
        $numMonth--;

        $aMonth = [
            'Janvier',
            'Février',
            'Mars',
            'Avril',
            'Mai',
            'Juin',
            'Juillet',
            'Aout',
            'Septembre',
            'Octobre',
            'Novembre',
            'Décembre'
        ];

        return $aMonth[$numMonth];
    }
}