<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get("search");
        $branch = Branch::orderBy('name', 'ASC');

        if ($branch) {
            $branch = new Branch;

            if ($search != '') {
                $branch = $branch->where('name', 'LIKE', "%$search%");
            }
        } else {
            $branch = Branch::where('id', '=', $branch->id);

            if ($search != '') {
                $branch = $branch->where('name', 'LIKE', "%$search%");
            }
        }

        $result = $branch->paginate(10);
        $i = 0;
        return view('master.branch.index')->withMaster($result)->withI($i)->withSearch($search)->withPerpage(10);
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
