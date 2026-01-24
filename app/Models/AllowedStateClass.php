<?php

namespace App\Models;

class AllowedStateClass extends BaseModel
{
    protected $fillable = ['state_class_code','is_allowed'];

    public function stateClass()
    {
        return $this->belongsTo(StateClass::class, 'state_class_code', 'code');
    }
}
