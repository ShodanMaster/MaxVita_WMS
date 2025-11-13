<?php
namespace App\Http\Controllers\Reports;
use App\Exports\Report\DispatchEntryBarcodeExport;
use App\Exports\Report\DispatchEntryDetailedExport;
use App\Exports\Report\DispatchEntryExport;
use App\Exports\Report\ReceiptBarcodeExport;
use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DispatchEntryReportController extends Controller
{
    public function index(Request $request)
    {
        $dispatches = Dispatch::select('id', 'dispatch_number')->get();
        return view('report.dispatch.dispatch_entry_report', compact('dispatches'));
    }

    public function store(Request $request)
    {

        $searchData = [
        'from_date'      => $request->fromdate,
        'to_date'        => $request->todate,
        'frombarcode' => $request->frombarcode,
        'tobarcode'     => $request->tobarcode,
        'dispatch_number'  => $request->dispatch_number,
        'dispatch_type'  => $request->dispatch_type,
        ];
    

     
        if ($request->button == 1) {
          
            return view('report.dispatch.dispatch_entry_summary_report', $searchData);
        }

      
        if ($request->button == 2) {
            $pren = Dispatch::reportSummary($searchData);
            return Excel::download(new DispatchEntryExport($pren), 'dispatch_summary_report.xlsx');
        }

      
        if ($request->button == 3) {
            return view('report.dispatch.dispatch_entry_detailed_report',$searchData);
        }

       
        if ($request->button == 4) {
            $pren = Dispatch::reportDetailed($searchData);
            return Excel::download(new DispatchEntryDetailedExport($pren), 'dispatch_entry_detailed_report.xlsx');
        }

      
        if ($request->button == 5) {
            return view('report.dispatch.dispatch_barcode_report',$searchData);
        }

      
        if ($request->button == 6) {
            $pren = Dispatch::scannedBarcodes($searchData);
            return Excel::download(new DispatchEntryBarcodeExport($pren), 'dispatch_entry_barcode_report.xlsx');
        }

       
        if ($request->button == 7) {
           
            return view('report.dispatch.dispatch_receipt_barcode_report', $searchData);
        }

        if ($request->button == 8) {
            $pren = Dispatch::receiptBarcodes($searchData);
            return Excel::download(new ReceiptBarcodeExport($pren), 'receipt_scan_barcode_details.xlsx');
        }
    }

    public function getDispatchEntryViews(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'frombarcode' => $request->frombarcode,
            'tobarcode' => $request->tobarcode,
            'dispatch_number' => $request->dispatch_number,
            'dispatch_type' => $request->dispatch_type,
            'dispatchentryid' => $request->dispatchentryid,
        ];

        $plans = Dispatch::reportSummary($filters);

        return DataTables::of($plans)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $detailUrl  = route('dispatch-entry-report.show', [$row['id']]);
                $barcodeUrl = route('dispatch-entry-report.showbarcode', [$row['id']]);
                $receiptUrl = route('dispatch-entry-report.receiptbarcode', [$row['id']]);
                 return '
                    <a href="' . $detailUrl . '" class="btn btn-primary btn-sm me-1">More</a>
                    <a href="' . $barcodeUrl . '" class="btn btn-primary btn-sm">Dispatch Barcode</a>
                      <a href="' .  $receiptUrl . '" class="btn btn-primary btn-sm">Receipt Barcode</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

   

    public function show(string $id)
    {
        return view('report.dispatch.dispatch_entry_detailed_report', ['id' => $id,]);
    }


    public function getdispatchEntryDetailed(Request $request)
    {
      
    $filters = [

        'from_date' => $request->fromdate,
        'to_date' => $request->todate,
        'dispatch_type' => $request->dispatch_type,
        'dispatch_number' => $request->dispatch_number,
        'dispatch_type' => $request->dispatch_type,
        'dispatchentryid' => $request->dispatchentryid,
     ];
        

         

        $plans = Dispatch::reportDetailed($filters);

        return DataTables::of($plans)
            ->addIndexColumn()
            ->make(true);
    }

    public function barcodeView(string $id)
    {

        return view('report.dispatch.dispatch_barcode_report', compact('id'));
    }

    public function receiptbarcode(string $id)
    {
        
        return view('report.dispatch.dispatch_receipt_barcode_report', ['id' => $id,]);
    }

    public function getDispatchBarcodeDetails(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'frombarcode' => $request->frombarcode,
            'tobarcode' => $request->tobarcode,
            'dispatch_number' => $request->dispatch_number,
            'dispatch_type' => $request->dispatch_type,
            'dispatchentryid' => $request->dispatchentryid,
        ];

        $data = Dispatch::scannedBarcodes($filters);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('uom', fn($row) => $row['uom'] ?? '')
            ->editColumn('barcode', fn($row) => $row['barcode'] ?? '')
            ->editColumn('user_name', fn($row) => $row['user_name'] ?? '')
            ->editColumn('scan_time', fn($row) => $row['scan_time'] ?? '')
            ->toJson();
    }

    public function getReceiptDetails(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'frombarcode' => $request->frombarcode,
            'tobarcode' => $request->tobarcode,
            'dispatch_number' => $request->dispatch_number,
            'dispatch_type' => $request->dispatch_type,
            'dispatchentryid' => $request->dispatchentryid,

        ];
        $data = Dispatch::receiptBarcodes($filters);

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}
