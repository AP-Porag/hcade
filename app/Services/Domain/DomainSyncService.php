<?php

namespace App\Services\Domain;
use App\Models\SyncLog;
use DB;

//class DomainSyncService
//{
//    protected array $services = [
//        BuildingStyleSyncService::class,
//        BuildingStyleUpdateService::class,
//        StateClassSyncService::class,
//        AllowedStateClassSyncService::class,
////        PropertySyncService::class,
//    ];
////    public function sync(int $taxYear)
////    {
////        DB::transaction(function () use ($taxYear) {
////            // Lookup tables (safe to re-run)
////            app(BuildingStyleSyncService::class)->sync();
////            app(BuildingStyleUpdateService::class)->run();
////            app(StateClassSyncService::class)->sync();
////            app(AllowedStateClassSyncService::class)->sync();
////            app(PropertySyncService::class)->sync($taxYear);
////            app(LandUseCodeSyncService::class)->sync();
////            app(MarketAreaSyncService::class)->sync();
////            app(NeighborhoodSyncService::class)->sync();
////            app(StateCategorySyncService::class)->sync();
////
////            // Business rules already imported (allowed_building_styles)
////
////            // Domain data
////            app(PropertyMasterSyncService::class)->sync($taxYear);
////            app(OwnerSyncService::class)->sync($taxYear);
////            app(ValuationSyncService::class)->sync($taxYear);
////            app(BuildingSyncService::class)->sync($taxYear);
////            app(LandSyncService::class)->sync($taxYear);
////            app(ExemptionSyncService::class)->sync($taxYear);
////            app(PermitSyncService::class)->sync($taxYear);
////        });
////    }
//
//    public function sync(int $taxYear,SyncLog $log): void
//    {
//        set_time_limit(0);
//        ini_set('memory_limit', '-1');
//
////        $log = SyncLog::create([
////            'context' => 'domain',
////            'tax_year' => $taxYear,
////            'total_services' => count($this->services),
////            'status' => 'running',
////        ]);
//
//        foreach ($this->services as $index => $serviceClass) {
//            $log->update([
//                'current_service_index' => $index + 1,
//                'current_service' => class_basename($serviceClass),
//                'service_progress' => intval((($index) / count($this->services)) * 100),
//                'chunk_progress' => 0,
//                'message' => 'Starting ' . class_basename($serviceClass),
//            ]);
//
//            try {
//                app($serviceClass)->sync($taxYear, $log);
//            } catch (\Throwable $e) {
//                $log->update([
//                    'status' => 'failed',
//                    'error' => $e->getMessage(),
//                ]);
//                throw $e;
//            }
//        }
//
//        $log->update([
//            'service_progress' => 100,
//            'chunk_progress' => 100,
//            'status' => 'success',
//            'message' => 'Domain synchronization completed',
//        ]);
//
////        return $log->id;
//    }
//}


//        BuildingStyleSyncService::class,
//        BuildingStyleUpdateService::class,
//        StateClassSyncService::class,
//        AllowedStateClassSyncService::class,
//        PropertySyncService::class,
//        LandUseCodeSyncService::class,
//        MarketAreaSyncService::class,
//        NeighborhoodSyncService::class,
class DomainSyncService
{
    protected array $services = [
        StateCategorySyncService::class,
    ];

    public function sync(int $taxYear, SyncLog $log): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        // ✅ Capture state classes ONCE
        $stateClasses = DB::table('raw_real_accts as a')
            ->join(
                'allowed_state_classes as asc_tbl',
                'asc_tbl.state_class_code',
                '=',
                'a.state_class'
            )
            ->where('asc_tbl.is_allowed', true)
            ->where('a.yr', $taxYear)
            ->distinct()
            ->orderBy('a.state_class')
            ->pluck('a.state_class')
            ->values()
            ->toArray();


        $log->update([
            'context'       => 'domain',
            'tax_year'      => $taxYear,
            'state_classes' => $stateClasses,
            'total_services'=> count($this->services),
        ]);

        foreach ($this->services as $index => $serviceClass) {
            $log->update([
                'current_service_index' => $index + 1,
                'current_service' => class_basename($serviceClass),
                'service_progress' => intval(($index / count($this->services)) * 100),
                'chunk_progress' => 0,
                'message' => 'Starting ' . class_basename($serviceClass),
            ]);

            try {
                app($serviceClass)->sync($taxYear, $log);
            } catch (\Throwable $e) {
                $log->update([
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }

        $log->update([
            'service_progress' => 100,
            'chunk_progress' => 100,
            'status' => 'success',
            'message' => 'Domain synchronization completed',
        ]);
    }
}
