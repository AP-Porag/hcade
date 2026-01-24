<?php

namespace App\Models;

class Building extends BaseModel
{
    protected $fillable = [
        'acct',
        'bld_num',
        'property_use_cd',
        'impr_tp',
        'impr_mdl_cd',
        'structure',
        'structure_dscr',
        'quality_code',
        'description',
        'year_built',
        'year_remodel',
        'year_roll',
        'gross_area',
        'effective_area',
        'base_area',
        'heated_area',
        'replacement_cost',
        'depreciated_value',
        'depreciation_pct',
        'total_income',
        'occupancy_rate',
        'exterior',
        'fixtures',
        'structural_elements',
        'extra_features',
    ];

    protected $casts = [
        'exterior' => 'array',
        'fixtures' => 'array',
        'structural_elements' => 'array',
        'extra_features' => 'array',
    ];
}
