<?php

namespace App\Controller;

use App\Entity\PharmacyDrug;
use App\Entity\Prescription;
use App\Entity\PrescriptionLine;
use App\Entity\PrescriptionPreparation;
use App\Entity\PrescriptionPreparationLine;
use App\Form\PrescriptionType;
use App\Repository\PharmacyDrugRepository;
use App\Repository\PrescriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prescription')]
final class PrescriptionController extends AbstractController
{
    #[Route(name: 'app_prescription_index', methods: ['GET'])]
    public function index(PrescriptionRepository $prescriptionRepository): Response
    {
        return $this->render('prescription/index.html.twig', [
            'prescriptions' => $prescriptionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_prescription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prescription = new Prescription();
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($prescription->getPrescriptionLines() as $prescriptionLine) {
                $prescriptionLine->setPrescription($prescription);
                $entityManager->persist($prescriptionLine);                
            }

            $entityManager->persist($prescription);
            $entityManager->flush();

            return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prescription/new.html.twig', [
            'prescription' => $prescription,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/confirm', name: 'app_prescription_confirm', methods: ['GET'])]
    public function confirm(Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        $prescription->setStatus(Prescription::PRESCRIPTION_PREPARATION);

        foreach($prescription->getPrescriptionLines() as $prescriptionLine) {
            $numberOfDayTreatment = $prescription->getDateEnd()->diff($prescription->getDateStart())->format("%a") + 1;
            $numberOfDrugByDay = $prescriptionLine->getQuantity() * count($prescriptionLine->getFrequency());
            // Compute total package
            $totalPackage = intval(ceil(($numberOfDayTreatment * $numberOfDrugByDay) / $prescriptionLine->getDrug()->getQuantityPackage()));
            // Compute rest of pills
            $totalWastePillWaste = intval(ceil(($numberOfDayTreatment * $numberOfDrugByDay) % $prescriptionLine->getDrug()->getQuantityPackage()));

            $prescriptionLine->setBoxToPrepare($totalPackage);
            $prescriptionLine->setUnitPillWaste($totalWastePillWaste);
        }
       
        $entityManager->persist($prescription);
        $entityManager->flush();

        return $this->redirectToRoute('app_prescription_index');
    }

    #[Route('/{id}/receipt', name: 'app_prescription_receipt', methods: ['GET'])]
    public function receipt(Prescription $prescription, EntityManagerInterface $entityManager, PharmacyDrugRepository $pharmacyDrugRepository): Response
    {
        $pharmacy = $prescription->getUser()->getPharmacy();
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $updateNow = false;

        foreach($prescription->getPrescriptionLines() as $prescriptionLine) {
            $pharmacyDrug = $pharmacyDrugRepository->findOneBy(['pharmacy' => $pharmacy, 'drug' => $prescriptionLine->getDrug()]);

            if($pharmacyDrug instanceof PharmacyDrug) {
                $newQuantityInStock = $pharmacyDrug->getQuantity() - $prescriptionLine->getBoxToPrepare();
                
                if($pharmacyDrug->getQuantityToPrepare() > 0) {
                    $pharmacyDrug->setQuantityToPrepare($pharmacyDrug->getQuantityToPrepare() + $prescriptionLine->getBoxToPrepare());
                    $prescription->setWaitingPharmacyForDrug(true);
                    $updateNow = true;
                } else {
                    if($newQuantityInStock < 0) {
                        $pharmacyDrug->setQuantity(0);
                        $pharmacyDrug->setQuantityToPrepare($newQuantityInStock * -1);
                        $prescription->setWaitingPharmacyForDrug(true);
                        $updateNow = true;
                    } else {
                        $pharmacyDrug->setQuantity($newQuantityInStock);
                        $pharmacyDrug->setQuantityToPrepare(0);
                        $prescription->setWaitingPharmacyForDrug(false);
                    }
                }                
            } else {
                $pharmacyDrug = new PharmacyDrug();
                $pharmacyDrug->setPharmacy($pharmacy);
                $pharmacyDrug->setDrug($prescriptionLine->getDrug());

                $pharmacyDrug->setQuantity(0);
                $pharmacyDrug->setQuantityToPrepare($prescriptionLine->getBoxToPrepare());
                $prescription->setWaitingPharmacyForDrug(true);
                $updateNow = true;
            }

            $entityManager->persist($pharmacyDrug);
        }

        if($updateNow) {
            $now->modify('+ 1 days');
        }

        $prescription->setStatus(Prescription::PRESCRIPTION_RECEIVE);
        $prescription->setDateReceipt($now);

        $entityManager->persist($prescription);

        $entityManager->flush();

        return $this->redirectToRoute('app_prescription_index');
    }

    #[Route('/{id}/confirm/receipt', name: 'app_prescription_confirm_receipt', methods: ['GET'])]
    public function confirmReceipt(Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        $prescription->setComplete(true);
        $prescription->setStatus(Prescription::PRESCRIPTION_COMPLETE);

        $entityManager->persist($prescription);
        $entityManager->flush();

        return $this->redirectToRoute('app_prescription_index');
    }

    #[Route('/{id}', name: 'app_prescription_show', methods: ['GET'])]
    public function show(Prescription $prescription): Response
    {
        return $this->render('prescription/show.html.twig', [
            'prescription' => $prescription,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prescription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach($prescription->getPrescriptionLines() as $prescriptionLine) {
                $prescriptionLine->setPrescription($prescription);
                $entityManager->persist($prescriptionLine);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prescription/edit.html.twig', [
            'prescription' => $prescription,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prescription_delete', methods: ['POST'])]
    public function delete(Request $request, Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prescription->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($prescription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
    }
}
