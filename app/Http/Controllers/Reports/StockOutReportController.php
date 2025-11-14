<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\StockOutDetailedExport;
use App\Exports\Report\StockOutSummaryExport;
use App\Models\Reason;
use App\Models\StockOut;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class StockOutReportController extends Controller
{
    public function index(Request $request)
    {
        $reasons = Reason::select('id', 'reason')->get();
        return view('report.stock_out.stock_out_report', compact('reasons'));
    }

    public function store(Request $request)
    {
        $searchData = [
            'from_date'      => $request->fromdate,
            'to_date'        => $request->todate,
            'reason' => $request->reason,
            'streason'  => $request->reason,
        ];


        if ($request->button == 1) {
            return view('report.stock_out.stock_out_summary_report', $searchData);
        }
        if ($request->button == 2) {

            $data = StockOut::reportSummary($searchData);

            return Excel::download(new StockOutSummaryExport($data), 'stock_out_summary_report.xlsx');
        }

        if ($request->button ==3) {
            return view('report.stock_out.stock_out_detailed_report', $searchData);
        }
        if ($request->button == 4) {

            $data = StockOut::reportDetailed($searchData);

            return Excel::download(new StockOutDetailedExport($data), 'stock_out_detailed_report.xlsx');
        }
    }

  public function getStockOutViews(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'reason' => $request->reason,
            'streason' => $request->reason,
        ];

        $stock_outs = StockOut::reportSummary($filters);

        return DataTables::of(collect($stock_outs))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
            $url = route('stock-out-report.show', [
                'stock_out_report' => $row['id'] 
            ]);
            return '<a href="' . $url . '" class="btn btn-sm btn-primary">More</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function show($id = null)
    {
        return view('report.stock_out.stock_out_detailed_report', [
          
            'id' => $id,
        ]);
    }
    public function getStockOutDetailed(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'reason' => $request->reason,
            'streason' => $request->reason,
            'streasonid' => $request->streasonid,
            'item_id' => $request->item_id
        ];

        $stock_outs = StockOut::reportDetailed($filters);
        return DataTables::of(collect($stock_outs))
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }
}
