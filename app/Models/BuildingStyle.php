<?php

namespace App\Models;

class BuildingStyle extends BaseModel
{
    protected $fillable = ['code','description','mapped_state_class','is_allowed'];

    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'acct', 'acct');
    }

    public function stateClass()
    {
        return $this->belongsTo(StateClass::class, 'mapped_state_class', 'dept');
    }
}
