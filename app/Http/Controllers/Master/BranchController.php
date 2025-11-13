<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\BranchExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\BranchImport;
use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('master.branch.index');
    }

    public function getBranches(Request $request)
    {
        if ($request->ajax()) {

            $branches = Branch::select(['id', 'name', 'branch_code', 'address', 'gst_no']);

            return DataTables::of($branches)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('branch.edit', $row->id);
                    $deleteUrl = route('branch.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    $btn = '
                    <td width="150px">
                        <a href="' . $editUrl . '" data-toggle="tooltip" data-placement="bottom" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit text-primary">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <button type="button" class="btn p-0" onclick="sweetAlertDelete(\'' . $deleteUrl . '\')" data-toggle="tooltip" title="Delete">
                            <span class="fa fa-trash text-danger"></span>
                        </button>
                    </td>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.branch.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:branches,name',
            'branch_code' => 'required|string|unique:branches,branch_code',
            'address' => 'nullable|string',
            'gst_no' => 'nullable|string',
        ]);

        try {

            $branch = Branch::create([
                'name'        => $request->get('name'),
                'branch_code' => strtoupper($request->get('branch_code')),
                'address'     => $request->get('address'),
                'gst_no'      => $request->get('gst_no'),
            ]);

            Alert::toast('Warehouse Added Successfully', 'success')->autoClose(3000);
            return redirect()->route('branch.index');

        } catch (Exception $e) {
            dd($e);
            Log::error('Warehouse Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the warehouse.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        try{

            return view("master.branch.create", compact('branch'));

        } catch(Exception $e){
            Log::error('Warehouse Edit Error: ' . $e->getMessage());
            Alert::toast('An error occurred while fetching the warehouse details.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|unique:branches,name,'.$id,
            'branch_code' => 'required|string|unique:branches,branch_code,'.$id,
            'address' => 'nullable|string',
            'gst_no' => 'nullable|string',
        ]);

        try{


            Branch::where('id', $id)->update([
                'name'        => $request->get('name'),
                'branch_code' => strtoupper($request->get('branch_code')),
                'address'     => $request->get('address'),
                'gst_no'      => $request->get('gst_no'),
            ]);

            Alert::toast('Branch Modified successfully', 'success')->autoClose(3000);
            return redirect()->route('branch.index');
        } catch(Exception $e){
            dd($e);
            Log::error('Warehouse Update Error: ' . $e->getMessage());
            Alert::toast('An error occurred while updating the warehouse details.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $branch = Branch::findOrFail($id);

            if($branch->locations()->exists() || $branch->barcodes()->exists()){
                return response()->json([
                    'status' => 409,
                    'message' => 'You cannot delete this record because it is linked to other records.'
                ], 409);
            }

            $branch->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Warehouse deleted successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Branch Delete Error: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'An unexpected error occurred while deleting the warehouse.'
                ], 500);
            }

            Alert::toast('An error occurred while deleting the Bin.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }

    public function branchExcelExport(){
        try{
            return Excel::download(new BranchExport, 'warehouses.xlsx');
        } catch(Exception $e){
            Log::error('Branch Excel Export Error: ' . $e->getMessage());
            Alert::toast('An error occurred while branch excel exporting.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }

    public function branchExcelUpload(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);
        $current = date('Y-m-d_H-i-s');

        try{
            Excel::import(new BranchImport, $request->file('excel_file'));

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/master_uploads/warehouse_uploads', $fileName, 'public');

            Alert::toast('Branch Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('branch.index');

        } catch (ValidationException $e) {

            Log::error('Branch Excel Validation Error: ' . json_encode($e->errors()));
            Alert::toast('Validation failed while importing branch Excel.', 'error')->autoClose(3000);
            return redirect()->route('branch.index')->withErrors($e->errors());

        } catch (Exception $e) {

            Log::error('Branch Excel Import Error: ' . $e->getMessage());
            Alert::toast('An unexpected error occurred while importing.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }

}
