<?php

namespace App\Http\Controllers\Transactions\Stock;

use App\Http\Controllers\Controller;
use App\Models\Bin;
use App\Models\Item;
use App\Models\Location;
use App\Models\OpeningStock;
use App\Models\OpeningStockSub;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OpeningStockController extends Controller
{
    public function index(){
        return view('transactions.stock.index');
    }

    public function store(Request $request){

    }

    public function excelUpload(Request $request){

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $current = date('Y-m-d_H-i-s');

        try {
            $fileName = $current . '_' . $request->file('excel_file')->getClientOriginalName();

            $path = $request->file('excel_file')->storeAs(
                'excel_uploads/transaction_uploads/opening_stock_uploads',
                $fileName,
                'public'
            );

            DB::beginTransaction();

            $user = Auth::user();

            $openingStock = OpeningStock::create([
                'opening_number' => OpeningStock::openingNumber(),
                'file_path' => $path,
                'user_id' => $user->id,
                'location_id' => $user->location_id,
                'branch_id' => $user->branch_id,
            ]);

            $sheets = Excel::toArray([], $request->file('excel_file'));
            $rows = $sheets[0];

            $errors = '';

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                // dd($row);
                $item_code           = trim($row[0]);
                $item_name           = trim($row[1]);
                $date_of_manufacture = $row[2];
                $best_before         = $row[3];
                $location_name       = trim($row[4]);
                $bin_code            = trim($row[5]);
                $total_quantity      = $row[6];
                $number_of_barcodes  = $row[7];
                $batch_number        = trim($row[8]);

                $item = Item::where('item_code', $item_code)
                            ->where('name', $item_name)
                            ->first();

                if (!$item) {
                    $errors .= "Row " . ($i + 1) . ": Item '{$item_code} - {$item_name}' not found|";
                    continue;
                }

                $location = Location::where('name', $location_name)->first();
                if (!$location) {
                    $errors .= "Row " . ($i + 1) . ": Location '{$location_name}' not found|";
                    continue;
                }

                if (!$item->locations->contains($location->id)) {
                    $errors .= "Row " . ($i + 1) . ": Item does not belong to location '{$location_name}'|";
                    continue;
                }

                $bin = Bin::where('bin_code', $bin_code)->first();
                if (!$bin) {
                    $errors .= "Row " . ($i + 1) . ": Bin '{$bin_code}' not found|";
                    continue;
                }

                if ($location->id != $bin->location_id) {
                    $errors .= "Row " . ($i + 1) . ": Bin '{$bin_code}' does not belong to location '{$location_name}'|";
                    continue;
                }

                if ($item->item_type != $bin->bin_type) {
                    $errors .= "Row " . ($i + 1) . ": Item and Bin type mismatch|";
                    continue;
                }

                OpeningStockSub::create([
                    'opening_stock_id' => $openingStock->id,
                    'item_id' => $item->id,
                    'manufacture_date' => Date::excelToDateTimeObject($date_of_manufacture)->format('Y-m-d'),
                    'best_before'      => Date::excelToDateTimeObject($best_before)->format('Y-m-d'),
                    'bin_id'           => $bin->id,
                    'total_quantity'   => (float) $total_quantity,
                    'number_of_barcodes' => (int) $number_of_barcodes,
                    'batch'            => $batch_number,
                ]);
            }

            // Handle all accumulated errors after loop
            if ($errors != '') {
                DB::rollBack();
                $errorList = array_filter(explode('|', trim($errors, '|')));
                return back()->withErrors($errorList);
            }

            DB::commit();

            return redirect()->route('opening-stock.index')
                            ->with('success', 'Opening Stock uploaded successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['Excel upload failed: ' . $e->getMessage()]);
        }
    }

}
