<?php

namespace App\Services\Domain;

use App\Models\Property;
use App\Models\PropertyMaster;
use Illuminate\Support\Facades\DB;

class PropertySyncService
{
    public function sync(int $taxYear, callable $progressCallback = null): void
    {
        $query = DB::table('raw_real_accts as a')
            ->join(
                'allowed_state_classes as asc',
                'asc.state_class_code',
                '=',
                'a.state_class'
            )
            ->where('asc.is_allowed', true)
            ->where('a.yr', $taxYear)
            ->select([
                'a.acct',
                'a.yr as tax_year',

                // Site address
                'a.site_addr_1 as site_street',
                'a.site_city',
                'a.site_state',
                'a.site_zip',

                // Mailing address
                'a.mail_addr_1 as mail_street',
                'a.mail_city',
                'a.mail_state',
                'a.mail_zip',

                // Classification
                'a.state_class',
                'a.neighborhood_code',
                'a.neighborhood_grp',
                'a.market_area_1',
                'a.market_area_2',

                // Physical
                'a.land_ar',
                'a.bld_ar',
                'a.acreage',

                // Legal
                DB::raw(
                    "CONCAT_WS(' ', a.lgl_1, a.lgl_2, a.lgl_3, a.lgl_4) as legal_description"
                ),
            ])
            ->orderBy('a.acct'); // REQUIRED for chunk()

        $total = $query->count();
        $processed = 0;

        $query->chunk(1000, function ($rows) use (&$processed, $total, $progressCallback) {
            foreach ($rows as $row) {
                Property::updateOrCreate(
                    [
                        'acct'     => $row->acct,
                        'tax_year' => $row->tax_year,
                    ],
                    [
                        'site_street'        => $row->site_street,
                        'site_city'          => $row->site_city,
                        'site_state'         => $row->site_state,
                        'site_zip'           => $row->site_zip,

                        'mail_street'        => $row->mail_street,
                        'mail_city'          => $row->mail_city,
                        'mail_state'         => $row->mail_state,
                        'mail_zip'           => $row->mail_zip,

                        'state_class'        => $row->state_class,
                        'neighborhood_code'  => $row->neighborhood_code,
                        'neighborhood_group' => $row->neighborhood_grp,
                        'market_area_1'      => $row->market_area_1,
                        'market_area_2'      => $row->market_area_2,

                        'land_area'          => $row->land_ar,
                        'building_area'      => $row->bld_ar,
                        'acreage'            => $row->acreage,

                        'legal_description'  => $row->legal_description,
                        'is_active'          => true,
                    ]
                );

                $processed++;

                if ($progressCallback) {
                    $progressCallback($processed, $total);
                }
            }
        });
    }
}

