<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\GrnDetailedExport;
use App\Exports\Report\GrnItemwiseExport;
use App\Exports\Report\GrnPoWiseExport;
use App\Exports\Report\GrnSummaryExport;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Category;
use App\Models\Grn;
use App\Models\GrnPurchaseOrder;
use App\Models\GrnPurchaseOrderSub;
use App\Models\GrnSub;
use App\Models\SubCategory;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Location;
use App\Models\Uom;
use App\Models\User;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GrnReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::select('id', 'name', 'item_code')->get();

        return view('report.grn.grn_report', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $fromdate = $request->from_date;
        $todate = $request->to_date;
        $grnNumber = $request->grn_number;
        $grn_type = $request->grn_type;
        $item_id = $request->item_id;
        $item =  $request->item_id;


        $searchData = [
            'from_date'  => $fromdate,
            'to_date'    => $todate,
            'grn_number'    => $grnNumber,
            'grn_type'   => $grn_type,
            'item_id'   => $item_id,
            'item'      => $item,
        ];


        if ($request->button == 2) {
            $grn = Grn::reportSummary($searchData);

            return Excel::download(new GrnSummaryExport($grn), 'grn_summary_report.xlsx');
        }
        if ($request->button == 3) {

            return view('report.grn.grn_report_itemwise', $searchData);
        }
        if ($request->button == 4) {
            $grn = Grn::reportitemwise($searchData);

            $item_id = $searchData['item_id'] ?? null;
            return Excel::download(new GrnItemwiseExport($grn, $item_id), 'grn_itemWise_report.xlsx');
        }

        if ($request->button == 5) {

            return view('report.grn.grn_report_detailed', $searchData);
        }
        if ($request->button == 6) {


            $grn = Grn::filteredData($searchData);

            return Excel::download(new GrnDetailedExport($grn), 'grn_detailed_report.xlsx');
        }

        if ($request->button == 7)
        {


            return view('report.grn.grn_report_po', $searchData);
        }

        if ($request->button == 8) {


            $grn = Grn::reportpowise($searchData);

            return Excel::download(new GrnPoWiseExport($grn), 'grn_powise_report.xlsx');
        }

        return view('report.grn.grn_report_summary', $searchData);
    }


    public function getGrnSummary(Request $request)
    {

        $filters = [
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'grn_number' => $request->grn_number,
            'grn_type' => $request->grn_type,
            'item_id' => $request->item_id,

        ];

        $grns = Grn::reportSummary($filters);

        return DataTables::of($grns)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $url = url('grn-report/' . $row['id']);
                return '<a href="' . $url . '" class="btn btn-sm btn-primary">More</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        return view('report.grn.grn_report_itemwise', compact('id'));
    }

    public function getGrnPo(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'grn_number' => $request->grn_number,
            'grnnumber' => $request->grnnumber,
            'grn_type' => $request->grn_type,
            'item_id' => $request->item_id,
            'item' => $request->item,
        ];


        $query = Grn::reportpowise($filters);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $id = $row['id'];
                $item_id = $row['item_id'];
                $url = route('grn-report.powise', [
                    'id' => $id,
                    'item_id' => $item_id
                ]);
                return '<a href="' . $url . '" class="btn btn-sm btn-primary">More</a>';
            })

            ->rawColumns(['action'])
            ->make(true);
    }


    public function grnItemWise(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'grn_number' => $request->grn_number,
            'grn_numberid' => $request->grn_numberid,
            'grn_type' => $request->grn_type,
            'item_id' => $request->item_id,
        ];

        $grns = Grn::reportitemwise($filters);

        return DataTables::of($grns)
            ->addIndexColumn()
            ->editColumn('total_quantity', function ($row) {
                return $row['total_quantity'];
            })
            ->addColumn('action', function ($row) {
                $url = route('grn-report.itemwise', [
                    'id' => $row['grn_id'],
                    'item_id' => $row['item_id']
                ]);
                return '<a href="' . $url . '" class="btn btn-sm btn-primary">More</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function poWise($id,$item_id)
    {

        return view('report.grn.grn_report_detailed', [
            'item_id' => $item_id,
            'id' => $id,
        ]);

    }


    public function itemwise($id, $item_id)
    {
        return view('report.grn.grn_report_detailed', [
            'item_id' => $item_id,
            'id' => $id,
        ]);
    }



    public function grnDetailed(Request $request)
    {
        $filters = [
            'from_date'     => $request->fromdate,
            'to_date'       => $request->todate,
            'grn_number'        => $request->grn_number,
            'grn_numberid'  => $request->grn_numberid,
            'grn_type'      => $request->grn_type,
            'item_id'       => $request->item_id,
        ];


        $grns = Grn::reportdetailed($filters);

        return DataTables::of($grns)
            ->addIndexColumn()
            ->make(true);
    }


}
