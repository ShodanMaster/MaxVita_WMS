<?php

namespace App\Http\Controllers\Transactions\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Dispatch;
use App\Models\DispatchSub;
use App\Models\Item;
use App\Models\Location;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class DispatchController extends Controller
{
    public function index(){
        $locations = Location::get(['id', 'name']);
        $customers = Customer::get(['id', 'name']);
        $fgItems = Item::where('item_type', 'FG')->get(['id', 'item_code', 'name']);
        $uoms = Uom::wherein('name', ['Bag', 'Box'])->get(['id', 'name']);
        return view('transactions.dispatch.index', compact('locations', 'customers', 'fgItems', 'uoms'));
    }

    public function store(Request $request){
        // dd($request->all());
        $request->validate([
            'dispatch_type' => 'required|in:sales,transfer',
            'dispatch_date' => 'required|date',
            'dispatch_number' => 'required|string|unique:dispatches,dispatch_number',
            'location_id' => 'nullable|exists:locations,id',
            'customer_id' => 'nullable|exists:customers,id',
            'items_json' => 'required|string',
        ]);

        try{
            DB::beginTransaction();

            if($request->location_id === $request->to_location_id){
                return redirect()->back()->withErrors(['to_location_id' => 'To Location must be different from From Location'])->withInput();
            }

            $location = Location::find($request->location_id, ['id', 'branch_id']);

            $dispatchTo = match($request->dispatch_type){
                'sales' => Customer::find($request->customer_id, ['id', 'name']),
                'transfer' => Location::find($request->to_location_id, ['id', 'name', 'branch_id']),
            };

            $dispatch = new Dispatch([
                'dispatch_number' => $request->dispatch_number ,
                'dispatch_date' => $request->dispatch_date ,
                'from_branch_id' => $location->branch_id ,
                'from_location_id' => $location->id,
                'dispatch_type' => $request->dispatch_type ,
                'user_id' => Auth::id(),
            ]);

            $dispatch->dispatchTo()->associate($dispatchTo);
            $dispatch->save();

            $items = json_decode($request->items_json, true);

            $dispatchSubs = [];
            foreach($items as $item){
                $dispatchSubs[] = [
                    'dispatch_id' => $dispatch->id,
                    'item_id' => $item['item_id'],
                    'uom_id' => $item['uom_id'],
                    'total_quantity' => $item['quantity'],
                ];
            }

            if(!empty($dispatchSubs)){
                DispatchSub::insert($dispatchSubs);
            }

            DB::commit();
            Alert::toast('Dispatch saved with Number ' . $dispatch->dispatch_number, 'success')->autoClose(3000);
            return redirect()->back();
        }catch(Exception $e){
            DB::rollBack();
            dd(vars: $e);
        }
    }

    public function dispatchExcelUpload(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try{

            $data = $this->getExcelData($request->excel_file);
            // dd($data);
            if ($data['error'] !== '') {

                $errors = array_filter(
                    explode('|', rtrim($data['error'], '|')),
                    fn($e) => trim($e) !== ''
                );

                return redirect()->route('dispatch.index')->withErrors($errors);
            }

            $location = Location::find($data['location_id']);

            DB::beginTransaction();
            $dispatchNumber = $data['dispatch_number'];
            $dispatch = new Dispatch([
                'dispatch_number' => $dispatchNumber,
                'dispatch_date' => $current ,
                'from_branch_id' => $location->branch_id,
                'from_location_id' => $location->id,
                'dispatch_type' => $data['dispatch_type'],
                'user_id' => Auth::id(),
            ]);

            $dispatch->dispatchTo()->associate($data['to']);
            $dispatch->save();

            $dispatchSubs = [];

            foreach($data['items'] as $item){
                $dispatchSubs[] = [
                    'dispatch_id' => $dispatch->id,
                    'item_id' => $item['item_id'],
                    'uom_id' => $item['uom_id'],
                    'total_quantity' => $item['total_quantity'],
                ];
            }
            // dd($dispatchSubs);

            if(!empty($dispatchSubs)){
                DispatchSub::insert($dispatchSubs);
            }

            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/transaction_uploads/dispatch_uploads', $fileName, 'public');
            DB::commit();
            Alert::toast('Dispatch saved with Number ' . $dispatchNumber, 'success')->autoClose(3000);
            return redirect()->back();

        }catch(Exception $e){
            DB::rollBack();
            dd($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload Excel file. ' . $e->getMessage()
            ], 500);
        }
    }

    private function getExcelData($file){
        $sheet = Excel::toArray([], $file);
        $rows = $sheet[0] ?? [];
        // dd($rows);
        $data = [
            'error' => '',
            'dispatch_type' => '',
            'dispatch_number' => '',
            'location_id' => '',
            'to' => '',
            'items' => [],
        ];

        $dispatch = [
            'dispatch_type' => strtolower($rows[1][1] ?? ''),
            'dispatch_number' => $rows[0][1] ?? '',
            'from_location' => $rows[0][3] ?? '',
            'to' => $rows[1][3] ?? '',
        ];

        if(!in_array($dispatch['dispatch_type'], ['sales', 'transfer'])){
            $data['error'] .= "Dispatch Type must be either 'Sales' or 'Transfer'|";
            return $data;
        }

        foreach(['dispatch_number', 'from_location', 'dispatch_type', 'to'] as $field){
            if(empty($dispatch[$field])){
                $label = ucwords(str_replace('_', ' ', $field));
                $data['error'] .= $label . " is required|";
                return $data;
            }
        }

        $data['dispatch_type'] = $dispatch['dispatch_type'];

        $dispatchExists = Dispatch::where('dispatch_number', $dispatch['dispatch_number'])->exists();
        if($dispatchExists){
            $data['error'] .= "Dispatch Number Already Exists|";
            return $data;
        }else{
            $data['dispatch_number'] = $dispatch['dispatch_number'];
        }

        $fromLocation = Location::where('name', $dispatch['from_location'])->first();
        if (!$fromLocation) {
            $data['error'] .= "From Location '" . $dispatch['from_location'] . "' not found|";
            return $data;
        }

        $data['location_id'] = $fromLocation->id;

        if($dispatch['dispatch_type'] === 'sales'){
            $customer = Customer::where('name', $dispatch['to'])->first();
            if(!$customer){
                $data['error'] .= "Customer '" . $dispatch['to'] . "' not found|";
                return $data;
            }
            $data['to'] = $customer;
        }elseif($dispatch['dispatch_type'] === 'transfer'){
            $location = Location::where('name', $dispatch['to'])->first();
            if(!$location){
                $data['error'] .= "To Location '" . $dispatch['to'] . "' not found|";
                return $data;
            }
            $data['to'] = $location;
        }

        for($i = 4; $i < count($rows); $i++){
            $row = $rows[$i];
            // dd($row);
            if(empty($row[0]) && empty($row[1])){
                continue;
            }

            $itemCode = $row[0] ?? '';
            $quantity = $row[1] ?? 0;
            $uomName = $row[2] ?? '';

            if(empty($itemCode) || empty($uomName) || empty($quantity)){
                $data['error'] .= "Item, UOM, and Quantity are required in row " . ($i + 1) . "|";
                return $data;
            }

            $item = Item::where('name', $itemCode)->first();
            if(!$item){
                $data['error'] .= "Item with name '" . $itemCode . "' not found in row " . ($i + 1) . "|";
                return $data;
            }

            $uom = Uom::where('name', $uomName)->first();
            if(!$uom){
                $data['error'] .= "UOM '" . $uomName . "' not found in row " . ($i + 1) . "|";
                return $data;
            }

            $allowedUoms = ['bag', 'box'];
            if (!in_array(strtolower($uom->name), $allowedUoms)) {
                $data['error'] .= "UOM '" . $uom->name . "' not allowed in row " . ($i + 1) . ". Only 'Bag' or 'Box' are permitted.|";
                return $data;
            }

            $isItemInLocation = $fromLocation->items()->where('items.id', $item->id)->exists();
            if (!$isItemInLocation) {
                $data['error'] .= "Item '" . $item->name . "' not available in location '" . $fromLocation->name . "' (row " . ($i + 1) . ")|";
                return $data;
            }

            $data['items'][] = [
                'item_id' => $item->id,
                'uom_id' => $uom->id,
                'total_quantity' => (int)$quantity,
            ];
        }


        return $data;
    }
}
