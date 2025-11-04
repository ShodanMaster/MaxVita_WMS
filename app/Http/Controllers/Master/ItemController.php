<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\ItemExport;
use App\Http\Controllers\Controller;
use App\Imports\Master\ItemImport;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Locale;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function index(){
        return view('master.item.index');
    }

    public function getItems(Request $request){
        if($request->ajax()){
            $items = Item::with('category:id,name', 'uom:id,name', 'locations:name')->select(['id', 'category_id', 'uom_id', 'item_code', 'name', 'in_stock', 'gst_rate', 'price', 'single_packet_weight', 'sku_code', 'item_type', 'spq_quantity']);

            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('category', function ($row) {
                    return $row->category->name ?? '-';
                })->addColumn('uom', function ($row) {
                    return $row->uom->name ?? '-';
                })->addColumn('locations', function ($row) {
                    $locationNames = $row->locations->pluck('name')->toArray();
                    return implode(', ', $locationNames);
                })->filterColumn('locations', function($query, $keyword) {
                    $query->whereHas('locations', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('item.edit', $row->id);
                    $deleteUrl = route('item.destroy', $row->id);
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
                            <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Are you sure, You want to delete this item?\')" style="display:inline;">
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
        $categories = Category::select('id', 'name')->get();
        $uoms = Uom::select('id', 'name')->get();
        $locations = Location::get(['id', 'name']);
        return view('master.item.create', compact('categories', 'uoms', 'locations'));
    }

    public function store(Request $request){
        // dd($request->all());
        $request->validate([
            'item_code' => 'required|string|unique:items,item_code',
            'name' => 'required|string|unique:items,name',
            'in_stock' => 'nullable|integer',
            'category_id' => 'required|integer|exists:categories,id',
            'uom_id' => 'required|integer|exists:uoms,id',
            'item_type' => 'required|in:FG,RM',
        ]);

        try{

            $spqQuantity = 1;

            if ($request->item_type === 'FG') {
                $spqQuantity = $request->spq_quantity;
            }

            $data = [
                'category_id' => $request->category_id,
                'uom_id' => $request->uom_id,
                'name' => $request->name,
                'item_code' => $request->item_code,
                'in_stock' => $request->in_stock,
                'item_type' => $request->item_type,
                'spq_quantity' => $spqQuantity,
            ];

            if ($request->item_type === 'FG') {
                $data = array_merge($data, [
                    'price' => $request->price,
                    'single_packet_weight' => $request->single_packet_weight,
                    'sku_code' => $request->sku_code,
                    'gst_rate' => $request->gst_rate,
                ]);
            }

            $item = Item::create($data);

            $locationsArray = explode(',', $request->selectedLocations);

            $item->locations()->sync($locationsArray);

            Alert::toast('Item added successfully!', 'success')->autoClose(3000);
            return redirect()->route('item.index');

        } catch (Exception $e) {
            dd($e);
            Log::error('Item Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the item.', 'error')->autoClose(3000);
            return redirect()->route('item.index');
        }
    }

    public function edit(Item $item){
        try{

            $categories = Category::select('id', 'name')->get();
            $uoms = Uom::select('id', 'name')->get();
            $locations = Location::get(['id', 'name']);
            $locationIds = $item->locations->pluck('pivot.location_id')->toArray();
            return view('master.item.create', compact('item', 'categories', 'uoms', 'locations', 'locationIds'));
        } catch (Exception $e) {
            dd($e);
            Log::error('item Fetch Error: ' . $e->getMessage());

            Alert::toast('An error occurred while fetching the item.', 'error')->autoClose(3000);
            return redirect()->route('item.index');
        }
    }

    public function update(Request $request, string $id){
        $request->validate([
            'item_code' => 'required|string|unique:items,item_code,' . $id,
            'name' => 'required|string|unique:items,name,' . $id,
            'in_stock' => 'nullable|integer',
            'category_id' => 'required|integer|exists:categories,id',
            'uom_id' => 'required|integer|exists:uoms,id',
            'item_type' => 'required|in:FG,RM',
            'price' => 'required',
        ]);
        try{

            $spqQuantity = 1;

            if ($request->item_type === 'FG') {
                $spqQuantity = $request->spq_quantity;
            }

            $data = [
                'category_id' => $request->category_id,
                'uom_id' => $request->uom_id,
                'name' => $request->name,
                'item_code' => $request->item_code,
                'in_stock' => $request->in_stock,
                'item_type' => $request->item_type,
                'price' => $request->price,
                'spq_quantity' => $spqQuantity,
            ];

            if ($request->item_type === 'FG') {

                $data = array_merge($data, [
                    'sku_code' => $request->sku_code,
                    'gst_rate' => $request->gst_rate,
                ]);
            } else {

                $data = array_merge($data, [
                    'sku_code' => null,
                    'gst_rate' => null,
                ]);
            }

            $item = Item::findOrFail($id);
            $item->update($data);

            $locationsArray = explode(',', $request->selectedLocations);
            $item->locations()->detach();
            $item->locations()->sync($locationsArray);

            Alert::toast('Item modified successfully', 'success')->autoClose(3000);
            return redirect()->route('item.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Item Update Error: ' . $e->getMessage());

            Alert::toast('An error occurred while updating the item.', 'error')->autoClose(3000);
            return redirect()->route('item.index');
        }
    }

    public function destroy(string $id){
        try{
            Item::where('id', $id)->delete();
            Alert::toast('Item Deleted Successfully', 'success')->autoClose(3000);
            return redirect()->route('item.index');
        } catch(Exception $e){
            dd($e);
            Log::error('Item Delete Error: ' . $e->getMessage());
            Alert::toast('An error occurred while deleting the item.', 'error')->autoClose(3000);
            return redirect()->route('item.index');
        }
    }

    public function itemExcelExport(){
        try{
            return Excel::download(new ItemExport, 'items.xlsx');
        } catch(Exception $e){
            Log::error('Item Excel Exporting Error: ' . $e->getMessage());
            Alert::toast('An error occurred while excel exporting the item.', 'error')->autoClose(3000);
            return redirect()->route('item.index');
        }
    }

    public function itemExcelUpload(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');


        try{
            Excel::import(new ItemImport, $request->file('excel_file'));

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/master_uploads/item_uploads', $fileName, 'public');


            Alert::toast('Item Excel file imported successfully.', 'success')->autoClose(3000);
            return redirect()->route('item.index');

        } catch(Exception $e){
            dd($e);
            Log::error('Item Excel Import Error: ' . $e->getMessage());
            Alert::toast('An error occurred while item excel importing', 'error')->autoClose(3000);
            $errors = $e->validator->errors();
            return redirect()->route('item.index')->withErrors($errors);
        }
    }
}
