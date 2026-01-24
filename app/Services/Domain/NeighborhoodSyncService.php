<?php

namespace App\Services\Domain;

use App\Models\Neighborhood;
use Illuminate\Support\Facades\DB;

class NeighborhoodSyncService
{
    public function sync()
    {
        DB::table('raw_desc_r_26_neighborhood_num_adjust')
            ->orderBy('code')
            ->chunk(500,function($rows){
                foreach($rows as $r){
                    Neighborhood::updateOrCreate(
                        ['code'=>$r->neighborhood],
                        [
                            'description'=>$r->description,
                            'adjustment_percent'=>$r->adj_pcent
                        ]
                    );
                }
            });
    }
}

