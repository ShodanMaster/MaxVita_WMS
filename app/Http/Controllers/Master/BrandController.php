<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\BrandExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\BrandImport;
use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function index(){
        return view('master.brand.index');
    }

    public function getBrands(Request $request){
        if ($request->ajax()) {

            $brands = Brand::select(['id', 'name']);

            return DataTables::of($brands)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('brand.edit', $row->id);
                    $deleteUrl = route('brand.destroy', $row->id);
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
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this Brand?\')" style="display:inline;">
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

    public function create(){
        return view('master.brand.create');
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required|max:50|unique:brands,name',
        ]);

        try{
            Brand::create([
                'name' => $request->name,
            ]);

            Alert::toast('Brand Added Successfully', 'success')->autoClose(3000);
            return redirect()->route('brand.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Brand Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the brand.', 'error')->autoClose(3000);
            return redirect()->route('brand.index');
        }
    }

    public function edit(Brand $brand){
        try{
            return view('master.brand.create', compact('brand'));
        } catch (Exception $e) {
            dd($e);
            Log::error('Brand Fetch Error: ' . $e->getMessage());

            Alert::toast('An error occurred while fetching the brand.', 'error')->autoClose(3000);
            return redirect()->route('brand.index');
        }
    }

    public function update(Request $request, Brand $brand){
        $request->validate([
            'name' => 'required|max:50|unique:brands,name,'.$brand->id,
        ]);

        try{
            $brand->update([
                'name' => $request->name,
            ]);

            Alert::toast('Brand Updated Successfully', 'success')->autoClose(3000);
            return redirect()->route('brand.index');
        } catch (Exception $e) {
            Log::error('Brand Update Error: ' . $e->getMessage());

            Alert::toast('An error occurred while updating the brand.', 'error')->autoClose(3000);
            return redirect()->route('brand.index');
        }
    }

    public function destroy(Brand $brand){
        try{
            $brand->delete();

            Alert::toast('Brand Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('brand.index');
        } catch (Exception $e) {
            Log::error('Brand Delete Error: ' . $e->getMessage());

            Alert::toast('An error occurred while deleting the brand.', 'error')->autoClose(3000);
            return redirect()->route('brand.index');
        }
    }

    public function brandExcelExport(){
        try {
            return Excel::download(new BrandExport, 'brands.xlsx');
        } catch (Exception $e) {
            Log::error('Brand Excel Export Error: ' . $e->getMessage());

            Alert::toast('An error occurred while exporting brands to Excel.', 'error')->autoClose(3000);
            return redirect()->route('brand.index');
        }
    }

    public function brandExcelUpload(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);
        $current = date('Y-m-d_H-i-s');
        try{
            Excel::import(new BrandImport, $request->file('excel_file'));

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/master_uploads/brand_uploads', $fileName, 'public');

            Alert::toast('Brand Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('brand.index');

        } catch (ValidationException $e) {

            Log::error(message: 'Brand Excel Validation Error: ' . json_encode($e->errors()));
            Alert::toast('Validation failed while importing brand Excel.', 'error')->autoClose(3000);
            return redirect()->route('brand.index')->withErrors($e->errors());

        } catch (Exception $e) {

            Log::error('Brand Excel Import Error: ' . $e->getMessage());
            Alert::toast('An unexpected error occurred while importing.', 'error')->autoClose(3000);
            return redirect()->route('brand.index');
        }
    }
}
