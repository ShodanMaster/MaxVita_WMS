<?php

namespace App\Http\Controllers\Master;

use App\Enums\LocationType;
use App\Exports\Master\LocationExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\LocationImport;
use App\Models\Branch;
use App\Models\Location;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('master.location.index');
    }

    public function getLocations(Request $request){
        if ($request->ajax()) {

            $locations = Location::with('branch')->select(['id', 'name', 'prefix', 'location_type', 'description', 'branch_id']);

            return DataTables::of($locations)
                ->addIndexColumn()
                ->addColumn('branch_name', function ($row) {
                    return $row->branch->name ?? '-';
                })->addColumn('location_type', function ($row) {
                    return LocationType::from($row->location_type)->label();
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('location.edit', $row->id);
                    $deleteUrl = route('location.destroy', $row->id);
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
                        <div class="btn-group">
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this location?\')" style="display:inline;">
                                ' . $csrf . '
                                ' . $method . '
                                <button type="submit" class="btn" data-toggle="tooltip" title="Delete">
                                    <span class="fa fa-trash text-danger"></span>
                                </button>
                            </form>
                        </div>
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
        $branches = Branch::all();
        return view('master.location.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|unique:locations,name',
            'prefix' => 'required|string|unique:locations,prefix',
            'branch_id' => 'required|integer|exists:branches,id',
            'location_type' => 'required|string',
            'description' => 'nullable|string'
        ]);

        try{

            Location::create([
                'name' => $request->name,
                'prefix' => $request->prefix,
                'branch_id' => $request->branch_id,
                'location_code' => $request->prefix,
                'location_type' => $request->location_type,
                'description' => $request->description
            ]);

            Alert::toast('Location added successfully!', 'success')->autoClose(3000);
            return redirect()->route('location.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Location Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the location.', 'error')->autoClose(3000);
            return redirect()->route('location.index');
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
    public function edit(Location $location)
    {
        try{
            $branches = Branch::all();
            return view('master.location.create', compact('location', 'branches'));
        } catch (Exception $e) {
            dd($e);
            Log::error('Location Fetch Error: ' . $e->getMessage());

            Alert::toast('An error occurred while fetching the location.', 'error')->autoClose(3000);
            return redirect()->route('location.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|unique:locations,name,'.$id,
            'prefix' => 'required|string|unique:locations,prefix,'.$id,
            'branch_id' => 'required|integer|exists:branches,id',
            'location_type' => 'required|string',
            'description' => 'nullable|string'
        ]);

        try{

            Location::where('id', $id)->update([
                'name' => $request->name,
                'prefix' => $request->prefix,
                'branch_id' => $request->branch_id,
                'location_type' => $request->location_type,
                'description' => $request->description
            ]);

            Alert::toast('Location modified successfully', 'success')->autoClose(3000);
            return redirect()->route('location.index');

        } catch (Exception $e) {
            dd($e);
            Log::error('Location Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the location.', 'error')->autoClose(3000);
            return redirect()->route('location.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            Location::where('id', $id)->delete();
            Alert::toast('Location Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('location.index');
        } catch(Exception $e){
            Log::error('Location Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the location.', 'error')->autoClose(3000);
            return redirect()->route('location.index');
        }
    }

    public function locationExcelExport(){
        try{
            return Excel::download(new LocationExport, 'locations.xlsx');
        } catch(Exception $e){
            Log::error('Location Excel Exporting Error: ' . $e->getMessage());
            Alert::toast('An error occurred while excel exporting the location.', 'error')->autoClose(3000);
            return redirect()->route('location.index');
        }
    }

    public function locationExcelUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try{
            Excel::import(new LocationImport, $request->file('excel_file'));

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/master_uploads/location_uploads', $fileName, 'public');

            Alert::toast('Location Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('location.index');
        }
        catch(Exception $e)
        {
            dd($e);
            Log::error('Location Excel Import Error: ' . $e->getMessage());
            Alert::toast('An error occurred while location excel importing: .'. $e->getMessage(), 'error')->autoClose(3000);
            return redirect()->route('location.index');
        }
    }
}
