<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\BinwiseStockExport;
use App\Exports\Report\DetailedStockExport;
use App\Exports\Report\ExpirewiseStockExport;
use App\Exports\Report\StockSummaryExport;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Bin;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Item;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Location;

use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index()
    {
        $items = Item::select('id', 'item_code', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        $locations = Location::select('id', 'name')->get();
        $branches = Branch::select('id', 'name')->get();
        $bins = Bin::select('id', 'name')->get();
        $barcodes = Barcode::select('id', 'batch_number')->get();



        return view('report.stock.stock_report', compact('items', 'categories', 'locations', 'branches', 'bins', 'barcodes'));
    }


    public function store(Request $request)
    {

        $searchData = [
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'from_barcode' => $request->frombarcode,
            'to_barcode' => $request->tobarcode,
            'category_id' => $request->category_id,
            'item_id' => $request->item_id,
            'branch_id' => $request->branch_id,
            'location_id' => $request->location_id,
            'grn_type' => $request->grn_type,
            'bin_id' => $request->bin_id,
            'stock_type' => $request->stock_type,

            'batch_number' => $request->batch_number,
        ];

        // $filters = $this->filteredArray($request);


        if ($request->button == 1) {

            return view('report.stock.stock_report_summary', $searchData);
        }

        if ($request->button == 2) {
            $stock = Barcode::stockreportsummary($searchData);
            return Excel::download(new StockSummaryExport($stock), 'stock_summary_report.xlsx');
        }

        if ($request->button == 3) {

            return view('report.stock.stock_report_binwise', $searchData);
        }

        if ($request->button == 4) {
            $stock = Barcode::stockreportbinwise($searchData);
            $i = 0;
            return Excel::download(new BinwiseStockExport($stock), 'bin_wise_stock_report.xlsx');
        }

        if ($request->button == 5) {

            return view('report.stock.stock_report_detailed', $searchData);
        }

        if ($request->button == 6) {
            $stock = Barcode::reportDetailed($searchData);
            $i = 0;
            return Excel::download(new DetailedStockExport($stock, $i), 'detailed_stock_report.xlsx');
        }

        if ($request->button == 7) {

            return view('report.stock.stock_report_expirewise', $searchData);
        }


        if ($request->button == 8) {
            $stock = Barcode::stockReportExpirewise($searchData);

            $i = 0;
            return Excel::download(new ExpirewiseStockExport($stock, $i), 'expirewise_stock_report.xlsx');
        }
    }

    public function getstockReportSummaries(Request $request)
    {


        $filters = $this->filteredArray($request);

        $stock = Barcode::stockReportSummary($filters);

        return DataTables::of($stock)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {


                $branch_id = $row['branch_id'];
                $location_id = $row['location_id'];
                $item_id = $row['item_id'];


                $url = route('stock-report.show1', [
                    'branch_id' => $branch_id,
                    'location_id' => $location_id,
                    'item_id' =>   $item_id,
                ]);

                return '<a href="' . $url . '" class="btn btn-sm btn-primary">More</a>';
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($branch_id, $location_id, $item_id)
    {

        return view('report.stock.stock_report_binwise', [
            'branch_id' => $branch_id,
            'location_id' => $location_id,
            'item_id' => $item_id,
        ]);
    }





    public function getstockBinWises(Request $request)
    {


        $filters = $this->filteredArray($request);

        $stock = Barcode::stockreportbinwise($filters);



        return DataTables::of($stock)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $branch_id = $row['branch_id'];
                $location_id = $row['location_id'];
                $bin_id = $row['bin_id'];
                $item_id = $row['item_id'];


                $url = route('stock-report.showExpireWises', [
                    'branch_id' => $branch_id,
                    'location_id' => $location_id,
                    'bin_id' => $bin_id,
                    'item_id' =>   $item_id,
                ]);

                return '<a href="' . $url . '" class="btn btn-sm btn-primary">More</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function showExpireWises($branch_id, $location_id, $bin_id, $item_id)
    {

        return view('report.stock.stock_report_detailed', [
            'branch_id' => $branch_id,
            'location_id' => $location_id,
            'item_id' => $item_id,
            'bin_id' => $bin_id,
        ]);
    }

    public function getstockDetailed(Request $request)
    {
        $filters = $this->filteredArray($request);

        $stock = Barcode::reportDetailed($filters);


        return DataTables::of($stock)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-sm btn-primary">More</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getStockExpireWises(Request $request)
    {

        $filters = $this->filteredArray($request);

        $stock = Barcode::stockReportExpirewise($filters);

        return DataTables::of($stock)
            ->addIndexColumn()
            ->make(true);
    }

    private function filteredArray($request){
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'from_barcode' => $request->from_barcode,
            'to_barcode' => $request->to_barcode,
            'category_id' => $request->category_id,
            'item_id' => $request->item_id,
            'branch_id' => $request->branch_id,
            'location_id' => $request->location_id,
            'grn_type' => $request->grn_type,
            'bin_id' => $request->bin_id,
            'stock_type' => $request->stock_type,
            'batch_number' => $request->batch_number,
        ];

        return $filters;
    }
}
