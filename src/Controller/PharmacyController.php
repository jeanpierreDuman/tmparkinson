<?php

namespace App\Controller;

use App\Entity\Pharmacy;
use App\Entity\PharmacyDrug;
use App\Entity\User;
use App\Form\PharmacyType;
use App\Form\UserTypeChoiceTypeEntity;
use App\Repository\DrugRepository;
use App\Repository\PharmacyDrugRepository;
use App\Repository\PharmacyRepository;
use App\Repository\PrescriptionLineRepository;
use App\Utils\PillUtils;
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
    public function show(Pharmacy $pharmacy, PrescriptionLineRepository $prescriptionLineRepository, 
                            PillUtils $pillUtils, PharmacyDrugRepository $pharmacyDrugRepository,
                            Request $request): Response
    {
        $aListPillWaste = $prescriptionLineRepository->getListWastePills($pharmacy);
        $aListPillWaste = $pillUtils->restructPillWaste($aListPillWaste);
        $stocks = $pharmacyDrugRepository->getStocks($pharmacy);
        
        $firstDayOfWeek = new \DateTime(date('Y-m-d', strtotime("this week")));

        $aPillsWeek = [];

        for($i = 0; $i < 7; $i++) {
            $aPillsWeek[$firstDayOfWeek->format('d/m/Y')] = $pillUtils->getDayPills($this->getUser(), $firstDayOfWeek);
            $firstDayOfWeek->modify('+ 1 day');
        }

        $form = $this->createForm(UserTypeChoiceTypeEntity::class);

        return $this->render('pharmacy/show.html.twig', [
            'pharmacy' => $pharmacy,
            'aListPillWaste' => $aListPillWaste,
            'stocks' => $stocks,
            'pillWeeks' => $aPillsWeek,
            'form' => $form
        ]);
    }

    #[Route('/{id}/convert/inbox', name: 'app_pharmacy_convert_inbox', methods: ['GET'])]
    public function convertInbox(Pharmacy $pharmacy, PrescriptionLineRepository $prescriptionLineRepository, PillUtils $pillUtils, 
        DrugRepository $drugRepository, PharmacyDrugRepository $pharmacyDrugRepository, EntityManagerInterface $entityManager)
    {
        $aListPillWaste = $prescriptionLineRepository->getListWastePills($pharmacy);
        $aListPillWaste = $pillUtils->restructPillWaste($aListPillWaste);

        foreach($aListPillWaste as $pillWaste) {
            if($pillWaste['quantity'] >= $pillWaste['quantityPackage']) {
                $drug = $drugRepository->findOneById($pillWaste['id']);
                $pharmacyDrug = $pharmacyDrugRepository->findOneBy(['pharmacy' => $pharmacy, 'drug' => $drug]);

                $totalPackage = intval($pillWaste['quantity'] / $pillWaste['quantityPackage']);
                
                if(!($pharmacyDrug instanceof PharmacyDrug)) {
                    $pharmacyDrug = new PharmacyDrug();
                    $pharmacyDrug->setDrug($drug);
                    $pharmacyDrug->setPharmacy($pharmacy);
                    $pharmacyDrug->setQuantity(0);
                    $pharmacyDrug->setQuantityToPrepare(0);
                }

                // Update Stock Pharmacy Drug
                if($totalPackage !== 0) {
                    if($pharmacyDrug->getQuantityToPrepare() === 0) {
                        $pharmacyDrug->setQuantity($pharmacyDrug->getQuantity() + $totalPackage);
                    } else {
                        if($totalPackage > $pharmacyDrug->getQuantityToPrepare()) {
                            $pharmacyDrug->setQuantityToPrepare(0);
                            $pharmacyDrug->setQuantity($totalPackage - $pharmacyDrug->getQuantityToPrepare());
                        } else {
                            $pharmacyDrug->setQuantityToPrepare($pharmacyDrug->getQuantityToPrepare() - $totalPackage);
                        }
                    }
                }

                // Update unit pill waste by prescription lines id
                $totalDrugToRemove = $totalPackage * $pillWaste['quantityPackage'];
                $prescriptionLines = $prescriptionLineRepository->findById($pillWaste['pLid']);

                foreach($prescriptionLines as $prescriptionLine) {
                    $totalDrugToRemove = $totalDrugToRemove - $prescriptionLine->getUnitPillWaste();
                    if($totalDrugToRemove > 0) {
                        $prescriptionLine->setUnitPillWaste(0);
                    } else {
                        $prescriptionLine->setUnitPillWaste($totalDrugToRemove * -1);
                        break;
                    }

                    $entityManager->persist($prescriptionLine);
                }

                $entityManager->persist($pharmacyDrug);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_pharmacy_show', ['id' => $pharmacy->getId()]);
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
