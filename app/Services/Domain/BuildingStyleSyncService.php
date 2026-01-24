<?php

namespace App\Services\Domain;

use App\Models\BuildingStyle;
use Illuminate\Support\Facades\DB;

class BuildingStyleSyncService
{
    public function sync()
    {
        DB::table('raw_desc_r_03_building_style')
            ->orderBy('Code')
            ->chunk(500, function ($rows) {
                foreach ($rows as $r) {
                    BuildingStyle::updateOrCreate(
                        ['code' => $r->Code],
                        ['description' => $r->Description]
                    );
                }
            });
    }
}

