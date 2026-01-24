<?php

namespace App\Services\Domain;

use App\Models\StateClass;
use Illuminate\Support\Facades\DB;

class StateClassSyncService
{
    public function sync()
    {
        DB::table('raw_desc_r_01_state_class')
            ->orderBy('code')
            ->chunk(500, function ($rows) {
                foreach ($rows as $r) {
                    StateClass::updateOrCreate(
                        ['code' => $r->state_class],
                        [
                            'description' => $r->description,
                            'dept' => $r->dept
                        ]
                    );
                }
            });
    }
}

