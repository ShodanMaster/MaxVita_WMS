<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\SubCategoryExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\SubCategoryImport;
use App\Models\Category;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use NunoMaduro\Collision\Adapters\Phpunit\Subscribers\Subscriber;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    //
    public function index()
    {
        return view('master.subcategory.index');
    }

    public function getSubCategory(Request $request)
    {
        if ($request->ajax()) {

            $subcategories = SubCategory::with('category')->select(['id', 'category_id', 'name', 'sub_category_code','description']);

            return DataTables::of($subcategories)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('sub-category.edit', $row->id);
                    $deleteUrl = route('sub-category.destroy', $row->id);
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
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this sub-category?\')" style="display:inline;">
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
        $categories = Category::all();
        return view('master.subcategory.create', compact('categories'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|unique:sub_categories,name',
            'category_id' => 'required|integer|exists:categories,id',
            'sub_category_code' => 'required|string|unique:sub_categories,sub_category_code',
            'description' => 'required|string'
        ]);

        try {


            SubCategory::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'sub_category_code' => $request->sub_category_code,
                'description' => $request->description
            ]);

            Alert::toast('Sub Category added successfully!', 'success')->autoClose(3000);
            return redirect()->route('sub-category.index');
        } catch (Exception $e) {
            // dd($e);
            Log::error('SubCategory Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the subcategory.', 'error')->autoClose(3000);
            return redirect()->route('sub-category.index');
        }
    }

    public function edit(SubCategory $sub_category)
    {
        try{
            $categories = Category::all();
            return view('master.subcategory.create', compact('sub_category','categories'));
        } catch (Exception $e) {
            // dd($e);
            Log::error('SubCategory Edit Error: ' . $e->getMessage());

            Alert::toast('An error occurred while Fetching the subcategory.', 'error')->autoClose(3000);
            return redirect()->route('sub-category.index');
        }
    }


    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required|string|unique:sub_categories,name,'. $id,
            'category_id' => 'required|integer|exists:categories,id',
            'sub_category_code' => 'required|string|unique:sub_categories,sub_category_code,'. $id,
            'description' => 'required|string'
        ]);
        try {


            SubCategory::where('id', $id)->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'sub_category_code' => $request->sub_category_code,
                'description' => $request->description
            ]);
            Alert::toast('SubCategory modified successfully', 'success')->autoClose(3000);
            return redirect()->route('sub-category.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Subcategory Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the subcategory.', 'error')->autoClose(3000);
            return redirect()->route('sub-category.index');
        }
    }

    public function destroy(string $id)
    {
        try {
            SubCategory::where('id', $id)->delete();
            Alert::toast('Subcategory Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('sub-category.index');
        } catch (Exception $e) {
            Log::error('Subcategory Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the subcategory.', 'error')->autoClose(3000);
            return redirect()->route('sub-category.index');
        }
    }

    public function subCategoryExcelExport()
    {
        try{
            return Excel::download(new SubCategoryExport, 'Sub_Categories.xlsx');
        } catch (Exception $e) {
            Log::error('Subcategory Excel Export Error: ' . $e->getMessage());
            Alert::toast('An error occurred while excel exporting the subcategory.', 'error')->autoClose(3000);
            return redirect()->route('sub-category.index');
        }
    }

    public function subCategoryExcelUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try {
            Excel::import(new SubCategoryImport, $request->file('excel_file'));

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/master_uploads/sub_category_uploads', $fileName, 'public');

            Alert::toast('Subcategory Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('sub-category.index');
        } catch (ValidationException $e) {

            Log::error('sub-category Excel Validation Error: ' . json_encode($e->errors()));
            Alert::toast('Validation failed while importing sub-category Excel.', 'error')->autoClose(3000);
            return redirect()->route('sub-category.index')->withErrors($e->errors());

        } catch (Exception $e) {

            Log::error('sub-category Excel Import Error: ' . $e->getMessage());
            Alert::toast('An unexpected error occurred while importing.', 'error')->autoClose(3000);
            return redirect()->route('sub-category.index');
        }
    }

}
