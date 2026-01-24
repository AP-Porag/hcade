<?php

namespace App\Models;

class Property extends BaseModel
{
    protected $fillable = [
        'acct','tax_year',
        'site_street','site_city','site_state','site_zip',
        'mail_street','mail_city','mail_state','mail_zip',
        'owner_name_current','owner_occupied',
        'state_class','neighborhood_code','neighborhood_group',
        'market_area_1','market_area_2',
        'land_area','building_area','acreage',
        'legal_description','is_active',
    ];

}
