<?php

namespace App\Services\Domain;

use App\Models\AllowedStateClass;
use App\Models\BuildingStyle;
use Illuminate\Support\Facades\DB;

class AllowedStateClassSyncService
{
    public function sync(): void
    {
        DB::transaction(function () {

            // Reset all
            AllowedStateClass::query()->update(['is_allowed' => false]);

            // Derive allowed state classes from building styles
            $allowedStateClasses = BuildingStyle::where('is_allowed', true)
                ->whereNotNull('mapped_state_class')
                ->distinct()
                ->pluck('mapped_state_class');

            foreach ($allowedStateClasses as $dept) {
                AllowedStateClass::updateOrCreate(
                    ['state_class_code' => $dept],
                    ['is_allowed' => true]
                );
            }
        });
    }
}

