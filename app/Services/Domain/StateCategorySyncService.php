<?php

namespace App\Services\Domain;

use App\Models\StateCategory;
use Illuminate\Support\Facades\DB;

class StateCategorySyncService
{
    public function sync()
    {
        DB::table('raw_desc_r_01_state_class')
            ->orderBy( 'code')
            ->chunk(500,function($rows){
                foreach($rows as $r){
                    StateCategory::updateOrCreate(
                        ['code'=>$r->code],
                        ['description'=>$r->description]
                    );
                }
            });
    }
}

