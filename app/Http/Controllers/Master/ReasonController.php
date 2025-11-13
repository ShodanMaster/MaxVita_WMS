<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\ReasonExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\ReasonImport;
use App\Models\Reason;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class ReasonController extends Controller
{
    public function index()
    {
        return view('master.reason.index');
    }

    public function getReasons(Request $request)
    {
        if ($request->ajax()) {

            $reasons = Reason::select(['id', 'reason', 'description']);

            return DataTables::of($reasons)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('reason.edit', $row->id);
                    $deleteUrl = route('reason.destroy', $row->id);

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

    public function create(){
        return view('master.reason.create');
    }

    public function store(Request $request){
        $request->validate([
            'reason' => 'required|unique:reasons,reason',
            'description' => 'nullable|string',
        ]);

        try{

            Reason::create([
                'reason' => $request->reason,
                'description' => $request->description,
            ]);

            Alert::toast('Reason added successfully!', 'success')->autoClose(3000);
            return redirect()->route('reason.index');

        }  catch (Exception $e) {
            dd($e);
            Log::error('Reason Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the reason.', 'error')->autoClose(3000);
            return redirect()->route('reason.index');
        }
    }

    public function edit(Reason $reason){
        try{
            return view('master.reason.create', compact('reason'));
        } catch (Exception $e) {
            dd($e);
            Log::error('Reason Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while fetching the reason.', 'error')->autoClose(3000);
            return redirect()->route('reason.index');
        }
    }

    public function update(Request $request, Reason $reason){
        $request->validate([
            'reason' => 'required|unique:reasons,reason,' . $reason->id,
            'description' => 'nullable|string',
        ]);

        try{

            $reason->update([
                'reason' => $request->reason,
                'description' => $request->description,
            ]);

            Alert::toast('Reason updated successfully!', 'success')->autoClose(3000);
            return redirect()->route('reason.index');

        }  catch (Exception $e) {
            Log::error('Reason Update Error: ' . $e->getMessage());

            Alert::toast('An error occurred while updating the reason.', 'error')->autoClose(3000);
            return redirect()->route('reason.index');
        }
    }

    public function destroy($id){
        try{
            $reason = Reason::findOrFail($id);

            $reason->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Location deleted successfully.'
            ]);

        } catch (Exception $e) {
            Log::error('Reason Delete Error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'An unexpected error occurred while deleting the reason.'
                ], 500);
            }

            Alert::toast('An error occurred while deleting the reason.', 'error')->autoClose(3000);
            return redirect()->route( 'reason.index');
        }
    }

    public function reasonExcelExport()
    {
        try {
            return Excel::download(new ReasonExport, 'reasons.xlsx');
        } catch (Exception $e) {
            Log::error('Reason Excel Export Error: ' . $e->getMessage());
            Alert::toast('An error occurred while exporting reasons to Excel.', 'error')->autoClose(3000);
            return redirect()->route('reason.index');
        }
    }

    public function reasonExcelUpload(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try{
            Excel::import(new ReasonImport, $request->file('excel_file'));

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/master_uploads/reason_uploads', $fileName, 'public');

            Alert::toast('Reason Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('reason.index');

        } catch (ValidationException $e) {

            Log::error('Reason Excel Validation Error: ' . json_encode($e->errors()));
            Alert::toast('Validation failed while importing reason Excel.', 'error')->autoClose(3000);
            return redirect()->route('reason.index')->withErrors($e->errors());

        } catch (Exception $e) {

            Log::error('Reason Excel Import Error: ' . $e->getMessage());
            Alert::toast('An unexpected error occurred while importing.', 'error')->autoClose(3000);
            return redirect()->route('reason.index');
        }
    }
}
