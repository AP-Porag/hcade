<?php

namespace App\Services\Domain;

use App\Models\Building;
use Illuminate\Support\Facades\DB;

class BuildingSyncService
{
    public function sync(int $taxYear)
    {
        DB::transaction(function () use ($taxYear) {

            // Base buildings (res + other)
            $baseBuildings = DB::table('raw_building_res')
                ->unionAll(
                    DB::table('raw_building_other')
                )
                ->where('yr_roll', $taxYear)
                ->get()
                ->groupBy(fn($r) => $r->acct . ':' . $r->bld_num);

            foreach ($baseBuildings as $key => $rows) {
                $b = $rows->first();

                // Collect children
                $exterior = DB::table('raw_exterior')
                    ->where('acct', $b->acct)
                    ->where('bld_num', $b->bld_num)
                    ->get();

                $fixtures = DB::table('raw_fixtures')
                    ->where('acct', $b->acct)
                    ->where('bld_num', $b->bld_num)
                    ->get();

                $structural = DB::table('raw_structural_elem1')
                    ->where('acct', $b->acct)
                    ->where('bld_num', $b->bld_num)
                    ->unionAll(
                        DB::table('raw_structural_elem2')
                            ->where('acct', $b->acct)
                            ->where('bld_num', $b->bld_num)
                    )
                    ->get();

                $extraFeatures = DB::table('raw_extra_features')
                    ->where('acct', $b->acct)
                    ->where('bld_num', $b->bld_num)
                    ->get();

                Building::updateOrCreate(
                    [
                        'acct' => $b->acct,
                        'bld_num' => $b->bld_num,
                    ],
                    [
                        'property_use_cd' => $b->property_use_cd,
                        'impr_tp' => $b->impr_tp,
                        'impr_mdl_cd' => $b->impr_mdl_cd,
                        'structure' => $b->structure,
                        'structure_dscr' => $b->structure_dscr,
                        'quality_code' => $b->qa_cd,
                        'description' => $b->dscr,
                        'year_built' => $b->date_erected,
                        'year_remodel' => $b->yr_remodel,
                        'year_roll' => $b->yr_roll,
                        'gross_area' => $b->gross_ar,
                        'effective_area' => $b->eff_ar,
                        'base_area' => $b->base_ar,
                        'heated_area' => $b->heat_ar,
                        'replacement_cost' => $b->cama_replacement_cost,
                        'depreciated_value' => $b->dpr_val ?? $b->Depr_Val ?? null,
                        'depreciation_pct' => $b->accrued_depr_pct,
                        'total_income' => $b->tot_inc ?? null,
                        'occupancy_rate' => $b->occ_rt ?? null,
                        'exterior' => $exterior,
                        'fixtures' => $fixtures,
                        'structural_elements' => $structural,
                        'extra_features' => $extraFeatures,
                    ]
                );
            }
        });
    }
}

