<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class StorageScan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'barcode',
        'item_id',
        'grn_id',
        'bin_id',
        'scanned_quantity',
        'batch_number',
        'net_weight',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function grn()
    {
        return $this->belongsTo(Grn::class, 'grn_id', 'id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public static function filteredData($filters = [])
    {
        $fromdate = $filters['from_date'] ?? null;
        $todate = $filters['to_date'] ?? null;
        $fromBarcode = $filters['from_barcode'] ?? null;
        $toBarcode = $filters['to_barcode'] ?? null;
        $itemId = $filters['item_id'] ?? null;
        $transactionType = $filters['transaction_type'] ?? null;



        $grnQuery = DB::table('storage_scans as gs')
            ->select([
                'gs.barcode',
                'i.id as item_id',
                'i.name as item_name',
                DB::raw("'GRN' as source_type"),
                'gs.grn_id',
                DB::raw('NULL as production_plan_id'),
                'b.name as bin_name',
                DB::raw('NULL as location_name'),
                'gs.scanned_quantity as quantity',
                'gs.net_weight',
                'u.id as user_id',
                'u.name as scanned_by',
                'gs.created_at as scan_time',
            ])
            ->leftJoin('items as i', 'i.id', '=', 'gs.item_id')
            ->leftJoin('bins as b', 'b.id', '=', 'gs.bin_id')
            ->leftJoin('users as u', 'u.id', '=', 'gs.user_id');



        if ($fromdate) {
            $grnQuery->whereDate('gs.created_at', '>=',  $fromdate);
        }
        if ($todate) {
            $grnQuery->whereDate('gs.created_at', '<=',  $todate);
        }
        if ($fromBarcode) {
            $grnQuery->where('gs.barcode', '>=', $fromBarcode);
        }
        if ($toBarcode) {
            $grnQuery->where('gs.barcode', '<=', $toBarcode);
        }
        if ($itemId) {
            $grnQuery->where('gs.item_id', $itemId);
        }


        $productionQuery = DB::table('production_scans as ps')
            ->select([
                'ps.barcode',
                'ps.item_id',
                'i.name as item_name',
                DB::raw("'Production' as source_type"),
                DB::raw('NULL as grn_id'),
                'ps.production_plan_id',
                'b.name as bin_name',
                'l.name as location_name',
                'ps.scanned_quantity as quantity',
                'ps.net_weight',
                'ps.user_id',
                'u.username as scanned_by',
                'ps.scan_time as scan_time',
            ])
            ->join('items as i', 'ps.item_id', '=', 'i.id')
            ->join('users as u', 'ps.user_id', '=', 'u.id')
            ->join('bins as b', 'ps.bin_id', '=', 'b.id')
            ->leftJoin('locations as l', 'b.location_id', '=', 'l.id');



        if ($fromdate) {
            $productionQuery->whereDate('ps.scan_time', '>=', $fromdate);
        }
        if ($todate) {
            $productionQuery->whereDate('ps.scan_time', '<=', $todate);
        }
        if ($fromBarcode) {
            $productionQuery->where('ps.barcode', '>=', $fromBarcode);
        }
        if ($toBarcode) {
            $productionQuery->where('ps.barcode', '<=', $toBarcode);
        }
        if ($itemId) {
            $productionQuery->where('ps.item_id', $itemId);
        }


        if (!empty($transactionType) && strtoupper($transactionType) === 'GRN') {
            return $grnQuery->orderBy('scan_time', 'desc')->get();
        } elseif (!empty($transactionType) && strtoupper($transactionType) === 'PRODUCTION') {
            return $productionQuery->orderBy('scan_time', 'desc')->get();
        }

        $combined = $grnQuery->unionAll($productionQuery);

        $final = DB::table(DB::raw("({$combined->toSql()}) as all_scans"))
            ->mergeBindings($combined)
            ->orderBy('scan_time', 'desc')
            ->get();

        return $final;
    }



    public static function reportSummary($filters = [])
    {
        $plans = self::filteredData($filters);


        $data = $plans->map(function ($plan, $index) {
            return [
                'slno' => $index + 1,
                'barcode' => $plan->barcode,
                'item_id' => $plan->item_id,
                'item_name' => $plan->item_name,
                'source_type' => $plan->source_type,
                'bin_name' => $plan->bin_name,
                'location_name' => $plan->location_name,
                'quantity' => $plan->quantity,
                'net_weight' => $plan->net_weight,
                'user_id' => $plan->user_id,
                'scanned_by' => $plan->scanned_by,
                'scan_time' => $plan->scan_time,
            ];
        });

        return $data;
    }
}
