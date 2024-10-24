<?php

namespace App\Utils;

use App\Entity\User;
use App\Repository\PrescriptionLineRepository;

class PillUtils {

    private $prescriptionLineRepository;

    public function __construct(PrescriptionLineRepository $prescriptionLineRepository)
    {
        $this->prescriptionLineRepository = $prescriptionLineRepository;
    }

    public function getDayPills(User $user = null, $today = new \DateTime('now', new \DateTimeZone('Europe/Paris'))) 
    {
        $aDataPill = $this->prescriptionLineRepository->getDataPills($user, $today);

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

        return [
            'morning' => $pills['morning'],
            'noon' => $pills['noon'],
            'night' => $pills['night'],
        ];
    }

    public function toStringType($type) {
        if($type === "comp") {
            return "Comprimé";
        } else {
            return "Gélule";
        }
    }
}