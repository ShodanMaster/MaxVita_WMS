<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\SalesReturnDetailedExport;
use App\Exports\Report\SalesReturnSummaryExport;
use App\Models\Customer;
use App\Models\Item;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class SalesReturnReportController extends Controller
{
    public function index(Request $request)
    {
        $items=Item::select('id','name','item_code')->get();
        $customers=Customer::select('id', 'name')->get();
        return view('report.sales_return.sales_return_view',compact('items','customers'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $button   = $request->button;

        $searchData = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'item_id' => $request->item_id,
            'customer_id' => $request->customer_id,
        ];

        if ($request->button == 1) {

            return view('report.sales_return.sales_return_summary', $searchData);
        }

        if ($button == 2) {

            $data = SalesReturn::reportSummary($searchData);


            return Excel::download(new SalesReturnSummaryExport($data), 'sales_return_summary_report.xlsx');
        }

        if ($request->button == 3)
        {

            return view('report.sales_return.sales_return_detail', $searchData);
        }
        if ($button == 4) {

            $data = SalesReturn::reportDetailed($searchData);

            return Excel::download(new SalesReturnDetailedExport($data), 'sales_return_detailed_report.xlsx');
        }


    }

    public function getSalesReportSummaries(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'item_id' => $request->item_id,
            'customer_id' => $request->customer_id,
        ];



        $salesreturns = SalesReturn::reportSummary($filters);

        return DataTables::of($salesreturns)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

            $id = $row['id'];
            $url = route('sales-return-report.show', [
                'sales_return_report' => $id,
            ]);

            return '<a href="' . $url . '" class="btn btn-sm btn-primary">More</a>';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function show(string $id)
    {

        return view('report.sales_return.sales_return_detail', compact('id'));
    }

    public function getSalesReportDetailed(Request $request)
    {

        $filters = [
            'sales_return_id' => $request->sales_return_id,
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'item_id' => $request->item_id,
            'customer_id' => $request->customer_id,
        ];

        $salesreturns = SalesReturn::reportDetailed($filters);

        return DataTables::of($salesreturns)
            ->addIndexColumn()
            ->make(true);
    }
}
