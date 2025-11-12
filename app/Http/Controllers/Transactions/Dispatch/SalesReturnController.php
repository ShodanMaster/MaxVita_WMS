<?php

namespace App\Http\Controllers\Transactions\Dispatch;

use App\Helpers\BarcodeGenerator;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Location;
use App\Models\SalesReturn;
use App\Models\Uom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class SalesReturnController extends Controller
{
    public function index(){
        $customers = Customer::get(['id', 'name']);
        $items = Item::where('item_type', 'FG')->get(['id', 'item_code', 'name']);
        $uoms = Uom::wherein('name', ['Bag', 'Box'])->get(['id', 'name']);
        $locations = Location::get(['id', 'name']);
        return view('transactions.dispatch.salesreturn', compact('customers', 'items', 'uoms', 'locations'));
    }

    public function store(Request $request){
        // dd($request->all());

        try{

            $userId = Auth::id();
            $location = Location::find($request->location);
            $branchId = $location->branch_id;
            $prefix = $location->prefix;
            $batchNumber = 'F' . date('ymd');

            $item = Item::find($request->item);

            $totalQuantity = $request->total_quantity;
            $spq = $request->spq;
            $fullBarcodes = $totalQuantity / $spq;
            $remainder = fmod((float)$totalQuantity, (float)$spq);
            $numberOfBarcodes = $request->number_of_barcodes;
            $quantityPerBarcode = $item->item_type == "FG" ? $spq : ($totalQuantity / $numberOfBarcodes);
            $itemPrice = $item->price;
            $baseQuantity = $item->item_type === "RM" ? $totalQuantity / $numberOfBarcodes : $spq;

            DB::beginTransaction();

            $returnNumber = SalesReturn::withoutNumber();

            for ($i = 0; $i < $numberOfBarcodes; $i++) {

                $barcode = BarcodeGenerator::nextNumber($prefix);

                if ($item->tem_type === "FG") {
                    $quantityPerBarcode = ($i == $numberOfBarcodes - 1 && $remainder > 0) ? $remainder : $spq;
                } else {
                    $quantityPerBarcode = ($i == $numberOfBarcodes - 1)
                        ? ($totalQuantity - ($baseQuantity * ($numberOfBarcodes - 1)))
                        : $baseQuantity;
                }

                $salesReturn = SalesReturn::create([
                    'return_number' => $returnNumber,
                    'barcode' => $barcode,
                    'spq' => $spq,
                    'customer_id' => $request->customer,
                    'item_id' => $item->id,
                    'bin_id' => $request->bin,
                    'user_id' => $userId,
                ]);

                $barcodeData[] = [
                    'serial_number' => $barcode,
                    'transaction_id' => $salesReturn->id,
                    'transaction_type' => '4',
                    'branch_id' => $branchId,
                    'location_id' => $request->location,
                    'bin_id' => $request->bin,
                    'item_id' => $item->id,
                    'date_of_manufacture' => $request->date_of_manufacture,
                    'best_before_date' => $request->best_before_date,
                    'batch_number' => $batchNumber,
                    'price' => $itemPrice,
                    // 'total_price' => $totalPrice,
                    'net_weight' => $quantityPerBarcode,
                    'grn_net_weight' => $quantityPerBarcode,
                    'spq_quantity' => $quantityPerBarcode,
                    'grn_spq_quantity' => $quantityPerBarcode,
                    'uom_id' => $request->uom,
                    'status' => '1',
                    'user_id' => $userId,
                    'qc_approval_status' => '0',
                    'created_at' => now()
                ];

                $itemName = $item->name;

                $contents[] = [
                    'transaction_number' => $returnNumber,
                    'barcode' => $barcode,
                    'item_name' => $itemName,
                    'spq_quantity' => $quantityPerBarcode
                ];
            }

            if (!empty($barcodeData)) {
                Barcode::insert($barcodeData);
            }

            DB::commit();
            Alert::toast('Return scan with Number ' . $salesReturn->return_number, 'success')->autoClose(3000);
            if ($request->has('prn')) {
                Session::put('contents', $contents);
                return redirect()->back();
            } else {
                return redirect()->back();
                // return back()->with('print_barcode', $print_barcode);
            }
        } catch(Exception $e){
            db::rollBack();
            dd($e);
        }
    }
}
