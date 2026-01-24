<?php

namespace App\Services\Domain;

use App\Models\Permit;
use Illuminate\Support\Facades\DB;

class PermitSyncService
{
    public function sync(int $taxYear)
    {
        DB::table('raw_permits')
            ->orderBy('acct')
            ->chunk(1000,function($rows){
                foreach($rows as $r){
                    Permit::create([
                        'acct'=>$r->acct,
                        'permit_type'=>$r->permit_type,
                        'permit_date'=>$r->permit_dt
                    ]);
                }
            });
    }
}

