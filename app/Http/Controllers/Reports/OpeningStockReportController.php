<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\OpeningStockDetailedExport;
use App\Exports\Report\OpeningStockSummaryExport;
use App\Http\Controllers\Controller;
use App\Models\Item;
use Yajra\DataTables\Facades\DataTables;
use App\Models\OpeningStock;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OpeningStockReportController extends Controller
{
    public function index(Request $request)
    {
        $openings=OpeningStock::select('id','opening_number')->get();
        $items =Item::select('id','name','item_code')->get();
        return view('report.opening_stock.opening_stock_report', compact('openings', 'items'));
    }

    public function store(Request $request)
    {
        $searchData = [
            'from_date'      => $request->fromdate,
            'to_date'        => $request->todate,
            'opening_number' => $request->opening_number,
            'opennumber'     => $request->opening_number,
            'item_id'           => $request->item_id,
        ];
       

        if ($request->button == 1) 
        {
            return view('report.opening_stock.opening_stock_summary_report', $searchData);
        }

        if ($request->button == 2) {

            $data = OpeningStock::reportSummary($searchData);

            return Excel::download(new OpeningStockSummaryExport($data), 'opening_stock_summary_report.xlsx');
        }

        if ($request->button == 3) 
        {
            return view('report.opening_stock.opening_stock_detailed_report', $searchData);
        }

        if ($request->button == 4) {

            $data = OpeningStock::reportDetailed($searchData);

            return Excel::download(new OpeningStockDetailedExport($data), 'opening_stock_detailed_report.xlsx');
        }
    }

    public function getOpeningStockViews(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
             'opening_number' =>$request->opening_number,
            'opennumber'    => $request->opening_number,
            'item_id' =>$request->item_id,
        ];


        $open_numbers= OpeningStock::reportSummary($filters);
        return DataTables::of(collect($open_numbers))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $url = route('opening-stock-report.itemwise', [
                    'id' => $row['id']
                ]);
                return '<a href="' . $url . '" class="btn btn-sm btn-primary">More</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function itemwise($id)
    {
        return view('report.opening_stock.opening_stock_detailed_report', [
            'id' => $id,
        ]);
       
    }
    
    public function getOpeningStockDetailed(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'opening_number' => $request->opening_number,
            'opennumber' => $request->opennumber,
            'item_id' => $request->item_id,
            'opennumberid' => $request->opennumberid,
        ];


        $open_numbers = OpeningStock::reportDetailed($filters);

        return DataTables::of(collect($open_numbers))
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }


}
