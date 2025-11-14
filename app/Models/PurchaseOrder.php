<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'location_id',
        'branch_id',
        'purchase_number',
        'vendor_id',
        'purchase_date',
        'status',
        'user_id',
    ];

    // public static function nextNumber()
    // {
    //     $prefix = 'PR' . date('ym');
    //     $len = strlen($prefix);

    //     $purchaseNumber = DB::select("
    //         SELECT CONCAT(
    //             '$prefix',
    //             LPAD(CAST(IFNULL(MAX(SUBSTRING(purchase_number, $len + 1)), '0') + 1 AS UNSIGNED), 5, '0')
    //         ) AS purchase_number
    //         FROM purchase_orders
    //         WHERE purchase_number LIKE '$prefix%'
    //     ");

    //     return $purchaseNumber[0]->purchase_number;
    // }

    public function purchaseOrderSubs()
    {
        return $this->hasMany(PurchaseOrderSub::class);
    }

    public function location(){
        return $this->belongsTo(Location::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function grnPurchaseOrderSubs(){
        return $this->hasMany(GrnPurchaseOrderSub::class);
    }

    public static function reportSummary($filters = [])
    {
        $purchaseOrders = self::filteredData($filters);

        $data = $purchaseOrders->map(function ($order) use ($purchaseOrders) {

            $status = '';
            switch ($order->status) {
                case 0:
                    $status = "open";
                    break;
                case 1:
                    $status = "close";
                    break;
                case 2:
                    $status = "canceled";
                    break;
                default:
                    $status = "unknown";
                    break;
            }

            return [
                'id' => $order->id,
                'purchase_number' => $order->purchase_number,
                'purchase_date' => $order->purchase_date,
                'vendor_name' => $order->vendor->name ?? '',
                'location' => $order->location->name,
                'branch' => $order->branch->name,
                'status' => $status,
            ];
        });

        return $data;
    }


    public static function reportDetailed($filters = [])
    {
        $item_id = $filters['item_id'] ?? null;

        $purchaseOrders = self::filteredData($filters);

        $data = $purchaseOrders->flatMap(function ($order) use ($item_id) {
            $subs = $order->purchaseOrderSubs;

            if ($item_id) {
                $subs = $subs->filter(fn($sub) => $sub->item_id == $item_id);
            }

            return $subs->map(function ($sub) use ($order) {
                return [
                    'purchase_number' => $order->purchase_number,
                    'purchase_date' => $order->purchase_date,
                    'item_name' => (!empty($sub->item->item_code) && !empty($sub->item->name))
                        ? $sub->item->item_code . '/' . $sub->item->name
                        : '',
                    // 'vendor' =>
                    'quantity' => $sub->quantity,
                    'picked_quantity' => $sub->picked_quantity,
                ];
            });
        })->values();

        return $data;
    }

    private static function filteredData($filters)
    {
        $query = PurchaseOrder::with(['purchaseOrderSubs.item']);

        $fromdate = $filters['from_date'] ?? null;
        $todate = $filters['to_date'] ?? null;
        $porderno = $filters['porderno'] ?? null;
        $item_id = $filters['item_id'] ?? null;
        $purchaseOrderid = $filters['purchaseOrderid'] ?? null;

        if ($fromdate && $todate) {
            $query->whereBetween('purchase_date', [$fromdate, $todate]);
        } elseif ($fromdate) {
            $query->whereDate('purchase_date', $fromdate);
        } elseif ($todate) {
            $query->whereDate('purchase_date', $todate);
        }

        if ($porderno) {
            $query->where('purchase_number', 'like', "%{$porderno}%");
        }
        if ($item_id) {
            $query->whereHas('purchaseOrderSubs', function ($q) use ($item_id) {
                $q->where('item_id', $item_id);
            });
        }

        if ($purchaseOrderid) {
            $query->where('id', $purchaseOrderid);
        }

        return $query->get();

    }
}
