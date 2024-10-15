<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use App\Utils\PillUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, PillUtils $pillUtils, ActivityRepository $activityRepository): Response
    {
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

        $pills = $pillUtils->getDayPills();

        $aActivity = [];

        foreach($activityRepository->getActivityOfDay() as $aa) {
            $aActivity[$aa['name']] = $aa['count'];
        }

        return $this->render('default/index.html.twig', [
            'form' => $form,
            'pillsMorning' => $pills['morning'],
            'pillsNoon' => $pills['noon'],
            'pillsNight' => $pills['night'],
            'activity' => $aActivity
        ]);
    }
}