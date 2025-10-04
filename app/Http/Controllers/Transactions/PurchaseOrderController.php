<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderCancel;
use App\Models\PurchaseOrderSub;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class PurchaseOrderController extends Controller
{
    public function index(){
        $purchaseNumber = PurchaseOrder::nextNumber();
        $items = Item::where('item_type', 'RM')->get(['id', 'item_code', 'name']);
        $vendors = Vendor::get(['id', 'name']);
        return view('transactions.purchaseorder.index', compact('purchaseNumber', 'items', 'vendors'));
    }

    public function store(Request $request){
        try{

            DB::beginTransaction();
            $purchaseNumber = PurchaseOrder::nextNumber();
            $purchaseOrder = PurchaseOrder::create([
                'branch_id' => auth()->user()->branch_id,
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $request->purchase_date,
                'vendor_id' => $request->vendor
            ]);

            foreach($request->items as $item){
                PurchaseOrderSub::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['total_quantity'],
                ]);
            }
            DB::commit();
            Alert::toast('Purchase Order saved with Number ' . $purchaseNumber, 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Purchase Store Error: ' . $e->getMessage());

            Alert::toast('An error occurred while adding the purchase.', 'error')->autoClose(3000);
            return redirect()->route('purchase-order.index');
        }
    }

    public function purchaseOrderExcelUpload(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try{
            DB::beginTransaction();

            $data = $this->getExcelData($request->excel_file);

            if ($data['error'] !== '') {

                $errors = array_filter(
                    explode('|', rtrim($data['error'], '|')),
                    fn($e) => trim($e) !== ''
                );

                return redirect()->route('purchase-order.index')->withErrors($errors);
            }

            $purchaseNumber = PurchaseOrder::nextNumber();
            $purchaseOrder = PurchaseOrder::create([
                'branch_id' => auth()->user()->branch_id,
                'purchase_number' => $purchaseNumber,
                'purchase_date' => today(),
                'vendor_id' => $data['vendor_id'],
            ]);

            foreach($data['items'] as $item){
                PurchaseOrderSub::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $request->file('excel_file')->storeAs('excel_uploads/transaction_uploads/purchaseorder_uploads', $fileName, 'public');
            DB::commit();
            Alert::toast('Purchase Order saved with Number ' . $purchaseNumber, 'success')->autoClose(3000);
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Purchase Order Excel Upload Error: ' . $e->getMessage());
            Alert::toast('An error occurred while purchase order excel upload.', 'error')->autoClose(3000);
            return redirect()->route('purchase-order.index');
        }
    }

    private function getExcelData($file){
        $sheet = Excel::toArray([], $file);
        $rows = $sheet[0] ?? [];
        $data = [
            'error' => '',
            'vendor_id' => '',
            'items' => [],
        ];

        $vendor = [
            'vendor_name' => $rows[0][1],
            'vendor_code' => $rows[0][3],
        ];

        foreach(['vendor_name', 'vendor_code'] as $key){
            if(empty($vendor[$key])){
                $data['error'] .= strtolower($key) . " is required|";
            }
        }

        if ($data['error'] !== '') return $data;

        $vendor = Vendor::where('name', $vendor['vendor_name'])->where('vendor_code', $vendor['vendor_code'])->first();
        if(!$vendor){
            $data['error'] .= "Vendor does not exist|";
        } else {
            $data['vendor_id'] = $vendor->id;
        }

        if ($data['error'] !== '') return $data;

        for($i = 2; $i < count($rows); $i++){
            $row = $rows[$i];

            if (!array_filter($row)) {
                continue;
            }

            $item = [
                'item_name' => $row[0] ?? '',
                'item_code' => $row[1] ?? '',
                'quantity' => $row[2] ?? '',
            ];

            foreach(['item_name', 'item_code', 'quantity'] as $key){
                if (empty($item[$key])) {
                    $data['error'] .= 'Row ' . ($i + 1) . " : $key required|";
                }
            }

            if ($data['error'] !== '') return $data;

            $item = Item::where('name', $item['item_name'])
                ->where('item_code', $item['item_code'])
                ->first();

            if(!$item){
                $data['error'] .= 'Row ' . ($i + 1) . " : Item does not exist|";
                return $data;
            }

            $data['items'][] = ['item_id' => $item->id, 'quantity' => $row[2]];
        }

        return $data;
    }

    public function purchaseOrderCancel(){

        $orders = PurchaseOrder::whereNot('status', 2)->get(['purchase_number']);
        return view('transactions.purchaseorder.cancel', compact('orders'));
    }

    public function cancelPurchaseOrder(Request $request){

        $user_id = Auth::user()->id;
        $purchaseOrderNumber = $request->purchaseOrderNumber;

        // $grn = Grn::where('po_no', '=', $id)->first();

        // if ($grn) {

        //     Alert::toast('The Purchase Order has initiated for GRN entry', 'error')->autoClose(3000);
        //     return redirect()->route('purchase_order_cancel');
        // }

        $purchaseOrder = PurchaseOrder::where('purchase_number', $purchaseOrderNumber)->first(['id']);

        $purchaseOrder->update([
            'status' => 2
        ]);

        PurchaseOrderCancel::create([
            'purchase_order_id' => $purchaseOrder->id,
            'user_id' => $user_id
        ]);


        Alert::toast('Purchase Order Canceled Successfully', 'success')->autoClose(3000);
        return redirect()->route('purchase-order-cancel');
    }

}
