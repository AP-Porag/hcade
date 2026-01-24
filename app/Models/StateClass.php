<?php

namespace App\Models;

class StateClass extends BaseModel
{
    protected $fillable = ['code','description','dept'];

    public function allowed()
    {
        return $this->hasOne(AllowedStateClass::class, 'state_class_code', 'code');
    }
}
