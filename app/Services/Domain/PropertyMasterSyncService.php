<?php

namespace App\Services\Domain;

use App\Models\PropertyMaster;
use Illuminate\Support\Facades\DB;

class PropertyMasterSyncService
{
    public function sync(int $taxYear)
    {
        DB::table('properties as p')
            ->leftJoin('property_owners as o', function ($j) {
                $j->on('o.acct','=','p.acct')->where('o.is_primary', true);
            })
            ->leftJoin('property_valuations as v', function ($j) use ($taxYear) {
                $j->on('v.acct','=','p.acct')->where('v.tax_year',$taxYear);
            })
            ->where('p.tax_year', $taxYear)
            ->orderBy('p.acct')
            ->chunk(1000, function ($rows) {
                foreach ($rows as $r) {
                    PropertyMaster::updateOrCreate(
                        [
                            'acct' => $r->acct,
                            'tax_year' => $r->tax_year,
                        ],
                        [
                            'address' => trim($r->site_street.' '.$r->site_city),
                            'owner_name' => $r->owner_name,
                            'state_class' => $r->state_class,
                            'neighborhood_code' => $r->neighborhood_code,
                            'neighborhood_group' => $r->neighborhood_group,
                            'market_area_1' => $r->market_area_1,
                            'market_area_2' => $r->market_area_2,
                            'land_area' => $r->land_area,
                            'building_area' => $r->building_area,
                            'acreage' => $r->acreage,
                            'market_value' => $r->market_value,
                            'is_active' => true,
                        ]
                    );
                }
            });
    }
}

