<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends BaseModel
{
    protected $fillable = [
        'acct','owner_name','ownership_pct','is_primary'
    ];

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'acct', 'acct');
    }
}
