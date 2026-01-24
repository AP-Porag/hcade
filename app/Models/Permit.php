<?php

namespace App\Models;

class Permit extends BaseModel
{
    protected $fillable = ['acct','permit_type','permit_date'];

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'acct', 'acct');
    }
}
