<?php

namespace App\Services\Domain;

use App\Models\BuildingStyle;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class BuildingStyleUpdateService
{
    /**
     * Update building_styles from local CSV
     * Path is fixed by design (no UI upload)
     */
    public function sync(): void
    {
        $filePath = 'D:/HCAD - Data/building.csv';

        if (!file_exists($filePath)) {
            throw new \RuntimeException("Building style CSV not found at {$filePath}");
        }

        DB::transaction(function () use ($filePath) {

            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0); // first row = header

            $payload = [];

            foreach ($csv->getRecords() as $row) {
                $code = trim($row['code'] ?? '');

                if ($code === '') {
                    continue;
                }

                $payload[] = [
                    'code'               => $code,
                    'description'        => trim($row['description'] ?? null),
                    'mapped_state_class' => trim($row['mapped_state_class'] ?? null),
                    'is_allowed'         => strtolower(trim($row['is_allowed'] ?? '')) === 'x',
                ];
            }

            if (!empty($payload)) {
                BuildingStyle::upsert(
                    $payload,
                    ['code'], // unique key
                    [
                        'description',
                        'mapped_state_class',
                        'is_allowed',
                    ]
                );
            }
        });
    }
}


