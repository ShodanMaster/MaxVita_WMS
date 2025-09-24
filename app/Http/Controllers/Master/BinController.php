<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Bin;
use App\Models\Location;
use Illuminate\Http\Request;
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
                ->select(['id', 'name', 'bin_code', 'description', 'location_id']);

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
                ->rawColumns(['action']) // Allow HTML in 'action' column
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
            'address' => 'nullable|string',
        ]);

        try{
            $bin = Bin::create([
                'location_id' => $request->location_id,
                'name' => $request->name,
                'bin_code' => $request->bin_code,
                'description' => $request->description,
            ]);

            Alert::toast('Branch Added Successfully', 'success')->autoClose(3000);
            return redirect()->route('bin.index');

        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
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
        $locations = Location::all();
        return view('master.bin.create', compact('bin', 'locations'));
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
            'address' => 'nullable|string',
        ]);

        try{
            $bin = Bin::findOrFail($id);
            $bin->update([
                'location_id' => $request->location_id,
                'name' => $request->name,
                'bin_code' => $request->bin_code,
                'description' => $request->description,
            ]);

            Alert::toast('Bin Updated Successfully', 'success')->autoClose(3000);
            return redirect()->route('bin.index');

        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
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

        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
