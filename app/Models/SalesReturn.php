<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalesReturn extends Model
{
    protected $fillable = [
        'return_number',
        'barcode',
        'spq',
        'customer_id',
        'dispatch_id',
        'item_id',
        'bin_id',
        'user_id',
    ];


    public static function withNumber(){
        $pre = 'R' . date("ymd");
        $len = strlen($pre);

        $withNumber = DB::select("
            SELECT CONCAT('$pre', LPAD(max_return_number + 1, GREATEST(3, LENGTH(max_return_number + 1)), '0')) return_number
            FROM (
                SELECT IFNULL(MAX(SUBSTRING(`return_number`, $len + 1)), '0') max_return_number
                FROM sales_returns
                WHERE return_number LIKE '$pre%'
            ) p2
        ");

        return $withNumber[0]->return_number;
    }

    public static function withoutNumber()
    {
        $pre = 'RW' . date("ymd");
        $len = strlen($pre);

        $withNumber = DB::select("
            SELECT CONCAT('$pre', LPAD(IFNULL(MAX(CAST(SUBSTRING(return_number, $len + 1) AS UNSIGNED)) + 1, 1), 3, '0')) AS return_number
            FROM (
                SELECT DISTINCT return_number
                FROM sales_returns
                WHERE return_number LIKE '$pre%'
            ) AS distinct_returns
        ");

        return $withNumber[0]->return_number ?? $pre . str_pad(1, 5, '0', STR_PAD_LEFT);
    }

    public function barcodes()
    {
        return $this->belongsTo(Barcode::class, 'barcode', 'serial_number');
    }


    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class, 'dispatch_id', 'id');
    }

    public static function filteredData($filters = [])
    {
        $fromdate = $filters['from_date'] ?? null;
        $todate = $filters['to_date'] ?? null;
        $item_id = $filters['item_id'] ?? null;
        $customer_id = $filters['customer_id'] ?? null;
        $sales_return_id = $filters['sales_return_id'] ?? null;


        $query = SalesReturn::with([
            'item',
            'customer',
            'barcodes.location',
            'barcodes.uom',
            'dispatch'
        ]);

        if ($fromdate && $todate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$fromdate, $todate]);
        } elseif ($fromdate) {
            $query->whereDate('created_at', $fromdate);
        } elseif ($todate) {
            $query->whereDate('created_at', $todate);
        }



        if ($item_id) {
            $query->where('item_id', $item_id);
        }
        if (!empty($sales_return_id)) {
            $query->where('id',     $sales_return_id);
        }


        if ($customer_id) {
            $query->where('customer_id', $customer_id);
        }

        return $query->get();
    }

    public static function reportSummary($filters = [])
    {
        $salesreturns = self::filteredData($filters);


        if ($salesreturns->isEmpty()) {
            return collect();
        }

        $data = $salesreturns->map(function ($salesReturn) {
            return [
                'sales_return_no' => $salesReturn->return_number ?? '-',
                'id'       => $salesReturn->id ?? '-',
                'dispatch_no'     => $salesReturn->dispatch->dispatch_number ?? '-',
                'item_name'       => $salesReturn->item->item_code . '/' . $salesReturn->item->name,
                'customer' => $salesReturn->customer->name ?? '-',
                'location' => $salesReturn->barcodes->location->name ?? '-',
            ];
        });


        return $data->values();
    }

    public static function reportDetailed($filters = [])
    {
        $salesreturns = self::filteredData($filters);


        if ($salesreturns->isEmpty()) {
            return collect();
        }

        $data = $salesreturns->map(function ($salesReturn) {
            return [
                'dispatch_no'     => $salesReturn->dispatch->dispatch_number ?? '-',
                'barcode' => $salesReturn->barcode ?? '-',
                'item_name'       => $salesReturn->item->item_code . '/' . $salesReturn->item->name,
                'uom'         => $salesReturn->barcodes->uom->name ?? '-',
                'spq' => $salesReturn->spq ?? '-',
                'net_weight' =>  $salesReturn->barcodes->net_weight ?? '-',
            ];
        });


        return $data->values();
    }
}
