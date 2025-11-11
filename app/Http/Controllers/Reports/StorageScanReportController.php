<?php

namespace App\Http\Controllers\Reports;
use App\Exports\Report\StorageScanExport;
use App\Http\Controllers\Controller;
use App\Models\Grn;
use App\Models\Item;
use App\Models\ProductionPlan;
use App\Models\ProductionScan;
use App\Models\StorageScan;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StorageScanReportController extends Controller
{

    public function index()
    {
        $items = Item::select('id', 'name', 'item_code')->get();
        return view('report.storage_scan.storage_scan_report', compact('items'));
    }

    public function store(Request $request)
    {

        

        $searchData = [
            'from_date'  => $request->fromdate,
            'to_date'  => $request->todate,
            'from_barcode' => $request->from_barcode,
            'to_barcode' => $request->to_barcode,
            'item_id' => $request->item_id,
            'transaction_type' => $request->transaction_type,
           
        ];


        if ($request->button == 1)
        {

            return view('report.storage_scan.storage_scan_report_view',$searchData);
        }

        if ($request->button == 2) {
            $storage_scan = StorageScan::reportSummary($searchData);
            return Excel::download(new StorageScanExport($storage_scan), 'Storage Scan Report.xlsx');
        }
    }
    

    public function getStorageScanViews(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'from_barcode' => $request->from_barcode,
            'to_barcode' => $request->to_barcode,
            'item_id' => $request->item_id,
            'transaction_type' => $request->transaction_type,
           
        ];
        if ($request->transaction_type == 'grn') {
            $source_type = 'GRN';
        } elseif ($request->transaction_type == 'production') {
            $source_type = 'Production';
        }

        $plan = StorageScan::reportSummary($filters);


        return DataTables::of($plan)
            ->addIndexColumn()
            ->make(true);
    }
}
