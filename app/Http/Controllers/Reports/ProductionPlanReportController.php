<?php

namespace App\Http\Controllers\Reports;

use App\Exports\Report\ProductionPlanBarcodedExport;
use App\Exports\Report\ProductionPlanDetailedExport;
use App\Exports\Report\ProductionPlanViewExport;
use App\Http\Controllers\Controller;
use App\Models\ProductionPlan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductionPlanReportController extends Controller
{
    public function index()
    {
        $production_plans = ProductionPlan::select('id', 'plan_number')->get();
        return view('report.productionplan.production_plan_report', compact('production_plans'));
    }

    public function store(Request $request)
    {

        $searchData = [
            'from_date'   => $request->fromdate,
            'to_date'     => $request->todate,
            'frombarcode' => $request->frombarcode,
            'tobarcode'   => $request->tobarcode,
            'plan_no'     => $request->plan_no,
            'status'      => $request->status,
        ];


        if ($request->button == 1) {
           
            return view('report.productionplan.production_plan_report_view', $searchData);
        }

        if ($request->button == 2) {
            $pren = ProductionPlan::reportsummary($searchData);
            return Excel::download(new ProductionPlanViewExport($pren), 'production_plan_view_report.xlsx');
        }

        if ($request->button == 3) {
           
            return view('report.productionplan.production_plan_report_detailed', $searchData);
        }


        if ($request->button == 4) {
            $data = ProductionPlan::reportDetailed($searchData);
            return Excel::download(new ProductionPlanDetailedExport($data), 'production_plan_detailed_report.xlsx');
        }

        if ($request->button == 5) {

            return view('report.productionplan.production_plan_report_scandetailed', $searchData);
        }

        if ($request->button == 6) {
            $data = ProductionPlan::scannedBarcodes($searchData);
            return Excel::download(new ProductionPlanBarcodedExport($data), 'production_plan_barcode_report.xlsx');
        }
    }

    public function getProductionPlanViews(Request $request)
    {

        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'plan_no' => $request->plan_no,
            'status' => $request->status,
            'productionplanid'=>$request->productionplanid,
        ];
        

        $plans = ProductionPlan::reportSummary($filters);


        return DataTables::of($plans)
            ->addIndexColumn()

            ->addColumn('action', function ($row) {
                $showUrl = route('production-plan-report.show1', [$row['id']]);
                $editUrl = route('production-plan-report.barcodewise', [$row['id']]);

                return '
        <a href="' . $showUrl . '" class="btn btn-primary btn-sm me-1">More</a>
        <a href="' . $editUrl . '" class="btn btn-primary btn-sm">Barcode</a>
    ';
            })

            ->rawColumns(['action'])
            ->make(true);
    }


    public function getProductionDetailViews(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'plan_no' => $request->plan_no,
            'productionplanid' => $request->productionplanid,
            'status' => $request->status,
        ];


        $plans = ProductionPlan::reportDetailed($filters);


        return DataTables::of($plans)
            ->addIndexColumn()

            ->rawColumns(['action'])
            ->make(true);
    }


    public function show(string $id)
    {
        
        return view('report.productionplan.production_plan_report_detailed',[
            'id' => $id,
        ]);
    }

    public function getProductionBarcodeDetails(Request $request)
    {
        $filters = [
            'from_date' => $request->fromdate,
            'to_date' => $request->todate,
            'frombarcode' => $request->frombarcode,
            'tobarcode'   => $request->tobarcode,
            'plan_no' => $request->plan_no,
            'status' => $request->status,
            'productionplanid' => $request->productionplanid,
        ];

       
        $plans = ProductionPlan::scannedBarcodes($filters);


        return DataTables::of($plans)
            ->addIndexColumn()

            ->rawColumns(['action'])
            ->make(true);
    }

    public function barcodeWise(string $id)
    {
    
        return view('report.productionplan.production_plan_report_scandetailed', [
            'id' => $id,
        ]);
    }
}
