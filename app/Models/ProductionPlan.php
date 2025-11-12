<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plan_number',
        'plan_date',
        'total_quantity',
        'picked_quantity',
        'scanned_quantity',
        'item_id',
        'user_id',
        'branch_id',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($plan) {
            $user = Auth::user();
            if ($user) {
                $plan->user_id = $user->id;
                $plan->branch_id = $user->branch_id;
            }
        });
    }

    public static function nextNumber()
    {
        $prefix = 'PP' . date('ym');
        $len = strlen($prefix);

        $planNumber = DB::select("
            SELECT CONCAT(
                '$prefix',
                LPAD(CAST(IFNULL(MAX(SUBSTRING(plan_number, $len + 1)), '0') + 1 AS UNSIGNED), 5, '0')
            ) AS plan_number
            FROM production_plans
            WHERE plan_number LIKE '$prefix%'
        ");

        return $planNumber[0]->plan_number;
    }

    public function productionPlanSubs(){
        return $this->hasMany(ProductionPlanSub::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function productionIssues(){
        return $this->hasMany(ProductionIssue::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


    public static function filteredData($filters = [])
    {
        $query = ProductionPlan::with([
            'user',
            'branch',
            'productionPlanSubs.item',
            'productionPlanSubs.productionIssues.item',
            'productionPlanSubs.productionIssues'
        ])
            ->select('id', 'plan_number', 'plan_date', 'total_quantity', 'picked_quantity', 'status', 'user_id', 'branch_id', 'item_id');

        $fromdate = $filters['from_date'] ?? null;
        $todate = $filters['to_date'] ?? null;
        $from_barcode   = $filters['frombarcode'] ?? null;
        $to_barcode     = $filters['tobarcode'] ?? null;
        $plan_no        = $filters['plan_no'] ?? null;
        $plan_number    = $filters['plan_number'] ?? null;
        $status = $filters['status'] ?? null;
        $productionplanid = $filters['productionplanid'] ?? null;


        if ($fromdate && $todate) {
            $query->whereBetween('plan_date', [$fromdate, $todate]);
        } elseif ($fromdate) {
            $query->whereDate('plan_date', $fromdate);
        } elseif ($todate) {
            $query->whereDate('plan_date', $todate);
        }

        if (!empty($from_barcode) && !empty($to_barcode)) {

            $query->whereHas('productionIssues', function ($q) use ($from_barcode, $to_barcode) {
                $q->whereBetween('barcode', [$from_barcode, $to_barcode]);
            });
        } elseif (!empty($from_barcode)) {

            $query->whereHas('productionIssues', function ($q) use ($from_barcode) {
                $q->where('barcode', $from_barcode);
            });
        } elseif (!empty($to_barcode)) {

            $query->whereHas('productionIssues', function ($q) use ($to_barcode) {
                $q->where('barcode', $to_barcode);
            });
        }

        if (!empty($plan_no)) {
            $query->where('id',   $plan_no);
        }

        if ($plan_number) {
            $query->where('plan_number', 'like', "%{$plan_number}%");
        }

        if (isset($status) && $status !== '') {
            $query->where('status', $status);
        }

        if ($productionplanid) {
            $query->where('id',  $productionplanid);
        }

        return $query->get();
    }

    public static function reportSummary($filters = [])
    {
        $plans = self::filteredData($filters);

        $data = $plans->map(function ($plan, $index) {
            $status = '';
            switch ($plan->status) {
                case 0:
                    $status = "production issue pending";
                    break;
                case 1:
                    $status = "barcode generation pending";
                    break;
                case 2:
                    $status = "fg storage scan pending";
                    break;
                case 3:
                    $status = "closed";
                    break;
                default:
                    $status = "unknown";
                    break;
            }
            return [
                'slno' => $index + 1,
                'id' => $plan->id,
                'plan_no' => $plan->plan_number,
                'plan_date' => $plan->plan_date ? date('d-m-Y', strtotime($plan->plan_date)) : '-',
                'branch_name' => $plan->branch->name ?? '-',
                'item_name' => $plan->item
                    ? $plan->item->item_code . ' - ' . $plan->item->name
                    : '-',
                'planned_qty' => $plan->total_quantity ?? 0,
                'picked_qty' => $plan->picked_quantity ?? 0,
                'entry_user' => $plan->user->username ?? '-',
                'status' => $status ?? '-',

            ];
        });

        return $data;
    }

    public static function reportDetailed($filters = [])
    {

        $plans = self::filteredData($filters);

        $data = $plans->flatMap(function ($plan) {
            $subs = $plan->productionPlanSubs;

            return $subs->map(function ($sub) use ($plan) {
                return [
                    'plan_no'          => $plan->plan_number,
                    'plan_date' => $plan->plan_date ? date('d-m-Y', strtotime($plan->plan_date)) : '-',
                    'branch_name' => $plan->branch->name ?? '-',
                    'item_name'        => $sub->item
                        ? $sub->item->item_code . ' / ' . $sub->item->name
                        : '-',
                    'total_quantity' => $sub->total_quantity ?? 0,
                    'picked_quantity'       => $sub->picked_quantity ?? 0,
                ];
            });
        })->values();

        return $data;
    }

    public static function scannedBarcodes($filters = [])
    {
        $plans = self::filteredData($filters);
        $data = $plans->flatMap(function ($plan) {
                return $plan->productionIssues->map(function ($issue) use ($plan) {
                    return [
                        'plan_no' => $plan->plan_number,
                        'plan_date'   => !empty($plan->plan_date)
                            ? date('d-m-Y', strtotime($plan->plan_date))
                            : '',
                        'branch_name' => $plan->branch->name ?? '',
                        'item_name'          => trim(($issue->item->item_code ?? '') . ' / ' . ($issue->item->name ?? '')),
                        'barcode' => $issue->barcode ?? '',
                        'scanned_quantity' => $issue->scanned_quantity ?? 0,
                        'net_weight' => $issue->net_weight ?? 0,
                        'production_plan_id' => $issue->production_plan_id ?? '',
                        'user_name' => $issue->user->username ?? '',
                        'scan_time' => $issue->scan_time
                            ? date('d-m-Y H:i:s', strtotime($issue->scan_time))
                            : '',
                    ];
                });
            });

        return $data;
    }
}
