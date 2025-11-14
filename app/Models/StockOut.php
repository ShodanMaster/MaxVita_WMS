<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockOut extends Model
{
    protected $fillable = [
        'barcode',
        'reason_id',
        'user_id',
    ];

    public function barcode()
    {
        return $this->belongsTo(Barcode::class, 'barcode', 'serial_number');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function filteredData($filters)
    {
        $fromdate = $filters['from_date'] ?? null;
        $todate = $filters['to_date'] ?? null;
        $streason = $filters['streason'] ?? null;
        $item_id = $filters['item_id'] ?? null;
        $streasonid = $filters['streasonid'] ?? null;

        $query = DB::table('stock_outs')
            ->select(
                'barcodes.item_id',
                'items.name as item_name',
                'items.item_type',
                'locations.name as location_name',
                'reasons.reason',
                'barcodes.batch_number',
                'stock_outs.created_at',
                'stock_outs.id as stoutid',
                DB::raw('COALESCE(SUM(barcodes.spq_quantity), 0) as total_spq_qty'),
                DB::raw('MAX(barcodes.serial_number) as serial_number'),
                DB::raw('COUNT(stock_outs.id) as total_records'),
                'users.username as done_by'
            )
            ->leftJoin('barcodes', 'barcodes.serial_number', '=', 'stock_outs.barcode')
            ->leftJoin('items', 'items.id', '=', 'barcodes.item_id')
            ->leftJoin('locations', 'locations.id', '=', 'barcodes.location_id')
            ->leftJoin('reasons', 'reasons.id', '=', 'stock_outs.reason_id')
            ->leftJoin('users', 'users.id', '=', 'stock_outs.user_id');

        if (!empty($fromdate) && !empty($todate)) {
            $query->whereBetween(DB::raw('DATE(stock_outs.created_at)'), [$fromdate, $todate]);
        } elseif (!empty($fromdate)) {
            $query->whereDate('stock_outs.created_at', $fromdate);
        } elseif (!empty($todate)) {
            $query->whereDate('stock_outs.created_at', $todate);
        }

        if (!empty($item_id)) {
            $query->where('barcodes.item_id', $item_id);
        }

        if (!empty($streason)) {
            $query->where('reasons.reason', 'like', "%{$streason}%");
        }

        if ($streasonid) {
            $query->where('stock_outs.id', $streasonid);
        }


        $query->groupBy(
            'barcodes.item_id',
            'items.name',
            'items.item_type',
            'locations.name',
            'reasons.reason',
            'stock_outs.created_at',
            'barcodes.batch_number',
            'users.username',
            'stock_outs.id'
        );

        return $query->get();
    }

    public static function reportSummary($filters = [])
    {
        $query = self::filteredData($filters);


        $data = $query->map(function ($item) {
            return [
                'id' => $item->stoutid ?? null,
                'item_name' => $item->item_name ?? '',
                'item_type' => $item->item_type ?? '',
                'Loc_Name'  => $item->location_name ?? '',
                'reason'    => $item->reason ?? '',
                'spq_qty'   => $item->total_spq_qty ?? 0,
            ];
        });

        return $data;
    }

    public static function reportDetailed($filters = [])
    {

        $query = self::filteredData($filters);


        $data = $query->map(function ($item) {
            return [

                'item_name' => $item->item_name ?? 'Unknown',
                'item_type' => $item->item_type ?? '',
                'batch_number' => $item->batch_number ?? '',
                'serial_number' => $item->serial_number ?? '',
                'Loc_Name'  => $item->location_name ?? '',
                'reason'    => $item->reason ?? '',
                'spq_qty'   => $item->total_spq_qty ?? 0,
                'done_by'   => $item->done_by ?? 0,
                'created_at'   => $item->created_at ?? 0,
            ];
        });

        return $data;
    }
}
