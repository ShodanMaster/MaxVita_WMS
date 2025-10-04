<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\CustomerExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\CustomerImport;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        return view('master.customer.index');
    }

    public function getCustomers(Request $request)
    {
        if ($request->ajax()) {

            $customers = Customer::select(['id', 'name', 'customer_code', 'shipping_code', 'customer_address', 'zip_code', 'gst_number']);

            return DataTables::of($customers)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    $editUrl = route('customer.edit', $row->id);
                    $deleteUrl = route('customer.destroy', $row->id);
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
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this customer?\')" style="display:inline;">
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
        return view('master.customer.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'customer_code' => 'required|string',
            'shipping_code' => 'required|string',
            'customer_address' => 'required|string',
            'zip_code' => 'required|integer',
            'gst_number' => 'required|string'
        ]);

        try {
            Customer::create([
                'name' => $request->name,
                'customer_code' => $request->customer_code,
                'shipping_code' => $request->shipping_code,
                'customer_address' => $request->customer_address,
                'zip_code' => $request->zip_code,
                'gst_number' => $request->gst_number
            ]);

            Alert::toast('Customer added successfully!', 'success')->autoClose(3000);
            return redirect()->route('customer.index');
        } catch (Exception $e) {
            // dd($e);
            Log::error('Customer Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the customer.', 'error')->autoClose(3000);
            return redirect()->route('customer.index');
        }
    }

    public function edit(Customer $customer)
    {
        return view('master.customer.create', compact('customer'));
    }

    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required|string',
            'customer_code' => 'required|string',
            'shipping_code' => 'required|string',
            'customer_address' => 'required|string',
            'zip_code' => 'required|integer',
            'gst_number' => 'required|string'
        ]);
        try {


            Customer::where('id', $id)->update([
                'name' => $request->name,
                'customer_code' => $request->customer_code,
                'shipping_code' => $request->shipping_code,
                'customer_address' => $request->customer_address,
                'zip_code' => $request->zip_code,
                'gst_number' => $request->gst_number
            ]);
            Alert::toast('Customer modified successfully', 'success')->autoClose(3000);
            return redirect()->route('customer.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Customer Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the customer.', 'error')->autoClose(3000);
            return redirect()->route('customer.index');
        }


    }


    public function destroy(string $id)
    {
        try {
            Customer::where('id', $id)->delete();
            Alert::toast('Customer Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('customer.index');
        } catch (Exception $e) {
            Log::error('Customer Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the customer.', 'error')->autoClose(3000);
            return redirect()->route('customer.index');
        }
    }

    public function CustomerExcelExport()
    {
        try{
            return Excel::download(new CustomerExport, 'Customer_Master.xlsx');
        } catch (Exception $e) {
            Log::error('Customer Excel Export Error: ' . $e->getMessage());
            Alert::toast('An error occurred while excel exporting the customer.', 'error')->autoClose(3000);
            return redirect()->route('customer.index');
        }
    }

    public function customerExcelUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try {
            Excel::import(new CustomerImport,$request->file('excel_file'));

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/master_uploads/customer_uploads', $fileName, 'public');

            Alert::toast('Customer Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('customer.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Customer Excel Import Error: ' . $e->getMessage());
            Alert::toast('An error occurred while customer excel importing.', 'error')->autoClose(3000);
            return redirect()->route('customer.index');
        }
    }
}
