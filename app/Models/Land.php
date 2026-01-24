<?php

namespace App\Models;

class Land extends BaseModel
{
    protected $fillable = ['acct','land_use_code','land_area'];

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'acct', 'acct');
    }

    public function landUse()
    {
        return $this->belongsTo(LandUseCode::class, 'land_use_code', 'code');
    }
}
