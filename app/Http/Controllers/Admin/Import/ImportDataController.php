<?php

namespace App\Http\Controllers\Admin\Import;

use App\Http\Controllers\Controller;
use App\Jobs\RunImportSync;
use App\Models\BuildingStyle;
use App\Models\StateClass;
use App\Models\SyncLog;
use App\Services\Domain\DomainSyncService;
use App\Utils\GlobalConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ImportDataController extends Controller
{
    public function index()
    {

        //TODO remove this if when raw table also available on server.
        if (DB::table('raw_real_accts')){
            $years = DB::table('raw_real_accts')
                ->whereNotNull('yr')
                ->distinct()
                ->orderByDesc('yr')
                ->pluck('yr')
                ->values()
                ->toArray();

            $runningLog = SyncLog::where('status', 'running')->first();

            if ($runningLog) {
                return Inertia::render('admin/import/index', [
                    'availableYears' => $years,
                    'defaultYear'    => $years[0] ?? GlobalConstant::DEFAULT_TAX_YEAR,
                    'sync_log_id' => $runningLog->id,
                ]);
            }
        }


        return Inertia::render('admin/import/index', [
            'availableYears' => $years,
            'defaultYear'    => $years[0] ?? GlobalConstant::DEFAULT_TAX_YEAR,
            'sync_log_id' => null,
        ]);
    }

    public function syncDomain(Request $request)
    {
        $validated = $request->validate([
            'tax_year' => 'required|integer',
        ]);

        $log = SyncLog::create([
            'status'  => 'running',
            'message' => 'Initializing domain synchronization…',
        ]);

        RunImportSync::dispatch(
            (int) $validated['tax_year'],
            $log->id,
            'domain'
        );

        return response()->json([
            'sync_log_id' => $log->id,
        ]);
    }

    public function syncStatus(int $id)
    {
        return SyncLog::findOrFail($id);
    }
}

