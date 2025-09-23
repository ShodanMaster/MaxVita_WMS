<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                        <div class="btn-group">
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this Branch?\')" style="display:inline;">
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
        return view('master.branch.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:branches,name',
            'branch_code' => 'required|string',
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

            Alert::toast('Branch Added Successfully', 'success')->autoClose(3000);
            return redirect()->route('branch.index');

        } catch (\Exception $e) {
            dd($e);
            Log::error('Branch Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the branch.', 'error')->autoClose(3000);
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

        } catch(\Exception $e){
            Log::error('Branch Edit Error: ' . $e->getMessage());
            Alert::toast('An error occurred while fetching the branch details.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{

            $request->validate([
                'name' => 'required|string|unique:branches,name,'.$id,
                'branch_code' => 'required|string',
                'address' => 'nullable|string',
                'gst_no' => 'nullable|string',
            ]);

            Branch::where('id', $id)->update([
                'name'        => $request->get('name'),
                'branch_code' => strtoupper($request->get('branch_code')),
                'address'     => $request->get('address'),
                'gst_no'      => $request->get('gst_no'),
            ]);

            Alert::toast('Branch Modified successfully', 'success')->autoClose(3000);
            return redirect()->route('branch.index');
        } catch(\Exception $e){
            dd($e);
            Log::error('Branch Update Error: ' . $e->getMessage());
            Alert::toast('An error occurred while updating the branch details.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            Branch::where('id', $id)->delete();
            Alert::toast('Branch Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('branch.index');
        } catch(\Exception $e){
            Log::error('Branch Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the branch.', 'error')->autoClose(3000);
            return redirect()->route('branch.index');
        }
    }
}
