<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\UomExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\UomImport;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class UomController extends Controller
{
    public function index()
    {
        return view('master.uom.index');
    }

    public function getUoms(Request $request)
    {
        if ($request->ajax()) {

            $uoms = Uom::select(['id', 'name', 'uom_code']);

            return DataTables::of($uoms)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    $editUrl = route('uom.edit', $row->id);
                    $deleteUrl = route('uom.destroy', $row->id);

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



    public function create()
    {
        $uoms = Uom::all();
        return view('master.uom.create', compact('uoms'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|unique:uoms,name',
            'uom_code' => 'required|string|unique:uoms,uom_code'
        ]);

        try {

         Uom::create([
                'name' => $request->name,
                'uom_code' => $request->uom_code
            ]);

            Alert::toast('Uom added successfully!', 'success')->autoClose(3000);
            return redirect()->route('uom.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Uom Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the uom.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }

    public function edit(Uom $uom)
    {
        try{
            return view('master.uom.create', compact('uom'));
        } catch (Exception $e) {
            dd($e);
            Log::error('Uom Edit Error: ' . $e->getMessage());

            Alert::toast('An error occurred while fetching the uom.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }

    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required|string|unique:uoms,name,' . $id,
            'uom_code' => 'required|string|unique:uoms,uom_code,' . $id,
        ]);
        try {


            Uom::where('id', $id)->update([
                'name' => $request->name,
                'uom_code' => $request->uom_code
            ]);
            Alert::toast('Uom modified successfully', 'success')->autoClose(3000);
            return redirect()->route('uom.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Uom Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the uom.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }


    public function destroy(string $id)
    {
        try {
            $uom = Uom::findOrFail($id);

            if ($uom->items()->exists()) {
                return response()->json([
                    'status' => 409,
                    'message' => 'You cannot delete this UOM because it is linked to other records.'
                ], 409);
            }

            $uom->delete();

            return response()->json([
                'status' => 200,
                'message' => 'UOM deleted successfully.'
            ]);

        } catch (Exception $e) {
            Log::error('UOM Delete Error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'An unexpected error occurred while deleting the uom.'
                ], 500);
            }

            Alert::toast('An error occurred while deleting the uom.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }

    public function uomExcelExport()
    {
        try{
            return Excel::download(new UomExport, 'Uom_Master.xlsx');
        } catch (Exception $e) {
            Log::error('Uom Excel Export Error: ' . $e->getMessage());
            Alert::toast('An error occurred while excel exporting the uom.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }

    public function uomExcelUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);
        $current = date('Y-m-d_H-i-s');
        try {
            Excel::import(new UomImport, $request->file('excel_file'));

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/master_uploads/uom_uploads', $fileName, 'public');

            Alert::toast('Uom Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('uom.index');
        } catch (ValidationException $e) {

            Log::error('Uom Excel Validation Error: ' . json_encode($e->errors()));
            Alert::toast('Validation failed while importing uom Excel.', 'error')->autoClose(3000);
            return redirect()->route('uom.index')->withErrors($e->errors());

        } catch (Exception $e) {

            Log::error('Uom Excel Import Error: ' . $e->getMessage());
            Alert::toast('An unexpected error occurred while importing.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }
}
