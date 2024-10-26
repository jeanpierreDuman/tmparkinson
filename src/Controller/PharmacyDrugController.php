<?php

namespace App\Controller;

use App\Entity\PharmacyDrug;
use App\Form\PharmacyDrugType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stock')]
final class PharmacyDrugController extends AbstractController
{
    #[Route('/{id}/edit',name: 'app_pharmacy_drug_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PharmacyDrug $pharmacyDrug, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PharmacyDrugType::class, $pharmacyDrug);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uow = $entityManager->getUnitOfWork();
            $originalPharmacyDrug = $uow->getOriginalEntityData($pharmacyDrug);
            $oldQuantity = $originalPharmacyDrug['quantity'];
            $oldQuantityToPrepare = $originalPharmacyDrug['quantityToPrepare'];

            if($pharmacyDrug->getQuantityToPrepare() === 0) {
                $pharmacyDrug->setQuantity($oldQuantity + $pharmacyDrug->getQuantity());
            } else {
                if($oldQuantityToPrepare > $pharmacyDrug->getQuantity()) {
                    $pharmacyDrug->setQuantityToPrepare($oldQuantityToPrepare - $pharmacyDrug->getQuantity());
                    $pharmacyDrug->setQuantity(0);
                } else {
                    $pharmacyDrug->setQuantityToPrepare(0);
                    $pharmacyDrug->setQuantity(($pharmacyDrug->getQuantity() - $oldQuantityToPrepare));
                }
            }
        
            $entityManager->persist($pharmacyDrug);
            $entityManager->flush();

            return $this->redirectToRoute('app_pharmacy_show', ['id' => $pharmacyDrug->getPharmacy()->getId()]);
        }

        return $this->render('pharmacy_drug/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
