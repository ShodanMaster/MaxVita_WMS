<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Grn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'grn_number',
        'purchase_number',
        'invoice_number',
        'invoice_date',
        'vendor_id',
        'location_id',
        'grn_type',
        'remarks',
        'branch_id',
        'status',
    ];

    public static function grnNumber()
    {
        $yr = 'GRN' . date("y");
        $len = strlen($yr);

        return DB::transaction(function () use ($yr, $len) {
            // Lock the table to prevent concurrent access
            DB::statement('LOCK TABLES grns WRITE');

            $result = DB::select("
                SELECT CONCAT('$yr', LPAD(max_grn_number + 1, GREATEST(5, LENGTH(max_grn_number + 1)), '0')) grn_number
                FROM (
                    SELECT IFNULL(MAX(SUBSTRING(`grn_number`, $len + 1)), '0') AS max_grn_number
                    FROM grns
                    WHERE grn_number LIKE '$yr%'
                ) p2
            ");

            $grnNumber = $result[0]->grn_number;

            DB::statement('UNLOCK TABLES');

            return $grnNumber;
        });
    }


    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function barcodes()
    {
        return $this->hasMany(Barcode::class);
    }

    public function grnSubs(){
        return $this->hasMany(GrnSub::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function grnPurchaseOrder()
    {
        return $this->belongsTo(GrnPurchaseOrder::class, 'purchase_number', 'id');
    }

    public function grnPurchaseOrderSubs()
    {
        return $this->hasManyThrough(GrnPurchaseOrderSub::class, GrnPurchaseOrder::class, 'id', 'grn_purchase_order_id', 'purchase_number', 'id');
    }


    public static function filteredData($filters)
    {
        $fromdate     = $filters['from_date'] ?? null;
        $todate       = $filters['to_date'] ?? null;
        $grn_type      = $filters['grn_type'] ?? null;
        $item_id       = $filters['item_id'] ?? null;
        $grnNumber        = $filters['grn_number'] ?? null;
        $grn_numberid  = $filters['grn_numberid'] ?? null;


        $query = Grn::with(['vendor', 'location', 'grnSubs.item']);


        if (!empty($fromdate) && !empty($todate)) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$fromdate, $todate]);
        } elseif (!empty($fromdate)) {
            $query->whereDate('created_at', $fromdate);
        } elseif (!empty($todate)) {
            $query->whereDate('created_at', $todate);
        }

        if (!empty($grnNumber)) {
            $query->where('grn_number', 'like', "%{$grnNumber}%");
        }

        if (!empty($grn_numberid)) {
            $query->where('id', $grn_numberid);
        }



        if (!empty($grn_type)) {
            $query->where('grn_type', $grn_type);
        }



        if (!empty($item_id)) {
            $query->whereHas('grnSubs', function ($q) use ($item_id) {
                $q->where('item_id', $item_id);
            });
        }



        return $query->get();
    }

    public static function reportSummary($filters = [])
    {
        $grns = self::filteredData($filters);

        $data = $grns->map(function ($grn) {
            return [
                'id' => $grn->id,
                'grn_number' => $grn->grn_number,
                'purchase_number' => $grn->purchase_number,
                'vendor_name' => $grn->vendor?->name ?? '',
                'invoice_number' => $grn->invoice_number,
                'invoice_date' => $grn->invoice_date,
                'location_name' => $grn->location?->name ?? '',
                'created_at' => $grn->created_at,
            ];
        });

        return $data;
    }

    public static function reportpowise($filters = [])
    {

        $grns = self::filteredData($filters)
            ->whereNotNull('purchase_number');

        $data = $grns->flatMap(function ($grn) {
            $subs = $grn->grnsubs;

            $i = 1;
            return $subs->map(function ($sub) use ($grn, &$i) {
                return [
                    'slno' => $i++,
                    'grn_number' => $grn->grn_number,
                    'id'=>$grn->id,
                    'purchase_number' => $grn->purchase_number,
                    'item_name' => $sub->item?->name ?? '',
                    'item_id' => $sub->item?->id ?? '',
                    'quantity' => $sub->total_quantity,
                    'uom_name' => $sub->item?->uom?->name ?? '',
                    'batch_number' => $sub->batch_number,
                    'spq_quantity' => $sub->item?->spq_quantity ?? '',
                    'date_of_manufacture' => !empty($sub->date_of_manufacture)
                        ? date('d-m-Y', strtotime($sub->date_of_manufacture))
                        : '',
                    'best_before_date' => !empty($sub->best_before_date)
                        ? date('d-m-Y', strtotime($sub->best_before_date))
                        : '',
                    'number_of_barcodes' => $sub->number_of_barcodes ?? 0,
                ];
            });
        });

        return $data;
    }

    public static function reportitemwise($filters = [])
    {
        $grns = self::filteredData($filters);

        $item_id = $filters['item_id'] ?? null;

        $data = $grns->flatMap(function ($grn) use ($item_id) {
            $subs = $grn->grnSubs;

            if ($item_id) {
                $subs = $subs->filter(function ($sub) use ($item_id) {
                    return $sub->item_id == $item_id;
                });
            }
            $i = 1;

            return $subs->map(function ($sub) use ($grn, &$i) {
                return [
                    'slno' => $i++,
                    'grn_id' => $grn->id,
                    'item_id' => $sub->item_id,
                    'grn_number' => $grn->grn_number,
                    'item_name' => $sub->item?->name ?? '',
                    'total_quantity' => $sub->total_quantity,
                    'uom_name' => $sub->item?->uom?->name ?? '',
                    'batch_number' => $sub->batch_number,
                    'spq_quantity' => $sub->item?->spq_quantity ?? '',
                    'date_of_manufacture' => $sub->date_of_manufacture ? date('d-m-Y', strtotime($sub->date_of_manufacture)) : '',
                    'best_before_date' => $sub->best_before_date ? date('d-m-Y', strtotime($sub->best_before_date)) : '',
                    'number_of_barcodes' => $sub->number_of_barcodes ?? 0,
                ];
            });
        });

        return $data;

    }

    public static function reportdetailed($filters = [])
    {
        $grnData = self::filteredData($filters);

        $item_id = $filters['item_id'] ?? null;
        $grnId  = $filters['grn_numberid'] ?? null;

        if ($grnId) {
            $grnData = $grnData->where('id', $grnId);
        }

        $data = $grnData->flatMap(function ($grn) use ($item_id) {
            $subs = $grn->grnSubs;

            if ($item_id) {
                $subs = $subs->filter(function ($sub) use ($item_id) {
                    return $sub->item_id == $item_id;
                });
            }


            $i = 1;

            return $subs->flatMap(function ($sub) use ($grn, &$i) {
                $barcodes = $sub->barcodes->where('transaction_type', 1)->where('transaction_id', $grn->id) ?? collect([null]);

                return $barcodes->map(function ($barcode) use ($grn, $sub, &$i) {
                    return [
                        'slno' => $i++,
                        'grn_number' => $grn->grn_number ?? '',
                        'item_name' => $sub->item ? ($sub->item->name . '/' . $sub->item->item_code) : '',
                        'serial_number' => $barcode->serial_number ?? '',
                        'net_weight' => $barcode->net_weight ?? '',
                        'batch_number' => $sub->batch_number ?? '',
                        'price' => $sub->price ?? '',
                        'total_price' => $sub->total_price ?? '',
                        'date_of_manufacture' => $sub->date_of_manufacture
                            ? Carbon::parse($sub->date_of_manufacture)->format('d-m-Y') : '',
                        'best_before_date' => $sub->best_before_date
                            ? Carbon::parse($sub->best_before_date)->format('d-m-Y') : '',
                        'created_at' => $grn->created_at
                            ? Carbon::parse($grn->created_at)->format('d-m-Y') : '',
                    ];
                });
            });
        });

        return $data;
    }
}
