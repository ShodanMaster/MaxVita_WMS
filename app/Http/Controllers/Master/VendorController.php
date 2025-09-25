<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\VendorExport;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
class VendorController extends Controller
{
    public function index()
    {
        return view('master.vendor.index');
    }

    public function getVendors(Request $request)
    {
        if ($request->ajax()) {

            $vendors = Vendor::select(['id', 'name', 'vendor_code', 'location', 'address', 'city', 'state', 'zip_code']);

            return DataTables::of($vendors)
                ->addIndexColumn()
                
                ->addColumn('action', function ($row) {
                    $editUrl = route('vendr.edit', $row->id);
                    $deleteUrl = route('vendr.destroy', $row->id);
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
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this vendor?\')" style="display:inline;">
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
        return view('master.vendor.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|unique:vendors,name',
            'vendor_code' => 'required|string',
            'location' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string'
        ]);

        try {
           

            Vendor::create([
                'name' => $request->name,
                'vendor_code' => $request->vendor_code,
                'location' => $request->location,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code
            ]);

            Alert::toast('Vendor added successfully!', 'success')->autoClose(3000);
            return redirect()->route('vendr.index');
        } catch (\Exception $e) {
            // dd($e);
            Log::error('Vendor Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the vendor.', 'error')->autoClose(3000);
            return redirect()->route('vendr.index');
        }
    }

    public function edit(Vendor $vendr)
    {
        return view('master.vendor.create', compact('vendr'));
    }

    public function update(Request $request, string $id)
    {


        $request->validate([
            'name' => 'required|string|unique:vendors,name,' . $id,
            'vendor_code' => 'required|string',
            'location' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string'
        ]);

        try {

            Vendor::where('id', $id)->update([
                'name' => $request->name,
                'vendor_code' => $request->vendor_code,
                'location' => $request->location,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code
            ]);
            Alert::toast('Vendor modified successfully', 'success')->autoClose(3000);
            return redirect()->route('vendr.index');
        } catch (\Exception $e) {
            dd($e);
            Log::error('vendor Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the vendor.', 'error')->autoClose(3000);
            return redirect()->route('vendr.index');
        }
    }

    public function destroy(string $id)
    {
        try {
            Vendor::where('id', $id)->delete();
            Alert::toast('Vendor Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('vendr.index');
        } catch (\Exception $e) {
            Log::error('Vendor Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the vendor.', 'error')->autoClose(3000);
            return redirect()->route('vendr.index');
        }
    }

    public function VendorExcelExport()
    {

        return Excel::download(new VendorExport, 'VendorMaster.xlsx');
    }
}
