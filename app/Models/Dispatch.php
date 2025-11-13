<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'dispatch_number',
        'dispatch_date',
        'from_branch_id',
        'from_location_id',
        'dispatch_to',
        'dispatch_time',
        'dispatch_type',
        'driver_name',
        'driver_phone',
        'vehicle_number',
        'user_id',
        'status',
    ];

    public function branch(){
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function location(){
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function dispatchTo()
    {
        return $this->morphTo();
    }

    public function dispatchSubs(){
        return $this->hasMany(DispatchSub::class);
    }

    public function dispatchScans(){
        return $this->hasMany(DispatchScan::class);
    }

    public function receiptScans(){
        return $this->hasMany(ReceiptScan::class);
    }

    public static function filteredData($filters = [])
    {
        $query = Dispatch::with([
            'dispatchSubs.item',
            'dispatchSubs.uom',
            'dispatchScans',
            'dispatchScans.user',
            'dispatchScans.item',
            'dispatchScans.uom',
            'receiptScans',
            'receiptScans.user',
            'receiptScans.item',
            'receiptScans.uom',
            'receiptScans.bin'
        ])->select(
            'id',
            'dispatch_number',
            'dispatch_date',
            'from_branch_id',
            'from_location_id',
            'dispatch_to_type',
            'dispatch_to_id',
            'dispatch_time',
            'dispatch_type',
            'driver_name',
            'driver_phone',
            'vehicle_number',
            'user_id',
            'status'
        );


        $fromdate = $filters['from_date'] ?? null;
        $todate = $filters['to_date'] ?? null;
        $from_barcode   = $filters['frombarcode'] ?? null;
        $to_barcode     = $filters['tobarcode'] ?? null;
        $dispatch_no    = $filters['dispatch_no'] ?? null;
        $dispatchNumber = $filters['dispatch_number'] ?? null;
        $dispatch_type = $filters['dispatch_type'] ?? null;
        $dispatchentryid    = $filters['dispatchentryid'] ?? null;

        if ($fromdate && $todate) {
            $query->whereBetween('dispatch_date', [$fromdate, $todate]);
        } elseif ($fromdate) {
            $query->whereDate('dispatch_date', $fromdate);
        } elseif ($todate) {
            $query->whereDate('dispatch_date', $todate);
        }

        if (!empty($from_barcode) && !empty($to_barcode)) {

            $query->whereHas('dispatchScans', function ($q) use ($from_barcode, $to_barcode) {
                $q->whereBetween('barcode', [$from_barcode, $to_barcode]);
            });
        } elseif (!empty($from_barcode)) {

            $query->whereHas('dispatchScans', function ($q) use ($from_barcode) {
                $q->where('barcode', $from_barcode);
            });
        } elseif (!empty($to_barcode)) {

            $query->whereHas('dispatchScans', function ($q) use ($to_barcode) {
                $q->where('barcode', $to_barcode);
            });
        }

        if (!empty($dispatch_type)) {
            $query->where('dispatch_type', strtolower($dispatch_type));
        }

        if (!empty($dispatchentryid)) {
            $query->where('id', $dispatchentryid);
        }

        if (!empty($dispatchNumber)) {
            $query->where('id', $dispatchNumber);
        }

        if (!empty($dispatch_no)) {
            $query->where('dispatch_number', 'like', "%{$dispatch_no}%");
        }

        if (!empty($dispatchentryid)) {
            $query->where('id', $dispatchentryid);
        }

        return $query->get();
    }


    public static function reportSummary($filters = [])
    {
        $dispatches = self::filteredData($filters);

        $data =  $dispatches->map(function ($dispatch, $index) {
            $status = '';
            switch ($dispatch->status) {
                case 0:
                    $status = "open";
                    break;
                case 1:
                    $status = "completed";
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
                'id' => $dispatch->id,
                'dispatch_number' => $dispatch->dispatch_number,
                'dispatch_date' => $dispatch->dispatch_date
                    ? date('d-m-Y', strtotime($dispatch->dispatch_date))
                    : '-',
                'branch_name' => $dispatch->branch->name ?? '-',
                'location_name' => $dispatch->location->name ?? '-',
                'dispatch_type' => ucfirst($dispatch->dispatch_type ?? 'N/A'),
                'status' => $status ?? '-',

            ];
        });

        return $data;
    }

    public static function reportDetailed($filters = [])
    {

        $plans = self::filteredData($filters);

        $data = $plans->flatMap(function ($plan) {
            $subs = $plan->dispatchSubs;

            return $subs->map(function ($sub) use ($plan) {
                return [
                    'dispatch_number'         => $plan->dispatch_number,
                    'dispatch_date'           => $plan->dispatch_date ? date('d-m-Y', strtotime($plan->dispatch_date)) : '-',
                    'item_name'           => ($sub->item->item_code ?? '-') . ' - ' . ($sub->item->name ?? '-'),
                    'uom'                 => $sub->uom->name ?? '-',
                    'total_quantity'      => $sub->total_quantity ?? 0,
                    'dispatched_quantity' => $sub->dispatched_quantity ?? 0,
                    'received_quantity'   => $sub->received_quantity ?? 0,
                ];
            });
        });
        return $data;
    }

    public static function scannedBarcodes($filters = [])
    {
        $plans = self::filteredData($filters);

        $data = $plans->flatMap(function ($plan) {

            return $plan->dispatchScans->map(function ($scan) use ($plan) {
                return [
                    'dispatch_number'      => $plan->dispatch_number,
                    'dispatch_date'        => $plan->dispatch_date
                        ? date('d-m-Y', strtotime($plan->dispatch_date))
                        : '',
                    'item_name'            => trim(($scan->item->item_code ?? '') . ' / ' . ($scan->item->name ?? '')),
                    'uom'                  => $scan->uom->name ?? '-',
                    'barcode'              => $scan->barcode ?? '',
                    'dispatched_quantity'  => $scan->dispatched_quantity ?? 0,
                    'dispatch_id'          => $scan->dispatch_id ?? '',
                    'user_name'            => $scan->user->username ?? '-',
                    'scan_time'            => $scan->scan_time
                        ? date('d-m-Y H:i:s', strtotime($scan->scan_time))
                        : '-',
                ];
            });
        });


        return $data;
    }

    public static function receiptBarcodes($filters = [])
    {
        $plans = self::filteredData($filters);

        $data = $plans->flatMap(function ($dispatch) {
            return $dispatch->receiptScans->map(function ($scan) use ($dispatch) {
                return [
                    'dispatch_number' => $dispatch->dispatch_number,
                    'dispatch_date' => $dispatch->dispatch_date ? Carbon::parse($dispatch->dispatch_date)->format('d-m-Y') : '',
                    'barcode'         => $scan->barcode,
                    'item_name' => (($scan->item->name ?? '') . ' / ' . ($scan->item->item_code ?? '')),
                    'scanned_qty'     => $scan->scanned_quantity,
                    'uom'             => $scan->uom->name ?? '',
                    'bin_name'        => $scan->bin->name ?? '-',
                    'scanned_by'      => $scan->user->name ?? '',
                    'scan_time'            => $scan->scan_time
                        ? date('d-m-Y H:i:s', strtotime($scan->scan_time))
                        : '-',
                ];
            });
        });

        return $data;
    }
}
