<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Barcode extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'serial_number',
        'transaction_id',
        'transaction_type',
        'branch_id',
        'location_id',
        'bin_id',
        'item_id',
        'date_of_manufacture',
        'best_before_date',
        'batch_number',
        'price',
        'total_price',
        'shelf_life',
        'spq_quantity',
        'net_weight',
        'grn_spq_quantity',
        'grn_net_weight',
        'barcode_time',
        'status',
        'user_id',
        'qc_approval_status',
    ];

    public static function nextNumber($locationId){

        $locationPrefix = Location::find($locationId)->prefix;
        $prefix = $locationPrefix . date('ymd');

        $row = DB::transaction(function () use ($locationId) {
            $today = date('Y-m-d');
            $sequence = DB::table('barcode_sequences')
                ->where('location_id', $locationId)
                ->where('sequence_date', $today)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                DB::table('barcode_sequences')->insert([
                    'location_id' => $locationId,
                    'current_number' => 1,
                ]);
                $current = 1;
            } else {
                $current = $sequence->current_number + 1;
                DB::table('barcode_sequences')
                    ->where('location_id', $locationId)
                    ->update(['current_number' => $current]);
            }

            return $current;
        });

        return $prefix . str_pad($row, 6, '0', STR_PAD_LEFT);
    }


    public function grn(){
        return $this->belongsTo(Grn::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public static function stockReportSummary($filters = [])
    {

        $stock = self::filteredData($filters);

        $data = $stock->map(function ($row, $index) {

            return [
                'slno' => $index + 1,
                'branch_name' => optional($row->branch)->name,
                'item_name' => isset($row->item)
                    ? $row->item->item_code . ' - ' . $row->item->name
                    : '-',
                'location_name' => $row->location->name ?? '-',
                'bin_name' => $row->bin->name ?? '-',
                'batch_number' => $row->batch_number,
                'net_weight' => $row->net_weight,
                'uom_name' => $row->uom?->name ?? '-',
                'spq_quantity' => $row->spq_quantity,
                'count' => $row->count,
                'qc_status' => $row->qc_approval_status,
                'status' => $row->status,
                'branch_id' => $row->branch->id,
                'location_id' => $row->location->id,
                'item_id' => $row->item->id,

            ];
        });

        return $data;
    }


    public static function stockreportbinwise($filters = [])
    {
        $stock = self::filteredData($filters);

        $data = $stock->map(function ($row, $index) {
            return [
                'slno' => $index + 1,
                'branch_name' => optional($row->branch)->name,
                'location_name' => $row->location->name ?? '-',
                'item_name' => isset($row->item)
                    ? $row->item->item_code . ' - ' . $row->item->name
                    : '-',
                'batch_number' => $row->batch_number,
                'bin_name' => $row->bin->name ?? '-',
                'net_weight' => $row->net_weight,
                'uom_name' => $row->uom?->name ?? '-',
                'spq_quantity' => $row->spq_quantity,
                'count' => $row->count,
                'branch_id'   => optional($row->branch)->id,
                'location_id' => optional($row->location)->id,
                'bin_id'      => optional($row->bin)->id,
                'item_id'     => optional($row->item)->id,
            ];
        });
        return $data;
    }



    public static function reportDetailed($filters = [])
    {

        $stocks = self::filteredData($filters);

        $data = $stocks->flatMap(function ($row) {

            $serialNumbers = array_filter(array_map('trim', explode(',', $row->serial_numbers ?? '')));
            $slno = 1;

            return collect($serialNumbers)->map(function ($serial) use ($row, &$slno) {
                return [
                    'slno'              => $slno++,
                    'branch_name'       => $row->branch->name ?? '-',
                    'location_name'     => $row->location->name ?? '-',
                    'bin_name'          => $row->bin->name ?? '-',
                    'serial'            => $serial,
                    'item_name'         => isset($row->item)
                        ? "{$row->item->item_code} - {$row->item->name}"
                        : '-',
                    'category_name'     => $row->category_name ?? '-',
                    'uom_name'          => $row->uom->name ?? '-',
                    'spq_quantity'      => $row->spq_quantity ?? 0,
                    'price'             => $row->price ?? 0,
                    'total_price'       => $row->total_price ?? 0,
                    'net_weight'        => $row->net_weight ?? 0,
                    'date_of_manufacture' => $row->date_of_manufacture ?? '-',
                    'best_before_date'  => $row->best_before_date ?? '-',
                    'days_before_expiry' => $row->days_before_expiry ?? '-',
                    'scan_time'         => $row->scan_time ?? '-',
                    'count'             => $row->count ?? 0,
                ];
            });
        });


        return $data;
    }



    public static function stockReportExpirewise($filters = [])
    {
        $stock = self::filteredData($filters);

        $data = $stock->map(function ($row, $index) {
            return [
                'slno' => $index + 1,
                'item_name' => $row->item->name ?? '-',
                'item_code' => $row->item->item_code ?? '-',
                'already_expired' => $row->already_expired ?? 0,
                'less_than_one_months' => $row->expiring_soon ?? 0,
                'less_than_six_months' => $row->expiring_in_1_to_6_months ?? 0,
                'less_than_one_year' => $row->expiring_in_6_to_12_months ?? 0,
                'more_than_one_year' => $row->expiring_more_than_1_year ?? 0,
            ];
        });

        return $data;
    }



    private static function filteredData($filters)
    {

        $fromdate      = $filters['from_date'] ?? null;
        $todate        = $filters['to_date'] ?? null;
        $from_barcode  = $filters['from_barcode'] ?? null;
        $to_barcode    = $filters['to_barcode'] ?? null;
        $category_id   = $filters['category_id'] ?? null;
        $grn_type      = $filters['grn_type'] ?? null;


        $query = Barcode::query()
            ->with(['item', 'branch', 'location', 'bin', 'uom'])
            ->join('items', 'items.id', '=', 'barcodes.item_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->join('uoms', 'uoms.id', '=', 'barcodes.uom_id')
            ->join('branches', 'branches.id', '=', 'barcodes.branch_id')
            ->join('locations', 'locations.id', '=', 'barcodes.location_id')
            ->join('bins', 'bins.id', '=', 'barcodes.bin_id')
            ->leftJoin('grns', 'grns.id', '=', 'barcodes.transaction_id')
            ->select(
                'barcodes.item_id',
                'barcodes.branch_id',
                'barcodes.location_id',
                'barcodes.bin_id',
                'barcodes.uom_id',
                'barcodes.batch_number',
                'barcodes.best_before_date',
                'barcodes.date_of_manufacture',
                'barcodes.qc_approval_status',
                'barcodes.status',
                'barcodes.transaction_id',
                'items.item_type',
                'items.category_id',
                'categories.name as category_name',
                'items.price',
                DB::raw('SUM(barcodes.net_weight) AS net_weight'),
                DB::raw('SUM(barcodes.spq_quantity) AS spq_quantity'),
                DB::raw('SUM(barcodes.spq_quantity * items.price) AS total_price'),
                DB::raw('CASE
                WHEN DATEDIFF(barcodes.best_before_date, CURDATE()) < 0 THEN 0
                ELSE DATEDIFF(barcodes.best_before_date, CURDATE())
                END AS days_before_expiry'),
                DB::raw('COUNT(CASE WHEN barcodes.best_before_date < NOW() THEN 1 END) AS already_expired'),
                DB::raw('COUNT(CASE WHEN barcodes.best_before_date >= NOW() AND barcodes.best_before_date <= DATE_ADD(NOW(), INTERVAL 1 MONTH) THEN 1 END) AS expiring_soon'),
                DB::raw('COUNT(CASE WHEN barcodes.best_before_date > DATE_ADD(NOW(), INTERVAL 1 MONTH) AND barcodes.best_before_date <= DATE_ADD(NOW(), INTERVAL 6 MONTH) THEN 1 END) AS expiring_in_1_to_6_months'),
                DB::raw('COUNT(CASE WHEN barcodes.best_before_date > DATE_ADD(NOW(), INTERVAL 6 MONTH) AND barcodes.best_before_date <= DATE_ADD(NOW(), INTERVAL 1 YEAR) THEN 1 END) AS expiring_in_6_to_12_months'),
                DB::raw('COUNT(CASE WHEN barcodes.best_before_date > DATE_ADD(NOW(), INTERVAL 1 YEAR) THEN 1 END) AS expiring_more_than_1_year'),
                DB::raw('MAX(barcodes.barcode_time) AS scan_time'),
                DB::raw('GROUP_CONCAT(barcodes.serial_number) AS serial_numbers'),
                DB::raw('COUNT(barcodes.serial_number) AS count')
            );

        $filtersMap = [
            'branch_id'     => 'barcodes.branch_id',
            'item_id'       => 'barcodes.item_id',
            'location_id'   => 'barcodes.location_id',
            'bin_id'        => 'barcodes.bin_id',
            'batch_number'  => 'barcodes.batch_number',
            'stock_type'    => 'barcodes.status',
        ];

        foreach ($filtersMap as $key => $column) {
            if (!empty($filters[$key])) {
                $query->where($column, $filters[$key]);
            }
        }

        if (!empty($fromdate) && !empty($todate)) {
            $query->whereBetween(DB::raw('DATE(barcode_time)'), [$fromdate, $todate]);
        } elseif (!empty($fromdate)) {
            $query->whereDate('barcode_time', $fromdate);
        } elseif (!empty($todate)) {
            $query->whereDate('barcode_time', $todate);
        }

        if (!empty($category_id)) {
            $query->where('items.category_id',      $category_id);
        }



        if (!empty( $grn_type )) {
            $query->where('items.item_type',   $grn_type );
        }



        if ($from_barcode && $to_barcode) {
            $query->whereBetween("barcodes.serial_number", [$from_barcode, $to_barcode]);
        } elseif ($from_barcode) {
            $query->where('barcodes.serial_number', '>=', $from_barcode);
        } elseif ($to_barcode) {
            $query->where('barcodes.serial_number', '<=', $to_barcode);
        }


        $query->groupBy(
            'barcodes.item_id',
            'barcodes.branch_id',
            'barcodes.location_id',
            'barcodes.bin_id',
            'barcodes.uom_id',
            'barcodes.batch_number',
            'barcodes.date_of_manufacture',
            'barcodes.qc_approval_status',
            'barcodes.status',
            'barcodes.transaction_id',
            'items.category_id',
            'items.price',
            'categories.name',
            'barcodes.best_before_date',
             'items.item_type'
        );

        return $query->get();
    }
}
