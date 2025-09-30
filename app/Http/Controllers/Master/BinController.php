<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\BinExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\BinImport;
use App\Models\Bin;
use App\Models\Location;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class BinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('master.bin.index');
    }

    public function getBins(Request $request){
        if ($request->ajax()) {
            $bins = Bin::with('location')
                ->select(['id', 'name', 'bin_code', 'bin_type', 'description', 'location_id']);

            return DataTables::of($bins)
                ->addIndexColumn()
                ->addColumn('location_name', function ($row) {
                    return $row->location->name ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('bin.edit', $row->id);
                    $deleteUrl = route('bin.destroy', $row->id);

                    $btn = '
                        <td width="150px">
                            <a href="' . $editUrl . '" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit text-primary">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this bin?\')" style="display:inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn" data-toggle="tooltip" title="Delete">
                                    <span class="fa fa-trash text-danger"></span>
                                </button>
                            </form>
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
        $locations = Location::all();
        return view('master.bin.create', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'name' => 'required|string|unique:bins,name',
            'bin_code' => 'required|string|unique:bins,bin_code',
            'bin_type' => 'required|in:FG,RM',
            'address' => 'nullable|string',
        ]);

        try{
            $bin = Bin::create([
                'location_id' => $request->location_id,
                'name' => $request->name,
                'bin_code' => $request->bin_code,
                'bin_type' => $request->bin_type,
                'description' => $request->description,
            ]);

            Alert::toast('Bin Added Successfully', 'success')->autoClose(3000);
            return redirect()->route('bin.index');

        } catch(Exception $e){
            Log::error('Bin Store Error: ' . $e->getMessage());
            Alert::toast('An error occurred while storing the Bin.', 'error')->autoClose(3000);
            return redirect()->route('bin.index');
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
    public function edit(Bin $bin)
    {
        try{
            $locations = Location::all();
            return view('master.bin.create', compact('bin', 'locations'));
        } catch(Exception $e){
            Log::error('Bin Edit Error: ' . $e->getMessage());
            Alert::toast('An error occurred while fetching the bin details.', 'error')->autoClose(3000);
            return redirect()->route('bin.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'name' => 'required|string|unique:bins,name,'.$id,
            'bin_code' => 'required|string|unique:bins,bin_code,'.$id,
            'bin_type' => 'required|in:FG,RM',
            'address' => 'nullable|string',
        ]);

        try{
            $bin = Bin::findOrFail($id);
            $bin->update([
                'location_id' => $request->location_id,
                'name' => $request->name,
                'bin_code' => $request->bin_code,
                'bin_type' => $request->bin_type,
                'description' => $request->description,
            ]);

            Alert::toast('Bin Updated Successfully', 'success')->autoClose(3000);
            return redirect()->route('bin.index');

        } catch(Exception $e){
            Log::error('Bin Update Error: ' . $e->getMessage());
            Alert::toast('An error occurred while updating the Bin.', 'error')->autoClose(3000);
            return redirect()->route('bin.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $bin = Bin::findOrFail($id);
            $bin->delete();

            Alert::toast('Bin Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('bin.index');

        } catch(Exception $e){
            Log::error('Bin Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the Bin.', 'error')->autoClose(3000);
            return redirect()->route('bin.index');
        }
    }

    public function binExcelExport()
    {
        try{
            return Excel::download(new BinExport, 'bins.xlsx');
        } catch(Exception $e){
            Log::error('Bin Excel Export Error: ' . $e->getMessage());
            Alert::toast('An error occurred while bin excel exporting.', 'error')->autoClose(3000);
            return redirect()->route('bin.index');
        }
    }

    public function binExcelUpload(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        try{
            Excel::import(new BinImport, $request->file('excel_file'));

            Alert::toast('Bin Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('bin.index');

        } catch(Exception $e){
            // dd($e);
            Log::error('Bin Excel Import Error: ' . $e->getMessage());
            Alert::toast('An error occurred while bin excel importing: '.$e->getMessage(), 'error')->autoClose(3000);
            return redirect()->route('bin.index');
        }
    }
}
