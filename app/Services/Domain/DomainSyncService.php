<?php

namespace App\Services\Domain;
use DB;

class DomainSyncService
{
    public function sync(int $taxYear)
    {
        DB::transaction(function () use ($taxYear) {
            // Lookup tables (safe to re-run)
            app(BuildingStyleSyncService::class)->sync();
            app(BuildingStyleUpdateService::class)->run();
            app(StateClassSyncService::class)->sync();
            app(AllowedStateClassSyncService::class)->sync();
            app(PropertySyncService::class)->sync($taxYear);
            app(LandUseCodeSyncService::class)->sync();
            app(MarketAreaSyncService::class)->sync();
            app(NeighborhoodSyncService::class)->sync();
            app(StateCategorySyncService::class)->sync();

            // Business rules already imported (allowed_building_styles)

            // Domain data
            app(PropertyMasterSyncService::class)->sync($taxYear);
            app(OwnerSyncService::class)->sync($taxYear);
            app(ValuationSyncService::class)->sync($taxYear);
            app(BuildingSyncService::class)->sync($taxYear);
            app(LandSyncService::class)->sync($taxYear);
            app(ExemptionSyncService::class)->sync($taxYear);
            app(PermitSyncService::class)->sync($taxYear);
        });
    }
}
