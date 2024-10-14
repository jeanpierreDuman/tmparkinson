<?php

namespace App\Controller;

use App\Repository\PrescriptionLineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PillController extends AbstractController
{
    #[Route('/pill', name: 'app_pill')]
    public function index(PrescriptionLineRepository $prescriptionLineRepository): Response
    {
        $aDataPill = $prescriptionLineRepository->getDataPills();
        $pills = [];

        foreach($aDataPill as $dataPill) {
            $drug = $dataPill['name'] . " " . $dataPill['milligram'] . " G " . $this->toStringType($dataPill['type']);

            foreach($dataPill['frequency'] as $frequency) {
                if(in_array($frequency, array_keys($pills))) {

                    if(in_array($drug, array_column($pills[$frequency], 'drug'))) {
                        $strpos = array_search($drug, array_column($pills[$frequency], 'drug'));
                        $pills[$frequency][$strpos]['quantity'] += $dataPill['quantity'];
                    } else {
                        $pills[$frequency][] = [
                            'drug' => $drug,
                            'quantity' => $dataPill['quantity']
                        ];
                    }

                } else {
                    $pills[$frequency][] = [
                        'drug' => $drug,
                        'quantity' => $dataPill['quantity']
                    ];
                }
            }
        }

        $aDay = ['morning', 'noon', 'night'];

        foreach($aDay as $day) {
            if(!in_array($day, array_keys($pills))) {
                $pills[$day] = [];
            }
        }

        return $this->render('pill/index.html.twig', [
            'pillsMorning' => $pills['morning'],
            'pillsNoon' => $pills['noon'],
            'pillsNight' => $pills['night'],
        ]);
    }

    public function toStringType($type) {
        if($type === "comp") {
            return "Comprimé";
        } else {
            return "Gélule";
        }
    }
}