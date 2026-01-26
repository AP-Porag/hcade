<?php

namespace App\Models;

class StateClass extends BaseModel
{
    protected $fillable = [
        'code',
        'dept',
        'description',
    ];

    /**
     * A state class can be allowed or not (business rule).
     */
    public function allowed()
    {
        return $this->hasOne(
            AllowedStateClass::class,
            'state_class_code',
            'code'
        );
    }

    /**
     * A state class can be referenced by many building styles
     * via mapped_state_class.
     */
    public function buildingStyles()
    {
        return $this->hasMany(
            BuildingStyle::class,
            'mapped_state_class',
            'code'
        );
    }
}
