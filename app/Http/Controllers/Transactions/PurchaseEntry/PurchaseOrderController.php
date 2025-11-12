<?php

namespace App\Http\Controllers\Transactions\PurchaseEntry;

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
        // $purchaseNumber = PurchaseOrder::nextNumber();
        $items = Item::where('item_type', 'RM')->get(['id', 'item_code', 'name']);
        $vendors = Vendor::get(['id', 'name']);
        return view('transactions.purchaseorder.index', compact( 'items', 'vendors'));
    }

    public function store(Request $request){
        $request->validate([
            "purchase_number" => 'required|unique:purchase_orders,purchase_number',
            "purchase_date" => 'required|date',
            "vendor" => 'required|exists:vendors,id',
        ]);

        try{

            DB::beginTransaction();
            // $purchaseNumber = PurchaseOrder::nextNumber();
            $purchaseNumber = $request->purchase_number;
            $purchaseOrder = PurchaseOrder::create([
                'branch_id' => Auth::user()->branch_id,
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $request->purchase_date,
                'vendor_id' => $request->vendor
            ]);

            $purchaseOrderSubData = [];
            foreach ($request->items as $item) {
                $purchaseOrderSubData[] = [
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['total_quantity'],
                    'created_at' => now()
                ];
            }

            if (!empty($purchaseOrderSubData)) {
                PurchaseOrderSub::insert($purchaseOrderSubData);
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
            // dd($data);
            if ($data['error'] !== '') {

                $errors = array_filter(
                    explode('|', rtrim($data['error'], '|')),
                    fn($e) => trim($e) !== ''
                );

                return redirect()->route('purchase-order.index')->withErrors($errors);
            }

            // $purchaseNumber = PurchaseOrder::nextNumber();
            $purchaseNumber = $data['po_number'];
            $purchaseOrder = PurchaseOrder::create([
                'branch_id' => Auth::user()->branch_id,
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
            'po_number' => '',
            'vendor_id' => '',
            'items' => [],
        ];

        $poNumber = $rows[0][1];
        if(empty($poNumber)){
            $data['error'] .= "PO Number is required|";
            return $data;
        }

        $purchaseOrder = PurchaseOrder::where('purchase_number', $poNumber)->first();

        if($purchaseOrder){
            $data['error'] .= "PO Number already exists!|";
            return $data;
        }else{
            $data['po_number'] = $poNumber;
        }

        $vendor = [
            'vendor_name' => $rows[1][1],
            'vendor_code' => $rows[2][1],
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

        for($i = 4; $i < count($rows); $i++){
            $row = $rows[$i];
            // dd($row);
            if (!array_filter($row)) {
                continue;
            }

            $item = [
                'item_name' => trim($row[0]) ?? '',
                'item_code' => trim($row[1]) ?? '',
                'quantity' => trim($row[2]) ?? '',
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

        $orders = PurchaseOrder::where('status', 0)->get(['id', 'purchase_number']);
        return view('transactions.purchaseorder.cancel', compact('orders'));
    }

    public function cancelPurchaseOrder(Request $request){

        $user_id = Auth::user()->id;

        $purchaseOrder = PurchaseOrder::find($request->purchaseOrderNumber);

        $purchaseOrder->update([
            'status' => 2
        ]);

        PurchaseOrderCancel::create([
            'purchase_order_id' => $purchaseOrder->id,
            'user_id' => $user_id
        ]);

        Alert::toast('Purchase Order Cancelled Successfully', 'success')->autoClose(3000);
        return redirect()->route('purchase-order-cancel');
    }

}
