<?php

namespace App\Models;

class Property extends BaseModel
{
    protected $fillable = [
        'acct',
        'tax_year',
        'site_addr_1',
        'site_addr_2',
        'site_addr_3',
        'mail_addr_1',
        'mail_addr_2',
        'mail_city',
        'mail_state',
        'mail_zip',
        'state_class',
        'neighborhood_code',
        'neighborhood_group',
        'market_area_1',
        'market_area_2',
        'land_ar',
        'bld_ar',
        'acreage',
        'legal_description',
        'is_active',
    ];

    public function allowedStateClass()
    {
        return $this->belongsTo(
            AllowedStateClass::class,
            'state_class',
            'state_class_code'
        );
    }

}
