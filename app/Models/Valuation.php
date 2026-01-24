<?php

namespace App\Models;

class Valuation extends BaseModel
{
    protected $fillable = [
        'acct','tax_year','land_value','building_value',
        'market_value','appraised_value'
    ];

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'acct', 'acct');
    }
}
