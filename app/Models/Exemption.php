<?php

namespace App\Models;
class Exemption extends BaseModel
{
    protected $fillable = ['acct','exemption_code','exemption_value'];

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'acct', 'acct');
    }
}
