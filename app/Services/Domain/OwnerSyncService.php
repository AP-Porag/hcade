<?php

namespace App\Services\Domain;

use App\Models\Owner;
use Illuminate\Support\Facades\DB;

class OwnerSyncService
{
    public function sync(int $taxYear)
    {
        DB::table('raw_owners as o')
            ->join('properties as p', function ($j) use ($taxYear) {
                $j->on('p.acct','=','o.acct')->where('p.tax_year',$taxYear);
            })
            ->select('o.acct','o.owner_name','o.pct_ownership')
            ->orderBy('o.acct')
            ->chunk(1000, function ($rows) {
                foreach ($rows as $r) {
                    Owner::updateOrCreate(
                        ['acct'=>$r->acct,'owner_name'=>$r->owner_name],
                        ['ownership_pct'=>$r->pct_ownership,'is_primary'=>true]
                    );
                }
            });
    }
}

