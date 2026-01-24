<?php

namespace App\Models;
class PropertyMaster extends BaseModel
{

    protected $fillable = [
        'acct','tax_year',
        'address','owner_name',
        'state_class','neighborhood_code','neighborhood_group',
        'market_area_1','market_area_2',
        'land_area','building_area','acreage',
        'market_value','is_active',
    ];
    public function owners()
    {
        return $this->hasMany(Owner::class, 'acct', 'acct');
    }

    public function valuation()
    {
        return $this->hasOne(Valuation::class, 'acct', 'acct');
    }

    public function buildings()
    {
        return $this->hasMany(Building::class, 'acct', 'acct');
    }

    public function land()
    {
        return $this->hasOne(Land::class, 'acct', 'acct');
    }

    public function exemptions()
    {
        return $this->hasMany(Exemption::class, 'acct', 'acct');
    }

    public function permits()
    {
        return $this->hasMany(Permit::class, 'acct', 'acct');
    }

    // Lookup relations (read-only helpers)
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_code', 'code');
    }

    public function marketArea()
    {
        return $this->belongsTo(MarketArea::class, 'market_area_1', 'code');
    }

    public function stateCategory()
    {
        return $this->belongsTo(StateCategory::class, 'state_class', 'code');
    }

    public function stateClass()
    {
        return $this->belongsTo(StateClass::class, 'state_class', 'dept');
    }

    public function allowedStateClass()
    {
        return $this->hasOne(
            AllowedStateClass::class,
            'state_class_code',
            'state_class'
        );
    }
}
