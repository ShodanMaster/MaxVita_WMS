<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\UomExport;
use App\Http\Controllers\Controller;
use App\Models\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this uom?\')" style="display:inline;">
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



    public function create()
    {
        $uoms = Uom::all();
        return view('master.uom.create', compact('uoms'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|unique:uoms,name',
            'uom_code' => 'required|string'
        ]);
       
        try {

         Uom::create([
                'name' => $request->name,
                'uom_code' => $request->uom_code
            ]);
           
            Alert::toast('Uom added successfully!', 'success')->autoClose(3000);
            return redirect()->route('uom.index');
        } catch (\Exception $e) {
             dd($e);
            Log::error('Uom Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the uom.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }

    public function edit(Uom $uom)
    {

        return view('master.uom.create', compact('uom'));
    }

    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required|string|unique:uoms,name,' . $id,
            'uom_code' => 'required|string'
        ]);
        try {


            Uom::where('id', $id)->update([
                'name' => $request->name,
                'uom_code' => $request->uom_code
            ]);
            Alert::toast('Uom modified successfully', 'success')->autoClose(3000);
            return redirect()->route('uom.index');
        } catch (\Exception $e) {
            dd($e);
            Log::error('Uom Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the uom.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }


    public function destroy(string $id)
    {
        try {
            Uom::where('id', $id)->delete();
            Alert::toast('Uom Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('uom.index');
        } catch (\Exception $e) {
            Log::error('Uom Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the uom.', 'error')->autoClose(3000);
            return redirect()->route('uom.index');
        }
    }

    public function UomExcelExport()
    {

        return Excel::download(new UomExport, 'Uom Master.xlsx');
    }
}
