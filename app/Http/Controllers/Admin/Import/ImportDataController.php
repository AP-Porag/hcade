<?php

namespace App\Http\Controllers\Admin\Import;

use App\Http\Controllers\Controller;
use App\Services\Domain\DomainSyncService;
use App\Utils\GlobalConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportDataController extends Controller
{
    public function index()
    {
        $years = DB::table('raw_real_accts')
            ->select('yr')
            ->whereNotNull('yr')
            ->distinct()
            ->orderByDesc('yr')
            ->pluck('yr')
            ->values()
            ->toArray();

        return inertia('admin/import/index', [
            'availableYears' => $years,
            'defaultYear' => $years[0] ?? GlobalConstant::DEFAULT_TAX_YEAR,
        ]);
    }

    public function syncDomain(Request $request)
    {
        $request->validate([
            'tax_year' => 'required|integer',
        ]);


        app(DomainSyncService::class)->sync($request->tax_year);

        return back()->with('success', 'Domain data synced successfully.');
    }
}

