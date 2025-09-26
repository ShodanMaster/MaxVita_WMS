<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\CategoryExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index()
    {

        return view('master.category.index');
    }

    public function getCategories(Request $request)
    {
        if ($request->ajax()) {

            $categories = Category::select(['id', 'name', 'erp_code', 'description']);

            return DataTables::of($categories)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    $editUrl = route('category.edit', $row->id);
                    $deleteUrl = route('category.destroy', $row->id);
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
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this category?\')" style="display:inline;">
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
        return view('master.category.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'erp_code' => 'required|string|unique:categories,erp_code',
            'description' => 'required|string'
        ]);

        try {

            Category::create([
                'name' => $request->name,
                'erp_code' => $request->erp_code,
                'description' => $request->description
            ]);

            Alert::toast('Category added successfully!', 'success')->autoClose(3000);
            return redirect()->route('category.index');
        } catch (\Exception $e) {
            //  dd($e);
            Log::error('Category Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the category.', 'error')->autoClose(3000);
            return redirect()->route('category.index');
        }
    }

    public function edit(Category $category)
    {
        return view('master.category.create', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $id,
            'erp_code' => 'required|string|unique:categories,erp_code,' . $id,
            'description' => 'required|string'
        ]);

        try {


            Category::where('id', $id)->update([
                'name' => $request->name,
                'erp_code' => $request->erp_code,
                'description' => $request->description
            ]);
            Alert::toast('Category modified successfully', 'success')->autoClose(3000);
            return redirect()->route('category.index');
        } catch (\Exception $e) {
            dd($e);
            Log::error('Category Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the category.', 'error')->autoClose(3000);
            return redirect()->route('category.index');
        }
    }


    public function destroy(string $id)
    {
        try {
            Category::where('id', $id)->delete();
            Alert::toast('Category Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('category.index');
        } catch (\Exception $e) {
            Log::error('Category Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the category.', 'error')->autoClose(3000);
            return redirect()->route('category.index');
        }
    }

    public function CategoryExcelExport()
    {
        return Excel::download(new CategoryExport,'Category Master.xlsx');
    }
}

