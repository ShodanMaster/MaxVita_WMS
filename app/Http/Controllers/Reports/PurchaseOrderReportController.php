<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\PurchaseOrderDetailedExport;
use App\Exports\Report\PurchaseOrderSummaryExport;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderReportController extends Controller
{

    public function index()
    {
        $items = Item::select('id','item_code', 'name')->get();
        return view('report.purchase.purchaseorderreport',compact('items'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $button   = $request->button;

        $searchData = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'orderno' => $request->orderno,
            'porderno' => $request->orderno,
            'item_id' => $request->item_id,
        ];

        if ($button == 2)
        {

            $data = PurchaseOrder::reportSummary($searchData);

            return Excel::download(new PurchaseOrderSummaryExport($data), 'purchase_order_summary_report.xlsx');
        }

        if ($request->button == 6)
         {
            $data = PurchaseOrder::reportDetailed($searchData);
            // dd($data);
            return Excel::download(new PurchaseOrderDetailedExport($data), 'purchase_order_detailed_report.xlsx');
        }

        if ($request->button == 5) {

            return view('report.purchase.purchaseorderreportdetailed', $searchData);
        }

        return view('report.purchase.purchaseorderreportsummary', $searchData);

    }

    public function getPurchaseOrders(Request $request)
    {
        $filters=[
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'orderno' => $request->orderno,
            'porderno' => $request->orderno,
            'item_id' => $request->item_id,
        ];


        $purchaseOrders = PurchaseOrder::reportSummary($filters);
        // dd($purchaseOrders);
        return DataTables::of($purchaseOrders)
            ->addIndexColumn()
            ->editColumn('purchase_date', function ($row) {
                return $row["purchase_date"] ? Carbon::parse($row["purchase_date"])->format('d-m-Y') : '';
            })
            ->addColumn('action', function ($row) {
                $url = route('purchase-order-report.show', [$row["id"]]);
                return '<a href="' . $url . '" class="btn btn-primary btn-sm">More</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getPurchaseDetailed(Request $request)
    {
        // dd($request->all());
        $filters=[
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'orderno' => $request->orderno,
            'porderno' => $request->porderno,
            'item_id' => $request->item_id,
            'purchaseOrderid' => $request->purchaseOrderid,
        ];

        $purchaseOrders = PurchaseOrder::reportDetailed($filters);

        return DataTables::of($purchaseOrders)
            ->addIndexColumn()
            ->make(true);
    }

    public function show(string $id)
    {
        return view('report.purchase.purchaseorderreportdetailed', compact('id'));
    }

}
