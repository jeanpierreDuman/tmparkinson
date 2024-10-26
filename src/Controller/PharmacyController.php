<?php

namespace App\Controller;

use App\Entity\Pharmacy;
use App\Form\PharmacyType;
use App\Repository\PharmacyRepository;
use App\Repository\PrescriptionLineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pharmacy')]
final class PharmacyController extends AbstractController
{
    #[Route(name: 'app_pharmacy_index', methods: ['GET'])]
    public function index(PharmacyRepository $pharmacyRepository): Response
    {
        return $this->render('pharmacy/index.html.twig', [
            'pharmacies' => $pharmacyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pharmacy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pharmacy = new Pharmacy();
        $form = $this->createForm(PharmacyType::class, $pharmacy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pharmacy);
            $entityManager->flush();

            return $this->redirectToRoute('app_pharmacy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pharmacy/new.html.twig', [
            'pharmacy' => $pharmacy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pharmacy_show', methods: ['GET'])]
    public function show(Pharmacy $pharmacy, PrescriptionLineRepository $prescriptionLineRepository): Response
    {
        $aListPillWaste = $prescriptionLineRepository->getListWastePills($pharmacy);

        return $this->render('pharmacy/show.html.twig', [
            'pharmacy' => $pharmacy,
            'aListPillWaste' => $aListPillWaste
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pharmacy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pharmacy $pharmacy, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PharmacyType::class, $pharmacy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pharmacy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pharmacy/edit.html.twig', [
            'pharmacy' => $pharmacy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pharmacy_delete', methods: ['POST'])]
    public function delete(Request $request, Pharmacy $pharmacy, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pharmacy->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pharmacy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pharmacy_index', [], Response::HTTP_SEE_OTHER);
    }
}
