<?php

namespace App\Models;

class Land extends BaseModel
{
    protected $fillable = [
        'acct',
        'num',
        'use_cd',
        'use_dscr',
        'inf_cd',
        'inf_dscr',
        'inf_adj',
        'tp',
        'uts',
        'sz_fact',
        'inf_fact',
        'cond',
        'ovr_dscr',
        'tot_adj',
        'unit_prc',
        'adj_unit_prc',
        'val',
        'ovr_val',
    ];

    protected $casts = [
        'inf_adj' => 'float',
        'inf_fact' => 'float',
        'sz_fact' => 'float',
        'tot_adj' => 'float',
        'unit_prc' => 'float',
        'adj_unit_prc' => 'float',
        'val' => 'float',
        'ovr_val' => 'float',
    ];

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'acct', 'acct');
    }

    public function landUse()
    {
        return $this->belongsTo(LandUseCode::class, 'land_use_code', 'code');
    }
}
